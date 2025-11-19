<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\EmployeeEmail;
use App\Models\AlumniConnections;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Alumnis;
use App\Models\Department;
use App\Models\BranchLocation;
use App\Models\Designation;
use App\Models\Location;
use App\Models\Role;
use App\Models\Task;
use App\Notifications\EmployeeCreateNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
                    return '<span>' . \Carbon\Carbon::parse($row->created_at)
                        ->setTimezone('Asia/Kolkata')
                        ->format('M d, Y') . '</span>';
                })

                ->editColumn('alumni', function ($row) {
                    $img = $row->image ? url('storage/' . $row->image ?? '') : asset('images/avatar/blank.png');
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
                ->addColumn('location', function ($row) {
                    return ($row->city?->state?->name ?? '-') . ', ' . ($row->city?->name ?? '-');
                })
                ->addColumn('occupation', function ($row) {
                    return $row->occupation->name ?? '-';
                })
                ->addColumn('connections', function ($row) {
                    return '<button onclick="viewConnections(' . $row->id . ')" class="btn btn-sm btn-primary">View Profile</button>';
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
                    $imageUrl = $row->image ? url('storage/' . $row->image) : asset('images/avatar/blank.png');

                    if ($status == 'blocked') {
                        return '
        <div class="dropdown d-inline-block ms-2">
            <button class="btn btn-sm" type="button" id="actionMenu' . $row->id . '" data-bs-toggle="dropdown" aria-expanded="false" style="padding:5px 8px; border:none;">
                <i class="fas fa-ellipsis-v"></i>
            </button>
            <ul class="dropdown-menu" aria-labelledby="actionMenu' . $row->id . '">
                <li><a class="dropdown-item" href="javascript:void(0)" onclick="viewProfilePic(\'' . $imageUrl . '\')"><i class="fa-regular fa-eye me-2"></i>View Profile Pic</a></li>
                <li><a class="dropdown-item" href="javascript:void(0)" onclick="updateStatus(' . $row->id . ', \'unblocked\')">Unblock</a></li>
            </ul>
        </div>';
                    } else {
                        return '
        <div class="dropdown d-inline-block ms-2">
            <button class="btn btn-sm" type="button" id="actionMenu' . $row->id . '" data-bs-toggle="dropdown" aria-expanded="false" style="padding:5px 8px; border:none;">
                <i class="fas fa-ellipsis-v"></i>
            </button>
            <ul class="dropdown-menu" aria-labelledby="actionMenu' . $row->id . '">
                <a class="dropdown-item" href="javascript:void(0)" onclick="viewProfilePic(\'' . $imageUrl . '\')">
                <i class="fa-regular fa-eye me-2"></i>View Profile Pic
            </a>
                <li><a class="dropdown-item text-danger" href="javascript:void(0)" onclick="updateStatus(' . $row->id . ', \'blocked\')"><i class="fa-solid fa-ban me-2"></i>Block</a></li>
            </ul>
        </div>';
                    }
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
        return view('directory.connectionView', compact('id'));
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

            // Step 2: Extract connected alumni IDs (the OTHER user)
            $connectedAlumniIds = $connections->map(function ($c) use ($id) {
                return $c->sender_id == $id ? $c->receiver_id : $c->sender_id;
            });

            $query = Alumnis::whereIn('id', $connectedAlumniIds)->with(['city', 'occupation'])->orderBy('id', 'desc');

            // Filters
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

                ->editColumn('alumni', function ($row) {
                    $img = $row->image ? url('storage/' . $row->image ?? '') : asset('images/avatar/blank.png');

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
                    fn($row) => ($row->city?->state?->name ?? '-') . ', ' . ($row->city?->name ?? '-')
                )

                ->addColumn(
                    'viewProfile',
                    fn($row) =>
                    '<button onclick="viewProfile(' . $row->id . ')" class="btn btn-sm btn-primary">View Profile</button>'
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


    public function updateStatus(Request $request)
    {
        try {
            $id = $request->id;
            $alumni = Alumnis::findOrFail($id);
            $status = strtolower($request->status);
            if ($status === 'blocked') {
                $alumni->status = 'blocked';
            } elseif ($status === 'unblocked') {
                $alumni->status = 'active';
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid status value!'
                ], 400);
            }
            $alumni->save();

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update alumni status: ' . $e->getMessage()
            ], 500);
        }
    }

    public function sendEmployeeMail(Request $request, $id)
    {
        try {
            $employee = Employee::where('id', $id)
                ->where('status', '1')
                ->whereNull('deleted_at')
                ->first();

            if (!$employee) {
                return $this->returnError('Employee not found, inactive, or deleted', 'Validation Error', 422);
            }
            if ($employee) {
                $employee->notify(new EmployeeCreateNotification($employee));
            }

            return $this->returnSuccess([], "We have e-mailed password to the employee mail!");
        } catch (\Exception $e) {
            // Handle any errors during the mail sending process
            return $this->returnError($e->getMessage());
        }
    }
}
