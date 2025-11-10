<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PasswordReset;
use App\Notifications\ForgotPasswordNotification;
use Illuminate\Http\Request;
use App\Models\Employee;
use Illuminate\Support\Facades\Validator;
use Mail;



class ForgotPasswordController extends Controller
{

    public function sendMail(Request $request)
    {
        $mobile = $request->input('phone_number');
        
        if (!$mobile) {
            return $this->returnError('Mobile required');
        }

        $employee = Employee::where('phone_number', $mobile)->first();
        
        if (!$employee) {
            return $this->returnError('Mobile not found');
        }


        $email = $employee['email'] ? $employee['email'] : null;

        if (!$email) {
            return $this->returnError('Email not found');
        }

        $password_reset = PasswordReset::where('email', $email)->first();
        if (!$password_reset) {
            $password_reset = new PasswordReset();
            $password_reset->email = $email;
        }

        if ($password_reset->canSend()) {
            $password_reset->generateCode();
            $password_reset->saveOrFail();

            $url = config('app.web_url') . '/reset-password' . '?email=' . $email;

            $detail = [
                'name' => $employee->name,
                'email' => $email,
                'token' => $password_reset->token,
                'url' => $url,
            ];

            $employee->notify(new ForgotPasswordNotification($detail));

            return $this->returnSuccess(['email' => $email], 'Password reset link sent to your email.');
        } else {
            return $this->returnError('Maximum try reached. Please try after sometimes.');
        }

    }

    public function verifyCode(Request $request)
    {
        $mobile = $request->input('phone_number');
        $token = $request->get('token');

        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|string',
            'token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->returnError($validator->errors());
        }

        $employee = Employee::where('phone_number', $mobile)->first();
        
        if (!$employee) {
            return $this->returnError('Mobile not found');
        }


        $email = $employee['email'] ? $employee['email'] : null;

        if (!$email) {
            return $this->returnError('Email not found');
        }
        

        $password_reset = PasswordReset::where('email', $email)->first();
        if (!$password_reset) {
            return $this->returnError('Token not found', 'Coe not found');
        }

        try {
            $password_reset->validateOtp($token);
        } catch (\Exception $e) {
            $password_reset->saveOrFail();
            return $this->returnError($e->getMessage());
        }
        return $this->returnSuccess(['email' => $email, 'token' => $token]);
    }


    public function changePasswordByCode(Request $request)
    {
        $mobile = $request->input('phone_number');
        $token = $request->get('token');
        $password = $request->get('password');
        

        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|string',
            'token' => 'required|string',
            'password' => 'required|string|min:6',
            'confirm_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return $this->returnError($validator->errors());
        }

        $employee = Employee::where('phone_number', $mobile)->first();
        
        if (!$employee) {
            return $this->returnError('Mobile not found');
        }


        $email = $employee['email'] ? $employee['email'] : null;

        if (!$email) {
            return $this->returnError('Email not found');
        }

        $password_reset = PasswordReset::where('email', $email)->first();
        if (!$password_reset) {
            return $this->returnError('Token not found', 'Code not found');
        }
        try {

            $password_reset->validateOtp($token);

            Employee::where('email', $email)->update(['password' => bcrypt($password)]);
            $password_reset->delete();

            return $this->returnSuccess([], 'Password Changed Successfully');
        } catch (\Exception $e) {
            $password_reset->saveOrFail();
            return $this->returnError($e->getMessage());
        }
    }

}
