<?php

namespace App\Http\Controllers\Alumni;

use App\Http\Controllers\Controller;
use App\Models\Alumnis;
use App\Models\AlumniConnections;
use App\Models\ForumPost;
use App\Models\PostLikes;
use App\Models\ForumReplies;
use App\Models\MobileOtp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $alumniId = session('alumni.id');
        $alumni = Alumnis::findOrFail($alumniId);

        // Get dashboard statistics
        $stats = $this->getDashboardStats($alumniId);
        
        // Get top 3 most liked posts
        $topPosts = $this->getTopPosts($alumniId);

        return view('alumni.dashboard.index', compact('alumni', 'stats', 'topPosts'));
    }

    private function getDashboardStats($alumniId)
    {
        // Connections Made (accepted connections)
        $connectionsMade = AlumniConnections::where(function($query) use ($alumniId) {
            $query->where('sender_id', $alumniId)
                  ->orWhere('receiver_id', $alumniId);
        })
        ->where('status', 'accepted')
        ->count();

        // Pending Requests (sent by user, still pending)
        $pendingRequests = AlumniConnections::where('sender_id', $alumniId)
            ->where('status', 'pending')
            ->count();

        // Posts Created (active/approved posts)
        $postsCreated = ForumPost::where('alumni_id', $alumniId)
            ->where('status', 'approved')
            ->count();

        // Total Engagement (likes on user's active posts)
        $totalEngagement = 0;

        return [
            'connectionsMade' => $connectionsMade,
            'pendingRequests' => $pendingRequests,
            'postsCreated' => $postsCreated,
            'totalEngagement' => $totalEngagement
        ];
    }

    private function getTopPosts($alumniId)
    {
        // Get top 3 most liked posts with author info
        $topPosts = ForumPost::with('alumni')
            ->where('status', 'approved')
            ->withCount('likes')
            ->orderBy('likes_count', 'desc')
            ->limit(3)
            ->get()
            ->map(function($post) use ($alumniId) {
                // Check if user is connected with post author
                $isConnected = false;
                if ($post->alumni_id != $alumniId) {
                    $isConnected = AlumniConnections::where(function($query) use ($alumniId, $post) {
                        $query->where(function($q) use ($alumniId, $post) {
                            $q->where('sender_id', $alumniId)
                              ->where('receiver_id', $post->alumni_id);
                        })->orWhere(function($q) use ($alumniId, $post) {
                            $q->where('sender_id', $post->alumni_id)
                              ->where('receiver_id', $alumniId);
                        });
                    })
                    ->where('status', 'accepted')
                    ->exists();
                }

                // Get reply count
                $replyCount = ForumReplies::where('forum_post_id', $post->id)->count();

                // Determine if we should show profile details
                $showProfile = $post->alumni_id == $alumniId || $isConnected;
                
                // Get author name and initials
                $authorName = $post->alumni->full_name ?? 'Unknown';
                $authorInitials = $this->getInitials($authorName);
                
                // Get profile image - only if connected or own post
                $profileImage = null;
                if ($showProfile && $post->alumni) {
                    $profileImage = $post->alumni->image_url;
                }

                return [
                    'id' => $post->id,
                    'title' => $post->title,
                    'description' => strip_tags($post->description),
                    'author' => $authorName,
                    'author_initials' => $authorInitials,
                    'profile_image' => $profileImage,
                    'show_profile' => $showProfile,
                    'views' => $post->views_count ?? 0,
                    'likes' => $post->likes_count,
                    'comments' => $replyCount
                ];
            });

        return $topPosts;
    }

    private function getInitials($name)
    {
        $words = explode(' ', $name);
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }
        return strtoupper(substr($name, 0, 2));
    }
}
