<?php

namespace App\Http\Controllers\Alumni;

use App\Http\Controllers\Controller;
use App\Models\AlumniConnections;
use App\Models\Alumnis;
use App\Models\MobileOtp;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class ConnectionsController extends Controller
{
    public function index()
    {
        $breadCrum = ['Alumni', 'Connections'];
        $title = 'Connection List';
        return view('alumni.connections.index', compact('breadCrum', 'title'));
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
                                       });
                                });
                        });
                    });
                }
            })
            
            ->addColumn('alumni', function ($row) use ($alumniId) {
                $alumni = $row->sender_id == $alumniId ? $row->receiver : $row->sender;
                $img = $alumni->image ? asset($alumni->image) : asset('images/avatar/blank.png');
                return '
                <div style="display:flex;align-items:center;gap:12px;">
                    <img src="' . $img . '" style="width:40px;height:40px;border-radius:50%;object-fit:cover;">
                    <div>
                        <div style="font-weight:700;color:#333;font-size:14px;">' . $alumni->full_name . '</div>
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
                return '<span style="background-color:#ffd966;padding:4px 10px;border-radius:12px;font-weight:600;font-size:12px;color:#333;">' . $year . '</span>';
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
            style="background-color:#c41e3a;color:white;border:none;border-radius:4px;padding:6px 12px;font-size:12px;">
            👁 View Profile
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
                            });
                    });
                }
            })
            
            ->editColumn('alumni', function ($row) {
                $alumni = $row->sender;
                $img = $alumni->image ? asset($alumni->image) : asset('images/avatar/blank.png');
                return '
                    <div style="display:flex;align-items:center;gap:12px;">
                        <img src="' . $img . '" style="width:40px;height:40px;border-radius:50%;object-fit:cover;">
                        <div>
                            <div style="font-weight:700;color:#333;font-size:14px;">' . $alumni->full_name . '</div>
                            <div style="color:#999;font-size:12px;">' . $alumni->email . '</div>
                        </div>
                    </div>';
            })
            ->addColumn('email', fn($row) => $row->sender->email)
            ->editColumn('batch', fn($row) => '<span style="background-color:#ffd966;padding:4px 10px;border-radius:12px;font-weight:600;font-size:12px;color:#333;">' . $row->sender->year_of_completion . '</span>')
            ->addColumn('location', fn($row) => $row->sender->city?->name . ', ' . $row->sender->city?->state?->name)
            ->addColumn('action', function ($row) {
                return '
                    <div style="display:flex;gap:8px;">
                        <button onclick="acceptRequest(' . $row->id . ')" class="btn btn-sm" style="background-color:#28a745;color:white;border:none;border-radius:4px;padding:6px 12px;font-size:12px;cursor:pointer;">Accept</button>
                        <button onclick="rejectRequest(' . $row->id . ')" class="btn btn-sm" style="background-color:#c41e3a;color:white;border:none;border-radius:4px;padding:6px 12px;font-size:12px;cursor:pointer;">Reject</button>
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
        if ($connection) {
            $connection->update(['status' => 'accepted']);
            return response()->json(['success' => true, 'message' => 'Connection accepted']);
        }
        return response()->json(['success' => false, 'message' => 'Connection not found'], 404);
    }

    public function rejectConnection(Request $request)
    {
        $connection = AlumniConnections::find($request->id);
        if ($connection) {
            $connection->delete();
            return response()->json(['success' => true, 'message' => 'Connection rejected']);
        }
        return response()->json(['success' => false, 'message' => 'Connection not found'], 404);
    }
}
