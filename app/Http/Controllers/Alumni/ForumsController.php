<?php

namespace App\Http\Controllers\Alumni;

use App\Http\Controllers\Controller;
use App\Mail\AdminApprovalMail;
use App\Mail\AdminPostDeleteMail;
use App\Mail\AlumniCommentMail;
use App\Mail\AlumniCreatePostMail;
use App\Mail\AlumniPostDeleteMail;
use App\Models\AlumniConnections;
use App\Models\Alumnis;
use App\Models\ForumReplies;
use App\Models\ForumPost;
use App\Models\PostLikes;
use App\Models\PostPinned;
use App\Models\PostViews;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ForumsController extends Controller
{
    public function index(Request $request)
    {
        $alumniId = session('alumni.id');
        $currentUser = Alumnis::find($alumniId);
        $forumPosts = ForumPost::with('alumni')->orderBy('created_at', 'desc')->get();
        return view('alumni.forums.index', compact('forumPosts', 'currentUser'));
    }

    public function activity(Request $request)
    {
        return view('alumni.forums.activity');
    }

    public function getFilterOptions()
    {
        try {
            // Get unique batch years from alumni who have posted
            $batchYears = ForumPost::with('alumni')
                ->whereHas('alumni', function ($query) {
                    $query->whereNotNull('year_of_completion');
                })
                ->where('status', 'approved')
                ->get()
                ->pluck('alumni.year_of_completion')
                ->unique()
                ->filter()
                ->sort()
                ->values()
                ->toArray();

            return response()->json([
                'success' => true,
                'batchYears' => $batchYears,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching filter options: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching filter options.'
            ], 500);
        }
    }

    public function getData(Request $request)
    {
        try {
            $query = ForumPost::with('alumni')
                ->where('status', 'approved');

            if ($request->has('search') && !empty($request->search)) {
                $searchTerm = $request->search;
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('title', 'like', "%{$searchTerm}%")
                      ->orWhere('description', 'like', "%{$searchTerm}%")
                      ->orWhere('labels', 'like', "%{$searchTerm}%")
                      ->orWhereHas('alumni', function ($alumniQuery) use ($searchTerm) {
                          $alumniQuery->where('full_name', 'like', "%{$searchTerm}%");
                      });
                });
            }

            if ($request->has('date_range') && !empty($request->date_range)) {
                $dateRange = $request->date_range[0];
                switch ($dateRange) {
                    case 'today':
                        $query->whereDate('created_at', today());
                        break;
                    case 'week':
                        $query->where('created_at', '>=', now()->subDays(7));
                        break;
                    case 'month':
                        $query->where('created_at', '>=', now()->subDays(30));
                        break;
                }
            }

            if ($request->has('sort_by') && !empty($request->sort_by)) {
                switch ($request->sort_by) {
                    case "most_recent":
                        $query->orderBy('created_at', 'desc');
                        break;
                    case "most_liked":
                        $query->orderBy('likes_count', 'desc');
                        break;
                    case "most_viewed":
                        $query->orderBy('views_count', 'desc');
                        break;
                    case "most_commented":
                        $query->orderBy('reply_count', 'desc');
                        break;
                }
            }

            if ($request->has('batch_year') && !empty($request->batch_year)) {
                $query->whereHas('alumni', function ($alumniQuery) use ($request) {
                    $alumniQuery->whereIn('year_of_completion', $request->batch_year);
                });
            }

            if ($request->has('post_type') && !empty($request->post_type)) {
                $postTypes = $request->post_type;
                if (in_array('pinned', $postTypes) && in_array('regular', $postTypes)) {
                } elseif (in_array('pinned', $postTypes)) {
                    $query->whereHas('pinned');
                } elseif (in_array('regular', $postTypes)) {
                    $query->whereDoesntHave('pinned');
                }
            }

            $alumniId = session('alumni.id');
            $forumPosts = $query->get();
            $forumPosts->each(function ($post) use ($alumniId) {
                $post->is_pinned_by_user = PostPinned::where('post_id', $post->id)
                    ->where('alumni_id', $alumniId)
                    ->exists();
                
                $post->is_liked_by_user = PostLikes::where('post_id', $post->id)
                    ->where('alumni_id', $alumniId)
                    ->exists();
                
                // Check if current user has connection with post author OR if it's their own post
                $post->has_connection = ($post->alumni_id == $alumniId) || AlumniConnections::where(function($q) use ($alumniId, $post) {
                    $q->where('sender_id', $alumniId)
                      ->where('receiver_id', $post->alumni_id)
                      ->where('status', 'accepted');
                })->orWhere(function($q) use ($alumniId, $post) {
                    $q->where('sender_id', $post->alumni_id)
                      ->where('receiver_id', $alumniId)
                      ->where('status', 'accepted');
                })->exists();
            });

            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = 'desc';
            
            $forumPosts = $forumPosts->sortBy([
                ['is_pinned_by_user', 'desc'], // Pinned posts first
                [$sortBy, $sortOrder]          // Then by selected sort
            ])->values();

            return response()->json([
                'success' => true,
                'data' => [
                    'posts' => $forumPosts,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching forum posts: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching forum posts.'
            ], 500);
        }
    }

    public function createPost(Request $request)
    {
        try {
            $alumniId = session('alumni.id');
            $alumni = Alumnis::find($alumniId);

            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'labels' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation Error',
                    'errors' => $validator->errors()
                ], 422);
            }
            // $lablelsArray = $request->labels ? explode(',', $request->labels) : [];

            ForumPost::create([
                'alumni_id' => $alumniId,
                'title' => $request->title,
                'description' => $request->description,
                'labels' => $request->labels,
                'status' => 'pending',
            ]);
            // Send email alumni
            if ($alumni->notify_post_comments === 1) {
                $data = [
                    'name' => $alumni->full_name,
                    'title' => $request->title,
                    'support_email' => env('SUPPORT_EMAIL'),
                ];
                Mail::to($alumni->email)->queue(new AlumniCreatePostMail($data));
            }
            // $role = Role::where('name', 'Super Admin')->first();
            $admins = User::whereNull('deleted_at')->get();
            $adminData = [
                'name' => $alumni->full_name,
                'support_email' => env('SUPPORT_EMAIL'),
            ];
            foreach ($admins as $admin) {
                Mail::to($admin->email)->queue(new AdminApprovalMail($adminData));
            }

            return response()->json([
                'success' => true,
                'message' => 'Forum post created successfully.'
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating forum post: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the forum post.'
            ], 500);
        }
    }

    public function updatePost(Request $request)
    {
        try {
            $alumniId = session('alumni.id');

            $validator = Validator::make($request->all(), [
                'post_id' => 'required',
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'labels' => 'required|string|max:255',
                'status' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation Error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $post = ForumPost::where('id', $request->post_id)
                ->where('alumni_id', $alumniId)
                ->first();

            if (!$post) {
                return response()->json([
                    'success' => false,
                    'message' => 'Post not found or you do not have permission to update this post.'
                ], 404);
            }

            $post->title = $request->title;
            $post->description = $request->description;
            $post->labels = $request->labels;

            if ($request->has('status')) {
                $post->status = $request->status;
                $alumni = $post->alumni;
                // Send email alumni
                if ($alumni->notify_post_comments === 1) {
                    $data = [
                        'name' => $alumni->full_name,
                        'title' => $request->title,
                        'support_email' => env('SUPPORT_EMAIL'),
                    ];
                    Mail::to($alumni->email)->queue(new AlumniCreatePostMail($data));
                }
                // $role = Role::where('name', 'Super Admin')->first();
                $admins = User::whereNull('deleted_at')->get();
                $adminData = [
                    'name' => $alumni->full_name,
                    'support_email' => env('SUPPORT_EMAIL'),
                ];
                foreach ($admins as $admin) {
                    Mail::to($admin->email)->queue(new AdminApprovalMail($adminData));
                }
            }
            
            $post->save();

            return response()->json([
                'success' => true,
                'message' => 'Post updated successfully.'
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating forum post: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the forum post.'
            ], 500);
        }
    }

    public function createReply(Request $request)
    {
        try {
            $alumniId = session('alumni.id');

            $validator = Validator::make($request->all(), [
                'forum_post_id' => 'required',
                'message' => 'required|string|max:255',
                'parent_reply_id' => 'nullable',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation Error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $post = ForumPost::findOrFail($request->forum_post_id);
            $alumni = $post->alumni;

            $form = ForumReplies::create([
                'forum_post_id' => $request->forum_post_id,
                'alumni_id' => $alumniId,
                'parent_reply_id' => $request->parent_reply_id ?? null,
                'message' => $request->message,
                'status' => 'pending',
            ]);
            if ($alumni->notify_post_comments === 1) {
                $data = [
                    'name' => $alumni->full_name,
                    'title' => $post->title,
                    'support_email' => env('SUPPORT_EMAIL'),
                ];
                Mail::to($alumni->email)->queue(new AlumniCommentMail($data));
            }

            return response()->json([
                'success' => true,
                'message' => 'Reply added successfully.'
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating forum reply: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while adding the reply.'
            ], 500);
        }
    }

    public function viewThread($id)
    {
        try {
            $alumniId =  session('alumni.id');
            $forumPost = ForumPost::with(['alumni'])->findOrFail($id);
            
            $forumPost->has_connection = ($forumPost->alumni_id == $alumniId) || AlumniConnections::where(function($q) use ($alumniId, $forumPost) {
                $q->where('sender_id', $alumniId)
                  ->where('receiver_id', $forumPost->alumni_id)
                  ->where('status', 'accepted');
            })->orWhere(function($q) use ($alumniId, $forumPost) {
                $q->where('sender_id', $forumPost->alumni_id)
                  ->where('receiver_id', $alumniId)
                  ->where('status', 'accepted');
            })->exists();
            
            // $replies = ForumReplies::with(['alumni', 'childReplies.alumni'])->where('forum_post_id', $id)->get();
            $replies = ForumReplies::with([
                'alumni',
                'childReplies.alumni',
                'childReplies.childReplies.alumni',
            ])
                ->where('forum_post_id', $id)
                ->whereNull('parent_reply_id')
                ->orderBy('created_at', 'ASC')
                ->get();
            
            // Add connection status for each reply
            $replies->each(function ($reply) use ($alumniId) {
                $this->addConnectionStatus($reply, $alumniId);
            });
            
            $view = PostViews::where('post_id', $id)
                ->where('alumni_id', $alumniId)
                ->first();

            if (!$view) {
                PostViews::create([
                    'post_id' => $id,
                    'alumni_id' => $alumniId
                ]);
            } else {
                $view->update([
                    'post_id' => $id,
                    'alumni_id' => $alumniId,
                    'updated_at' => now()
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'post' => $forumPost,
                    'replies' => $replies
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching forum thread: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching the forum thread.'
            ], 500);
        }
    }
    
    private function addConnectionStatus($reply, $alumniId)
    {
        if ($reply->alumni) {
            $reply->has_connection = ($reply->alumni_id == $alumniId) || AlumniConnections::where(function($q) use ($alumniId, $reply) {
                $q->where('sender_id', $alumniId)
                  ->where('receiver_id', $reply->alumni_id)
                  ->where('status', 'accepted');
            })->orWhere(function($q) use ($alumniId, $reply) {
                $q->where('sender_id', $reply->alumni_id)
                  ->where('receiver_id', $alumniId)
                  ->where('status', 'accepted');
            })->exists();
        }
        
        if ($reply->childReplies) {
            $reply->childReplies->each(function ($childReply) use ($alumniId) {
                $this->addConnectionStatus($childReply, $alumniId);
            });
        }
    }

    public function toggleLike(Request $request)
    {
        try {
            $alumniId = session('alumni.id');

            $validator = Validator::make($request->all(), [
                'post_id' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation Error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $post = ForumPost::findOrFail($request->post_id);

            $like = PostLikes::where('post_id', $post->id)
                ->where('alumni_id', $alumniId)
                ->first();

            if ($like) {
                $like->delete();
                $likes = $post->likes()->count() - 1;
            } else {
                PostLikes::create([
                    'post_id' => $post->id,
                    'alumni_id' => $alumniId,
                ]);
                $likes = $post->likes()->count() + 1;
            }
            return response()->json([
                'success' => true,
                'message' => 'Like status updated successfully.',
                'likes_count' => $likes
            ]);
        } catch (\Exception $e) {
            Log::error('Error toggling like: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating like status.'
            ], 500);
        }
    }
    public function pinnedPost(Request $request)
    {
        try {
            $alumniId = session('alumni.id');

            $validator = Validator::make($request->all(), [
                'post_id' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation Error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $post = ForumPost::findOrFail($request->post_id);

            $pinned = PostPinned::where('post_id', $post->id)
                ->where('alumni_id', $alumniId)
                ->first();

            if ($pinned) {
                $pinned->delete();
            } else {
                PostPinned::create([
                    'post_id' => $post->id,
                    'alumni_id' => $alumniId,
                ]);
            }
            return response()->json([
                'success' => true,
                'message' => 'Pinned status updated successfully.',
            ]);
        } catch (\Exception $e) {
            Log::error('Error pinning post: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating pinned status.'
            ], 500);
        }
    }

    public function getActivityData(Request $request)
    {
        try {
            $alumniId = session('alumni.id');
            
            // Get all posts by the current user
            $userPosts = ForumPost::with('alumni')
                ->where('alumni_id', $alumniId)
                ->orderBy('updated_at', 'desc')
                ->get();

            // Add additional data for each post
            $userPosts->each(function ($post) use ($alumniId) {
                $post->likes_count = PostLikes::where('post_id', $post->id)->count();
                $post->reply_count = ForumReplies::where('forum_post_id', $post->id)->count();
                $post->views_count = PostViews::where('post_id', $post->id)->count();
            });

            // Calculate statistics
            $stats = [
                'totalPosts' => $userPosts->count(),
                'activePosts' => $userPosts->where('status', 'approved')->count(),
                'pendingPosts' => $userPosts->where('status', 'pending')->count(),
                'rejectedPosts' => $userPosts->where('status', 'rejected')->count(),
                'archivedPosts' => $userPosts->whereIn('status', ['removed_by_admin', 'post_deleted'])->count(),
                'totalLikes' => $userPosts->where('status', 'approved')->sum('likes_count'),
                'totalViews' => $userPosts->where('status', 'approved')->sum('views_count'),
                'totalComments' => $userPosts->where('status', 'approved')->sum('reply_count'),
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'posts' => $userPosts,
                    'stats' => $stats
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching activity data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching activity data.'
            ], 500);
        }
    }

    public function updateStatus(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'post_id' => 'required',
                'status' => 'required|in:approved,pending,rejected,post_deleted,removed_by_admin',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation Error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $alumniId = session('alumni.id');
            $post = ForumPost::where('id', $request->post_id)
                ->where('alumni_id', $alumniId)
                ->first();

            if (!$post) {
                return response()->json([
                    'success' => false,
                    'message' => 'Post not found or you do not have permission to update this post.'
                ], 404);
            }

            $alumni = Alumnis::where('id', $alumniId)->first();
            $status = $request->status;
            if ($status == 'post_deleted') {
                $post->status = 'post_deleted';
                if ($alumni->notify_admin_approval === 1) {
                    $data = [
                        'name' => $alumni->full_name,
                        'title' => $post->title,
                        'support_email' => env('SUPPORT_EMAIL'),
                    ];
                    Mail::to($alumni->email)->queue(new AlumniPostDeleteMail($data));
                }

                $admins = User::whereNull('deleted_at')->get();
                $adminData = [
                    'alumni_name' => $alumni->full_name,
                    'title' => $post->title,
                    'support_email' => env('SUPPORT_EMAIL'),
                ];
                foreach ($admins as $admin) {
                    Mail::to($admin->email)->queue(new AdminPostDeleteMail($adminData));
                }
            } else {
                $post->status = $status;
            }
            $post->save();
            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully!'
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating status.'
            ], 500);
        }
    }
}
