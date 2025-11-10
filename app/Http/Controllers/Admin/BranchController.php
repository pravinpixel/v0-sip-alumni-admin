<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\IAlertURBillingAccountInvoice;
use Illuminate\Http\Request;
use App\Models\BranchLocation;
use App\Models\Employee;
use App\Models\Iallert;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class BranchController extends Controller
{
    public function index(Request $request)
    {

        $search = $request->input('search');
        $status = $request->input('status');
        $perPage = $request->input('pageItems');

        $query = BranchLocation::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
                $q->orWhere('branch_code', 'like', '%' . $search . '%');
            });
        }

        if ($status === '1' || $status === '0') {
            $query->where('status', $status);
        }

        $datas = $query->orderBy('id', 'desc')->paginate($perPage);
        $currentPage = $datas->currentPage();
        $serialNumberStart = ($currentPage - 1) * $perPage + 1;

        $total_count = BranchLocation::count();

        $branches = BranchLocation::where('status', '1')->get();



        return view('masters.branch.index', [

            'datas' => $datas,
            'selectedStatus' => $status,
            'search' => $search,
            'branches' => $branches,
            'total_count' => $total_count,
            'serialNumberStart' => $serialNumberStart,
        ]);
    }

    public function getChildBranches($id)
    {
        $branches = BranchLocation::where('parent_id', $id)->get(['id', 'name']);
        return response()->json($branches);
    }

    public function get(Request $request, $id)
    {
        $data = BranchLocation::find($id);

        return response()->json($data);
    }

    public function save(Request $request)
    {
        // if($request->parent_branch && $request->parent_branch != '#' ){
        //     $parentBranch = BranchLocation::find($request->parent_branch);
        //     if ($parentBranch) {
        //         if($parentBranch->name == $request->name){
        //             return $this->returnError('Parent branch and child branch name should not be same');
        //         }
        //     }
        // }
        $form_data = $request->validate([
            'branch_code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('branch_locations')->whereNull('deleted_at'),
            ],
            'parent_branch' => [
                'nullable',
                'string',
                'max:50',
            ],
            'name' => [
                'required',
                'string',
                'max:200',
                Rule::unique('branch_locations')->whereNull('deleted_at'),
            ],
            'status' => 'required|boolean',

        ]);
        try {

            $branch = new BranchLocation;
            $branch->branch_code = $request->input('branch_code');
            $branch->parent_id = $request->input('parent_branch') == '#' ? null : $request->input('parent_branch');
            $branch->name = $request->input('name');
            $branch->status = $request->input('status');
            $branch->save();

            return response()->json(['message' => 'Branch Location created successfully', 'data' => $branch]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'errors' => $e->getMessage()], 422);
        }
    }


    public function update(Request $request, $id)
    {
        // if ($request->parent_branch) {
        //     $parentBranch = BranchLocation::find($request->parent_branch);
        //     if ($parentBranch && strtolower($parentBranch->name) == strtolower($request->name)) {
        //        return $this->returnError('Parent branch and child branch name should not be same');
        //     }
        // }
        $form_data = $request->validate([
            'branch_code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('branch_locations')->whereNull('deleted_at')->ignore($id),
            ],
            'parent_branch' => [
                'nullable',
                'string',
                'max:50',
            ],
            'name' => [
                'required',
                'string',
                'max:200',
                Rule::unique('branch_locations')->whereNull('deleted_at')->ignore($id),
            ],
            'status' => 'required|boolean',

        ]);
        try {

            $branch = BranchLocation::find($id);
            $originalData = $branch->getOriginal();
            $emp = Employee::select('branch_id')->get();
            $branchIds = $emp->pluck('branch_id')->toArray();
            $decodedBranchIds = [];
            foreach ($branchIds as $json) {
                $ids = json_decode($json, true);
                if (is_array($ids)) {
                    $decodedBranchIds = array_merge($decodedBranchIds, $ids);
                }
            }
            if(in_array($id, $decodedBranchIds) &&  $originalData['status'] == 1 && $request->input('status') == 0){
                return $this->returnError('This branch is already in use by employee');
            }
            $branch->branch_code = $request->input('branch_code');
            $branch->name = $request->input('name');
            $branch->parent_id = $request->input('parent_branch') == '#' ? null : $request->input('parent_branch');
            $branch->status = $request->input('status');
            $branch->save();
            return response()->json(['message' => 'Branch Location updated successfully', 'data' => $branch]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'errors' => $e->getMessage()], 422);
        }
    }

    public function delete(Request $request, $id)
    {
        try {
            $data = BranchLocation::find($id);
            $emp = Employee::select('branch_id')->get();
            $branchIds = $emp->pluck('branch_id')->toArray();
            $decodedBranchIds = [];
            foreach ($branchIds as $json) {
                $ids = json_decode($json, true);
                if (is_array($ids)) {
                    $decodedBranchIds = array_merge($decodedBranchIds, $ids);
                }
            }
            if(in_array($id, $decodedBranchIds)){
                return $this->returnError('This branch is already in use by employee');
            }
            $data->delete();
            return response()->json(['message' => 'Branch Location deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'errors' => $e->getMessage()], 422);
        }
    }
    
    // public function testMail(Request $request)
    // {

    //     try {
    //         $ialertInvoice =  Iallert::with('documents')
    //             ->where('wcr_status', '1')
    //             ->where('bill_ac_status', '0')
    //             ->whereDate('ba_customer_commitment_date', '<=', now()->format('Y-m-d'))
    //             ->get();
    //         if ($ialertInvoice->isNotEmpty()) {
    //             foreach ($ialertInvoice as $invoice) {
    //                 $invoice['auth_user'] = Employee::where('id', $invoice->updated_by)->first();
    //                 $setting = Setting::where('name', 'signature')->first();
    //                 $invoice['signature_logo'] = $setting ? $setting->value : null;
    //                 $customer_emails = is_array($invoice->customer_email) ? $invoice->customer_email : explode(',', $invoice->customer_email);
    //                 $customer_emails = array_filter(array_map('trim', $customer_emails));
    //                 $customer_emails = array_unique($customer_emails);
    //                 try {
    //                     Mail::mailer('ialert_smtp')->to('panneer63834@gmail.com')->send(new IAlertURBillingAccountInvoice($invoice));
    //                 } catch (\Exception $e) {
    //                     dd($e->getMessage());
    //                     // Log the exception but do not break the flow
    //                     Log::channel('cron')->info('BAC Reminder failed notification failed: ' . $e->getMessage());
    //                 }
    //             }
    //             // config([
    //             //     'mail.from.address' => $originalFromAddress,
    //             // ]);
    //             echo "Command ended...";
    //         } else {
    //             Log::channel('cron')->info('No Invoices Found');
    //         }
    //         // \Config::set('mail.from.address', $originalFromAddress);
    //     } catch (\Exception $e) {
    //         // Log the exception but do not break the flow
    //         Log::channel('cron')->info('BAC Reminder failed: ' . $e->getMessage());
    //     }

    // }
}
