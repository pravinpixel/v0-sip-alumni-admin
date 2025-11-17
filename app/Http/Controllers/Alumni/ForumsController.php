<?php

namespace App\Http\Controllers\Alumni;

use App\Http\Controllers\Controller;
use App\Models\ForumReplies;
use App\Models\ForumPost;
use App\Models\MobileOtp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ForumsController extends Controller
{
    public function index(Request $request)
    {
        $forumPosts = ForumPost::with('alumni')->orderBy('created_at', 'desc')->get();
        return view('alumni.forums.index', compact('forumPosts'));
    }

    public function getData(Request $request)
    {
        try {
            $forumPosts = ForumPost::with('alumni')->orderBy('created_at', 'desc')->get();

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

            $validator      = Validator::make($request->all(), [
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

    public function createReply(Request $request)
    {
        try {
            $alumniId = session('alumni.id');

            $validator      = Validator::make($request->all(), [
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
                'liked' => 'required|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation Error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $post = ForumPost::findOrFail($request->post_id);

            if ($request->liked) {
                $post->likes += 1;
            } else {
                $post->likes -= 1;
                if ($post->likes < 0) $post->likes = 0;
            }

            $post->save();
            $likes = $post->likes;
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
}
