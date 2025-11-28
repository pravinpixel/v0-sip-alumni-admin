<?php

namespace App\Http\Controllers\Alumni;

use App\Http\Controllers\Controller;
use App\Mail\AdminApprovalMail;
use App\Mail\AlumniCreatePostMail;
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
        $forumPosts = ForumPost::with('alumni')->orderBy('created_at', 'desc')->get();
        return view('alumni.forums.index', compact('forumPosts'));
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
                ->get()
                ->pluck('alumni.year_of_completion')
                ->unique()
                ->filter()
                ->sort()
                ->values()
                ->toArray();

            // Get unique post types/labels
            $postTypes = ForumPost::whereNotNull('labels')
                ->where('labels', '!=', '')
                ->pluck('labels')
                ->flatMap(function ($labels) {
                    return explode(',', $labels);
                })
                ->map(function ($label) {
                    return trim($label);
                })
                ->unique()
                ->filter()
                ->values()
                ->toArray();

            return response()->json([
                'success' => true,
                'batchYears' => $batchYears,
                'postTypes' => $postTypes
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
                $dateRange = $request->date_range;
                switch ($dateRange) {
                    case 'today':
                        $query->whereDate('created_at', today());
                        break;
                    case 'week':
                        $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                        break;
                    case 'month':
                        $query->whereMonth('created_at', now()->month)
                              ->whereYear('created_at', now()->year);
                        break;
                    case 'year':
                        $query->whereYear('created_at', now()->year);
                        break;
                }
            }

            if ($request->has('batch_year') && !empty($request->batch_year)) {
                $query->whereHas('alumni', function ($alumniQuery) use ($request) {
                    $alumniQuery->where('year_of_completion', $request->batch_year);
                });
            }

            if ($request->has('post_type') && !empty($request->post_type)) {
                $query->where('labels', 'like', "%{$request->post_type}%");
            }

            $alumniId = session('alumni.id');
            
            // Get all posts first
            $forumPosts = $query->get();

            // Add pinned and liked status
            $forumPosts->each(function ($post) use ($alumniId) {
                $post->is_pinned_by_user = PostPinned::where('post_id', $post->id)
                    ->where('alumni_id', $alumniId)
                    ->exists();
                
                $post->is_liked_by_user = PostLikes::where('post_id', $post->id)
                    ->where('alumni_id', $alumniId)
                    ->exists();
            });

            // Sort: Pinned posts first, then by selected sort option
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = 'desc';
            
            $forumPosts = $forumPosts->sortBy([
                ['is_pinned_by_user', 'desc'], // Pinned posts first
                [$sortBy, $sortOrder]          // Then by selected sort
            ])->values();

            return response()->json([
                'success' => true,
                'data' => [
                    'posts' => $forumPosts
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
                'labels' => 'nullable|string|max:255',
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
            $data = [
                'name' => $alumni->full_name,
                'title' => $request->title,
                'support_email' => env('SUPPORT_EMAIL'),
            ];
            Mail::to($alumni->email)->queue(new AlumniCreatePostMail($data));

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
                'labels' => 'nullable|string|max:255',
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

            $form = ForumReplies::create([
                'forum_post_id' => $request->forum_post_id,
                'alumni_id' => $alumniId,
                'parent_reply_id' => $request->parent_reply_id ?? null,
                'message' => $request->message,
                'status' => 'pending',
            ]);

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
                ->orderBy('created_at', 'desc')
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
                'archivedPosts' => $userPosts->whereIn('status', ['post_deleted', 'removed_by_admin'])->count(),
                'totalLikes' => $userPosts->sum('likes_count'),
                'totalViews' => $userPosts->sum('views_count'),
                'totalComments' => $userPosts->sum('reply_count'),
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

            $status = $request->status;
            if ($status == 'post_deleted') {
                $post->status = 'post_deleted';
            } else if ($status == 're_post') { 
                $post->status = 'pending';
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
