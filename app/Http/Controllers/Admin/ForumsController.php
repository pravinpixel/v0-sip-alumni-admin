<?php

namespace App\Http\Controllers\Admin;

use App\Exports\OrganizationExport;
use App\Http\Controllers\Controller;
use App\Models\Alumnis;
use App\Models\ContactMaster;
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
            $query = Alumnis::with(['city', 'occupation'])->orderBy('id', 'desc');

            // Apply filters
            if ($request->filled('batch')) {
                $query->where('year_of_completion', $request->batch);
            }
            if ($request->filled('location')) {
                $query->whereHas('city', function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->location . '%');
                });
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('created_at', function ($row) {
                    return '<span>' . Carbon::createFromFormat('Y-m-d H:i:s', $row['created_at'])->setTimezone('Asia/kolkata')->format('d-m-Y h:i A') . '</span>';
                })
                ->editColumn('alumni', function ($row) {
                    $img = $row->image ? asset($row->image) : asset('images/avatar/blank.png');
                    $occ = $row->occupation->name ?? '—';
                    return '<div style="display:flex;align-items:center;gap:12px;">
                        <img src="' . $img . '" style="width:40px;height:40px;border-radius:50%;object-fit:cover;">
                    </div>';
                })
                ->addColumn('full_name', function ($row) {
                    return '<span style="background-color:#fff3cd;color:#ff8c42;padding:5px 12px;border-radius:20px;font-size:12px;font-weight:600;">' . ($row->full_name ?? '—') . '</span>';
                })
                ->addColumn('batch', function ($row) {
                    return '<span style="background-color:#fff3cd;color:#ff8c42;padding:5px 12px;border-radius:20px;font-size:12px;font-weight:600;">' . ($row->year_of_completion ?? '—') . '</span>';
                })
                ->addColumn('mobile_number', function ($row) {
                    return '<span style="background-color:#fff3cd;color:#ff8c42;padding:5px 12px;border-radius:20px;font-size:12px;font-weight:600;">' . ($row->mobile_number ?? '—') . '</span>';
                })
                ->addColumn('location', function ($row) {
                    return ($row->city?->state?->name ?? '-') . ', ' . ($row->city?->name ?? '-');
                })
                ->addColumn('occupation', function ($row) {
                    return $row->occupation->name ?? '-';
                })
                ->addColumn('connections', function ($row) {
                    return '<button onclick="viewProfile(' . $row->id . ')" class="btn btn-sm btn-primary">View Profile</button>';
                })
                ->addColumn('action', function ($row) {
                    return '
                      <div class="dropdown">
                                <button class="btn btn-sm btn-light" type="button" id="actionMenu' . $row->id . '" data-bs-toggle="dropdown" aria-expanded="false" style="padding:5px 8px; border:none;">
                                <i class="fas fa-ellipsis-v"></i>
                                     </button>
                                <ul class="dropdown-menu" aria-labelledby="actionMenu' . $row->id . '">
                                      <li><a class="dropdown-item" href="javascript:void(0)" onclick="sendMessage(' . $row->id . ')">Send Message</a></li>
                               </ul>
                               </div>';
                })
                ->rawColumns(['alumni', 'batch', 'location', 'action', 'full_name', 'mobile_number', 'created_at', 'connections'])
                ->make(true);
        } catch (\Throwable $e) {
            Log::error('Directory DataTable error: ' . $e->getMessage(), [
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
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
