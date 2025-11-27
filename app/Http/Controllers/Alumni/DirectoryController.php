<?php

namespace App\Http\Controllers\Alumni;

use App\Http\Controllers\Controller;
use App\Mail\AlumniShareContactMail;
use App\Models\AlumniConnections;
use App\Models\Alumnis;
use App\Models\MobileOtp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;


class DirectoryController extends Controller
{
    public function index()
    {
        $alumniId = session('alumni.id');
        $breadCrum = ['Alumni', 'Directory'];
        $title = 'Alumni Directory';
        $totalAlumni = Alumnis::where('id', '!=', $alumniId)->where('status', 'active')->count();
        $alumni = Alumnis::find($alumniId);
        $isDirectoryRibbon = $alumni ? $alumni->is_directory_ribbon : 0;

        return view('alumni.directory.index', compact('breadCrum', 'title', 'totalAlumni', 'isDirectoryRibbon'));
    }

    public function getFilterOptions()
    {
        try {
            $alumniId = session('alumni.id');

            $batchYears = Alumnis::where('id', '!=', $alumniId)
                ->whereNotNull('year_of_completion')
                ->where('status', 'active')
                ->distinct()
                ->orderBy('year_of_completion', 'desc')
                ->pluck('year_of_completion')
                ->toArray();

            $locations = Alumnis::with(['city.state'])
                ->where('id', '!=', $alumniId)
                ->where('status', 'active')
                ->whereNotNull('city_id')
                ->get()
                ->map(function ($alumni) {
                    if ($alumni->city && $alumni->city->state) {
                        return [
                            'id' => $alumni->city_id,
                            'name' => $alumni->city->name . ', ' . $alumni->city->state->name
                        ];
                    }
                    return null;
                })
                ->filter()
                ->unique('id')
                ->values()
                ->toArray();

            $allOtherAlumni = Alumnis::where('id', '!=', $alumniId)->where('status', 'active')->pluck('id')->toArray();
            $connections = AlumniConnections::where('sender_id', $alumniId)
                ->orWhere('receiver_id', $alumniId)
                ->get();

            $connectedIds = $connections->map(function ($c) use ($alumniId) {
                return $c->sender_id == $alumniId ? $c->receiver_id : $c->sender_id;
            })
                ->unique()
                ->toArray();
            $notSharedExists = count(array_diff($allOtherAlumni, $connectedIds)) > 0;
            $existingStatuses = $connections->pluck('status')->unique()->toArray();

            $statuses = [];
            if ($notSharedExists) {
                $statuses[] = ['id' => 'not_shared', 'name' => 'Not Shared'];
            }

            foreach ($existingStatuses as $status) {
                if ($status === 'pending') {
                    $statuses[] = ['id' => 'pending', 'name' => 'Shared'];
                }
                if ($status === 'accepted') {
                    $statuses[] = ['id' => 'accepted', 'name' => 'Accepted'];
                }
                if ($status === 'rejected') {
                    $statuses[] = ['id' => 'rejected', 'name' => 'Rejected'];
                }
            }

            return response()->json([
                'batchYears' => $batchYears,
                'locations' => $locations,
                'connectionStatuses' => $statuses
            ]);
        } catch (\Throwable $e) {
            Log::error('Filter options error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getData(Request $request)
    {
        try {
            $alumniId = session('alumni.id');

            $query = Alumnis::with(['city', 'occupation'])
                ->where('id', '!=', $alumniId)
                ->where('status', 'active');

            // Apply filters
            if ($request->filled('batch_years') && $request->batch_years != '') {
                $batchYears = is_array($request->batch_years) ? $request->batch_years : explode(',', $request->batch_years);
                $batchYears = array_filter($batchYears); // Remove empty values
                if (!empty($batchYears)) {
                    $query->whereIn('year_of_completion', $batchYears);
                }
            }

            if ($request->filled('locations') && $request->locations != '') {
                $locations = is_array($request->locations) ? $request->locations : explode(',', $request->locations);
                $locations = array_filter($locations); // Remove empty values
                if (!empty($locations)) {
                    $query->whereIn('city_id', $locations);
                }
            }

            $alumniConnections = AlumniConnections::where('sender_id', $alumniId)
                ->orWhere('receiver_id', $alumniId)
                ->get()
                ->mapWithKeys(function ($conn) use ($alumniId) {
                    $otherId = $conn->sender_id == $alumniId ? $conn->receiver_id : $conn->sender_id;
                    return [$otherId => $conn->status];
                })
                ->toArray();
            $activeAlumniIds = Alumnis::where('status', 'active')
                ->where('id', '!=', $alumniId)
                ->pluck('id')
                ->toArray();
            $connectedIds = array_keys($alumniConnections);
            $notSharedIds = array_diff($activeAlumniIds, $connectedIds);


            if ($request->filled('connection_statuses') && $request->connection_statuses != '') {
                $connectionStatuses = is_array($request->connection_statuses)
                    ? $request->connection_statuses
                    : explode(',', $request->connection_statuses);
                $connectionStatuses = array_filter($connectionStatuses);
                if (!empty($connectionStatuses)) {

                    $query->where(function ($q) use (
                        $connectionStatuses,
                        $alumniConnections,
                        $notSharedIds
                    ) {

                        foreach ($connectionStatuses as $status) {

                            if ($status === 'not_shared') {
                                if (!empty($notSharedIds)) {
                                    $q->orWhereIn('id', $notSharedIds);
                                }
                            }

                            if ($status === 'pending') {
                                $pendingIds = array_keys(array_filter($alumniConnections, fn($s) => $s === 'pending'));
                                if (!empty($pendingIds)) $q->orWhereIn('id', $pendingIds);
                            }

                            if ($status === 'accepted') {
                                $acceptedIds = array_keys(array_filter($alumniConnections, fn($s) => $s === 'accepted'));
                                if (!empty($acceptedIds)) $q->orWhereIn('id', $acceptedIds);
                            }

                            if ($status === 'rejected') {
                                $rejectedIds = array_keys(array_filter($alumniConnections, fn($s) => $s === 'rejected'));
                                if (!empty($rejectedIds)) $q->orWhereIn('id', $rejectedIds);
                            }
                        }
                    });
                }
            }


            return DataTables::of($query)
                ->addIndexColumn()

                ->filter(function ($query) use ($request) {
                    if ($request->has('search') && !empty($request->search['value'])) {
                        $searchValue = $request->search['value'];

                        $query->where(function ($q) use ($searchValue) {
                            $q->where('full_name', 'like', "%{$searchValue}%")
                                ->orWhere('year_of_completion', 'like', "%{$searchValue}%")
                                ->orWhere('status', 'like', "%{$searchValue}%")
                                ->orWhereHas('occupation', function ($occQuery) use ($searchValue) {
                                    $occQuery->where('name', 'like', "%{$searchValue}%");
                                })
                                ->orWhereHas('city', function ($cityQuery) use ($searchValue) {
                                    $cityQuery->where('name', 'like', "%{$searchValue}%")
                                        ->orWhereHas('state', function ($stateQuery) use ($searchValue) {
                                            $stateQuery->where('name', 'like', "%{$searchValue}%");
                                        });
                                });
                        });
                    }
                })

                ->editColumn('alumni', function ($row)  use ($alumniConnections) {
                    $status = $alumniConnections[$row->id] ?? null;
                    $isAccepted = $status === 'accepted';
                    $alumni = Alumnis::find($row->id);
                    if (!$isAccepted) {
                        $img = asset('images/avatar/blank.png');
                    } else {
                        $img = $alumni->image_url ?? asset('images/avatar/blank.png');
                    }
                    $occ = $row->occupation->name ?? '—';
                    return '
            <div style="display:flex;align-items:center;gap:12px;">
                <img src="' . $img . '" style="width:40px;height:40px;border-radius:50%;object-fit:cover;">
                <div>
                    <div style="font-weight:700;color:#333;font-size:14px;">' . $row->full_name . '</div>
                    <div style="color:#999;font-size:12px;">' . $occ . '</div>
                </div>
            </div>';
                })

                ->addColumn('batch', function ($row) {
                    return '
            <span style="color:#B1040E;padding:2px 12px;border-radius:20px;font-size:11px;font-weight:700; border: 1px solid #F7C744; background-color: color-mix(in oklab, #F7C744 20%, transparent)">
                ' . ($row->year_of_completion ?? '—') . '
            </span>';
                })

                ->addColumn('location', function ($row) {
                    return ($row->city?->state?->name ?? '-') . ', ' . ($row->city?->name ?? '-');
                })

                ->addColumn('action', function ($row) use ($alumniConnections, $alumniId) {
                    $status = $alumniConnections[$row->id] ?? null;

                    if (!$status) {
                        return '<form method="POST" action="' . route('alumni.send.request', $row->id) . '">' .
                            csrf_field() . '
                <button style="background-color:#c41e3a;color:white;padding:6px 12px;border:none;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;">
                    Share Contact
                </button></form>';
                    } elseif ($status == 'pending') {
                        return '<button style="background-color:#e5e7eb;color:#6b7280;padding:4px 12px;border:none;border-radius:16px;font-size:12px;font-weight:600;cursor:default;">Contact Shared</button>';
                    } elseif ($status == 'accepted') {
                        return '<button style="background-color:#f59e0b;color:#000;padding:4px 12px;border:none;border-radius:16px;font-size:12px;font-weight:600;cursor:default;">Contact Accepted</button>';
                    } elseif ($status == 'rejected') {
                        return '<div style="display:flex;align-items:center;gap:6px;">
                        <span style="background-color:#fee2e2;color:#dc2626;padding:4px 8px;border-radius:16px;font-size:12px;font-weight:600;">
                            Contact Rejected
                        </span>
                        <form method="POST" action="' . route('alumni.send.request', $row->id) . '" style="margin:0;">' .
                            csrf_field() . '
                            <button type="submit" style="background:white;color:#dc2626;padding:6px;border:2px solid #dc2626;border-radius:8px;font-size:11px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:6px;">
                                <i class="fas fa-sync-alt"></i> Reshare
                            </button>
                        </form>
                    </div>';
                    }

                    return '—';
                })

                ->rawColumns(['alumni', 'batch', 'location', 'action'])
                ->make(true);
        } catch (\Throwable $e) {
            Log::error('Directory DataTable error: ' . $e->getMessage(), [
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function sendRequest($receiverId)
    {
        $senderId = session('alumni.id');

        $receiver = Alumnis::find($receiverId);
        $receiver->is_request_ribbon = 1;
        $receiver->save();

        if ($senderId == $receiverId) {
            return back()->with('error', 'You cannot connect with yourself.');
        }

        $existing = AlumniConnections::where(function ($q) use ($senderId, $receiverId) {
            $q->where('sender_id', $senderId)->where('receiver_id', $receiverId);
        })
            ->orWhere(function ($q) use ($senderId, $receiverId) {
                $q->where('sender_id', $receiverId)->where('receiver_id', $senderId);
            })
            ->first();

        $sender = Alumnis::find($senderId);
        $data = [
            'name' => $receiver->full_name,
            'requester' => $sender->full_name,
            'support_email' => env('SUPPORT_EMAIL'),
        ];

        if ($existing && $existing->status == 'rejected') {
            $existing->update(['status' => 'pending']);
            Mail::to($receiver->email)->queue(new AlumniShareContactMail($data));
            return back()->with('success', 'Reshare request sent successfully!');
        }

        if ($existing) {
            return back()->with('info', 'Connection already exists.');
        }

        AlumniConnections::create([
            'sender_id' => $senderId,
            'receiver_id' => $receiverId,
            'status' => 'pending',
        ]);

        Mail::to($receiver->email)->queue(new AlumniShareContactMail($data));

        return back()->with('success', 'Connection request sent successfully!');
    }
}
