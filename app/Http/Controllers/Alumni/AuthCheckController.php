<?php

namespace App\Http\Controllers\Alumni;

use App\Http\Controllers\Controller;
use App\Models\Alumnis;
use App\Models\MobileOtp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthCheckController extends Controller
{
    public function index(Request $request)
    {
        return view('alumni.login.login');
    }

    public function sendOtp(Request $request)
    {
        try {
            $request->validate([
                'number' => 'required|digits:10',
                'is_login' => 'boolean'
            ]);

            $mobile = $request->number;

            // Check if mobile exists
            if($request->is_login == 1) {
                $alumni = Alumnis::where('mobile_number', $mobile)->first();
                if (!$alumni) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Mobile number not registered'
                    ], 400); // 400 Bad Request for invalid mobile
                }
                if($alumni->status == 'blocked') {
                    return response()->json([
                        'success' => false,
                        'error' => 'Your account has been blocked. Please contact admin.'
                    ], 400);
                }
            }

            // Generate new OTP
            $otp = rand(100000, 999999);

            // Store OTP
            MobileOtp::updateOrCreate(
                ['mobile_number' => $mobile],
                [
                    'otp' => $otp,
                    'expires_at' => now()->addSeconds(30)
                ]
            );

           $message = "Welcome to SIP Academy Alumni!\nYour verification code is {$otp}. It expires in 10 minutes. Please don't share this code.\nTeam - SIP Academy";
           $smsNumber = '91' . $mobile;
           sendsms($smsNumber, $message);

            // Store mobile in session
            session(['verify_mobile' => $mobile]);

            // TODO: Send SMS via your SMS gateway

            return response()->json([
                'success' => true,
                'message' => 'OTP sent successfully',
                'redirect' => route('verify.otp.page'),
                // 'otp' => $otp 
            ]);
        } catch (\Exception $e) {
            Log::error('Send OTP Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Failed to send OTP. Please try again.'
            ], 400); // 500 Internal Server Error
        }
    }
    public function showVerifyOtp(Request $request)
    {
        $mobile = session('verify_mobile');

        if (!$mobile) {
            return redirect()->route('alumni.login')->with('error', 'Session expired. Please login again.');
        }

        return view('alumni.login.verify_otp', compact('mobile'));
    }

    public function verifyOtp(Request $request)
    {
        try {
            Log::info('OTP Verification Request:', $request->all());

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

            $alumni = Alumnis::where('mobile_number', $mobile)->first();
            if ($alumni) {
                // Store alumni data in session instead of using Auth
                session([
                    'alumni' => [
                        'id' => $alumni->id,
                        'name' => $alumni->name,
                        'email' => $alumni->email,
                        'mobile_number' => $alumni->mobile_number,
                        // add other fields you need
                    ],
                    'alumni_logged_in' => true
                ]);
                // session()->forget('verify_mobile');

                return response()->json([
                    'success' => true,
                    'message' => 'OTP verified successfully',
                    'redirect' => route('alumni.dashboard')
                ]);
            }

            return response()->json([
                'success' => false,
                'error' => 'User not found'
            ], 400);
        } catch (\Exception $e) {
            Log::error('OTP Verification Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Server error'
            ], 500);
        }
    }





    public function getValidationRules()
    {
        $rule_arr = [
            'number' => 'required|digits:10',
        ];

        return $rule_arr;
    }

    function getValidationMessages()
    {
        return [
            'number.required' => 'Mobile Number field is required.',
            'number.digits' => 'Mobile Number must be exactly 10 digits.',

        ];
    }

    public function session(Request $request)
    {
        $session = session([
            'alumni_logged_in' => true
        ]);
        return $session;
    }

    public function logout(Request $request)
    {
        auth()->guard('alumni')->logout();
        session()->forget('alumni');
        session()->forget('alumni_logged_in');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('alumni.login');
    }
}
