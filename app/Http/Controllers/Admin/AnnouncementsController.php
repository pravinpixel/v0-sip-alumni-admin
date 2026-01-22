<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcements;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AnnouncementsController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status'); 
        $perPage = $request->input('pageItems') ?? 10;

        $query = Announcements::query();

        if ($search) {
            $searchLower = strtolower(trim($search));
            $statusMap = ['active' => '1', 'inactive' => '0'];
            $searchStatus = $statusMap[$searchLower] ?? null;

            $query->where(function ($q) use ($search, $searchStatus) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");

                if ($searchStatus !== null) {
                    $q->orWhere('status', $searchStatus);
                }
            });
        }

        if (!empty($status)) {
            $query->whereIn('status', (array) $status);
        }

        $datas = $query->orderBy('id', 'desc')->paginate($perPage);
        $currentPage = $datas->currentPage();
        $serialNumberStart = ($currentPage - 1) * $perPage + 1;
        $total_count = Announcements::count();

        return view('announcements.index', [
            'datas' => $datas,
            'search' => $search,
            'total_count' => $total_count,
            'serialNumberStart' => $serialNumberStart,
        ]);
    }

    public function create(Request $request)
    {
        return view('announcements.add_edit');
    }

    public function edit($id)
    {
        $announcement = Announcements::findOrFail($id);
        return view('announcements.add_edit', compact('announcement'));
    }

    public function save(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'expiry_date' => 'required|date',
            'status' => 'required|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validator->errors()
            ], 422);
        }

        try {
            if ($request->filled('id') && $request->id != '') {
                // Update existing announcement
                $announcement = Announcements::findOrFail($request->id);
                $announcement->update($request->only(['title', 'description', 'expiry_date', 'status']));
                $message = 'Announcement updated successfully.';
            } else {
                // Create new announcement
                Announcements::create($request->only(['title', 'description', 'expiry_date', 'status']));
                $message = 'Announcement created successfully.';
            }

            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'An error occurred while saving the announcement.'
            ], 500);
        }
    }

    public function toggleStatus(Request $request)
    {
        try {
            $announcement = Announcements::findOrFail($request->id);
            $announcement->status = $request->status;
            $announcement->save();

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'An error occurred while updating status.'
            ], 500);
        }
    }

    public function delete($id)
    {
        try {
            $announcement = Announcements::findOrFail($id);
            $announcement->delete();

            return response()->json([
                'success' => true,
                'message' => 'Announcement deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'An error occurred while deleting the announcement.'
            ], 500);
        }
    }
}
