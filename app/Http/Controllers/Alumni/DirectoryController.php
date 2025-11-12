<?php

namespace App\Http\Controllers\Alumni;

use App\Http\Controllers\Controller;
use App\Models\AlumniConnections;
use App\Models\Alumnis;
use App\Models\MobileOtp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;


class DirectoryController extends Controller
{
    public function index()
    {
        $breadCrum = ['Alumni', 'Directory'];
        $title = 'Alumni Directory';
        return view('alumni.directory.index', compact('breadCrum', 'title'));
    }

    public function getData(Request $request)
    {
        try {
            $alumniId = session('alumni.id');

            $query = Alumnis::with(['city', 'occupation'])
                ->where('id', '!=', $alumniId);

            // filters etc... (same code)

            $alumniConnections = AlumniConnections::where('sender_id', $alumniId)
                ->orWhere('receiver_id', $alumniId)
                ->get()
                ->groupBy(function ($conn) use ($alumniId) {
                    return $conn->sender_id == $alumniId ? $conn->receiver_id : $conn->sender_id;
                })
                ->map(fn($group) => $group->first()->status)
                ->toArray();

            return DataTables::of($query)
                ->addIndexColumn()

                ->editColumn('alumni', function ($row) {
                    $img = $row->image ? asset($row->image) : asset('images/avatar/blank.png');
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
            <span style="background-color:#fff3cd;color:#ff8c42;padding:5px 12px;border-radius:20px;font-size:12px;font-weight:600;">
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
                <button style="background-color:#c41e3a;color:white;padding:7px 14px;border:none;border-radius:4px;font-size:12px;font-weight:600;">
                    Share Contact
                </button></form>';
                    } elseif ($status == 'pending') {
                        return '<button style="background-color:#fff3cd;color:#856404;padding:7px 14px;border:none;border-radius:4px;font-size:12px;font-weight:600;">Contact Shared</button>';
                    } elseif ($status == 'accepted') {
                        return '<button style="background-color:#ff8c42;color:white;padding:7px 14px;border:none;border-radius:4px;font-size:12px;font-weight:600;">Contact Accepted</button>';
                    } elseif ($status == 'rejected') {
                        return '<div style="display:flex;align-items:center;gap:8px;">
                        <span style="background-color:#f8d7da;color:#721c24;padding:7px 14px;border-radius:4px;font-size:12px;font-weight:600;">
                            Contact Rejected
                        </span>
                        <form method="POST" action="' . route('alumni.send.request', $row->id) . '" style="margin:0;">' .
                            csrf_field() . '
                            <button type="submit" style="background:white;color:#c41e3a;padding:7px 14px;border:1px solid #c41e3a;border-radius:4px;font-size:12px;font-weight:600;">↻ Reshare</button>
                        </form>
                    </div>';
                    }

                    return '—';
                })

                ->rawColumns(['alumni', 'batch', 'location', 'action'])
                ->make(true);
        } catch (\Throwable $e) {
            \Log::error('Directory DataTable error: ' . $e->getMessage(), [
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function sendRequest($receiverId)
    {
        $senderId = session('alumni.id');

        if ($senderId == $receiverId) {
            return back()->with('error', 'You cannot connect with yourself.');
        }

        // Check if existing connection exists
        $existing = AlumniConnections::where(function ($q) use ($senderId, $receiverId) {
            $q->where('sender_id', $senderId)->where('receiver_id', $receiverId);
        })
            ->orWhere(function ($q) use ($senderId, $receiverId) {
                $q->where('sender_id', $receiverId)->where('receiver_id', $senderId);
            })
            ->first();

        // If exists and rejected → update to pending (RESHARE)
        if ($existing && $existing->status == 'rejected') {
            $existing->update(['status' => 'pending']);
            return back()->with('success', 'Reshare request sent successfully!');
        }

        // If exists in any other state
        if ($existing) {
            return back()->with('info', 'Connection already exists.');
        }

        // Otherwise create new
        AlumniConnections::create([
            'sender_id' => $senderId,
            'receiver_id' => $receiverId,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Connection request sent successfully!');
    }
}
