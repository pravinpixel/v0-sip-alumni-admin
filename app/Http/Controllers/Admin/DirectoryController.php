<?php

namespace App\Http\Controllers\Admin;

use App\Exports\Admin\DirectoryExport;
use App\Http\Controllers\Controller;
use App\Mail\AlumniBlockedMail;
use App\Mail\AlumniUnBlockedMail;
use App\Mail\EmployeeEmail;
use App\Models\AlumniConnections;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Alumnis;
use App\Models\Department;
use App\Models\BranchLocation;
use App\Models\Designation;
use App\Models\ForumPost;
use App\Models\Location;
use App\Models\Role;
use App\Models\Task;
use App\Notifications\EmployeeCreateNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\F;
use PhpParser\Node\Scalar\MagicConst\Dir;
use Yajra\DataTables\Facades\DataTables;

class DirectoryController extends Controller
{
    public function index(Request $request)
    {
        return view('directory.index');
    }

    public function getData(Request $request)
    {
        try {
            $query = Alumnis::with(['city', 'occupation']);

            $user = auth()->user();
            if ($user && $user->role->name === 'Franchisee') {
                $query->where('center_id', $user->center_location_id);
            }

            // Apply filters - handle multiple selections
            if ($request->filled('years')) {
                $years = is_array($request->years) ? $request->years : explode(',', $request->years);
                $query->whereIn('year_of_completion', $years);
            }

            if ($request->filled('cities')) {
                $cities = is_array($request->cities) ? $request->cities : explode(',', $request->cities);
                $query->whereHas('city', function ($q) use ($cities) {
                    $q->whereIn('name', $cities);
                });
            }

            if ($request->filled('occupations')) {
                $occupations = is_array($request->occupations) ? $request->occupations : explode(',', $request->occupations);
                $query->whereHas('occupation', function ($q) use ($occupations) {
                    $q->whereIn('name', $occupations);
                });
            }

            if ($request->filled('centerLocations')) {
                $centerLocations = is_array($request->centerLocations) ? $request->centerLocations : explode(',', $request->centerLocations);
                $query->whereHas('centerLocation', function ($q) use ($centerLocations) {
                    $q->whereIn('name', $centerLocations);
                });
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->filter(function ($query) use ($request) {
                    if ($request->has('search') && !empty($request->search['value'])) {
                        $searchValue = $request->search['value'];
                        
                        $parsedDate = date('Y-m-d', strtotime($searchValue));
                        $yearSearch = preg_match('/^\d{4}$/', $searchValue) ? $searchValue : null;
                        $keywords = preg_split('/[\s,]+/', $searchValue, -1, PREG_SPLIT_NO_EMPTY);
                        $query->where(function ($q) use ($searchValue, $keywords, $parsedDate, $yearSearch) {
                            $q->where('full_name', 'like', "%{$searchValue}%")
                                ->orWhere('year_of_completion', 'like', "%{$searchValue}%")
                                ->orWhere('status', 'like', "%{$searchValue}%")
                                ->orWhere('email', 'like', "%{$searchValue}%")
                                ->orWhere('mobile_number', 'like', "%{$searchValue}%")
                                ->orWhereHas('occupation', function ($occQuery) use ($searchValue) {
                                    $occQuery->where('name', 'like', "%{$searchValue}%");
                                })
                                ->orWhereHas('centerLocation', function ($centerQuery) use ($searchValue) {
                                    $centerQuery->where('name', 'like', "%{$searchValue}%");
                                })
                                ->orWhereHas('city', function ($cityQuery) use ($searchValue) {
                                    $cityQuery->where('name', 'like', "%{$searchValue}%")
                                        ->orWhereHas('state', function ($stateQuery) use ($searchValue) {
                                            $stateQuery->where('name', 'like', "%{$searchValue}%");
                                        });
                                })
                                ->orWhereHas('city', function ($c) use ($searchValue) {
                                    $c->whereRaw(
                                        "CONCAT(name, ', ', (SELECT name FROM states WHERE id = cities.state_id)) LIKE ?",
                                        ["%{$searchValue}%"]
                                    );
                                });
                            if ($parsedDate && $parsedDate !== '1970-01-01') {
                                $q->orWhereDate('created_at', $parsedDate);
                            }
                            if ($yearSearch) {
                                $q->orWhereYear('created_at', $yearSearch);
                            }
                        });
                    }
                })
                
                ->orderColumn('full_name', function ($query, $order) {
                    $query->orderBy('alumnis.full_name', $order);
                })
                ->orderColumn('batch', function ($query, $order) {
                    $query->orderBy('alumnis.year_of_completion', $order);
                })
                ->orderColumn('mobile_number', function ($query, $order) {
                    $query->orderBy('alumnis.mobile_number', $order);
                })
                ->orderColumn('location', function ($query, $order) {
                    $query->orderBy(
                        DB::raw("(SELECT cities.name FROM cities WHERE cities.id = alumnis.city_id)"),
                        $order
                    );
                })
                ->orderColumn('center_location', function ($query, $order) {
                    $query->orderBy(
                        DB::raw("(SELECT name FROM center_locations WHERE center_locations.id = alumnis.center_id)"),
                        $order
                    );
                })
                ->orderColumn('occupation', function ($query, $order) {
                    $query->orderBy(
                        DB::raw("(SELECT name FROM occupations WHERE id = alumnis.occupation_id)"),
                        $order
                    );
                })
                ->orderColumn('status', function ($query, $order) {
                    $query->orderBy('alumnis.status', $order);
                })
                ->orderColumn('created_at', function ($query, $order) {
                    $query->orderBy('alumnis.created_at', $order);
                })

                ->editColumn('created_at', function ($row) {
                    return '<span>' . \Carbon\Carbon::parse($row->created_at)
                        ->setTimezone('Asia/Kolkata')
                        ->format('M d, Y') . '</span>';
                })

                ->editColumn('alumni', function ($row) {
                        $img = $row->image_url ?? asset('images/avatar/blank.png');
                    return '<div style="display:flex;align-items:center;gap:12px;">
                        <img src="' . $img . '" style="width:40px;height:40px;border-radius:50%;object-fit:cover;">
                    </div>';
                })
                ->addColumn('full_name', function ($row) {
                    return '<span style="font-weight:600; color:#333;">' . e($row->full_name ?? '—') . '</span>';
                })
                ->addColumn('batch', function ($row) {
                    return '<span style="background-color:#fff3cd;color:#ff8c42;padding:5px 12px;border-radius:20px;font-size:12px;font-weight:600;">' . ($row->year_of_completion ?? '—') . '</span>';
                })
                ->addColumn('mobile_number', function ($row) {
                    return '<span style="font-weight:600; color:#333;">' . ($row->mobile_number ?? '—') . '</span>';
                })
                ->addColumn('center_location', function ($row) {
                    return ($row->centerLocation->name ?? '-');
                })
                ->addColumn('location', function ($row) {
                    return ($row->city?->name ?? '-') . ', ' . ($row->city?->state?->name ?? '-');
                })
                ->addColumn('occupation', function ($row) {
                    return $row->occupation->name ?? '-';
                })
                ->addColumn('connections', function ($row) {
                    return '<button onclick="viewConnections(' . $row->id . ')" class="btn btn-sm" style="border:1px solid #e5e7eb;font-weight:600;"
                    onmouseover="this.style.backgroundColor=\'#ba0028\'; this.style.color=\'#fff\'" onmouseout="this.style.backgroundColor=\'#fff\'; this.style.color=\'#000000ff\'">
                    <i class="fa fa-users"></i> View</button>';
                })
                ->addColumn('status', function ($row) {
                    $status = strtolower($row->status);

                    // Custom color styles
                    $style = match ($status) {
                        'active' => 'background-color:#b71c1c;color:#fff;',    // dark red
                        'blocked' => 'background-color:#f28b82;color:#fff;',   // light red
                        default => 'background-color:#b0bec5;color:#fff;',     // gray for inactive
                    };

                    return '<span style="padding:4px 10px;border-radius:12px;font-weight:600;font-size:12px;' . $style . '">'
                        . ucfirst($status) .
                        '</span>';
                })

                ->addColumn('action', function ($row) {

                    $status = strtolower($row->status);
                    if($status == 'active') {
                        $imageUrl = $row->image_url ?? asset('images/avatar/blank.png');
                    } else {
                        $imageUrl = asset('images/avatar/blank.png');
                    }
                    $user = auth()->user();
                    $action = '
                    <div class="dropdown d-inline-block ms-2">
                        <button class="btn btn-sm" type="button" id="actionMenu' . $row->id . '" 
                            data-bs-toggle="dropdown" aria-expanded="false" 
                            style="padding:5px 8px; border:none;">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="actionMenu' . $row->id . '" 
                            style="padding:4px; border:1px solid #e5e7eb;">
                            <li>
                                <a class="dropdown-item" href="javascript:void(0)" 
                                onclick="viewProfilePic(\'' . $imageUrl . '\')"
                                onmouseover="this.style.backgroundColor=\'#ba0028\';this.style.color=\'#fff\';"
                                onmouseout="this.style.backgroundColor=\'#fff\';this.style.color=\'#000\'">
                                <i class="fa-regular fa-eye me-2"></i>View Profile Pic
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="javascript:void(0)" 
                                onclick="openDetailsModal(' . $row->id . ')"
                                onmouseover="this.style.backgroundColor=\'#ba0028\';this.style.color=\'#fff\';"
                                onmouseout="this.style.backgroundColor=\'#fff\';this.style.color=\'#000\'">
                                <i class="fa fa-info me-2"></i>View Other Info
                                </a>
                            </li>
                    ';
                    if ($user->can('directory.edit')) {
                        if ($status == 'blocked') {
                            $action .= '
                                <li>
                                    <a class="dropdown-item" href="javascript:void(0)" 
                                    onclick="updateStatus(' . $row->id . ', \'unblocked\')"
                                    onmouseover="this.style.backgroundColor=\'#ba0028\';this.style.color=\'#fff\';"
                                    onmouseout="this.style.backgroundColor=\'#fff\';this.style.color=\'#000\'">
                                    <i class="fa-solid fa-unlock me-2"></i>Unblock
                                    </a>
                                </li>';
                        } else {
                            $action .= '
                                <li>
                                    <a class="dropdown-item" href="javascript:void(0)" 
                                    onclick="updateStatus(' . $row->id . ', \'blocked\')"
                                    style="color:#ff0000;"
                                    onmouseover="this.style.backgroundColor=\'#ba0028\';this.style.color=\'#fff\';"
                                    onmouseout="this.style.backgroundColor=\'#fff\';this.style.color=\'#ff0000\';">
                                    <i class="fa-solid fa-ban me-2"></i>Block
                                    </a>
                                </li>';
                        }
                    }
                    $action .= '</ul></div>';
                    return $action;
                })
                ->rawColumns(['alumni', 'batch', 'location', 'action', 'full_name', 'mobile_number', 'created_at', 'connections', 'status'])
                ->make(true);
        } catch (\Throwable $e) {
            Log::error('Directory DataTable error: ' . $e->getMessage(), [
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function connectionViewPage($id)
    {
        return view('directory.connectionview', compact('id'));
    }

    public function viewConnectionList(Request $request, $id)
    {
        try {

            $connections = AlumniConnections::where(function ($q) use ($id) {
                $q->where('sender_id', $id)
                    ->orWhere('receiver_id', $id);
            })
                ->where('status', 'accepted')
                ->get();

            $connectedAlumniIds = $connections
            ->sortByDesc('updated_at')
            ->map(function ($c) use ($id) {
                return $c->sender_id == $id ? $c->receiver_id : $c->sender_id;
            })->values();

            $query = Alumnis::whereIn('id', $connectedAlumniIds)
                ->with(['city', 'occupation']);
            // $idsOrder = $connectedAlumniIds->implode(',');
            // $query = Alumnis::whereIn('id', $connectedAlumniIds)
            //     ->with(['city', 'occupation'])
            //     ->orderByRaw("FIELD(id, $idsOrder)");

            if ($request->filled('batch')) {
                $batches = is_array($request->batch) ? $request->batch : [$request->batch];
                $query->whereIn('year_of_completion', $batches);
            }
            
            if ($request->filled('location')) {
                $locations = is_array($request->location) ? $request->location : [$request->location];
                $query->whereHas('city', function ($q) use ($locations) {
                    $q->whereIn('name', $locations);
                });
            }
            
            // Search
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('full_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('year_of_completion', 'like', "%{$search}%")
                      ->orWhereHas('city', function ($cityQuery) use ($search) {
                                    $cityQuery->where('name', 'like', "%{$search}%")
                                        ->orWhereHas('state', function ($stateQuery) use ($search) {
                                            $stateQuery->where('name', 'like', "%{$search}%");
                                        });
                                })
                                ->orWhereHas('city', function ($c) use ($search) {
                                    $c->whereRaw(
                                        "CONCAT(name, ', ', (SELECT name FROM states WHERE id = cities.state_id)) LIKE ?",
                                        ["%{$search}%"]
                                    );
                                });
                });
            }

            return DataTables::of($query)
                ->addIndexColumn()

                ->editColumn('alumni', function ($row) {
                    $img = $row->image_url ?? asset('images/avatar/blank.png');

                    return '
                    <div style="display:flex;align-items:center;gap:12px;">
                        <img src="' . $img . '" 
                            style="width:40px;height:40px;border-radius:50%;object-fit:cover;">
                        <span style="font-weight:600;">' . $row->full_name . '</span>
                    </div>';
                })

                ->addColumn(
                    'batch',
                    fn($row) =>
                    '<span style="padding:5px 12px;border-radius:20px;font-size:12px;font-weight:600;">'
                        . ($row->year_of_completion ?? '—') . '</span>'
                )

                ->addColumn(
                    'location',
                    fn($row) => ($row->centerLocation?->name ?? '-') . ', ' . ($row->city?->name ?? '-') . ', ' . ($row->city?->state?->name ?? '-')
                )

                ->addColumn(
                    'viewProfile',
                    fn($row) =>
                    '<button onclick="viewProfileDetails(' . $row->id . ')" class="btn btn-sm"><i class="fa-regular fa-eye"></i></button>'
                )

                ->addColumn('status', function ($row) {
                    return '<span style="background:#4caf50;color:#fff;padding:5px 10px;border-radius:12px;font-weight:600;font-size:12px;">
                    Contact Accepted
                </span>';
                })

                ->rawColumns(['alumni', 'batch', 'location', 'viewProfile', 'status'])
                ->make(true);
        } catch (\Throwable $e) {
            Log::error('Connection List Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getConnectionFilterOptions($id)
    {
        try {
            // Get all connections for this alumni
            $connections = AlumniConnections::where(function ($q) use ($id) {
                $q->where('sender_id', $id)->orWhere('receiver_id', $id);
            })
            ->where('status', 'accepted')
            ->get();

            $connectedAlumniIds = $connections->map(function ($c) use ($id) {
                return $c->sender_id == $id ? $c->receiver_id : $c->sender_id;
            });

            // Get unique batch years from connected alumni
            $batches = Alumnis::whereIn('id', $connectedAlumniIds)
                ->whereNotNull('year_of_completion')
                ->distinct()
                ->pluck('year_of_completion')
                ->sort()
                ->values()
                ->toArray();

            // Get unique locations from connected alumni
            $locations = Alumnis::whereIn('id', $connectedAlumniIds)
                ->with('city')
                ->get()
                ->pluck('city.name')
                ->filter()
                ->unique()
                ->sort()
                ->values()
                ->toArray();

            return response()->json([
                'success' => true,
                'batches' => $batches,
                'locations' => $locations
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching connection filter options: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load filter options'
            ], 500);
        }
    }

    public function updateStatus(Request $request)
    {
        try {
            $id = $request->id;
            $alumni = Alumnis::findOrFail($id);
            $status = strtolower($request->status);
            if ($status === 'blocked') {
                $connections = AlumniConnections::where(function ($q) use ($id) {
                    $q->where('sender_id', $id)
                        ->orWhere('receiver_id', $id);
                });
                $connections->delete();
                
                $defaultRemark = "Your profile has been blocked. Therefore, your posts will not be visible to other alumni.";
                ForumPost::where('alumni_id', $id)
                    ->whereIn('status', ['pending', 'approved', 'rejected'])
                    ->update(['status' => 'removed_by_admin',
                            'remarks' => $defaultRemark
                    ]);
                if ($alumni->notify_admin_approval === 1) {
                    $data = [
                        'name' => $alumni->full_name,
                        'remarks' => $request->remarks,
                        'support_email' => env('SUPPORT_EMAIL'),
                    ];
                    Mail::to($alumni->email)->queue(new AlumniBlockedMail($data));
                }
                $alumni->status = 'blocked';
                $message = 'User Blocked Successfully.';
            } elseif ($status === 'unblocked') {
                $alumni->remarks = null;
                if ($alumni->notify_admin_approval === 1) {
                    $data = [
                        'name' => $alumni->full_name,
                        'support_email' => env('SUPPORT_EMAIL'),
                    ];
                    Mail::to($alumni->email)->queue(new AlumniUnBlockedMail($data));
                }
                $alumni->status = 'active';
                $message = 'User Unblocked Successfully.';
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid status value!'
                ], 400);
            }
            $alumni->save();

            return response()->json([
                'success' => true,
                'message' => $message ?? 'Status updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update alumni status: ' . $e->getMessage()
            ], 500);
        }
    }

    public function viewProfileDetails(Request $request, $id)
    {
        try {
            $alumni = Alumnis::with(['city.state', 'occupation', 'centerLocation'])->findOrFail($id);
            if (!$alumni) {
                return response()->json([
                    'success' => false,
                    'message' => 'Alumni not found'
                ], 404);
            }

            $data = [
                'name' => $alumni->full_name,
                'email' => $alumni->email,
                'batch' => $alumni->year_of_completion,
                'location' => ($alumni->city?->state?->name ?? '-') . ', ' . ($alumni->city?->name ?? '-'),
                'occupation' => $alumni->occupation->name ?? '-',
                'company' => $alumni->company_name ?? '-',
                'mobile_number' => $alumni->mobile_number,
                'image_url' => $alumni->image_url ?? asset('images/avatar/blank.png'),
                'status' => $alumni->status ?? 'inactive',
                'center_location' => $alumni->centerLocation->name ?? '-',
                'state' => $alumni->city?->state?->name ?? '-',
                'city' => $alumni->city?->name ?? '-',
                'pincode' => $alumni->pincode->pincode ?? '-',
                'current_location' => $alumni->current_location ?? '-',
                'linkedin_profile' => $alumni->linkedin_profile ?? '-',
                'organization' => $alumni->organization ?? '-',
                'university' => $alumni->university ?? '-',
                'level_completed' => $alumni->level_completed ?? '-',
            ];

            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'Profile details fetched successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch profile details: ' . $e->getMessage()
            ], 500);
        }
    }


    public function getFilterOptions()
    {
        try {
            // Get unique years
            $years = Alumnis::whereNotNull('year_of_completion')
                ->distinct()
                ->pluck('year_of_completion')
                ->sort()
                ->values();

            // Get unique cities
            $cities = Alumnis::with('city')
                ->whereHas('city')
                ->get()
                ->pluck('city.name')
                ->unique()
                ->sort()
                ->values();

            // Get unique center locations
            $centerLocations = Alumnis::with('centerLocation')
                ->whereHas('centerLocation')
                ->get()
                ->pluck('centerLocation.name')
                ->unique()
                ->sort()
                ->values();

            // Get unique occupations
            $occupations = Alumnis::with('occupation')
                ->whereHas('occupation')
                ->get()
                ->pluck('occupation.name')
                ->unique()
                ->sort()
                ->values();

            return response()->json([
                'success' => true,
                'years' => $years,
                'cities' => $cities,
                'centerLocations' => $centerLocations,
                'occupations' => $occupations
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching filter options: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching filter options'
            ], 500);
        }
    }

    public function export(Request $request)
    {
        if ($request->format == 'csv') {
            return Excel::download(new DirectoryExport($request), 'alumni_directory.csv');
        } else {
            return Excel::download(new DirectoryExport($request), 'alumni_directory.xlsx');
        };
    }
}
