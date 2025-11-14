<?php

namespace App\Http\Controllers\Alumni;

use App\Http\Controllers\Controller;
use App\Models\Alumnis;
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
        return view('alumni.forums.index');
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

}
