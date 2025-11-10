<?php

namespace App\Http\Controllers\Admin;

use App\Exports\OrganizationExport;
use App\Http\Controllers\Controller;
use App\Models\ContactMaster;
use App\Models\Iallert;
use App\Models\Location;
use App\Models\Organization;
use App\Models\OrganizationContact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class OrganizationController extends Controller
{
    public function index(Request $request)
    {

        $search = $request->input('search');
        $status = $request->input('status');
        $perPage = $request->input('pageItems');
        $selectedLocationName = $request->input('location');


        $query = Organization::query()->with('location');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('customer_code', 'like', '%' . $search . '%')
                    ->orWhere('company_name', 'like', '%' . $search . '%')
                    ->orWhere('address', 'like', '%' . $search . '%')
                    ->orWhere('primary_mail_id1', 'like', '%' . $search . '%')
                    ->orWhere('primary_mail_id2', 'like', '%' . $search . '%')
                    ->orWhere('primary_phone1', 'like', '%' . $search . '%')
                    ->orWhere('primary_phone2', 'like', '%' . $search . '%')
                    ->orWhere('primary_name1', 'like', '%' . $search . '%')
                    ->orWhere('primary_name2', 'like', '%' . $search . '%');
            });
        }

        if ($selectedLocationName) {
            $locationIds = Location::where('name', $selectedLocationName)->pluck('id');
            $query->whereIn('location_id', $locationIds);
        }

        $organizations = $query->orderBy('id', 'desc')->paginate($perPage);


        $currentPage = $organizations->currentPage();
        $serialNumberStart = ($currentPage - 1) * $perPage + 1;
        $locations = Location::where('status', '1')->whereNull('deleted_at')->get();

        $total_count = Organization::count();


        return view('organization.index', [
            'organizations' => $organizations,
            'selectedStatus' => $status,
            'search' => $search,
            'total_count' => $total_count,
            'serialNumberStart' => $serialNumberStart,
            'locations' => $locations
        ]);
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
