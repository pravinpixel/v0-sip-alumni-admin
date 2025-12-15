<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AlumniApprovedPostMail;
use App\Mail\AlumniPostRemoveMail;
use App\Mail\AlumniRejectPostMail;
use App\Models\ForumPost;
use App\Models\ForumReplies;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
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
            $query = ForumPost::with('alumni');

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
                ->filter(function ($query) use ($request) {
                    if ($request->has('search') && !empty($request->search['value'])) {
                        $searchValue = $request->search['value'];
                    $statusMap = [
                        'post deleted' => 'post_deleted',
                        'removed by admin' => 'removed_by_admin',
                    ];
                    $searchLower = strtolower(trim($searchValue));
                    $dbStatus = $statusMap[$searchLower] ?? $searchValue;
                        $parsedDate = date('Y-m-d', strtotime($searchValue));
                        $yearSearch = preg_match('/^\d{4}$/', $searchValue) ? $searchValue : null;
                        $query->where(function ($q) use ($searchValue, $parsedDate, $dbStatus, $yearSearch) {
                            $q->where('status', 'like', "%{$dbStatus}%")
                                ->orWhereHas('alumni', function ($alumniQuery) use ($searchValue) {
                                    $alumniQuery->where('full_name', 'like', "%{$searchValue}%")
                                    ->orWhere('mobile_number', 'like', "%{$searchValue}%");
                                });
                            
                            if ($parsedDate && $parsedDate !== '1970-01-01') {
                                $q->orWhereDate('created_at', $parsedDate)
                                    ->orWhereDate('updated_at', $parsedDate);
                            }
                            if ($yearSearch) {
                                $q->orWhereYear('created_at', $yearSearch);
                            }
                        });
                    }
                })

                ->orderColumn('created_at', function ($query, $order) {
                    $query->orderBy('forum_post.created_at', $order);
                })
                ->orderColumn('alumni', function ($query, $order) {
                    $query->orderBy(
                        DB::raw("(SELECT alumnis.full_name FROM alumnis WHERE alumnis.id = forum_post.alumni_id)"),
                        $order);
                })
                ->orderColumn('contact', function ($query, $order) {
                    $query->orderBy(
                        DB::raw("(SELECT alumnis.mobile_number FROM alumnis WHERE alumnis.id = forum_post.alumni_id)"),
                        $order);
                })
                ->orderColumn('action_taken_on', function ($query, $order) {
                    $query->orderBy('forum_post.updated_at', $order);
                })

                ->addColumn('created_at', function ($row) {
                    return \Carbon\Carbon::parse($row->created_at)
                        ->setTimezone('Asia/Kolkata')
                        ->format('M j, Y');
                })

                ->addColumn('alumni', function ($row) {

                    $alumni = $row->alumni;
                    $img = $alumni->image_url ?? asset('images/avatar/blank.png');

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
                    return '<div onclick="viewPost(' . $row->id . ')" style="background-color: #ffffffff; padding: 6px 12px; border-radius: 6px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: background 0.2s; border: 1px solid #374151; font-weight: 600;" 
                        onmouseover="this.style.background=\'#ba0028\', this.style.color=\'#fff\'" onmouseout="this.style.background=\'#ffffffff\', this.style.color=\'#374151\', this.style.border=\'1px solid #374151\'"> 
                        <i class="fas fa-eye" style="color: #374151;"></i>View
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
                        if (auth()->user()->can('forum.edit')) {
                        $actionMenu = '
                            <li><a class="dropdown-item" href="javascript:void(0)" onclick="statusChange(' . $row->id . ', \'approved\')">
                                <i class="fas fa-check-circle" style="color:green;"></i> Approve
                            </a></li>

                            <li><a class="dropdown-item" href="javascript:void(0)" onclick="statusChange(' . $row->id . ', \'rejected\')">
                                <i class="fas fa-times-circle" style="color:red;"></i> Reject
                            </a></li>
                            ';
                            }
                    }

                    // APPROVED → Remove
                    elseif ($status === 'approved') {
                        if (auth()->user()->can('forum.delete')) {
                        $actionMenu = '
                        <li><a class="dropdown-item" href="javascript:void(0)" onclick="statusChange(' . $row->id . ', \'removed_by_admin\')">
                            <i class="fas fa-trash" style="color:#d9534f;"></i> Remove
                        </a></li>
                        ';
                        }
                    }

                    // REJECTED → Approved + Reject (reject disabled)
                    elseif ($status === 'rejected') {
                        if (auth()->user()->can('forum.edit')) {
                        $actionMenu = '
                        <li><a class="dropdown-item" disabled" href="#">
                            <i class="fas fa-check-circle" style="color:gray;"></i> Approve
                        </a></li>

                        <li><a class="dropdown-item" disabled" href="#">
                            <i class="fas fa-times-circle" style="color:gray;"></i> Reject
                        </a></li>
                        ';
                        }
                    }
                        if (empty(trim($actionMenu))) {
                            return '';
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
                    'awards_count' => $post->pinned()->count() ?? 0,
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

    public function getCommentsData(Request $request, $postId)
    {
        try {
            $post = ForumPost::with(['alumni'])->findOrFail($postId);
            // Only get parent comments (where parent_reply_id is null)
            $query = $post->replies()
                ->with('alumni')
                ->whereNull('parent_reply_id')
                ->withCount(['childReplies' => function($query) {
                    $query->withCount('childReplies');
                }])
                ->orderBy('created_at', 'desc');

            return DataTables::of($query)
                ->addIndexColumn()
                ->filter(function ($query) use ($request) {
                    if ($request->has('search') && !empty($request->search['value'])) {
                        $searchValue = $request->search['value'];
                        $parsedDate = date('Y-m-d', strtotime($searchValue));
                        $query->where(function ($q) use ($searchValue, $parsedDate) {
                            $q->where('message', 'like', '%' . $searchValue . '%');
                            $q->orWhereHas('alumni', function ($alumniQuery) use ($searchValue) {
                                $alumniQuery->where('full_name', 'like', '%' . $searchValue . '%');
                            });
                            if ($parsedDate && $parsedDate !== '1970-01-01') {
                                $q->orWhereDate('created_at', $parsedDate);
                            }
                        });
                    }
                })
                ->addColumn('alumni_profile', function ($row) {
                    $alumni = $row->alumni;
                    if (!$alumni) {
                        return '<div style="width:40px;height:40px;border-radius:50%;background:#e5e7eb;"></div>';
                    }

                    $img = $alumni->image_url ?? asset('images/avatar/blank.png');
                    return '<img src="' . $img . '" style="width:40px;height:40px;border-radius:50%;object-fit:cover;">';
                })
                ->addColumn('alumni_name', function ($row) {
                    return '<span style="font-weight:600;color:#111827;">' . ($row->alumni->full_name ?? 'Unknown') . '</span>';
                })
                ->addColumn('comment', function ($row) {
                    $comment = $row->message ?? '';
                    $truncated = strlen($comment) > 100 ? substr($comment, 0, 100) . '...' : $comment;
                    return '<span style="color:#374151;font-size:14px;">' . htmlspecialchars($truncated) . '</span>';
                })
                ->addColumn('time_commented', function ($row) {
                    return \Carbon\Carbon::parse($row->created_at)
                        ->setTimezone('Asia/Kolkata')
                        ->format('M j, Y, h:i A');
                })
                ->addColumn('threads', function ($row) {
                    // Calculate total replies including nested ones
                    $directRepliesCount = $row->child_replies_count ?? 0;
                    $totalRepliesCount = $directRepliesCount;
                    
                    // Add nested replies count
                    if ($directRepliesCount > 0) {
                        $nestedCount = ForumReplies::whereIn('parent_reply_id', 
                            ForumReplies::where('parent_reply_id', $row->id)->pluck('id')
                        )->count();
                        $totalRepliesCount += $nestedCount;
                    }
                    
                    if ($totalRepliesCount > 0) {
                        return '<button onclick="toggleReplies(' . $row->id . ')" class="btn btn-sm" style="display:flex;align-items:center;gap:6px;background:#f3f4f6;border:1px solid #e5e7eb;padding:6px 12px;border-radius:6px;cursor:pointer;">
                            <i class="fas fa-chevron-right" id="icon-' . $row->id . '" style="color:#6b7280;font-size:12px;"></i>
                            <span style="font-weight:600;color:#374151;">' . $totalRepliesCount . ' ' . ($totalRepliesCount == 1 ? 'Reply' : 'Replies') . '</span>
                        </button>';
                    }
                    return '<span style="color:#9ca3af;font-size:13px;">No replies</span>';
                })
                ->addColumn('action', function ($row) {
                    return '<button onclick="deleteComment(' . $row->id . ')" class="btn btn-sm" style="background:white;border:1px solid #fecaca;color:#dc2626;padding:8px 16px;border-radius:6px;font-weight:600;font-size:13px;display:flex;align-items:center;gap:6px;cursor:pointer;transition:all 0.2s;" onmouseover="this.style.background=\'#fef2f2\'" onmouseout="this.style.background=\'white\'">
                        <i class="fas fa-trash" style="font-size:13px;"></i>
                        Delete
                    </button>';
                })
                ->rawColumns(['alumni_profile', 'alumni_name', 'comment', 'threads', 'action'])
                ->make(true);
        } catch (\Exception $e) {
            Log::error('Comments DataTable error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getCommentReplies($commentId)
    {
        try {
            $replies = ForumReplies::with(['alumni', 'childReplies.alumni'])
                ->where('parent_reply_id', $commentId)
                ->orderBy('created_at', 'asc')
                ->get()
                ->map(function($reply) {
                    $childReplies = $reply->childReplies->map(function($childReply) {
                        return [
                            'id' => $childReply->id,
                            'message' => $childReply->message,
                            'alumni_name' => $childReply->alumni?->full_name ?? '',
                            'alumni_image' => $childReply->alumni?->image_url ?? asset('images/avatar/blank.png'),
                            'created_at' => \Carbon\Carbon::parse($childReply->created_at)
                                ->setTimezone('Asia/Kolkata')
                                ->format('M j, Y, h:i A')
                        ];
                    });

                    return [
                        'id' => $reply->id,
                        'message' => $reply->message,
                        'alumni_name' => $reply->alumni?->full_name ?? '',
                        'alumni_image' => $reply->alumni?->image_url ?? asset('images/avatar/blank.png'),
                        'created_at' => \Carbon\Carbon::parse($reply->created_at)
                            ->setTimezone('Asia/Kolkata')
                            ->format('M j, Y, h:i A'),
                        'child_replies' => $childReplies,
                        'child_replies_count' => $childReplies->count()
                    ];
                });
            
            return response()->json([
                'success' => true,
                'replies' => $replies
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching replies: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch replies'
            ], 500);
        }
    }

    public function deleteComment($id)
    {
        try {
            $ids = $this->getAllReplyIds($id);
            ForumReplies::whereIn('id', $ids)->delete();
            
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

    private function getAllReplyIds($parentId)
    {
        $ids = [$parentId];

        $children = ForumReplies::where('parent_reply_id', $parentId)->pluck('id');

        foreach ($children as $childId) {
            $ids = array_merge($ids, $this->getAllReplyIds($childId));
        }

        return $ids;
    }

    public function changeStatus(Request $request)
    {
        try {
            $id = $request->id;
            $post = ForumPost::findOrFail($id);
            $alumni = $post->alumni;
            if($request->status == 'approved'){
                $post->status = 'approved';
                $post->remarks = null;
                if($alumni->notify_admin_approval === 1){
                    $data = [
                        'name' => $alumni->full_name,
                        'title' => $post->title,
                        'support_email' => env('SUPPORT_EMAIL'),
                    ];
                    Mail::to($alumni->email)->queue(new AlumniApprovedPostMail($data));
                }
            } elseif($request->status == 'rejected'){
                $post->status = 'rejected';
                $post->remarks = $request->remarks;
                if($alumni->notify_admin_approval === 1){
                    $data = [
                        'name' => $alumni->full_name,
                        'title' => $post->title,
                        'remarks' => $request->remarks,
                        'support_email' => env('SUPPORT_EMAIL'),
                    ];
                    Mail::to($alumni->email)->queue(new AlumniRejectPostMail($data));
                }
            } elseif($request->status == 'removed_by_admin'){
                $post->status = 'removed_by_admin';
                $post->remarks = $request->remarks;
                if ($alumni->notify_admin_approval === 1) {
                    $data = [
                        'name' => $alumni->full_name,
                        'title' => $post->title,
                        'remarks' => $request->remarks,
                        'support_email' => env('SUPPORT_EMAIL'),
                    ];
                    Mail::to($alumni->email)->queue(new AlumniPostRemoveMail($data));
                }
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
