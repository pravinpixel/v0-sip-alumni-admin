<?php

namespace App\Http\Controllers\Alumni;

use App\Http\Controllers\Controller;
use App\Mail\AlumniAcceptRequestMail;
use App\Mail\AlumniRejectRequestMail;
use App\Models\AlumniConnections;
use App\Models\Alumnis;
use App\Models\MobileOtp;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Yajra\DataTables\Facades\DataTables;

class ConnectionsController extends Controller
{
    public function index()
    {
        $breadCrum = ['Alumni', 'Connections'];
        $title = 'Connection List';
        $alumniId = session('alumni.id');
        $alumni = Alumnis::find($alumniId);
        $isRequestRibbon = $alumni ? $alumni->is_request_ribbon : 0;
        
        return view('alumni.connections.index', compact('breadCrum', 'title', 'isRequestRibbon'));
    }

    public function getConnections(Request $request)
    {
        $alumniId = session('alumni.id');

        $query = AlumniConnections::with(['sender.city.state', 'sender.occupation', 'receiver.city.state', 'receiver.occupation'])
            ->where(function ($q) use ($alumniId) {
                $q->where('sender_id', $alumniId)
                    ->orWhere('receiver_id', $alumniId);
            })
            ->where('status', 'accepted');

        return DataTables::of($query)
            ->addIndexColumn()
            
            // Add search functionality
            ->filter(function ($query) use ($request, $alumniId) {
                if ($request->has('search') && !empty($request->search['value'])) {
                    $searchValue = $request->search['value'];
                    
                    $query->where(function ($q) use ($searchValue, $alumniId) {
                        // Search in sender (when current user is receiver)
                        $q->whereHas('sender', function ($senderQuery) use ($searchValue, $alumniId) {
                            $senderQuery->where('id', '!=', $alumniId)
                                ->where(function ($sq) use ($searchValue) {
                                    $sq->where('full_name', 'like', "%{$searchValue}%")
                                       ->orWhere('email', 'like', "%{$searchValue}%")
                                       ->orWhere('year_of_completion', 'like', "%{$searchValue}%")
                                       ->orWhereHas('occupation', function ($occQuery) use ($searchValue) {
                                           $occQuery->where('name', 'like', "%{$searchValue}%");
                                       })
                                       ->orWhereHas('city', function ($cityQuery) use ($searchValue) {
                                           $cityQuery->where('name', 'like', "%{$searchValue}%")
                                                    ->orWhereHas('state', function ($stateQuery) use ($searchValue) {
                                                        $stateQuery->where('name', 'like', "%{$searchValue}%");
                                                    });
                                       })
                                       ->orWhereHas('city', function ($city) use ($searchValue) {
                                            $city->whereRaw("
                                                LOWER(CONCAT(cities.name, ', ', (SELECT name FROM states WHERE states.id = cities.state_id))) 
                                                LIKE ?
                                            ", ["%{$searchValue}%"]);
                                        });
                                });
                        })
                        // Search in receiver (when current user is sender)
                        ->orWhereHas('receiver', function ($receiverQuery) use ($searchValue, $alumniId) {
                            $receiverQuery->where('id', '!=', $alumniId)
                                ->where(function ($rq) use ($searchValue) {
                                    $rq->where('full_name', 'like', "%{$searchValue}%")
                                       ->orWhere('email', 'like', "%{$searchValue}%")
                                       ->orWhere('year_of_completion', 'like', "%{$searchValue}%")
                                       ->orWhereHas('occupation', function ($occQuery) use ($searchValue) {
                                           $occQuery->where('name', 'like', "%{$searchValue}%");
                                       })
                                       ->orWhereHas('city', function ($cityQuery) use ($searchValue) {
                                           $cityQuery->where('name', 'like', "%{$searchValue}%")
                                                    ->orWhereHas('state', function ($stateQuery) use ($searchValue) {
                                                        $stateQuery->where('name', 'like', "%{$searchValue}%");
                                                    });
                                       })
                                        ->orWhereHas('city', function ($city) use ($searchValue) {
                                            $city->whereRaw("
                                                LOWER(CONCAT(cities.name, ', ', (SELECT name FROM states WHERE states.id = cities.state_id))) 
                                                LIKE ?
                                            ", ["%{$searchValue}%"]);
                                        });
                                });
                        });
                    });
                }
            })
            ->orderColumn('alumni', function ($query, $order) use ($alumniId) {
                $query->orderBy(
                    DB::raw("CASE 
                        WHEN sender_id = {$alumniId} THEN (SELECT full_name FROM alumnis WHERE id = receiver_id)
                        ELSE (SELECT full_name FROM alumnis WHERE id = sender_id)
                    END"),
                    $order
                );
            })
            ->orderColumn('email', function ($query, $order) use ($alumniId) {
                $query->orderBy(
                    DB::raw("CASE 
                        WHEN sender_id = {$alumniId} THEN (SELECT email FROM alumnis WHERE id = receiver_id)
                        ELSE (SELECT email FROM alumnis WHERE id = sender_id)
                    END"),
                    $order
                );
            })
            ->orderColumn('batch', function ($query, $order) use ($alumniId) {
                $query->orderBy(
                    DB::raw("CASE 
                        WHEN sender_id = {$alumniId} THEN (SELECT year_of_completion FROM alumnis WHERE id = receiver_id)
                        ELSE (SELECT year_of_completion FROM alumnis WHERE id = sender_id)
                    END"),
                    $order
                );
            })
            ->orderColumn('location', function ($query, $order) use ($alumniId) {
                $query->orderBy(
                    DB::raw("CASE 
                        WHEN sender_id = {$alumniId} THEN (
                            SELECT name FROM cities WHERE id = (SELECT city_id FROM alumnis WHERE id = receiver_id)
                        )
                        ELSE (
                            SELECT name FROM cities WHERE id = (SELECT city_id FROM alumnis WHERE id = sender_id)
                        )
                    END"),
                    $order
                );
            })
            ->addColumn('alumni', function ($row) use ($alumniId) {
                $alumni = $row->sender_id == $alumniId ? $row->receiver : $row->sender;
                $img = $alumni->image_url ?? asset('images/avatar/blank.png');
                $occ = $alumni->occupation->name ?? '—';
                return '
                <div style="display:flex;align-items:center;gap:12px;">
                    <img src="' . $img . '" style="width:40px;height:40px;border-radius:50%;object-fit:cover;">
                    <div>
                        <div style="font-weight:700;color:#333;font-size:14px;">' . $alumni->full_name . '</div>
                        <div style="font-size:12px;color:#666;">' . $occ . '</div>
                    </div>
                </div>';
            })
            ->addColumn('email', function ($row) use ($alumniId) {
                $alumni = $row->sender_id == $alumniId ? $row->receiver : $row->sender;
                return $alumni->email ?? '-';
            })
            ->addColumn('batch', function ($row) use ($alumniId) {
                $alumni = $row->sender_id == $alumniId ? $row->receiver : $row->sender;
                $year = $alumni->year_of_completion ?? '-';
                return '<span style="color:#B1040E;padding:2px 12px;border-radius:20px;font-size:11px;font-weight:700; border: 1px solid #F7C744; background-color: color-mix(in oklab, #F7C744 20%, transparent)">' . $year . '</span>';
            })
            ->addColumn('location', function ($row) use ($alumniId) {
                $alumni = $row->sender_id == $alumniId ? $row->receiver : $row->sender;
                return ($alumni->city?->name ?? '-') . ', ' . ($alumni->city?->state?->name ?? '-');
            })
            ->addColumn('action', function ($row) use ($alumniId) {
                $alumni = $row->sender_id == $alumniId ? $row->receiver : $row->sender;
                return '
        <button onclick="viewProfile(' . $alumni->id . ')" 
            class="btn btn-sm" 
            style="background-color:#c41e3a;width:100%;color:white;border:none;border-radius:4px;padding:clamp(4px,1.5vw,8px) clamp(6px,2vw,12px);font-size:12px;">
            <i class="fa-regular fa-eye me-2"></i> View Profile
        </button>';
            })
            ->editColumn('created_at', fn($row) => Carbon::parse($row->created_at)->format('d M Y'))
            ->rawColumns(['alumni', 'batch', 'action'])
            ->make(true);
    }


    public function getRequests(Request $request)
    {
        $alumniId = session('alumni.id');

        $query = AlumniConnections::with(['sender.city.state', 'sender.occupation', 'receiver'])
            ->where('receiver_id', $alumniId)
            ->where('status', 'pending');

        return DataTables::of($query)
            ->addIndexColumn()
            
            // Add search functionality
            ->filter(function ($query) use ($request) {
                if ($request->has('search') && !empty($request->search['value'])) {
                    $searchValue = $request->search['value'];
                    
                    $query->whereHas('sender', function ($senderQuery) use ($searchValue) {
                        $senderQuery->where('full_name', 'like', "%{$searchValue}%")
                            ->orWhere('email', 'like', "%{$searchValue}%")
                            ->orWhere('year_of_completion', 'like', "%{$searchValue}%")
                            ->orWhereHas('occupation', function ($occQuery) use ($searchValue) {
                                $occQuery->where('name', 'like', "%{$searchValue}%");
                            })
                            ->orWhereHas('city', function ($cityQuery) use ($searchValue) {
                                $cityQuery->where('name', 'like', "%{$searchValue}%")
                                         ->orWhereHas('state', function ($stateQuery) use ($searchValue) {
                                             $stateQuery->where('name', 'like', "%{$searchValue}%");
                                         });
                            })
                            ->orWhereHas('city', function ($city) use ($searchValue) {
                                            $city->whereRaw("
                                                LOWER(CONCAT(cities.name, ', ', (SELECT name FROM states WHERE states.id = cities.state_id))) 
                                                LIKE ?
                                            ", ["%{$searchValue}%"]);
                                        });
                    });
                }
            })
            
            ->orderColumn('alumni', function ($query, $order) {
                $query->orderBy(
                    DB::raw("(SELECT full_name FROM alumnis WHERE id = alumni_connections.sender_id)"),
                    $order
                );
            })
            ->orderColumn('email', function ($query, $order) {
                $query->orderBy(
                    DB::raw("(SELECT email FROM alumnis WHERE id = alumni_connections.sender_id)"),
                    $order
                );
            })
            ->orderColumn('batch', function ($query, $order) {
                $query->orderBy(
                    DB::raw("(SELECT year_of_completion FROM alumnis WHERE id = alumni_connections.sender_id)"),
                    $order
                );
            })
            ->orderColumn('location', function ($query, $order) {
                $query->orderBy(
                    DB::raw("(SELECT name FROM cities WHERE id = (SELECT city_id FROM alumnis WHERE id = alumni_connections.sender_id))"),
                    $order
                );
            })

            ->editColumn('alumni', function ($row) {
                $alumni = $row->sender;
                $img = asset('images/avatar/blank.png');
                $occ = $alumni->occupation->name ?? '—';
                return '
                    <div style="display:flex;align-items:center;gap:12px;">
                        <img src="' . $img . '" style="width:40px;height:40px;border-radius:50%;object-fit:cover;">
                        <div>
                            <div style="font-weight:700;color:#333;font-size:14px;">' . $alumni->full_name . '</div>
                            <div style="font-size:12px;color:#666;">' . $occ . '</div>
                        </div>
                    </div>';
            })
            ->addColumn('email', fn($row) => $row->sender->email)
            ->editColumn('batch', fn($row) => '<span style="color:#B1040E;padding:2px 12px;border-radius:20px;font-size:11px;font-weight:700; border: 1px solid #F7C744; background-color: color-mix(in oklab, #F7C744 20%, transparent)">' . $row->sender->year_of_completion . '</span>')
            ->addColumn('location', fn($row) => $row->sender->city?->name . ', ' . $row->sender->city?->state?->name)
            ->addColumn('action', function ($row) {
                return '
                    <div style="display:flex;gap:8px;">
                        <button onclick="acceptRequest(' . $row->id . ', this)" class="btn btn-sm" style="background-color:#28a745;color:white;border:none;border-radius:4px;padding:6px 12px;font-size:12px;cursor:pointer;">Accept</button>
                        <button onclick="rejectRequest(' . $row->id . ', this)" class="btn btn-sm" style="background-color:#c41e3a;color:white;border:none;border-radius:4px;padding:6px 12px;font-size:12px;cursor:pointer;">Reject</button>
                    </div>';
            })
            ->rawColumns(['alumni', 'batch', 'action'])
            ->make(true);
    }

    public function getProfileData($id)
    {
        $alumni = Alumnis::with(['city', 'occupation'])
            ->findOrFail($id);

        return response()->json([
            'name' => $alumni->full_name,
            'email' => $alumni->email,
            'batch' => $alumni->year_of_completion,
            'location' => ($alumni->city?->name ?? '-') . ', ' . ($alumni->city?->state?->name ?? '-'),
            'occupation' => $alumni->occupation->name ?? '-',
            'contact' => $alumni->mobile_number ?? '-',
            'image' => $alumni->image_url ?? asset('images/avatar/blank.png'),
        ]);
    }


    public function acceptConnection(Request $request)
    {
        $connection = AlumniConnections::find($request->id);
        $sender = $connection->sender;
        $receiver = $connection->receiver;
        if ($connection) {
            $connection->update(['status' => 'accepted']);
            if ($sender->notify_post_comments === 1) {
                $data = [
                    'name' => $sender->full_name,
                    'alumni_name' => $receiver->full_name,
                    'support_email' => env('SUPPORT_EMAIL'),
                ];
                Mail::to($sender->email)->queue(new AlumniAcceptRequestMail($data));
                Log::info('Accepted Mail Sent');
            }
            $message = ($sender->full_name . ' invite accepted.');
            return response()->json(['success' => true, 'message' => $message]);
        }
        return response()->json(['success' => false, 'message' => 'Connection not found'], 404);
    }

    public function rejectConnection(Request $request)
    {
        $connection = AlumniConnections::find($request->id);
        $sender = $connection->sender;
        $receiver = $connection->receiver;
        if ($connection) {
            $connection->update(['status' => 'rejected']);
            if ($sender->notify_post_comments === 1) {
                $data = [
                    'name' => $sender->full_name,
                    'alumni_name' => $receiver->full_name,
                    'support_email' => env('SUPPORT_EMAIL'),
                ];
                Mail::to($sender->email)->queue(new AlumniRejectRequestMail($data));
                Log::info('Rejected Mail Sent');
            }
            $message = ($sender->full_name . ' invite rejected.');
            return response()->json(['success' => true, 'message' => $message]);
        }
        return response()->json(['success' => false, 'message' => 'Connection not found'], 404);
    }
}
