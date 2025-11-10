<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\IAlertBillingAccountCompletion;
use App\Mail\IAlertRNR;
use App\Mail\IAlertWorkCompletionInvoice;
use App\Models\BranchLocation;
use App\Models\Employee;
use App\Models\Iallert;
use App\Models\InvoiceComment;
use App\Models\InvoiceCommentDocument;
use App\Models\InvoiceDocument;
use App\Models\InvoiceMention;
use App\Models\Notification;
use App\Models\Organization;
use App\Models\OrganizationContact;
use App\Models\Role;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\AuthController;
use App\Models\Document;
use App\Models\PaymentCommitedOn;
use App\Models\RNR;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class IallertController extends Controller
{

    public function index(Request $request)
    {
        $search = $request->search;
        $bde_name = $request->input('bde_name', []);
        $branch = $request->input('branch', []);
        $invoice_number = $request->input('invoice_number', []);
        $age = $request->input('age', []);
        $value = $request->input('value', []);
        $status = $request->input('status', []);
        $followUp = $request->input('follow_up', []);
        $customerName = $request->input('customer_name', []);
        $sortColumn = $request->input('sort_column', 'id');
        $sortOrder = $request->input('sort_order', 'desc');

        $filters = [
            'search' => $search,
            'bde_name' => $bde_name,
            'branch' => $branch,
            'invoice_number' => $invoice_number,
            'age' => $age,
            'value' => $value,
            'status' => $status,
            'follow_up' => $followUp,
            'customer_name' => $customerName,
            'sort_column' => $sortColumn,
            'sort_order' => $sortOrder,
        ];

        try {
            $emp = Employee::find(Auth::id());
            $json_branch = json_decode($emp->branch_id, true);
            if (is_array($json_branch)) {
                $branches = $json_branch;
            } else {
                $branches = [$json_branch];
            }
            $branch_codes = BranchLocation::whereIn('id', $branches)->pluck('branch_code')->toArray();

            $role_check = Role::where('id', $emp->role_id)->select('name', 'status')->first();
            if ($role_check) {
                $role_check = $role_check->toArray();
            }
            if (!$role_check) {
                return response()->json('Role not found', 500);
            }
            if ($role_check['status'] == 0) {
                return response()->json('Role is inactive', 500);
            }
            
            if (in_array("Business Development Executive", $role_check) || in_array("BDE", $role_check)) {
                $totals = $this->getTotal($branch_codes,$emp->employee_id,$filters);
                $query = Iallert::whereIn('branch_id', $branch_codes)->where('os_value', '!=', 0)->where('bde_id', $emp->employee_id);
            } else {
                $totals = $this->getTotal($branch_codes,null,$filters);
                $query = Iallert::where('os_value', '!=', 0)->whereIn('branch_id', $branch_codes);
            }

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('customer_name', 'like', "%$search%")
                        ->orWhere('bde_name', 'like', "%$search%")
                        ->orWhere('order_type', 'like', "%$search%")
                        ->orWhere('invoice_number', 'like', "%$search%")
                        ->orWhere('customer_code', 'like', "%$search%")
                        ->orWhere('po_reference', 'like', "%$search%")
                        ->orWhere('contact_person', 'like', "%$search%")
                        ->orWhere('mobile', 'like', "%$search%")
                        ->orWhere('email_id', 'like', "%$search%")
                        ->orWhere('bde_email_id', 'like', "%$search%")
                        ->orWhere('manager_email_id', 'like', "%$search%")
                        ->orWhere('art_email_id', 'like', "%$search%")
                        ->orWhere('logistics_email_id', 'like', "%$search%")
                        ->orWhere('art_head_email_id', 'like', "%$search%")
                        ->orWhere('branch', 'like', "%$search%");
                });
            }

            if (is_array($bde_name) && count($bde_name) > 0) {
                $query->whereIn('bde_name', $bde_name);
            }

            if (is_array($branch) && count($branch) > 0) {
                $query->whereIn('branch', $branch);
            }

            if (is_array($customerName) && count($customerName) > 0) {
                $query->whereIn('customer_name', $customerName);
            }

            if (is_array($invoice_number) && count($invoice_number) > 0) {
                $query->whereIn('invoice_number', $invoice_number);
            }

            if (is_array($age) && count($age) > 0) {
                $query->where(function ($q) use ($age) {
                    foreach ($age as $data) {
                        switch ($data) {
                            case '0-7':
                                $q->orWhere('age', '<=', 7);
                                break;
                            case '30':
                                $q->orWhere('age', '<=', 30);
                                break;
                            case '30-60':
                                $q->orWhereBetween('age', [30, 60]);
                                break;
                            case '60-90':
                                $q->orWhereBetween('age', [60, 90]);
                                break;
                            case '90+':
                                $q->orWhere('age', '>=', 90);
                                break;
                            case '120+':
                                $q->orWhere('age', '>=', 120);
                                break;
                            case '150+':
                                $q->orWhere('age', '>=', 150);
                                break;
                            case '200+':
                                $q->orWhere('age', '>=', 200);
                                break;
                            case '365+':
                                $q->orWhere('age', '>=', 365);
                                break;
                        }
                    }
                });
            }

            if ($value) {
                if (is_array($value) && count($value) > 0) {
                    $query->where(function ($q) use ($value) {
                        foreach ($value as $data) {
                            switch ($data) {
                                case '1-1000':
                                    $q->orWhere('os_value', '>=', 1)->where('os_value', '<=', 1000);
                                    break;
                                case '1001-10000':
                                    $q->orWhere('os_value', '>=', 1001)->where('os_value', '<=', 10000);
                                    break;
                                case '10001-50000':
                                    $q->orWhere('os_value', '>=', 10001)->where('os_value', '<=', 50000);
                                    break;
                                case '50001-100000':
                                    $q->orWhere('os_value', '>=', 50001)->where('os_value', '<=', 100000);
                                    break;
                                case '100001-500000':
                                    $q->orWhere('os_value', '>=', 100001)->where('os_value', '<=', 500000);
                                    break;
                                case '500000+':
                                    $q->orWhere('os_value', '>=', 500000);
                                    break;
                            }
                        }
                    });
                }
            }


            if (is_array($status) && count($status) > 0) {
                $query->where(function ($q) use ($status) {
                    foreach ($status as $data) {
                        switch ($data) {
                            case 'wcr-blank':
                                $q->orWhereNull('wcr_status')
                                    ->whereHas('organization', function ($query) {
                                        $query->where(function ($q) {
                                            $q->where(function ($q1) {
                                                $q1->whereNotNull('primary_mail_id1')
                                                    ->where('primary_mail_id1', '!=', '');
                                            })->orWhere(function ($q2) {
                                                $q2->whereNotNull('primary_mail_id2')
                                                    ->where('primary_mail_id2', '!=', '');
                                            });
                                        });
                                    });
                                break;
                            case 'blank-org':
                                $q->where(function ($query) {
                                    $query->whereHas('organization', function ($query) {
                                        $query->where(function ($query) {
                                            $query->where(function ($q) {
                                                $q->whereNull('primary_mail_id1')
                                                ->orWhere('primary_mail_id1', '');
                                            })->where(function ($q) {
                                                $q->whereNull('primary_mail_id2')
                                                ->orWhere('primary_mail_id2', '');
                                            });
                                        });
                                    });
                                });
                                break;
                            case 'tds':
                                $q->orWhere('invoice_status','TDS');
                                break;
                            case 'tcs':
                                $q->orWhere('invoice_status','TCS');
                                break;
                            case 'wcr-no':
                                $q->orWhere('wcr_status', '0');
                                break;
                            case 'ba-blank':
                                $q->orWhereNull('bill_ac_status')->where('wcr_status', '1');
                                break;
                            case 'ba-no':
                                $q->orWhere('bill_ac_status', '0');
                                break;
                            case 'tough-yes':
                                $q->orWhere('tough_nut_status', '1');
                                break;
                            case 'rnr':
                                $q->orWhereNotNull('rnr');
                                break;
                        }
                    }
                });
            }

            $now = Carbon::now()->toDateString();
            $tomorrow = Carbon::tomorrow()->toDateString();
            $endOfWeek = Carbon::now()->endOfWeek()->toDateString();

            if (is_array($followUp) && count($followUp) > 0) {
                $query->where(function ($q) use ($followUp, $now , $tomorrow, $endOfWeek) {
                    foreach ($followUp as $followUpItem) {
                        switch ($followUpItem) {
                            case 'wc-overdue':
                                $q->orWhereDate('wc_date', '<=', $now)->where('wcr_status', '0');
                                break;
                            case 'ba-overdue':
                                $q->orWhereDate('ba_customer_commitment_date', '<=', $now);
                                break;
                            case 'customer-follow-up':
                                $q->orWhereDate('customer_follow_up_date', '<=', $now);
                                break;
                            case 'customer-follow-up-this-week':
                                $q->orWhereDate('customer_follow_up_date', '>=', $now)
                                    ->whereDate('customer_follow_up_date', '<=', $endOfWeek);
                                break;
                            case 'customer-follow-up-tomorrow':
                                $q->orWhereDate('customer_follow_up_date', '=', $tomorrow);
                                break;
                            case 'payment-commited-on':
                                $q->orWhereDate('payment_commited_on', '<=', $now);
                                break;
                            case 'internal-follow-up':
                                $q->orWhereDate('internal_follow_up_date', '<=', $now);
                                break;
                        }
                    }
                });
            }
            $iallerts = $query->orderBy($sortColumn, $sortOrder)->paginate($request->input('per_page', 25));
            // $osValueTotal = 0;
            // $invoiceValueTotal = 0;
            $iallerts->getCollection()->transform(function ($item) use (&$osValueTotal, &$invoiceValueTotal) {
                if (!empty($item->invoice_date)) {
                    try {
                        $item->invoice_date = \Carbon\Carbon::parse($item->invoice_date)->format('d-m-Y');
                    } catch (\Exception $e) {
                        $item->invoice_date = $item->invoice_date;
                    }
                }
                // $osValueTotal += $item->os_value ?? 0;
                // $invoiceValueTotal += $item->invoice_value ?? 0;
                return $item;
            });

            $response = $iallerts->toArray();
            $response['os_value'] = $totals['os_value_total'] ?? 0;
            $response['invoice_value'] = $totals['invoice_value_total'] ?? 0;

            return response()->json(['message' => 'Data retrieved successfully', 'data' => $response], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getTotal($branchCodes, $employeeId = null, $filters = [])
    {
        $query = Iallert::where('os_value', '!=', 0)
                        ->whereIn('branch_id', $branchCodes);

        if ($employeeId) {
            $query->where('bde_id', $employeeId);
        }
        if ($filters['search']) {
                $search = $filters['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('customer_name', 'like', "%$search%")
                        ->orWhere('bde_name', 'like', "%$search%")
                        ->orWhere('order_type', 'like', "%$search%")
                        ->orWhere('invoice_number', 'like', "%$search%")
                        ->orWhere('customer_code', 'like', "%$search%")
                        ->orWhere('po_reference', 'like', "%$search%")
                        ->orWhere('contact_person', 'like', "%$search%")
                        ->orWhere('mobile', 'like', "%$search%")
                        ->orWhere('email_id', 'like', "%$search%")
                        ->orWhere('bde_email_id', 'like', "%$search%")
                        ->orWhere('manager_email_id', 'like', "%$search%")
                        ->orWhere('art_email_id', 'like', "%$search%")
                        ->orWhere('logistics_email_id', 'like', "%$search%")
                        ->orWhere('art_head_email_id', 'like', "%$search%")
                        ->orWhere('branch', 'like', "%$search%");
                });
            }

            if (is_array($filters['bde_name']) && count($filters['bde_name']) > 0) {
                $bde_name = $filters['bde_name'];
                $query->whereIn('bde_name', $bde_name);
            }

            if (is_array($filters['branch']) && count(($filters['branch'])) > 0) {
                $branch = $filters['branch'];
                $query->whereIn('branch', $branch);
            }

            if (is_array($filters['customer_name']) && count($filters['customer_name']) > 0) {
                $customerName = $filters['customer_name'];
                $query->whereIn('customer_name', $customerName);
            }

            if (is_array($filters['invoice_number']) && count($filters['invoice_number']) > 0) {
                $invoice_number = $filters['invoice_number'];
                $query->whereIn('invoice_number', $invoice_number);
            }

             if (is_array($filters['age']) && count($filters['age']) > 0) {
                $age = $filters['age'];
                $query->where(function ($q) use ($age) {
                    foreach ($age as $data) {
                        switch ($data) {
                            case '0-7':
                                $q->orWhere('age', '<=', 7);
                                break;
                            case '30':
                                $q->orWhere('age', '<=', 30);
                                break;
                            case '30-60':
                                $q->orWhereBetween('age', [30, 60]);
                                break;
                            case '60-90':
                                $q->orWhereBetween('age', [60, 90]);
                                break;
                            case '90+':
                                $q->orWhere('age', '>=', 90);
                                break;
                            case '120+':
                                $q->orWhere('age', '>=', 120);
                                break;
                            case '150+':
                                $q->orWhere('age', '>=', 150);
                                break;
                            case '200+':
                                $q->orWhere('age', '>=', 200);
                                break;
                            case '365+':
                                $q->orWhere('age', '>=', 365);
                                break;
                        }
                    }
                });
            }

            if ($filters['value']) {
                $value = $filters['value'];
                if (is_array($value) && count($value) > 0) {
                    $query->where(function ($q) use ($value) {
                        foreach ($value as $data) {
                            switch ($data) {
                                case '1-1000':
                                    $q->orWhere('os_value', '>=', 1)->where('os_value', '<=', 1000);
                                    break;
                                case '1001-10000':
                                    $q->orWhere('os_value', '>=', 1001)->where('os_value', '<=', 10000);
                                    break;
                                case '10001-50000':
                                    $q->orWhere('os_value', '>=', 10001)->where('os_value', '<=', 50000);
                                    break;
                                case '50001-100000':
                                    $q->orWhere('os_value', '>=', 50001)->where('os_value', '<=', 100000);
                                    break;
                                case '100001-500000':
                                    $q->orWhere('os_value', '>=', 100001)->where('os_value', '<=', 500000);
                                    break;
                                case '500000+':
                                    $q->orWhere('os_value', '>=', 500000);
                                    break;
                            }
                        }
                    });
                }
            }


            if (is_array($filters['status']) && count($filters['status']) > 0) {
                $status = $filters['status'];
                $query->where(function ($q) use ($status) {
                    foreach ($status as $data) {
                        switch ($data) {
                            case 'wcr-blank':
                                $q->orWhereNull('wcr_status')
                                    ->whereHas('organization', function ($query) {
                                        $query->where(function ($q) {
                                            $q->where(function ($q1) {
                                                $q1->whereNotNull('primary_mail_id1')
                                                    ->where('primary_mail_id1', '!=', '');
                                            })->orWhere(function ($q2) {
                                                $q2->whereNotNull('primary_mail_id2')
                                                    ->where('primary_mail_id2', '!=', '');
                                            });
                                        });
                                    });
                                break;
                            case 'blank-org':
                                $q->where(function ($query) {
                                    $query->whereHas('organization', function ($query) {
                                        $query->where(function ($query) {
                                            $query->where(function ($q) {
                                                $q->whereNull('primary_mail_id1')
                                                ->orWhere('primary_mail_id1', '');
                                            })->where(function ($q) {
                                                $q->whereNull('primary_mail_id2')
                                                ->orWhere('primary_mail_id2', '');
                                            });
                                        });
                                    });
                                });
                                break;
                            case 'tds':
                                $q->orWhere('invoice_status','TDS');
                                break;
                            case 'tcs':
                                $q->orWhere('invoice_status','TCS');
                                break;
                            case 'wcr-no':
                                $q->orWhere('wcr_status', '0');
                                break;
                            case 'ba-blank':
                                $q->orWhereNull('bill_ac_status')->where('wcr_status', '1');
                                break;
                            case 'ba-no':
                                $q->orWhere('bill_ac_status', '0');
                                break;
                            case 'tough-yes':
                                $q->orWhere('tough_nut_status', '1');
                                break;
                            case 'rnr':
                                $q->orWhereNotNull('rnr');
                                break;
                        }
                    }
                });
            }

            $now = Carbon::now()->toDateString();

            if (is_array($filters['follow_up']) && count($filters['follow_up']) > 0) {
                $followUp = $filters['follow_up'];
                $query->where(function ($q) use ($followUp, $now) {
                    foreach ($followUp as $followUpItem) {
                        switch ($followUpItem) {
                            case 'wc-overdue':
                                $q->orWhereDate('wc_date', '<', $now)->where('wcr_status', '0');
                                break;
                            case 'ba-overdue':
                                $q->orWhereDate('ba_customer_commitment_date', '<', $now);
                                break;
                            case 'customer-follow-up':
                                $q->orWhereDate('customer_follow_up_date', '=', $now);
                                break;
                            case 'payment-commited-on':
                                $q->orWhereDate('payment_commited_on', '=', $now);
                                break;
                            case 'internal-follow-up':
                                $q->orWhereDate('internal_follow_up_date', '<', $now);
                                break;
                        }
                    }
                });
            }

            $totals = [
                'os_value_total'      => $query->sum('os_value'),
                'invoice_value_total' => $query->sum('invoice_value')
            ];

            return $totals;
    }


    public function essential(Request $request)
    {
        try {
            $emp = Employee::find(Auth::id());
            $json_branch = json_decode($emp->branch_id, true);
            if (is_array($json_branch)) {
                $branches = $json_branch;
            } else {
                $branches = [$json_branch];
            }

            $branch_codes = BranchLocation::whereIn('id', $branches)->pluck('branch_code')->toArray();

            $role_check = Role::where('id', $emp->role_id)->pluck('name')->toArray();

            if (in_array("Business Development Executive", $role_check)) {
                $companyName = Iallert::whereIn('branch_id', $branch_codes)->where('bde_id', $emp->employee_id)->select('customer_name')->distinct()->orderBy('customer_name', 'asc')->get()->map(function ($item) {
                    return ['name' => $item->customer_name];
                });

                $bdeName = Iallert::whereIn('branch_id', $branch_codes)->where('bde_id', $emp->employee_id)->select('bde_name')->distinct()->get()->map(function ($item) {
                    return ['name' => $item->bde_name];
                });

                $branch = Iallert::whereIn('branch_id', $branch_codes)->where('bde_id', $emp->employee_id)->select('branch')->distinct()->get()->map(function ($item) {
                    return ['name' => $item->branch];
                });

                $invoiceNumber = Iallert::whereIn('branch_id', $branch_codes)->where('bde_id', $emp->employee_id)->select('invoice_number')->distinct()->get()->map(function ($item) {
                    return ['name' => $item->invoice_number];
                });
            } else {
                $companyName = Iallert::whereIn('branch_id', $branch_codes)->select('customer_name')->distinct()->orderBy('customer_name', 'asc')->get()->map(function ($item) {
                    return ['name' => $item->customer_name];
                });

                $bdeName = Iallert::whereIn('branch_id', $branch_codes)->select('bde_name')->distinct()->get()->map(function ($item) {
                    return ['name' => $item->bde_name];
                });

                $branch = Iallert::whereIn('branch_id', $branch_codes)->select('branch')->distinct()->get()->map(function ($item) {
                    return ['name' => $item->branch];
                });

                $invoiceNumber = Iallert::whereIn('branch_id', $branch_codes)->select('invoice_number')->distinct()->get()->map(function ($item) {
                    return ['name' => $item->invoice_number];
                });
            }

            $response = ['company_name' => $companyName, 'branch' => $branch, 'bde_name' => $bdeName, 'invoice_number' => $invoiceNumber];

            return response()->json(['message' => 'Data retrieved successfully', 'data' => $response], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function view(Request $request, $id)
    {
        try {
            //start permission check for invoice view
            $user = Auth::user();
            $json_branch = json_decode($user->branch_id, true);
            if (is_array($json_branch)) {
                $branches = $json_branch;
            } else {
                $branches = [$json_branch];
            }
            $branch_codes = BranchLocation::whereIn('id', $branches)->pluck('branch_code')->toArray();
            $res = (new AuthController)->getEmployeePermissions($user->role_id);
            $result = json_decode(json_encode($res), true);
            $mention_check = InvoiceMention::where('invoice_id', $id)
                ->where('mentioned_id', $user->id)
                ->first();
            if (!empty($result) && $result['view'] == false && !$mention_check) {
                return response()->json(['error' => 'Unauthorized access'], 403);
            }
            // end permission check for invoice view

            $iallert = Iallert::with('comments', 'documents', 'comments.documents', 'comments.from')->find($id);
            if (!$iallert) {
                return response()->json(['error' => 'Invoice not found'], 404);
            }
            $primaryEmail = Organization::where('customer_code', $iallert->customer_code)->select('id', 'primary_mail_id1', 'primary_mail_id2', 'company_name')->first();
            if ($primaryEmail) {
                $additionalEmails = OrganizationContact::where('organization_id', $primaryEmail->id)
                    ->pluck('email_id')
                    ->toArray();
            }
            $org = Organization::where('customer_code', $iallert->customer_code)->first();
            $response = $iallert;
            $response['organization'] = $org ? $org : null;
            $response['is_same_branch'] = in_array($iallert->branch_id, $branch_codes);
            $response['primary_email'] = $primaryEmail
                ? array_values(array_filter([
                    $primaryEmail->primary_mail_id1,
                    $primaryEmail->primary_mail_id2
                ]))
                : [];
            $response['company_name'] = $primaryEmail ? $primaryEmail->company_name : null;


            $response['additional_emails_essential'] = $additionalEmails ?? [];

            $response['current_date'] = Carbon::now()->format('Y-m-d H:i:s');
            $response['current_date_asia'] = Carbon::now('Asia/Kolkata')->format('Y-m-d H:i:s');

            $response['tasks'] = Task::with(['assignedto', 'assignedby'])
                ->where('ialert_id', $iallert->id)
                ->where('status_id', 2)
                ->where(function ($query) {
                    $query->where(function ($query) {
                        $query->where('is_recurrence', 0)
                            ->whereNull('parent_id');
                    })->orWhere(function ($query) {
                        $query->whereNotNull('parent_id')
                            ->where('is_recurrence', 1);
                    });
                })->get();
            // $response['task_count'] = $response['tasks']->count();
            return response()->json(['message' => 'Data retrieved successfully', 'data' => $response], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function save(Request $request)
    {
        $user_name = $request->header('user-name');
        $password = $request->header('password');
        try {
            if ($user_name != 'ushaialert' || $password != '$/%%usha$$') {
                return response()->json(['error' => 'Unauthorized access'], 401);
            }
            // start validation for the request
            $singleInvoiceRules = [
                'invoice_number' => 'required|string',
                'doc_entry' => 'required|string',
                'branch_id' => 'nullable|string',
                'branch' => 'nullable|string',
                'bde_id' => 'nullable|string',
                'bde_name' => 'nullable|string',
                'order_type' => 'nullable|string',
                'invoice_date' => 'nullable',
                'einvoice_number' => 'nullable|string',
                'customer_code' => 'nullable|string',
                'customer_name' => 'nullable|string',
                'po_reference' => 'nullable|string',
                'payment_terms' => 'nullable|string',
                'balance_remarks' => 'nullable|string',
                'contact_person' => 'nullable|string',
                'mobile' => 'nullable|string',
                'email_id' => 'nullable',
                'logistic_wcr_status' => 'nullable|string',
                'portal_invoice' => 'nullable|string',
                'invoice_value' => 'nullable',
                'os_value' => 'nullable',
                'age' => 'nullable|integer',
                'bde_email_id' => 'nullable',
                'manager_email_id' => 'nullable',
                'art_email_id' => 'nullable',
                'logistics_email_id' => 'nullable',
                'art_head_email_id' => 'nullable',
                'sap_attachments' => 'nullable|string',
                'invoice_pdf' => 'nullable|string',
            ];

            $messages = [
                'invoice_number.required' => 'Invoice number is required.',
                'doc_entry.required' => 'Doc entry is required.',
                'invoice_date.date' => 'Invoice date must be a valid date.',
            ];

            $rawIalerts = $request->input('iallerts', []);
            $ialerts = isset($rawIalerts[0]) ? $rawIalerts : [$rawIalerts];
            $errors = [];

            foreach ($ialerts as $index => $invoice) {
                $invoice = array_map(fn($val) => $val === '' ? null : $val, $invoice);

                $validator = Validator::make($invoice, $singleInvoiceRules, $messages);

                if ($validator->fails()) {
                    $invoiceError = [
                        'invoice_number' => $invoice['invoice_number'] ?? 'Unknown',
                        'doc_entry' => $invoice['doc_entry'] ?? 'Unknown',
                    ];

                    foreach ($validator->errors()->toArray() as $field => $messages) {
                        $invoiceError[$field] = $messages[0];
                    }

                    $errors[] = $invoiceError;
                }
            }

            if (!empty($errors)) {
                return response()->json(['data' => $errors], 422);
            }
            // end validation for the request

            $iallerts = collect($request->input('iallerts', []))
                        ->unique('invoice_number')
                        ->values();

            $data_count = $iallerts->count();
            $iallerts = $iallerts->all();
            $time = now('Asia/Kolkata')->format('Y-m-d H:i:s');
            $savedRecords = [];
            ini_set('max_execution_time', 300);

            Log::info('Ialert data start saving....' . $data_count . ' records at ' . $time);

            foreach ($iallerts as $iallertData) {

                $iallert = Iallert::where('invoice_number', $iallertData['invoice_number'])->first();
                if ($iallert) {
                    try {
                        $iallert->update([
                            'doc_entry'        => $iallertData['doc_entry'] ?? null,
                            'branch_id'          => $iallertData['branch_id'] ?? null,
                            'branch'             => $iallertData['branch'] ?? null,
                            'bde_id'             => $iallertData['bde_id'] ?? null,
                            'bde_name'           => $iallertData['bde_name'] ?? null,
                            'order_type'         => $iallertData['order_type'] ?? null,
                            'invoice_number'     => $iallertData['invoice_number'] ?? null,
                            'invoice_date'       => $iallertData['invoice_date'] ?? null,
                            'einvoice_number'    => $iallertData['einvoice_number'] ?? null,
                            'customer_code'      => $iallertData['customer_code'] ?? null,
                            'customer_name'      => $iallertData['customer_name'] ?? null,
                            'po_reference'       => $iallertData['po_reference'] ?? null,
                            'payment_terms'      => $iallertData['payment_terms'] ?? null,
                            'balance_remarks'    => $iallertData['balance_remarks'] ?? null,
                            'contact_person'     => $iallertData['contact_person'] ?? null,
                            'mobile'             => $iallertData['mobile'] ?? null,
                            'email_id'           => $iallertData['email_id'] ?? null,
                            'logistic_wcr_status' => $iallertData['logistic_wcr_status'] ?? null,
                            'portal_invoice'     => $iallertData['portal_invoice'] ?? null,
                            'sap_attachments'    => $iallertData['sap_attachments'] ?? null,
                            'invoice_pdf'        => $iallertData['invoice_pdf'] ?? null,
                            'invoice_value'      => $iallertData['invoice_value'] ?? null,
                            'os_value'           => $iallertData['os_value'] ?? null,
                            'age'                => $iallertData['age'] ?? null,
                            'bde_email_id'       => $iallertData['bde_email_id'] ?? null,
                            'manager_email_id'   => $iallertData['manager_email_id'] ?? null,
                            'art_email_id'       => $iallertData['art_email_id'] ?? null,
                            'logistics_email_id' => $iallertData['logistics_email_id'] ?? null,
                            'art_head_email_id'  => $iallertData['art_head_email_id'] ?? null,
                        ]);
                    } catch (\Exception $e) {
                         Log::info('Error while updating Ialert' . $e->getMessage());
                    }
                } else {
                    try {
                        $iallert = new Iallert;
                        $iallert->doc_entry          = $iallertData['doc_entry'] ?? null;
                        $iallert->branch_id          = $iallertData['branch_id'] ?? null;
                        $iallert->branch             = $iallertData['branch'] ?? null;
                        $iallert->bde_id             = $iallertData['bde_id'] ?? null;
                        $iallert->bde_name           = $iallertData['bde_name'] ?? null;
                        $iallert->order_type         = $iallertData['order_type'] ?? null;
                        $iallert->invoice_number     = $iallertData['invoice_number'] ?? null;
                        $iallert->invoice_date       = $iallertData['invoice_date'] ?? null;
                        $iallert->einvoice_number    = $iallertData['einvoice_number'] ?? null;
                        $iallert->customer_code      = $iallertData['customer_code'] ?? null;
                        $iallert->customer_name      = $iallertData['customer_name'] ?? null;
                        $iallert->po_reference       = $iallertData['po_reference'] ?? null;
                        $iallert->payment_terms      = $iallertData['payment_terms'] ?? null;
                        $iallert->balance_remarks    = $iallertData['balance_remarks'] ?? null;
                        $iallert->contact_person     = $iallertData['contact_person'] ?? null;
                        $iallert->mobile             = $iallertData['mobile'] ?? null;
                        $iallert->email_id           = $iallertData['email_id'] ?? null;
                        $iallert->logistic_wcr_status = $iallertData['logistic_wcr_status'] ?? null;
                        $iallert->portal_invoice     = $iallertData['portal_invoice'] ?? null;
                        $iallert->sap_attachments    = $iallertData['sap_attachments'] ?? null;
                        $iallert->invoice_pdf        = $iallertData['invoice_pdf'] ?? null;
                        $iallert->invoice_value      = $iallertData['invoice_value'] ?? null;
                        $iallert->os_value           = $iallertData['os_value'] ?? null;
                        $iallert->age                = $iallertData['age'] ?? null;
                        $iallert->bde_email_id       = $iallertData['bde_email_id'] ?? null;
                        $iallert->manager_email_id   = $iallertData['manager_email_id'] ?? null;
                        $iallert->art_email_id       = $iallertData['art_email_id'] ?? null;
                        $iallert->logistics_email_id = $iallertData['logistics_email_id'] ?? null;
                        $iallert->art_head_email_id  = $iallertData['art_head_email_id'] ?? null;
                        $iallert->save();
                    } catch (\Exception $e) {
                         Log::info('Error while saving Ialert' . $e->getMessage());
                    }
                }

                // Organization check
                if (!empty($iallertData['customer_code'])) {
                    $org = Organization::where('customer_code', $iallertData['customer_code'])->first();
                    if (!$org) {
                        Organization::create([
                            'customer_code' => $iallertData['customer_code'],
                            'company_name'  => $iallertData['customer_name'] ?? null,
                        ]);
                    }
                }

                $savedRecords[] = $iallert;
            }

            $now = now('Asia/Kolkata')->format('Y-m-d H:i:s');
            $saved_count = count($savedRecords);

            Log::info('Ialert data saved successfully.' . $saved_count . ' records at ' . $now);

            return response()->json(['message' => 'Data saved successfully', 'data' => $savedRecords], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function storeOrganization(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'primary_mail_id1' => 'nullable|email|different:primary_mail_id2',
                'primary_mail_id2' => 'nullable|email|different:primary_mail_id1',

                'primary_phone1' => 'nullable|numeric',
                'primary_phone2' => 'nullable|numeric|different:primary_phone1',

                'primary_name1' => 'nullable|string',
                'primary_name2' => 'nullable|string',
            ], [
                'primary_mail_id1.different' => 'Primary mail 1 must be different from Primary mail 2.',
                'primary_mail_id2.different' => 'Primary mail 2 must be different from Primary mail 1.',

                'primary_phone2.different' => 'Primary mobile number 2 must be different from Primary mobile number 1.',
            ]);

            if ($validator->fails()) {
                return $this->returnError($validator->errors(), 'Validation Error', 422);
            }

            $errors = [];

            if (empty($request->primary_mail_id1) && empty($request->primary_mail_id2)) {
                $errors['primary_mail'] = ['At least one primary mail ID is required.'];
            }

            if (empty($request->primary_name1) && empty($request->primary_name2)) {
                $errors['primary_name'] = ['At least one primary name is required.'];
            }

            if (!empty($errors)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation Error',
                    'error' => $errors,
                ], 422);
            }

            $customer_code = null;
            $ialert = Iallert::find($request->invoice_id);
            if ($ialert) {
                $customer_code = $ialert->customer_code;
            }
            // $errors = [];
            // $exists_check1 = null;
            // $exists_check2 = null;
            // $exists_phone_check1 = null;
            // $exists_phone_check2 = null;
            // if(!empty($request->primary_mail_id1) && $request->primary_mail_id1 != null) {
            //     $exists_check1 = Organization::where(function ($query) use ($request) {
            //                 $query->whereRaw('LOWER(primary_mail_id1) = ?', [strtolower($request->primary_mail_id1)])
            //                     ->orWhereRaw('LOWER(primary_mail_id2) = ?', [strtolower($request->primary_mail_id1)]);
            //             })
            //         ->where('customer_code', '!=', $customer_code)
            //         ->first();
            // }

            // if (!empty($request->primary_mail_id2) && $request->primary_mail_id2 != null) {
            //     $exists_check2 = Organization::where(function ($query) use ($request) {
            //         $query->whereRaw('LOWER(primary_mail_id1) = ?', [strtolower($request->primary_mail_id2)])
            //             ->orWhereRaw('LOWER(primary_mail_id2) = ?', [strtolower($request->primary_mail_id2)]);
            //     })
            //         ->where('customer_code', '!=', $customer_code)
            //         ->first();
            // }

            // if(!empty($request->primary_phone1) && $request->primary_phone1 != null) {
            //      $exists_phone_check1 = Organization::where('primary_phone1', $request->primary_phone1)
            //     ->orWhere('primary_phone2', $request->primary_phone1)
            //     ->where('customer_code','!=', $customer_code)
            //     ->first();
            // }

            // if(!empty($request->primary_phone2) && $request->primary_phone2 != null) {
            //     $exists_phone_check2 = Organization::where('primary_phone1', $request->primary_phone2)
            //     ->orWhere('primary_phone2', $request->primary_phone2)
            //     ->where('customer_code','!=', $customer_code)
            //     ->first();
            // }
            
            // if ($exists_phone_check1) {
            //     $errors['primary_phone1'][] = 'Primary mobile number 1 already exists.';
            // }
            // if ($exists_phone_check2) {
            //     $errors['primary_phone2'][] = 'Primary mobile number 2 already exists.';
            // }

            // if ($exists_check1) {
            //     $errors['primary_mail_id1'][] = 'Primary mail 1 already exists.';
            // }

            // if ($exists_check2) {
            //     $errors['primary_mail_id2'][] = 'Primary mail 2 already exists.';
            // }
            // if (!empty($errors)) {
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'Error',
            //         'error' => $errors,
            //     ], 422);
            // }
            $organization = Organization::where('customer_code', $customer_code)->first();
            if (!$organization) {
                return response()->json([
                    'success' => false,
                    'message' => 'Organization not found.',
                ], 404);
            }
            $organization->primary_mail_id1 = $request->primary_mail_id1;
            $organization->primary_mail_id2 = $request->primary_mail_id2;
            $organization->primary_phone1 = $request->primary_phone1;
            $organization->primary_phone2 = $request->primary_phone2;
            $organization->primary_name1 = $request->primary_name1;
            $organization->primary_name2 = $request->primary_name2;
            $organization->save();


            return $this->returnSuccess($organization, "Organization added successfully");
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $request['invoice_id'] =  $id;
        $data = Iallert::find($id);
        if ($data) {
            if ($request->documents || $request->documents_deleted) {
                $attachments = [];

                // Process added documents
                if ($request->hasFile('documents')) {
                    foreach ($request->file('documents') as $uploadedFile) {
                        // Save the file and get the file URL (you can modify this as per your actual file storage logic)
                        $fileContent = base64_encode(file_get_contents($uploadedFile->getRealPath()));
                        $mimeType = $uploadedFile->getMimeType();

                        // Assuming file is uploaded and saved somewhere like public storage
                        $fileUrl = url('storage/admin/' . $uploadedFile->hashName());

                        // Prepare the attachment data in the required format
                        $attachments[] = [
                            'InvoiceAttachment' => $fileContent,
                            'FileName' => $uploadedFile->getClientOriginalName(),
                            'Type' => 'Add',
                        ];
                    }
                }

                // Process deleted documents
                if ($request->documents_deleted) {
                    $removedDocuments = InvoiceDocument::whereIn('id', $request->documents_deleted)->get();

                    foreach ($removedDocuments as $doc) {
                        // Assuming that removal means we set the attachment URL to empty
                        $attachments[] = [
                            'InvoiceAttachment' => '',
                            'FileName' => $doc->name,
                            'Type' => 'Remove',
                        ];
                    }
                }

                // Build the final invoice details array
                $invDetails = [
                    [
                        'invoice_number' => $data->invoice_number,
                        'doc_entry' => $data->doc_entry,
                        'AttachmentDetails' => $attachments,
                    ]
                ];

                // Build the payload
                $payload = [
                    'InvDetails' => $invDetails
                ];
                $this->uploadToThirdParty(new \Illuminate\Http\Request($payload));
            }
        }

        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'customer_email' => 'nullable',
                    'additional_emails' => [
                        'nullable',
                        'string',
                        function ($attribute, $value, $fail) use ($request) {
                            $value = is_array($value) ? $value : explode(',', $value);
                            $emails = array_filter(array_map('trim', $value), fn($email) => !empty($email));
                            if (empty($emails)) {
                                return;
                            }
                            $lowercaseEmails = array_map('strtolower', $emails);
                            if (count($lowercaseEmails) !== count(array_unique($lowercaseEmails))) {
                                $fail('Additional emails should not be duplicate.');
                            }
                            $customerEmails = array_map('trim', explode(',', $request->input('customer_email')));
                            $lowercaseCustomerEmails = array_map('strtolower', $customerEmails);
                            foreach ($lowercaseEmails as $email) {
                                if (in_array($email, $lowercaseCustomerEmails)) {
                                    $fail('The Customer Email should not be included in Additional Emails.');
                                    return;
                                }
                            }
                        },
                    ],
                    'wcr_status' => 'nullable',
                    'wc_date' => 'nullable|date',
                    'bill_ac_status' => 'nullable',
                    'ba_customer_commitment_date' => 'nullable',
                    'invoice_status' => 'nullable',
                    'customer_follow_up_date' => 'nullable|date',
                    'payment_commited_on' => 'nullable|date',
                    'tough_nut_status' => 'nullable',
                    'rnr' => 'nullable|string',
                    'remark_for_email' => 'nullable|string|max:500',
                    'documents' => [
                        'nullable',
                        function ($attribute, $value, $fail) {
                            if (is_array($value)) {
                                if (count($value) > 5) {
                                    $fail("You can upload a maximum of 5 files.");
                                    return;
                                }
                                foreach ($value as $file) {
                                    if ($file instanceof \Illuminate\Http\UploadedFile) {
                                        $originalName = $file->getClientOriginalName();
                                        if (!$file->isValid()) {
                                            $fail("The file '{$originalName}' is invalid.");
                                        } elseif (!in_array($file->getClientOriginalExtension(), ['pdf', 'txt', 'jpeg', 'jpg', 'png', 'xls', 'xlsx', 'doc', 'docx', 'ppt', 'pptx'])) {
                                            $fail("The file '{$originalName}' must be a valid file type (PDF, TXT, JPG, etc).");
                                        } elseif ($file->getSize() > 5120 * 1024) {
                                            $fail("The file '{$originalName}' must not exceed 5MB.");
                                        }
                                    }
                                }
                            }
                        },
                    ],
                ]
            );

            if ($validator->fails()) {
                return $this->returnError($validator->errors());
            }


            $iallert = Iallert::with(['documents'])->find($id);
            if (!$iallert) {
                return response()->json(['error' => 'Invoice not found'], 404);
            }
            // save organization additional mail for future invoice
            if ($request->additional_emails) {
                $value = is_array($request->additional_emails)
                    ? array_map('trim', $request->additional_emails)
                    : array_filter(array_map('trim', explode(',', $request->additional_emails)));

                $org = Organization::where('customer_code', $iallert->customer_code)->first();

                if ($org) {
                    $org_contact = OrganizationContact::where('organization_id', $org->id)
                        ->pluck('email_id')
                        ->map('trim')
                        ->toArray();

                    $uniq_check_mails = array_diff($value, $org_contact);

                    if (!empty($uniq_check_mails)) {
                        foreach ($uniq_check_mails as $email) {
                            // $contactExists = DB::table('organization_contacts')
                            //     ->where('email_id', $email)
                            //     ->where('organization_id', '!=', $org->id)
                            //     ->whereNull('deleted_at')
                            //     ->exists();

                            // $primaryExists = DB::table('organizations')
                            //     ->where(function ($query) use ($email) {
                            //         $query->where('primary_mail_id1', $email)
                            //             ->orWhere('primary_mail_id2', $email);
                            //     })
                            //     ->where('id', '!=', $org->id)
                            //     ->whereNull('deleted_at')
                            //     ->exists();

                            // if ($contactExists || $primaryExists) {
                            //     return response()->json([
                            //         'success' => false,
                            //         'message' => 'Error',
                            //         'error' => [
                            //             'additional_emails' => [
                            //                 "The email '$email' is already used in another organization."
                            //             ]
                            //         ]
                            //     ], 422);
                            // }

                            OrganizationContact::create([
                                'organization_id' => $org->id,
                                'email_id' => $email
                            ]);
                        }
                    }
                }
            }
            // end save organization additional mail for future invoice

            // if ($request->customer_email && $iallert->customer_code) {
            //     $primaryEmail = Organization::where('customer_code', $iallert->customer_code)->select('id', 'primary_mail_id1', 'primary_mail_id2', 'company_name')->first();
            //     if (!$primaryEmail->primary_mail_id1 || !$primaryEmail->primary_mail_id2) {
            //         $pri_email = explode(',', $request->customer_email);
            //         $org_contact = OrganizationContact::where('organization_id', $primaryEmail->id)
            //             ->pluck('email_id')
            //             ->map('trim')
            //             ->toArray();

            //         $uniq_check_mails = array_diff($pri_email, $org_contact);

            //         if (!empty($uniq_check_mails)) {
            //             foreach ($uniq_check_mails as $email) {
            //                 $contactExists = DB::table('organization_contacts')
            //                     ->where('email_id', $email)
            //                     ->where('organization_id', '!=', $primaryEmail->id)
            //                     ->whereNull('deleted_at')
            //                     ->exists();

            //                 $primaryExists = DB::table('organizations')
            //                     ->where(function ($query) use ($email) {
            //                         $query->where('primary_mail_id1', $email)
            //                             ->orWhere('primary_mail_id2', $email);
            //                     })
            //                     ->where('id', '!=', $primaryEmail->id)
            //                     ->whereNull('deleted_at')
            //                     ->exists();

            //                 if ($contactExists || $primaryExists) {
            //                     return response()->json([
            //                         'success' => false,
            //                         'message' => 'Error',
            //                         'error' => [
            //                             'additional_emails' => [
            //                                 "The email '$email' is already used in another organization."
            //                             ]
            //                         ]
            //                     ], 422);
            //                 }
            //             }
            //         }
            //         $primaryEmail->primary_mail_id1 = $pri_email[0] ?? null;
            //         $primaryEmail->primary_mail_id2 = $pri_email[1] ?? null;
            //         $primaryEmail->save();
            //     }
            // }

            // start restrictedRole check
            $originalData = $iallert->getOriginal();
            $emp = Employee::find(Auth::id());
            $restrictedRoles = ['Accounts Receivable Team'];
            $restrictedFields = [
                'wcr_status',
                'bill_ac_status',
                'tough_nut_status',
            ];
            foreach ($restrictedFields as $field) {
                if (in_array($emp->role->name, $restrictedRoles) && $originalData[$field] == 1 && $request->$field == 0) {
                    return response()->json(['message' => "You cannot change {$field} from Yes to No."], 403);
                }
            }
            // end restrictedRole check
            $iallert->customer_email = $request->customer_email ?? null;
            $iallert->updated_by = Auth::id() ?? null;
            $iallert->additional_emails = $request->additional_emails ?? null;
            $iallert->wcr_status = $request->wcr_status ?? null;
            $iallert->wc_date = $request->wc_date ?? null;
            $iallert->bill_ac_status = $request->bill_ac_status ?? null;
            $iallert->ba_customer_commitment_date = $request->ba_customer_commitment_date ?? null;
            $iallert->invoice_status = $request->invoice_status ?? null;
            $iallert->customer_follow_up_date = $request->customer_follow_up_date ?? null;
            $iallert->payment_commited_on = $request->payment_commited_on ?? null;
            $iallert->tough_nut_status = $request->tough_nut_status ?? null;
            $iallert->rnr = $request->rnr ?? null;
            $iallert->internal_follow_up_date = $request->internal_follow_up_date ?? null;
            $iallert->remark_for_email = $request->remark_for_email ?? null;
            $iallert->update();

            // for RNR revision count for mail
            if ($originalData['rnr'] != $iallert->rnr) {
                $rnr = new RNR;
                $rnr->ialert_id = $iallert->id;
                $rnr->old_value = $originalData['rnr'];
                $rnr->new_value = $iallert->rnr;
                $rnr->save();
            }
            if ($originalData['payment_commited_on'] != $iallert->payment_commited_on && $request->wcr_status == 1) {
                $rnr = new PaymentCommitedOn;
                $rnr->ialert_id = $iallert->id;
                $rnr->old_value = $originalData['payment_commited_on'];
                $rnr->new_value = $iallert->payment_commited_on;
                $rnr->save();
            }
            if ($request->wcr_status == 0) {
                PaymentCommitedOn::where('ialert_id', $iallert->id)->delete();
            }
            $rnr_count = RNR::where('ialert_id', $iallert->id)->get();
            if ($rnr_count) {
                $iallert['rnr_count'] = $rnr_count->count();
            }
            $payment_committed_count = PaymentCommitedOn::where('ialert_id', $iallert->id)->get();
            if ($payment_committed_count) {
                $iallert['payment_revision_count'] = $payment_committed_count->count();
            }


            $documentPaths = [];
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $file) {
                    $originalName = $file->getClientOriginalName();
                    $fileName = "document_" . uniqid() . "_" . time() . "." . $file->extension();
                    $path = $file->storeAs('public/admin/', $fileName);
                    $documentPaths[] = [
                        'path' => 'admin/' . $fileName,
                        'original_name' => $originalName,
                    ];
                }
                if (!empty($documentPaths)) {
                    foreach ($documentPaths as $file) {
                        InvoiceDocument::create([
                            'invoice_id' => $iallert->id,
                            'name' => $file['original_name'],
                            'document' => $file['path']
                        ]);
                    }
                }
            }

            if ($request->documents_deleted) {
                $this->handleDocumentDeletion($request->documents_deleted);
            }
            $iallert['auth_user'] = Auth::user()->load('designation');
            $res = (new AuthController)->getEmployeePermissions($iallert['auth_user']->role_id);
            $customer_emails = is_array($iallert->customer_email) ? $iallert->customer_email : explode(',', $iallert->customer_email);
            $customer_emails = array_filter(array_map('trim', $customer_emails));
            $customer_emails = array_unique($customer_emails);
            try {
                if ($iallert->wcr_status == 1 && $res->edit == true &&  $originalData['wcr_status'] != $iallert->wcr_status && $iallert->os_value != 0) {
                    try {
                        Mail::mailer('ialert_smtp')->to(
                            $customer_emails,
                        )->send(new IAlertWorkCompletionInvoice($iallert));
                    } catch (\Exception $e) {
                        // Log the exception but do not break the flow
                        Log::error('WCR Status Mail send failed : ' . $e->getMessage());
                    }
                }
                if (!empty($iallert->rnr) && $res->edit == true && $originalData['rnr'] != $iallert->rnr && $iallert->os_value != 0) {
                    try {
                        Mail::mailer('ialert_smtp')->to(
                            $customer_emails,
                        )->send(new IAlertRNR($iallert));
                    } catch (\Exception $e) {
                        // Log the exception but do not break the flow
                        Log::error('RNR Mail send failed : ' . $e->getMessage());
                    }
                }
                if ($iallert->bill_ac_status == 1 && $res->edit == true &&  $originalData['bill_ac_status'] != $iallert->bill_ac_status && $iallert->os_value != 0) {
                    try {
                        Mail::mailer('ialert_smtp')->to(
                            $customer_emails,
                        )->send(new IAlertBillingAccountCompletion($iallert));
                    } catch (\Exception $e) {
                        // Log the exception but do not break the flow
                        Log::error('Billing Account Completion Mail send failed : ' . $e->getMessage());
                    }
                }
            } catch (\Exception $e) {
                // Log the exception but do not break the flow
                Log::error('WCR Status notification failed: ' . $e->getMessage());
            }
            return response()->json(['message' => 'Invoice updated successfully', 'data' => $iallert], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function uploadToThirdParty(Request $request)
    {
        set_time_limit(300);

        $invDetails = $request->InvDetails;

        $responses = [];

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'http://115.160.248.84:90/Api/SalesInvoice');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 120);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);


        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
            ],
            CURLOPT_POSTFIELDS => json_encode(['invDetails' => $invDetails]),
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);


        if ($err) {
            Log::error("cURL Error :  $err");
            return response()->json(['error' => "cURL Error: $err"], 500);
        }

        Log::info('File upload completed', [
            'response' => $response
        ]);

        $responses[] = json_decode($response, true);

        curl_close($curl);


        return response()->json(['responses' => $responses]);
    }


    private function handleDocumentDeletion(array $documentIds)
    {
        $removed_docs = [];
        $removed_doc_names = [];

        foreach ($documentIds as $id) {
            $document = InvoiceDocument::find($id);
            if ($document) {
                $filePath = public_path($document->file_path);
                if (is_file($filePath) && file_exists($filePath)) {
                    unlink($filePath);
                }
                $document->delete();
            }
        }

        // foreach ($documentIds as  $id) {
        //     $document = InvoiceDocument::withTrashed()->find($id);

        //     if ($document) {
        //         $removed_docs[] = $document->document;
        //         $removed_doc_names[] = $document->name;
        //     }
        // }

        // if(!empty($removed_docs)){
        //     $request = new \Illuminate\Http\Request([
        //         'documents_deleted' => $removed_docs,
        //         'documents' => $removed_doc_names,
        //         'invoice_id' => $document->invoice_id,
        //         'type' => 'removed',
        //     ]);
        //     $this->uploadToDeletedDocuments($request);
        // }
    }

    public function uploadToDeletedDocuments(Request $request)
    {
        $files = $request->documents_deleted;
        $file_names = $request->documents;
        if (!is_array($file_names)) {
            $file_names = [$file_names];
        }
        $file_type = $request->type;
        set_time_limit(300);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://taskmaster-api.designonline.in/api/ialert/upload');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 120);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        // curl_setopt($curl, CURLOPT_HTTPHEADER, [
        //     'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwczovL3Rhc2ttYXN0ZXItYXBpLmRlc2lnbm9ubGluZS5pbiIsImlhdCI6MTc0NDI5MTE1OCwiZXhwIjoxNzc1ODI3MTU4LCJuYmYiOjE3NDQyOTExNTgsImp0aSI6IlVUTnh6WXZFOU9DQWpDT0siLCJzdWIiOiIyNSIsInBydiI6IjMyOTYzYTYwNmMyZjE3MWYxYzE0MzMxZTc2OTc2NmNkNTkxMmVkMTUifQ.p9QYdqNwR_z3TI3U-BHbEzrWr2hSjjUSonb-HBILhOI',
        // ]);

        $responses = [];
        foreach ($files as $file) {
            Log::info('Removed file upload started');

            curl_setopt($curl, CURLOPT_POSTFIELDS, [
                'removed_file' => $file,
                'documents' => $file_names,
                'invoice_id' => $request->invoice_id,
                'removed_file_type' => $file_type,
            ]);

            $response = curl_exec($curl);
            $err = curl_error($curl);

            if ($err) {
                Log::error("cURL Error : $err");
                return response()->json(['error' => "cURL Error: $err"], 500);
            }

            Log::info('Removed file upload completed', ['response' => $response]);

            $responses[] = json_decode($response, true);
        }
        curl_close($curl);

        return response()->json(['responses' => $responses]);
    }

    public function commentList(Request $request, $id)
    {
        try {
            $search = $request->get('search', '');
            $pageNumber = $request->get('page', 1);
            $perPage = $request->get('per_page', 15);
            $sortColumn = $request->get('sort_column', 'created_at');
            $sortOrder = $request->get('sort_order', 'asc');

            $data = InvoiceComment::where('invoice_id', $id)
                ->with('from', 'documents')
                ->orderBy($sortColumn, $sortOrder)
                ->paginate($perPage, ['*'], 'page', $pageNumber);
        } catch (\Throwable $e) {
            return $this->returnError($e->getMessage());
        }
        return $this->returnSuccess($data, 'Invoice Comment List');
    }

    public function comment(Request $request)
    {

        try {
            $request->merge([
                'mentions' => is_string($request->mentions) ? json_decode($request->mentions, true) : $request->mentions
            ]);
            $validator = Validator::make($request->all(), [
                'invoice_id' => 'required|integer',
                'comment' => 'required|string',
                'mentions' => 'nullable|array',
                'documents.*' => [
                    'nullable',
                    'file',
                    'mimes:pdf,txt,jpeg,jpg,png,xls,xlsx,doc,docx,ppt,pptx',
                    'max:5120',
                ],
            ]);

            if ($validator->fails()) {
                return $this->returnError($validator->errors());
            }

            $comment = new InvoiceComment;
            $comment->invoice_id = $request->invoice_id;
            $comment->comment = $request->comment;
            $comment->from_id = Auth::user()->id;
            $comment->save();

            $documentPaths = [];
            if ($request->hasFile('documents')) {

                foreach ($request->file('documents') as $file) {
                    if ($file->getClientMimeType() === 'application/pdf' || $file->extension() === 'pdf') {
                        $originalName = $file->getClientOriginalName();
                        $fileName = "document_" . uniqid() . "_" . time() . "." . $file->extension();
                        $path = $file->storeAs('public/admin/', $fileName);
                        $documentPaths[] = [
                            'path' => 'admin/' . $fileName,
                            'original_name' => $originalName,
                        ];
                    } else {
                        $originalName = $file->getClientOriginalName();
                        $fileName = "document_" . uniqid() . "_" . time() . "." . $file->extension();
                        $path = $file->storeAs('public/admin/', $fileName);
                        $documentPaths[] = [
                            'path' => 'admin/' . $fileName,
                            'original_name' => $originalName,
                        ];
                    }
                }
                if (!empty($documentPaths)) {
                    foreach ($documentPaths as $file) {
                        InvoiceCommentDocument::create([
                            'comment_id' => $comment->id,
                            'name' => $file['original_name'],
                            'document' => $file['path']
                        ]);
                    }
                }
            }

            $mentions = $request->input('mentions');
            if (!empty($mentions)) {
                foreach ($mentions as $data) {
                    $mention = new InvoiceMention;
                    $mention->comment_id = $comment->id;
                    $mention->mentioned_by = Auth::user()->id;
                    $mention->mentioned_id = $data;
                    $mention->invoice_id = $comment->invoice_id;
                    $mention->save();

                    $notification = new Notification;
                    $notification->module = 'invoice comment';
                    $notification->action = 'mentioned in invoice';
                    $notification->message = 'mentioned you on this invoice';
                    $notification->ialert_id = $comment->invoice_id;
                    $notification->to_id = $data;
                    $notification->created_by = Auth::user()->id;
                    $notification->save();
                }
            }

            return response()->json(['message' => 'Comments saved successfully', 'data' => $comment], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
