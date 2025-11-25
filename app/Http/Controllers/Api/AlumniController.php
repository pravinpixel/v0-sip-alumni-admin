<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\AdminAlumniRegistedMail;
use App\Mail\AlumniWelcomeMail;
use Illuminate\Http\Request;
use App\Models\Alumnis;
use App\Models\Cities;
use App\Models\MobileOtp;
use App\Models\Occupation;
use App\Models\States;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AlumniController extends Controller
{
    public function register(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'full_name'            => 'required|string|max:255',
                'year_of_completion'   => 'required|integer|digits:4',
                'state_id'             => 'required|integer',
                'city_id'              => 'required',
                'email'                => 'required|email|unique:alumnis,email',
                'mobile_number'        => 'required|digits:10|unique:alumnis,mobile_number',
                'occupation'           => 'required|string|max:255',
                'other_city'           => 'required_if:city_id,others|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors',
                    'errors' => $validator->errors()
                ], 422);
            }
            // $otpRecord = MobileOtp::where('mobile_number', $request->mobile_number)->first();

            // if (!$otpRecord || $otpRecord->is_verified == 0) {
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'Please verify OTP before registration.'
            //     ], 400);
            // }
            $cityId = $request->city_id;

            if ($request->city_id == "others") {

                $cityName = ucfirst(strtolower($request->other_city));

                $city = Cities::firstOrCreate([
                    'name'     => $cityName,
                    'state_id' => $request->state_id
                ]);

                $cityId = $city->id;
            }

            $occupationName = ucfirst(strtolower($request->occupation));

            $occupation = Occupation::firstOrCreate([
                'name' => $occupationName
            ]);

            $alumni = Alumnis::create([
                'full_name'          => $request->full_name,
                'year_of_completion' => $request->year_of_completion,
                'state_id'           => $request->state_id,
                'city_id'            => $cityId,
                'email'              => $request->email,
                'mobile_number'      => $request->mobile_number,
                'occupation_id'      => $occupation->id,
                'status'             => 'active',
                'image'              => asset('images/avatar/blank.png')
            ]);
            // $otpRecord->delete();

            Alumnis::where('is_directory_ribbon', '!=', 1)
                ->orWhereNull('is_directory_ribbon')
                ->update(['is_directory_ribbon' => 1]);

            $alumniData = [
                'name' => $alumni->full_name,
                'url' => env('APP_URL'),
                'support_email' => env('SUPPORT_EMAIL'),
            ];
            Mail::to($alumni->email)->queue(new AlumniWelcomeMail($alumniData));

            // $adminData = [
            //     'name' => $alumni->name,
            //     'email' => $alumni->email,
            //     'mobile' => $alumni->mobile,
            //     'year_of_passing' => $alumni->year,
            //     'department' => $alumni->department,
            //     'support_email' => 'sipinfo@sipacademyindia.com'
            // ];
            // Mail::to('admin@sipabacus.com')->queue(new AdminAlumniRegistedMail($adminData));


            return response()->json([
                'success' => true,
                'message' => 'Alumni registered successfully!',
                'data'    => $alumni
            ], 201);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Registration failed',
                'error'   => $e->getMessage()
            ], 500);
        }
    }


    public function sendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile_number' => 'required|digits:10'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $mobile = $request->mobile_number;
        $otp = rand(100000, 999999);
        $smsMobile = '91' . $mobile;
        $message = "Welcome to SIP Academy Alumni!\nYour verification code is {$otp}. It expires in 10 minutes. Please don't share this code.\nTeam - SIP Academy";

        // Check existing OTP record
        $existingOtp = MobileOtp::where('mobile_number', $mobile)->first();

        if ($existingOtp) {
            $seconds = now()->diffInSeconds($existingOtp->updated_at);

            // Cooldown check (30 sec)
            if ($seconds < 30) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please wait before requesting a new OTP.',
                    'wait_seconds' => 30 - $seconds
                ], 429);
            }

            // Update OTP (Resend)
            $existingOtp->update([
                'otp' => $otp,
                'expires_at' => now()->addMinutes(5)
            ]);
            sendsms($smsMobile, $message);

            return response()->json([
                'success' => true,
                'message' => 'OTP resent successfully',
                // 'otp' => $otp 
            ], 200);
        } else {
            // First time send OTP
            MobileOtp::create([
                'mobile_number' => $mobile,
                'otp' => $otp,
                'expires_at' => now()->addMinutes(5)
            ]);
            sendsms($smsMobile, $message);

            return response()->json([
                'success' => true,
                'message' => 'OTP sent successfully',
                // 'otp' => $otp
            ], 200);
        }
    }


    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile_number' => 'required|digits:10',
            'otp' => 'required|digits:6'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $record = MobileOtp::where('mobile_number', $request->mobile_number)
            ->where('otp', $request->otp)
            ->first();

        if (!$record) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP'
            ], 400);
        }

        if (now()->gt($record->expires_at)) {
            return response()->json([
                'success' => false,
                'message' => 'OTP expired'
            ], 400);
        }

        // Delete OTP after success
        $record->update([
            'is_verified' => 1
        ]);

        return response()->json([
            'success' => true,
            'message' => 'OTP verified successfully'
        ]);
    }

    public function essentials(Request $request)
    {
        try {
            $results = [];
            $string = $request->required;
            $required = explode(",", $string);

            if (in_array("state", $required)) {
                $states = [];
                $states = States::select('id', 'name')->get();
                $results['state'] = $states;
            }
            if (in_array("city", $required)) {
                $city = [];
                if ($request->state_id) {
                    $city = Cities::select('id', 'name')->where('state_id', $request->state_id)->get();
                } else {
                    $city = Cities::select('id', 'name')->get();
                }
                $results['city'] = $city;
            }
            if (in_array("occupation", $required)) {
                $occupation = [];
                $occupation = Occupation::select('id', 'name')->get();
                $results['occupation'] = $occupation;
            }
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
        return response()->json(['status' => true, 'message' => 'Essentials', 'data' => $results]);
    }
}
