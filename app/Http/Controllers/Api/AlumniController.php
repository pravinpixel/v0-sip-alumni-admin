<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\AdminAlumniRegistedMail;
use App\Mail\AlumniOtpMail;
use App\Mail\AlumniWelcomeMail;
use Illuminate\Http\Request;
use App\Models\Alumnis;
use App\Models\Cities;
use App\Models\CountryCodes;
use App\Models\MobileOtp;
use App\Models\Occupation;
use App\Models\CenterLocations;
use App\Models\CurrentLocation;
use App\Models\EmailOtp;
use App\Models\Pincodes;
use App\Models\Role;
use App\Models\States;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Current;

class AlumniController extends Controller
{
    public function register(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'location_type'          => 'required',
                'country_code'         => 'required|string',
                'full_name'            => 'required|string|max:255',
                'year_of_completion'   => 'required|integer|digits:4',
                'state_id'             => 'required|integer',
                'city_id'              => 'required',
                'pincode_id'              => 'required|integer',
                'center_id'            => 'required',
                'email'                => 'required|email:rfc,dns|unique:alumnis,email',
                'mobile_number'        => 'required|digits:10|unique:alumnis,mobile_number',
                'occupation'           => 'required|string|max:255',
                // 'other_city'           => 'required_if:city_id,others|string|max:255',
                'other_center'         => 'required_if:center_id,others|string|max:255',
                'current_location'     => 'nullable|string|max:255',
                'level_completed'     => 'required|string|max:255',
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

            if ($request->location_type == 0) {
                $otpRecord = MobileOtp::where('mobile_number', $request->mobile_number)->first();

                if (!$otpRecord || $otpRecord->is_verified == 0) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Please verify mobile OTP first.'
                    ], 400);
                }
            } else {
                $emailVerify = EmailOtp::where('email', $request->email)->first();

                if (!$emailVerify || $emailVerify->is_verified == 0) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Please verify your email before registration.'
                    ], 400);
                }
            }

            $cityId = $request->city_id;

            // if ($request->city_id == "others") {

            //     $cityName = ucfirst(strtolower($request->other_city));

            //     $city = Cities::firstOrCreate([
            //         'name'     => $cityName,
            //         'state_id' => $request->state_id
            //     ], [
            //         'is_custom' => 1
            //     ]);

            //     $cityId = $city->id;
            // }

            $centerId = $request->center_id;

            if ($request->center_id == "others") {

                $centerName = ucfirst(strtolower($request->other_center));

                $center = CenterLocations::firstOrCreate([
                    'name'     => $centerName,
                    'pincode_id' => $request->pincode_id
                ], [
                    'is_custom' => 1
                ]);

                $centerId = $center->id;
            }

            $occupationName = ucfirst(strtolower($request->occupation));

            $occupation = Occupation::firstOrCreate([
                'name' => $occupationName
            ]);

            // $currentLocationName = ucfirst(strtolower($request->current_location));
            // $currentLocation = CurrentLocation::firstOrCreate([
            //     'name'     => $currentLocationName,
            // ]);

            $alumni = Alumnis::create([
                'location_type'      => $request->location_type,
                'country_code'       => $request->country_code,
                'full_name'          => $request->full_name,
                'year_of_completion' => $request->year_of_completion,
                'state_id'           => $request->state_id,
                'city_id'            => $cityId,
                'center_id'          => $centerId,
                'pincode_id'            => $request->pincode_id,
                'current_location'   => $request->current_location,
                'level_completed'   => $request->level_completed,
                'email'              => $request->email,
                'mobile_number'      => $request->mobile_number,
                'occupation_id'      => $occupation->id,
                'status'             => 'active',
                'image'              => asset('images/avatar/blank.png')
            ]);
            // $otpRecord->delete();
            if ($request->location_type == 0) {
                $otpRecord->delete();
            } else {
                $emailVerify->delete();
            }

            Alumnis::where('is_directory_ribbon', '!=', 1)
                ->orWhereNull('is_directory_ribbon')
                ->update(['is_directory_ribbon' => 1]);

            $alumniData = [
                'name' => $alumni->full_name,
                'url' => env('SITE_URL'),
                'support_email' => env('SUPPORT_EMAIL'),
            ];
            Mail::to($alumni->email)->queue(new AlumniWelcomeMail($alumniData));

            // $role = Role::where('name', 'Super Admin')->first();
            $admins = User::where('status', 1)->whereNull('deleted_at')
                ->whereHas('role', function ($q) {
                    $q->where('status', 1);
                })
                ->get();

            $adminData = [
                'name' => $alumni->full_name ?? '',
                'email' => $alumni->email ?? '',
                'mobile' => $alumni->mobile_number ?? '',
                'year_of_passing' => $alumni->year_of_completion ?? '',
                'department' => $alumni->occupation->name ?? '',
                'support_email' => env('SUPPORT_EMAIL'),
            ];
            foreach ($admins as $admin) {
                Mail::to($admin->email)->queue(new AdminAlumniRegistedMail($adminData));
            }

            $smsNumber = '91' . $alumni->mobile_number;
            $smsMessage = "Your alumni registration has been completed. Welcome to the SIP Alumni community!\nTeam - SIP Academy";
            sendSms($smsNumber, $smsMessage);

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
            'location_type' => 'required|in:0,1',
            'mobile_number' => 'required_if:location_type,0|digits:10',
            'email'         => 'required_if:location_type,1|email:rfc,dns',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors()
            ], 422);
        }

        // INSIDE INDIA → MOBILE OTP
        if ($request->location_type == 0) {
            return $this->processMobileOtp($request->mobile_number);
        }

        // OUTSIDE INDIA → EMAIL OTP
        return $this->processEmailOtp($request->email);
    }

    private function processMobileOtp($mobile)
    {
        $exists = Alumnis::where('mobile_number', $mobile)->first();
        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Mobile number already registered'
            ], 400);
        }

        $otp = rand(100000, 999999);
        $record = MobileOtp::firstOrNew(['mobile_number' => $mobile]);

        $last = $record->updated_at ? now()->diffInSeconds($record->updated_at) : 31;
        if ($last < 30) {
            return response()->json([
                'success' => false,
                'message' => 'Please wait before requesting a new OTP.',
                'wait_seconds' => 30 - $last
            ], 429);
        }

        $record->otp = $otp;
        $record->expires_at = now()->addSeconds(30);
        $record->save();

        $message = "Welcome to SIP Academy Alumni!\nYour verification code is {$otp}. It expires in 10 minutes. Please don't share this code.\nTeam - SIP Academy";
        $smsNumber = '91' . $mobile;
        sendsms($smsNumber, $message);

        return response()->json([
            'success' => true,
            'message' => 'Mobile OTP sent successfully',
            'otp' => $otp
        ], 200);
    }

    private function processEmailOtp($email)
    {
        $exists = Alumnis::where('email', $email)->first();
        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Email already registered'
            ], 400);
        }

        $otp = rand(100000, 999999);
        $record = EmailOtp::firstOrNew(['email' => $email]);

        $last = $record->updated_at ? now()->diffInSeconds($record->updated_at) : 31;
        if ($last < 30) {
            return response()->json([
                'success' => false,
                'message' => 'Please wait before requesting a new OTP.',
                'wait_seconds' => 30 - $last
            ], 429);
        }

        $record->otp = $otp;
        $record->expires_at = now()->addSeconds(30);
        $record->save();

        $data = [
            'otp' => $otp,
            'support_email' => env('SUPPORT_EMAIL'),
        ];
        Mail::to($email)->queue(new AlumniOtpMail($data));
        Log::info('OTP Mail Sent');

        return response()->json([
            'success' => true,
            'message' => 'Email OTP sent successfully',
            'otp' => $otp
        ], 200);
    }




    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'location_type' => 'required|in:0,1',
            'mobile_number' => 'required_if:location_type,0|digits:10',
            'email'         => 'required_if:location_type,1|email:rfc,dns',
            'otp' => 'required|digits:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        if ($request->location_type == 0) {
            return $this->verifyMobileOtp($request->mobile_number, $request->otp);
        } else {
            return $this->verifyEmailOtp($request->email, $request->otp);
        }
    }

    private function verifyMobileOtp($mobile, $otp)
    {
        $record = MobileOtp::where('mobile_number', $mobile)
            ->where('otp', $otp)
            ->first();

        if (!$record) {
            return response()->json(['success' => false, 'message' => 'Invalid OTP'], 400);
        }
        if (now()->gt($record->expires_at)) {
            return response()->json(['success' => false, 'message' => 'OTP expired'], 400);
        }
        $record->update(['is_verified' => 1]);

        return response()->json(['success' => true, 'message' => 'Mobile OTP verified']);
    }

    private function verifyEmailOtp($email, $otp)
    {
        $record = EmailOtp::where('email', $email)
            ->where('otp', $otp)
            ->first();

        if (!$record) {
            return response()->json(['success' => false, 'message' => 'Invalid OTP'], 400);
        }
        if (now()->gt($record->expires_at)) {
            return response()->json(['success' => false, 'message' => 'OTP expired'], 400);
        }
        $record->update(['is_verified' => 1]);

        return response()->json(['success' => true, 'message' => 'Email OTP verified']);
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
                    $normalCities = Cities::select('id', 'name')
                        ->where('state_id', $request->state_id)
                        ->where('is_custom', 0)
                        ->orderBy('name')
                        ->get();

                    $customCities = Cities::select('id', 'name')
                        ->where('state_id', $request->state_id)
                        ->where('is_custom', 1)
                        ->orderBy('name')
                        ->get();
                } else {
                    $normalCities = Cities::select('id', 'name')
                        ->where('is_custom', 0)
                        ->orderBy('name')
                        ->get();

                    $customCities = Cities::select('id', 'name')
                        ->where('is_custom', 1)
                        ->orderBy('name')
                        ->get();
                }
                $results['city'] = [
                    'normal' => $normalCities,
                    'others' => $customCities
                ];
            }
            if (in_array("occupation", $required)) {
                $occupation = [];
                $occupation = Occupation::select('id', 'name')->get();
                $results['occupation'] = $occupation;
            }
            if (in_array("pincode", $required)) {
                $pincode = [];
                $pincode = Pincodes::select('id', 'pincode', 'city_id')->get();
                if($request->city_id){
                    $pincode = $pincode->where('city_id', $request->city_id)->values()->all();
                }
                $results['pincode'] = $pincode;
            }
            if (in_array("country_code", $required)) {
                $countryCode = [];
                $countryCodes = CountryCodes::select('id', 'dial_code', 'is_inside')->get();
                if($request->location_type == 0){
                    $countryCode = $countryCodes->where('is_inside', 1)->values()->all();
                } else {
                    $countryCode = $countryCodes->where('is_inside', 0)->values()->all();
                }
                $results['country_code'] = $countryCode;
            }
            if (in_array("center_location", $required)) {
                $centerLocation = [];
                if ($request->pincode_id) {
                    $Location = CenterLocations::select('id', 'name')
                        ->where('pincode_id', $request->pincode_id)
                        ->orderBy('name')
                        ->get();
                    $normalLocation = $Location->where('is_custom', 0)->values()->all();
                    $customLocation = $Location->where('is_custom', 1)->values()->all();
                } else {
                    $Location = CenterLocations::select('id', 'name')
                        ->orderBy('name')
                        ->get();
                    $normalLocation = $Location->where('is_custom', 0)->values()->all();
                    $customLocation = $Location->where('is_custom', 1)->values()->all();
                }
                $results['center_location'] = [
                    'normal' => $normalLocation,
                    'others' => $customLocation
                ];
            }
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
        return response()->json(['status' => true, 'message' => 'Essentials', 'data' => $results]);
    }
}
