<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Alumni\AuthCheckController;
use App\Http\Controllers\Alumni\DashboardController;
use App\Http\Controllers\Alumni\DirectoryController;
use App\Http\Controllers\Alumni\ConnectionsController;
use App\Http\Controllers\Alumni\ForumsController;
use App\Http\Controllers\Alumni\CommonController;
use App\Models\Cities;
use App\Models\States;
use Illuminate\Support\Facades\Auth;

Route::controller(AuthCheckController::class)->group(function () {
    Route::get('/',  'index')->name('alumni.login');
    Route::post('/send-otp', 'sendOtp')->name('send.otp');
    Route::post('/verify-otp', 'verifyOtp')->name('verify.otp');
    Route::get('/verify-otp-page', 'showVerifyOtp')->name('verify.otp.page');
    Route::post('/logout', 'logout')->name('alumni.logout');
});

Route::middleware('alumni.auth')->group(function () {
    Route::prefix('dashboard')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('alumni.dashboard');
    });
    
    Route::prefix('directory')->group(function () {
        Route::get('/', [DirectoryController::class, 'index'])->name('alumni.directory');
        Route::get('/data', [DirectoryController::class, 'getData'])->name('alumni.directory.data');
        Route::post('/connect/{receiverId}', [DirectoryController::class, 'sendRequest'])->name('alumni.send.request');
    });
    
    Route::prefix('connections')->group(function () {
        Route::get('/', [ConnectionsController::class, 'index'])->name('alumni.connections');
        Route::get('/list', [ConnectionsController::class, 'getConnections'])->name('alumni.connections.list');
        Route::get('/requests', [ConnectionsController::class, 'getRequests'])->name('alumni.connections.requests');
        Route::get('/profile/{id}', [ConnectionsController::class, 'getProfileData'])->name('alumni.connections.profile');
        Route::post('/accept/{id}', [ConnectionsController::class, 'acceptConnection'])->name('alumni.connections.accept');
        Route::post('/reject/{id}', [ConnectionsController::class, 'rejectConnection'])->name('alumni.connections.reject');
    });

    Route::post('/update-settings', [CommonController::class, 'updateSettings'])->name('alumni.update.settings');

    // Profile routes
    Route::prefix('profile')->group(function () {
        Route::post('/update/{id}', [CommonController::class, 'updateProfile'])->name('alumni.profile.update');
    });
    Route::prefix('forums')->group(function () {
        Route::get('/', [ForumsController::class, 'index'])->name('alumni.forums');
        Route::get('/data', [ForumsController::class, 'getData'])->name('alumni.forums.data');
        Route::post('/create-post', [ForumsController::class, 'createPost'])->name('alumni.create.post');
        Route::post('/create-reply', [ForumsController::class, 'createReply'])->name('alumni.create.reply');
        Route::post('/like', [ForumsController::class, 'toggleLike'])->name('alumni.like.post');
    });
    
    Route::get('/alumni/{id}', [CommonController::class, 'getAlumni']);
});

Route::get('/view-thread/{id}', [ForumsController::class, 'viewThread'])->name('alumni.view.thread');

Route::get('/states', [CommonController::class, 'getStates'])->name('alumni.states');
Route::get('/cities-by-state/{stateId}', [CommonController::class, 'getCitiesByState'])->name('alumni.cities');