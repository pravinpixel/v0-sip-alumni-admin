<?php

namespace App\Http\Controllers\Alumni;

use App\Http\Controllers\Controller;
use App\Models\AlumniConnections;
use App\Models\Alumnis;
use App\Models\Cities;
use App\Models\City;
use App\Models\State;
use App\Models\MobileOtp;
use App\Models\Occupation;
use App\Models\States;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;


class CommonController extends Controller
{

    public function showEditProfile($id)
    {
        $alumni = Alumnis::with('city.state')->findOrFail($id);
        $states = States::orderBy('name')->get(); // Fetch all states

        return view('alumni.modals.edit-profile-modal', compact('alumni', 'states'));
    }

    public function updateProfile(Request $request)
    {
        try {
            $alumniId = session('alumni.id');
            $alumni = Alumnis::findOrFail($alumniId);
            $validator      = Validator::make($request->all(), [
                'full_name' => 'required|string|max:255',
                'year_of_completion' => 'required|digits:4',
                'email' => 'required|email|unique:alumnis,email,' . $alumniId,
                'mobile_number' => 'required|digits:10|unique:alumnis,mobile_number,' . $alumniId,
                'city_id' => 'required|exists:cities,id',
                'state_id' => 'required|exists:states,id',
                'occupation_id' => 'required|string|max:255',
                'image' => 'nullable|image:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation Error',
                    'errors' => $validator->errors()
                ], 422);
            }



            // Update basic fields
            $alumni->full_name = $request->full_name;
            $alumni->year_of_completion = $request->year_of_completion;
            $alumni->email = $request->email;
            $alumni->mobile_number = $request->mobile_number;
            $alumni->city_id = $request->city_id;
            $alumni->state_id = $request->state_id;
            $alumni->occupation_id = $request->occupation_id;

            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($alumni->image && Storage::disk('public')->exists($alumni->image)) {
                    Storage::disk('public')->delete($alumni->image);
                }
                $imagePath = $request->file('image')->store('alumni_profiles', 'public');
                $alumni->image = $imagePath;
            }

            $alumni->save();

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully!',
                'alumni' => $alumni
            ]);
        } catch (\Exception $e) {
            Log::error('Profile Update Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getAlumni($id)
    {
        try {
            $alumni = Alumnis::with(['city.state', 'occupation'])->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'alumni' => $alumni
            ]);
        } catch (\Exception $e) {
            Log::error('Get Alumni Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Alumni not found'
            ], 404);
        }
    }

    public function getStates()
    {
        try {
            $states = States::select('id', 'name')->orderBy('name')->get();
            $occupations = Occupation::select('id', 'name')->orderBy('name')->get();
            return response()->json([
                'success' => true,
                'states' => $states,
                'occupations' => $occupations,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching cities and states: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch cities and states: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getCitiesByState($stateId)
    {
        try {
            $cities = Cities::where('state_id', $stateId)
                ->select('id', 'name')
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'cities' => $cities,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching cities for state ID ' . $stateId . ': ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch cities: ' . $e->getMessage()
            ], 500);
        }
    }
}
