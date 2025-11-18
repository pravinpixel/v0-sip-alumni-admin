<?php

namespace App\Http\Controllers\Admin;

use App\Exports\OrganizationExport;
use App\Http\Controllers\Controller;
use App\Models\Alumnis;
use App\Models\ContactMaster;
use App\Models\ForumPost;
use App\Models\Iallert;
use App\Models\Location;
use App\Models\Organization;
use App\Models\OrganizationContact;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;

class ForumsController extends Controller
{
    public function index(Request $request)
    {
        return view('forums.index');
    }

    public function getData(Request $request)
    {
        try {
            $query = ForumPost::with('alumni')->orderBy('created_at', 'desc');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('created_at', function ($row) {
                    return \Carbon\Carbon::parse($row->created_at)
                        ->setTimezone('Asia/Kolkata')
                        ->format('M j, Y');
                })

                ->addColumn('alumni', function ($row) {

                    $alumni = $row->alumni;

                    if (!$alumni) {
                        return '—';
                    }

                    $img = $alumni->image_url ? url('storage/' . $alumni->image ?? '') : asset('images/avatar/blank.png');

                    return '
                    <div style="display:flex;align-items:center;gap:12px;">
                        <img src="' . $img . '" 
                            style="width:40px;height:40px;border-radius:50%;object-fit:cover;">
                        <span style="font-weight:600;">' . $alumni->full_name . '</span>
                    </div>';
                })

                ->addColumn('contact', function ($row) {
                    return '<span style="font-size:12px;font-weight:600;">'
                        . ($row->alumni->mobile_number ?? '—') . '</span>';
                })

                ->addColumn('view_post', function ($row) {
                    return '<div class="btn-group" style = "background-color: #f3f4f6; padding: 6px 12px; border-radius: 6px;"> 
                    <i class="fas fa-eye"></i>
                    <a href="" 
                        class="" style= "margin-left: 6px; font-weight: 600; color: #374151;">
                        View
                    </a>
                 </div>';
                })

                ->addColumn('action_taken_on', function ($row) {
                    return \Carbon\Carbon::parse($row->updated_at)
                        ->setTimezone('Asia/Kolkata')
                        ->format('M j, Y');
                })

                ->addColumn('status', function ($row) {

                    $status = strtolower($row->status);

                    // Normal colors
                    $colors = [
                        'pending'       => '#f7c948', // yellow
                        'approved'      => '#4caf50', // green
                        'rejected'      => '#e53935', // red
                        'post_deleted'  => '#6c757d', // dark grey
                        'removed_by_admin' => '#ff7215ff', // light grey
                    ];
                    $hover = [
                        'pending'       => '#f4b400',
                        'approved'      => '#43a047',
                        'rejected'      => '#c62828',
                        'post_deleted'  => '#5a6268',
                        'removed_by_admin' => '#ff8800ff',
                    ];
                    $bg  = $colors[$status] ?? '#9e9e9e';
                    $hov = $hover[$status] ?? '#7e7e7e';
                    return '
                 <span class="status-badge-' . $status . '" 
                 style="
                background: ' . $bg . ';
                color: white;
                padding: 5px 12px;
                font-size: 12px;
                border-radius: 20px;
                font-weight: 600;
                text-transform: capitalize;
                cursor: pointer;
                transition: 0.3s;
              ">
            ' . str_replace('_', ' ', $status) . '
              </span>

            <style>
            .status-badge-' . $status . ':hover {
                background: ' . $hov . ' !important;
            }
            </style>
             ';
                })
                ->addColumn('action', function ($row) {

                    $status = strtolower($row->status);

                    // statuses where action must NOT appear at all
                    if (in_array($status, ['post_deleted', 'removed_by_admin'])) {
                        return '';
                    }

                    $actionMenu = '';

                    // PENDING → Approved + Reject
                    if ($status === 'pending') {
                        $actionMenu = '
    <li><a class="dropdown-item" href="javascript:void(0)" onclick="statusChange(' . $row->id . ', \'approved\')">
        <i class="fas fa-check-circle" style="color:green;"></i> Approve
    </a></li>

    <li><a class="dropdown-item" href="javascript:void(0)" onclick="statusChange(' . $row->id . ', \'rejected\')">
        <i class="fas fa-times-circle" style="color:red;"></i> Reject
    </a></li>
     ';
                    }

                    // APPROVED → Remove
                    elseif ($status === 'approved') {
                        $actionMenu = '
    <li><a class="dropdown-item" href="javascript:void(0)" onclick="statusChange(' . $row->id . ', \'removed_by_admin\')">
        <i class="fas fa-trash" style="color:#d9534f;"></i> Remove
    </a></li>
     ';
                    }

                    // REJECTED → Approved + Reject (reject disabled)
                    elseif ($status === 'rejected') {
                        $actionMenu = '
    <li><a class="dropdown-item" disabled" href="#">
        <i class="fas fa-check-circle" style="color:gray;"></i> Approve
    </a></li>

    <li><a class="dropdown-item" disabled" href="#">
        <i class="fas fa-times-circle" style="color:gray;"></i> Reject
    </a></li>
     ';
                    }

                    // Build dropdown only if actions exist
                    return '
        <div class="dropdown">
            <button class="btn btn-sm" type="button" data-bs-toggle="dropdown">
                <i class="fas fa-ellipsis-v"></i>
            </button>
            <ul class="dropdown-menu">
                ' . $actionMenu . '
            </ul>
        </div>
    ';
                })



                ->rawColumns(['alumni', 'contact', 'view_post', 'status', 'action'])
                ->make(true);
        } catch (\Throwable $e) {
            Log::error('Directory DataTable error: ' . $e->getMessage(), [
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function changeStatus(Request $request)
    {
        try {
            $id = $request->id;
            $post = ForumPost::findOrFail($id);
            if($request->status == 'approved'){
                $post->status = 'approved';
            } elseif($request->status == 'rejected'){
                $post->status = 'rejected';
            } elseif($request->status == 'removed_by_admin'){
                $post->status = 'removed_by_admin';
            }else{
                return $this->returnError(false,'Invalid status provided');
            }
            
            $post->save();
            return $this->returnSuccess($post, 'Status updated successfully');
        } catch (\Exception $e) {
            return $this->returnError('Failed to update status: ' . $e->getMessage(), 500);
        }
    }


    public function create(Request $request)
    {
        $locations = Location::where('status', '1')->get();
        return view(
            'organization.action',
            ['locations' => $locations]
        );
    }

    public function get(Request $request, $id)
    {
        $organization = Organization::with('organizationContacts')->find($id);
        $locations = Location::where('status', '1')->get();
        return view(
            'organization.action',
            [
                'organization' => $organization,
                'locations' => $locations,
            ]
        );
    }

    public function save(Request $request)
    {
        $id = $request->id ?? null;


        $rules = [
            'customer_code' => 'required|string|max:255|unique:organizations,customer_code,' . $id . ',id,deleted_at,NULL',
            // 'company_name' => 'required|string|max:255|unique:organizations,company_name,' . $id . ',id,deleted_at,NULL',
            'company_name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'location_id' => 'nullable|exists:locations,id',
            'primary_mail_id_1' => [
                'nullable',
                'email'
            ],
            'primary_mail_id_2' => [
                'nullable',
                'email',
                'different:primary_mail_id_1'
            ],
            'primary_contact_no_1' => [
                'nullable',
            ],
            'primary_contact_no_2' => [
                'nullable',
                'different:primary_contact_no_1'
            ]
        ];

        if ($request->has('contact_master_name')) {

            // Contact Emails - Unique across all organizations
            foreach ($request->input('contact_master_mail_id', []) as $index => $email) {
                $rules["contact_master_mail_id.$index"] = [
                    'required',
                    'email'
                ];
            }
            // Contact Phones - Unique across all organizations
            foreach ($request->input('contact_master_phone_no', []) as $index => $email) {
                $rules["contact_master_phone_no.$index"] = [
                    'required',
                ];
            }

            // Custom Validation: Check if email exists in any field
            // Validator::extend('unique_across_all', function ($attribute, $value, $parameters, $validator) use ($id) {
            //     $exists = DB::table('organizations')
            //         ->where(function ($query) use ($value, $id) {
            //             $query->where('primary_mail_id1', $value)
            //                 ->orWhere('primary_mail_id2', $value);
            //         })
            //         ->where('id', '!=', $id)
            //         ->whereNull('deleted_at')
            //         ->exists();

            //     $contactExists = DB::table('organization_contacts')
            //         ->where('email_id', $value)
            //         ->where('organization_id', '!=', $id)
            //         ->whereNull('deleted_at')
            //         ->exists();

            //     return !$exists && !$contactExists;
            // });

            // Custom Validation: Check if phone in any field
            // Validator::extend('unique_phone_across_all', function ($attribute, $value, $parameters, $validator) use ($id) {
            //     $exists_phone = DB::table('organizations')
            //         ->where(function ($query) use ($value, $id) {
            //             $query->where('primary_phone1', $value)
            //                 ->orWhere('primary_phone2', $value);
            //         })
            //         ->where('id', '!=', $id)
            //         ->whereNull('deleted_at')
            //         ->exists();

            //     $contactExists_phone = DB::table('organization_contacts')
            //         ->where('phone_number', $value)
            //         ->where('organization_id', '!=', $id)
            //         ->whereNull('deleted_at')
            //         ->exists();

            //     return !$exists_phone && !$contactExists_phone;
            // });

            // $rules['primary_mail_id_1'][] = 'unique_across_all';
            // $rules['primary_mail_id_2'][] = 'unique_across_all';
            // $rules['primary_contact_no_1'][] = 'unique_phone_across_all';
            // $rules['primary_contact_no_2'][] = 'unique_phone_across_all';

            // foreach ($request->input('contact_master_mail_id', []) as $index => $email) {
            //     $rules["contact_master_mail_id.$index"][] = 'unique_across_all';
            // }

            // foreach ($request->input('contact_master_phone_no', []) as $index => $phone) {
            //     $rules["contact_master_phone_no.$index"][] = 'unique_phone_across_all';
            // }

            // Corrected Custom Messages
            $customMessages = [
                'contact_master_mail_id.*.unique_across_all' => 'This email is already used in another organization or contact.',
                'contact_master_mail_id.*.required' => 'The contact email is required.',
                'primary_mail_id_1.unique_across_all' => 'This email is already used in another organization or contact.',
                'primary_mail_id_2.unique_across_all' => 'This email is already used in another organization or contact.',

                'contact_master_phone_no.*.unique_phone_across_all' => 'This phone no is already used in another organization or contact.',
                'contact_master_phone_no.*.required' => 'The contact phone no is required.',
                'primary_contact_no_1.unique_phone_across_all' => 'This phone no is already used in another organization or contact.',
                'primary_contact_no_2.unique_phone_across_all' => 'This phone no is already used in another organization or contact.',
            ];
            $validatedData = Validator::make($request->all(), $rules, $customMessages);
        }
        //  else {
        //     Validator::extend('unique_across_all', function ($attribute, $value, $parameters, $validator) use ($id) {
        //         $exists = DB::table('organizations')
        //             ->where(function ($query) use ($value, $id) {
        //                 $query->where('primary_mail_id1', $value)
        //                     ->orWhere('primary_mail_id2', $value);
        //             })
        //             ->where('id', '!=', $id)
        //             ->whereNull('deleted_at')
        //             ->exists();
        //         return !$exists;
        //     });
        //     Validator::extend('unique_phone_across_all', function ($attribute, $value, $parameters, $validator) use ($id) {
        //         $exists_phone = DB::table('organizations')
        //             ->where(function ($query) use ($value, $id) {
        //                 $query->where('primary_phone1', $value)
        //                     ->orWhere('primary_phone2', $value);
        //             })
        //             ->where('id', '!=', $id)
        //             ->whereNull('deleted_at')
        //             ->exists();
        //         return !$exists_phone;
        //     });
        //     $rules['primary_mail_id_1'][] = 'unique_across_all';
        //     $rules['primary_mail_id_2'][] = 'unique_across_all';
        //     $rules['primary_contact_no_1'][] = 'unique_phone_across_all';
        //     $rules['primary_contact_no_2'][] = 'unique_phone_across_all';
        //     $customMessages = [
        //         'primary_mail_id_1.unique_across_all' => 'This email is already used in another organization or contact.',
        //         'primary_mail_id_2.unique_across_all' => 'This email is already used in another organization or contact.',
        //         'primary_contact_no_1.unique_phone_across_all' => 'This phone no is already used in another organization or contact.',
        //         'primary_contact_no_2.unique_phone_across_all' => 'This phone no is already used in another organization or contact.',
        //     ];
        //     $validatedData = Validator::make($request->all(), $rules, $customMessages);
        // }



        if (isset($validatedData) && $validatedData->fails()) {
            return $this->returnError($validatedData->errors(), 'Validation Error', 422);
        }


        try {
            DB::beginTransaction();
            if (is_null($request->primary_mail_id_1) && is_null($request->primary_mail_id_2)) {
                return $this->returnError('Primary email id any one is required');
            }
            if ($id) {
                $organization = Organization::findOrFail($id);
                $message = "Organization updated successfully";
            } else {
                $organization = new Organization();
                $message = "Organization created successfully";
            }

            $organization->customer_code = $request->customer_code;
            $organization->company_name = $request->company_name;
            $organization->address = $request->address;
            $organization->location_id = $request->location_id;
            $organization->primary_mail_id1 = $request->primary_mail_id_1;
            $organization->primary_mail_id2 = $request->primary_mail_id_2;
            $organization->primary_phone1 = $request->primary_contact_no_1;
            $organization->primary_phone2 = $request->primary_contact_no_2;
            $organization->primary_name1 = $request->primary_contact_name_1;
            $organization->primary_name2 = $request->primary_contact_name_2;
            $organization->save();
            if ($id) {
                OrganizationContact::where('organization_id', $organization->id)->delete();
            }
            if ($request->has('contact_master_name')) {
                foreach ($request->contact_master_name as $index => $name) {
                    OrganizationContact::create([
                        'organization_id' => $organization->id,
                        'name' => $name,
                        'email_id' => $request->contact_master_mail_id[$index],
                        'phone_number' => $request->contact_master_phone_no[$index],
                    ]);
                }
            }

            DB::commit();
            return $this->returnSuccess($organization, $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->returnError($e->getMessage());
        }
    }

    public function export(Request $request)
    {
        // Retrieve filters
        $filters = [
            'search' => $request->input('search'),
            'location' => $request->input('location'),
        ];

        // Export the filtered data
        return Excel::download(new OrganizationExport($filters), 'Organization.xlsx');
    }

    public function delete(Request $request, $id)
    {
        try {

            $organization = Organization::find($id);
            if ($organization) {
                $organizationContacts = OrganizationContact::where('organization_id', $organization->id);
                $organizationContacts->delete();
                $organization->delete();
                return $this->returnSuccess($organization, "Organization deleted successfully");
            } else {
                return $this->returnError('No Organization Found!');
            }
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }
    }
}
