<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ForumPost;
use App\Models\ForumReplies;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;

class ForumsController extends Controller
{
    public function index(Request $request)
    {
        return view('forums.index');
    }

    public function getFilterOptions(Request $request)
    {
        try {
            // Get unique statuses from forum_posts table
            $statuses = ForumPost::select('status')
                ->distinct()
                ->whereNotNull('status')
                ->pluck('status')
                ->toArray();

            return response()->json([
                'success' => true,
                'statuses' => $statuses
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading filter options: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load filter options'
            ], 500);
        }
    }

    public function getData(Request $request)
    {
        try {
            $query = ForumPost::with('alumni')->orderBy('created_at', 'desc');

            // Apply status filter
            if ($request->has('statuses') && !empty($request->statuses)) {
                $query->whereIn('status', $request->statuses);
            }

            // Apply date range filter
            if ($request->has('from_date') && !empty($request->from_date)) {
                $query->whereDate('created_at', '>=', $request->from_date);
            }

            if ($request->has('to_date') && !empty($request->to_date)) {
                $query->whereDate('created_at', '<=', $request->to_date);
            }

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
                    return '<div onclick="viewPost(' . $row->id . ')" style="background-color: #f3f4f6; padding: 6px 12px; border-radius: 6px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: background 0.2s;" 
                        onmouseover="this.style.background=\'#e5e7eb\'" onmouseout="this.style.background=\'#f3f4f6\'"> 
                        <i class="fas fa-eye" style="color: #374151;"></i>
                        <span style="font-weight: 600; color: #374151;">View</span>
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

    public function getPostDetails($id)
    {
        try {
            $post = ForumPost::with(['alumni', 'replies'])->findOrFail($id);
            
            // Parse labels if stored as JSON or comma-separated
            $labels = [];
            if ($post->labels) {
                if (is_string($post->labels)) {
                    // Try to decode as JSON first
                    $decoded = json_decode($post->labels, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $labels = $decoded;
                    } else {
                        // Otherwise split by comma
                        $labels = array_map('trim', explode(',', $post->labels));
                    }
                } elseif (is_array($post->labels)) {
                    $labels = $post->labels;
                }
            }
            
            return response()->json([
                'success' => true,
                'post' => [
                    'id' => $post->id,
                    'title' => $post->title,
                    'description' => $post->description,
                    'labels' => $labels,
                    'comments_count' => $post->replies->count(),
                    'likes_count' => $post->likes_count ?? 0,
                    'views_count' => $post->views_count ?? 0,
                    'awards_count' => $post->awards_count ?? 0,
                    'status' => $post->status,
                    'created_at' => $post->created_at->format('M j, Y'),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading post details: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load post details'
            ], 500);
        }
    }

    public function viewComments($id)
    {
        return view('forums.comments', ['postId' => $id]);
    }

    public function getCommentsData($postId)
    {
        try {
            $post = ForumPost::findOrFail($postId);
            $query = $post->replies()->with('alumni')->orderBy('created_at', 'desc');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('alumni_profile', function ($row) {
                    $alumni = $row->alumni;
                    if (!$alumni) {
                        return '<div style="width:40px;height:40px;border-radius:50%;background:#e5e7eb;"></div>';
                    }

                    $img = $alumni->image ? url('storage/' . $alumni->image) : asset('images/avatar/blank.png');
                    return '<img src="' . $img . '" style="width:40px;height:40px;border-radius:50%;object-fit:cover;">';
                })
                ->addColumn('alumni_name', function ($row) {
                    return '<span style="font-weight:600;color:#111827;">' . ($row->alumni->full_name ?? 'Unknown') . '</span>';
                })
                ->addColumn('comment', function ($row) {
                    $comment = $row->reply ?? '';
                    $truncated = strlen($comment) > 100 ? substr($comment, 0, 100) . '...' : $comment;
                    return '<span style="color:#374151;font-size:14px;">' . htmlspecialchars($truncated) . '</span>';
                })
                ->addColumn('time_commented', function ($row) {
                    return \Carbon\Carbon::parse($row->created_at)
                        ->setTimezone('Asia/Kolkata')
                        ->format('M j, Y, h:i A');
                })
                ->addColumn('threads', function ($row) {
                    $repliesCount = $row->replies_count ?? 0;
                    return '<div style="display:flex;align-items:center;gap:6px;">
                        <i class="fas fa-reply" style="color:#6b7280;"></i>
                        <span style="font-weight:600;color:#374151;">' . $repliesCount . ' Replies</span>
                    </div>';
                })
                ->addColumn('action', function ($row) {
                    return '<div class="dropdown">
                        <button class="btn btn-sm" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="javascript:void(0)" onclick="deleteComment(' . $row->id . ')">
                                <i class="fas fa-trash" style="color:#dc2626;"></i> Delete
                            </a></li>
                        </ul>
                    </div>';
                })
                ->rawColumns(['alumni_profile', 'alumni_name', 'comment', 'threads', 'action'])
                ->make(true);
        } catch (\Exception $e) {
            Log::error('Comments DataTable error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function deleteComment($id)
    {
        try {
            $comment = ForumReplies::findOrFail($id);
            $comment->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Comment deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting comment: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete comment'
            ], 500);
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

}
