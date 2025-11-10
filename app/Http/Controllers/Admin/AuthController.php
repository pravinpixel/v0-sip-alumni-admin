<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminResetPassword;
use App\Models\Employee;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon; 
use Illuminate\Support\Facades\Mail;
use App\Mail\AdminEmail;
use App\Models\User;

class AuthController extends Controller
{
    public function index(Request $request)
    {
        return view('auth.login');
    }

    public function login_check(Request $request)
    {

        $validatedData = Validator::make($request->all(), $this->getValidationRules(),$this->getValidationMessages());
        if ($validatedData->fails()) {
            return $this->returnError($validatedData->errors(), 'Validation Error', 422);
        }
        try {
            $active_check = User::where('email', $request->email)->where('status', 1)->first();
            if (!$active_check) {
                return response()->json(['success' => false, 'error' => 'This user is not active!'], 422);
            }
            $credentials = ['email' => request('email'), 'password' => request('password')];
            if (Auth::guard('web')->attempt($credentials)) {
                $user = Auth::guard('web')->user();
                if ($user->role_id) {
                    // Fetch the role status directly from the roles table
                    $role_status = Role::where('id', $user->role_id)->value('status');
                    
                    if ($role_status == 1) {
                        $request->session()->regenerate();
                        return redirect()->route('dashboard.view');
                    } else {
                        Auth::guard('web')->logout();
                        return view('403');
                    }
                } else {
                    Auth::guard('web')->logout();
                    return view('403');
                }
            } else {
                // If authentication fails, redirect back with an error message
                return response()->json(['success' => false, 'error' => 'Incorrect Email or Password. Try again!'], 422);
            }
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }
    }

    public function getValidationRules()
    {
        $rule_arr = [
            'email' => ['required', 'email','regex:/(.+)@(.+)\.(.+)/i'],
            'password' => ['required','string']
        ];

        return $rule_arr;
    }

    function getValidationMessages() {
        return [
            'email.required' => 'The email field is required.',
            'email.email' => 'The email address must be a valid email address.',
            'password.required' => 'The password field is required.',
            
        ];
    }



    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('index');
    }

    public function forgot_password()
    {
              return view('auth.forgot_password');
    }

    public function password_reset_mail(Request $request)
    {
        $email = $request->input('email');
        if (!$email) {
            return $this->returnError('Email is required', 'Validation Error', 422);
        }

        $user = User::where('email', $email)->first();
        
        if (!$user) {
            return $this->returnError('Email not found', 'Validation Error', 422);
        }

        $user_name = $user->name;

        $token = Str::random(64);
        $email = $request->input('email');

        $user = new AdminResetPassword();
        $user->email = $email;
        $user->token = $token;
        $user->created_at = Carbon::now();
        $user->save();
        
        Mail::to($request->email)->send(new AdminEmail($token,$email,$user_name));
        
        return $this->returnSuccess([], "We have e-mailed your password reset link!");

    }

    public function reset_forgot_password($email,$token) 
    { 
        return view('auth.forgot_password_form', ['email' => $email,'token' => $token]);
    }

    public function reset_forgot_password_submit(Request $request)
    {
        
        $email = $request->get('email');
        $token = $request->get('token');
        $password = $request->get('password');

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|string|min:6',
            'confirm_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return $this->returnError($validator->errors(), 'Validation Error', 422);
        }



        $password_reset = AdminResetPassword::where('email', $email)
                                              ->where('token', $token)
                                              ->first();
        if (!$password_reset) {
            return $this->returnError('Token not found', 'Code not found',422);
        }
        try{
            User::where('email', $email)->update(['password' => bcrypt($password)]);
            $password_reset->delete();
            return $this->returnSuccess([], 'Password Changed Successfully');
        }catch (\Exception $e) {
            $password_reset->saveOrFail();
            return $this->returnError($e->getMessage());
        }
    }
}
