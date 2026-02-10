<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alumnis;
use App\Models\AlumniConnections;
use App\Models\ForumPost;
use App\Models\PostLikes;
use App\Models\PostViews;
use App\Models\ForumReplies;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard');
    }

    public function getData(Request $request)
    {
        try {
            $fromDate = $request->from_date;
            $toDate = $request->to_date;

            // Get dashboard statistics
            $stats = $this->getDashboardStats($fromDate, $toDate);
            
            // Get top alumni by connections
            $topAlumni = $this->getTopAlumniByConnections();
            
            // Get forum statistics
            $forumStats = $this->getForumStats();
            
            // Get highest engagement posts
            $topPosts = $this->getHighestEngagementPosts();

            return response()->json([
                'success' => true,
                'data' => [
                    'stats' => $stats,
                    'topAlumni' => $topAlumni,
                    'forumStats' => $forumStats,
                    'topPosts' => $topPosts
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching dashboard data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching dashboard data.'
            ], 500);
        }
    }

    private function getDashboardStats($fromDate = null, $toDate = null)
    {
        $query = Alumnis::query();

        if ($fromDate) {
            $query->whereDate('created_at', '>=', $fromDate);
        }
        if ($toDate) {
            $query->whereDate('created_at', '<=', $toDate);
        }

        $totalAlumni = $query->count();
        $activeAlumni = (clone $query)->where('status', 'active')->count();
        $blockedAlumni = (clone $query)->where('status', 'blocked')->count();

        return [
            'totalAlumni' => $totalAlumni,
            'activeAlumni' => $activeAlumni,
            'blockedAlumni' => $blockedAlumni
        ];
    }

    private function getTopAlumniByConnections()
    {
        // Get all alumni and calculate their connections
        $topAlumni = Alumnis::all()->map(function($alumni) {
            // Count connections where this alumni is sender
            $sentConnections = AlumniConnections::where('sender_id', $alumni->id)
                ->where('status', 'accepted')
                ->count();
            
            // Count connections where this alumni is receiver
            $receivedConnections = AlumniConnections::where('receiver_id', $alumni->id)
                ->where('status', 'accepted')
                ->count();
            
            // Total connections
            $totalConnections = $sentConnections + $receivedConnections;
            
            return [
                'name' => $alumni->full_name,
                'year' => $alumni->year_of_completion,
                'connections' => $totalConnections,
                'image' => $alumni->image_url ?? asset('images/avatar/blank.png')
            ];
        })
        ->sortByDesc('connections')
        ->take(8)
        ->values();

        return $topAlumni;
    }

    private function getForumStats()
    {
        $activePosts = ForumPost::where('status', 'approved')->count();
        $pendingPosts = ForumPost::where('status', 'pending')->count();
        $rejectedPosts = ForumPost::where('status', 'rejected')->count();

        return [
            'activePosts' => $activePosts,
            'pendingPosts' => $pendingPosts,
            'rejectedPosts' => $rejectedPosts
        ];
    }

    private function getHighestEngagementPosts()
    {
        $topPosts = ForumPost::with('alumni')
            ->where('status', 'approved')
            ->withCount(['likes', 'views', 'replies'])
            ->get()
            ->sortByDesc('likes_count')
            ->take(3)
            ->map(function($post) {
                // Check if post is pinned
                $isPinned = $post->pinned()->exists();
                
                return [
                    'alumni' => $post->alumni->full_name ?? 'Unknown',
                    'title' => $post->title,
                    'description' => $post->description,
                    'date' => $post->created_at->format('M d, Y'),
                    'image' => $post->alumni->image_url ?? asset('images/avatar/blank.png'),
                    'likes' => $post->likes_count,
                    'comments' => $post->replies_count,
                    'views' => $post->views_count,
                    'pinned' => $isPinned
                ];
            })
            ->values();

        return $topPosts;
    }
}
