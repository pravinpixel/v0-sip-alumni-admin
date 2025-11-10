<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index(Request $request)
    {
        // testing
        $user = Setting::find(1);
        return view('masters.setting.action', ['user' => $user]);
    }


    public function create(Request $request)
    {
         
            $user = Setting::find(1);
            $user->timestamps = false;
            $user->name = 'signature';
            
            // Handle profile picture update if provided
            if (!empty($request->file('image')) && $request->input('avatar_remove') == 0) {
                $fileName = "image_" . uniqid() . "_" . time() . ".png";
                $path = $request->file('image')->move(storage_path("app/public/signature"), $fileName);
                $user->value = 'signature/'.$fileName;
            }

            if($request->input('avatar_remove') == 1){
                // Path to the profile images folder
                $profileImagePath = storage_path("app/public/signature");
                // Check if the user has a profile image and if the file exists
                if ($user->value && file_exists($profileImagePath . $user->value)) {
                    // Delete the existing profile image file
                    unlink($profileImagePath . $user->value);
                }
                // Update the user's profile image field to be empty
                $user->value = null;

                if (!empty($request->file('image'))) {
                    $fileName = "image_" . uniqid() . "_" . time() . ".png";
                    $path = $request->file('image')->move(storage_path("app/public/signature"), $fileName);
                    $user->value = 'signature/'.$fileName;
                }
            }
        $user->save();

        return $this->returnSuccess($user, "Signature updated successfully.");
    }
}
