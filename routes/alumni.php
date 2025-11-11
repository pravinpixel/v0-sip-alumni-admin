<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Alumni\AuthCheckController;
use App\Http\Controllers\Alumni\DashboardController;
use App\Http\Controllers\Alumni\DirectoryController;
use App\Http\Controllers\Alumni\ConnectionsController;
use App\Http\Controllers\Alumni\ForumsController;
use Illuminate\Support\Facades\Auth;

Route::controller(AuthCheckController::class)->group(function () {
    Route::get('/',  'index')->name('alumni.login');
    Route::post('/send-otp', 'sendOtp')->name('send.otp');
    Route::post('/verify-otp', 'verifyOtp')->name('verify.otp');
    Route::get('/verify-otp-page', 'showVerifyOtp')->name('verify.otp.page');

});

Route::middleware('alumni.auth')->group(function () {
    Route::get('/alumni/dashboard', [DashboardController::class, 'index'])->name('alumni.dashboard');
});
Route::get('/alumni/directory', [DirectoryController::class, 'index'])->name('alumni.directory');
Route::get('/alumni/connections', [ConnectionsController::class, 'index'])->name('alumni.connections');
Route::get('/alumni/forums', [ForumsController::class, 'index'])->name('alumni.forums');