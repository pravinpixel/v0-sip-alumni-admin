<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;


class ProfileController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::guard('web')->user();
        return view('profile.index',['user' => $user]);
    }

    public function changePassword(){
        $user = Auth::guard('web')->user();
        return view('profile.changepassword',['user' => $user]);
    }

    public function getValidationRules(Request $request,$id = null)
    {
        $rule_arr = [
            'user_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id . ',id,deleted_at,NULL',
            'mobile_number' => 'required|digits:10',
            'profile_picture' => 'nullable|file|mimes:png,jpg,jpeg|max:5120',
        ];

        return $rule_arr;
    }

    public function getPasswordValidationRules(Request $request,$id = null){
        return [
            'current_password' => ['required', function ($attribute, $value, $fail) {
                $user = Auth::user();
                if (!Hash::check($value, $user->password)) {
                    $fail('The provided current password does not match our records.');
                }
            }],
            'new_password' => 'required|min:6',
            'retype_password' => 'required|same:new_password|min:6',
        ];
    }

    public function update(Request $request)
    {
        $id = $request->id ?? null;
        
        $validatedData = Validator::make($request->all(), $this->getValidationRules($request,$id));

        if ($validatedData->fails()) {
            return response()->json(['error' => $validatedData->errors()], 422);
        }

        try {
            if (isset($id)) {
                $user = User::find($id);
                // Update other user details
                $user->name = $request->input('user_name');
                $user->mobile_number = $request->input('mobile_number');
                $user->email = $request->input('email');

                

                // Handle profile picture update if provided
                if (!empty($request->file('profile_picture')) && $request->input('avatar_remove') == 0) {
                    $fileName = "profile_picture_" . uniqid() . "_" . time() . ".png";
                    $path = $request->file('profile_picture')->move(storage_path("app/public/profiles"), $fileName);
                    $user->profile_image = 'profiles/'.$fileName;
                }

                if($request->input('avatar_remove') == 1){
                    // Path to the profile images folder
                    $profileImagePath = storage_path("app/public/profiles");
                    // Check if the user has a profile image and if the file exists
                    if ($user->profile_image && file_exists($profileImagePath . $user->profile_image)) {
                        // Delete the existing profile image file
                        unlink($profileImagePath . $user->profile_image);
                    }
                    // Update the user's profile image field to be empty
                    $user->profile_image = null;

                    if (!empty($request->file('profile_picture'))) {
                        $fileName = "profile_picture_" . uniqid() . "_" . time() . ".png";
                        $path = $request->file('profile_picture')->move(storage_path("app/public/profiles"), $fileName);
                        $user->profile_image = 'profiles/'.$fileName;
                    }
                }
                
                
                $user->save();
                return $this->returnSuccess($user, "Profile updated successfully.");
            }

            return response()->json(['error' => 'User not found.'], 404);
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }
    }

    public function updatePassword(Request $request)
    {
        $id = $request->id ?? null;
        $validatedData = Validator::make($request->all(), $this->getPasswordValidationRules($request,$id));
        if ($validatedData->fails()) {
            return response()->json(['error' => $validatedData->errors()], 422);
        }
        try {
            if (isset($id)) {
                $user = User::find($id);
                $isUpdatingPassword = !empty($request->input('retype_password'));
                // Check if we are updating the password
                if ($isUpdatingPassword) {
                    $user->password = bcrypt($request->input('new_password'));
                    $user->hash_password = Crypt::encryptString($request->input('new_password'));
                }

                $user->save();
                return $this->returnSuccess($user, "Password updated successfully.");
            }
        }catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }
    }
}
