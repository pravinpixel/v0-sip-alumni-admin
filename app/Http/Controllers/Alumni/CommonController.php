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
                'image' => 'nullable|image:jpeg,png,jpg,gif,webp|max:2048',
            ], [
                'full_name.required' => 'Full name is required.',
                'year_of_completion.required' => 'Year of completion is required.',
                'year_of_completion.digits' => 'Year of completion must be 4 digits.',
                'email.required' => 'Email is required.',
                'email.email' => 'Invalid email format.',
                'email.unique' => 'Email already exists.',
                'mobile_number.required' => 'Mobile number is required.',
                'mobile_number.digits' => 'Mobile number must be 10 digits.',
                'mobile_number.unique' => 'Mobile number already exists.',
                'city_id.required' => 'City is required.',
                'state_id.required' => 'State is required.',
                'occupation_id.required' => 'Occupation is required.',
                'image.image' => 'Invalid image format.',
                'image.max' => 'Image size should not exceed 2MB.',
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

            if ($request->remove_image == 1) {
                if ($alumni->image && Storage::disk('public')->exists($alumni->image)) {
                    Storage::disk('public')->delete($alumni->image);
                }
                $alumni->image = null;
            }

            if ($request->hasFile('image')) {
                if (!empty($alumni->image)) {
                    $parsed = parse_url($alumni->image);
                    $path = ltrim($parsed['path'], '/'); 
                    $relativePath = preg_replace('/^public\/storage\//', '', $path);

                    if (Storage::disk('public')->exists($relativePath)) {
                        Storage::disk('public')->delete($relativePath);
                    }
                }
                $imagePath = $request->file('image')->store('alumni_profiles', 'public');
                // $fullUrl = url('public/storage/' . $imagePath);
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

    public function editVerifyOtp(Request $request)
    {
        try {
            Log::info('OTP Verification Request:', $request->all());
            $alumniId = session('alumni.id');

            $request->validate([
                'otp' => 'required|digits:6',
                'mobile' => 'required|digits:10'
            ]);

            $mobile = $request->mobile;
            $otp = $request->otp;

            // Verify OTP
            $otpRecord = MobileOtp::where('mobile_number', $mobile)
                ->where('otp', $otp)
                ->where('expires_at', '>', now())
                ->first();

            if (!$otpRecord) {
                return response()->json([
                    'success' => false,
                    'error' => 'Invalid OTP or OTP has expired'
                ], 400);
            }

            // OTP verified - Store alumni in session instead of Auth
            $otpRecord->is_verified = 1;
            $otpRecord->save();
            $otpRecord->delete();

            $alumni = Alumnis::find($alumniId);
            $alumni->mobile_number = $mobile;
            $alumni->save();

            return response()->json([
                'success' => true,
                'message' => 'OTP verified successfully',
            ]);
            
        } catch (\Exception $e) {
            Log::error('OTP Verification Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Server error'
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

    public function updateSettings(Request $request)
    {
        try {
            $alumniId = session('alumni.id');
            $alumni = Alumnis::findOrFail($alumniId);

            $validator = Validator::make($request->all(), [
                'notify_admin_approval' => 'nullable|boolean',
                'notify_post_comments' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation Error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $alumni->notify_admin_approval = $request->notify_admin_approval;
            $alumni->notify_post_comments = $request->notify_post_comments;
            $alumni->save();

            return response()->json([
                'success' => true,
                'message' => 'Settings updated successfully!',
                'alumni' => $alumni
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update settings: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateRibbon(Request $request)
    {
        try {
            $alumniId = session('alumni.id');
            $alumni = Alumnis::findOrFail($alumniId);

            $validator = Validator::make($request->all(), [
                'is_request_ribbon' => 'sometimes|boolean',
                'is_directory_ribbon' => 'sometimes|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation Error',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Update request ribbon if provided
            if ($request->has('is_request_ribbon')) {
                $alumni->is_request_ribbon = $request->is_request_ribbon;
            }

            // Update directory ribbon if provided
            if ($request->has('is_directory_ribbon')) {
                $alumni->is_directory_ribbon = $request->is_directory_ribbon;
            }

            $alumni->save();

            return response()->json([
                'success' => true,
                'message' => 'Ribbon preference updated successfully!',
                'is_request_ribbon' => $alumni->is_request_ribbon,
                'is_directory_ribbon' => $alumni->is_directory_ribbon
            ]);
        } catch (\Exception $e) {
            Log::error('Update Ribbon Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update ribbon preference: ' . $e->getMessage()
            ], 500);
        }
    }
}
