<?php

use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Admin\TestController;
use App\Http\Controllers\Api\AlumniController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ForgotPasswordController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\EssentialController;
use App\Http\Controllers\Api\IallertController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\TaskCommentController;
use App\Models\Notification;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('task-recurring', [TestController::class, 'recurrence']);
Route::post('register', [AlumniController::class, 'Register']);
Route::post('send-otp', [AlumniController::class, 'sendOtp']);
Route::post('verify-otp', [AlumniController::class, 'verifyOtp']);
Route::get('essentials', [AlumniController::class, 'essentials']);



Route::group([
    'middleware' => ['assign.guard:api'],
    'as' => 'users.',
], function () {

    Route::post('auth/login', [AuthController::class, 'login'])->name('login');


    Route::post('auth/forget-password', [ForgotPasswordController::class, 'sendMail'])->name('forgot-password');
    Route::post('auth/verify-code', [ForgotPasswordController::class, 'verifyCode'])->name('verify-code');
    Route::post('auth/reset-password', [ForgotPasswordController::class, 'changePasswordByCode'])->name('reset-password');


    Route::group([
        'middleware' => ['jwt.verify']
    ], function () {
        Route::get('/auth/me', [AuthController::class, 'me'])->name('me');
        Route::post('/auth/logout', [AuthController::class, 'Logout'])->name('logout');
        Route::post('auth/refresh', [AuthController::class, 'refresh'])->name('refresh');
        Route::post('auth/change-password', [AuthController::class, 'changePassword'])->name('change.password');

        Route::post('mail', [TestController::class, 'mail']);
    });
});
