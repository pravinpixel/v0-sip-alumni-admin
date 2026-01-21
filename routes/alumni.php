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

Route::middleware(['route.access:website'])->group(function () {
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
            Route::get('/filter-options', [DirectoryController::class, 'getFilterOptions'])->name('alumni.directory.filter-options');
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
        Route::post('/update-ribbon', [CommonController::class, 'updateRibbon'])->name('alumni.update.ribbon');

        // Profile routes
        Route::prefix('profile')->group(function () {
            Route::get('/view/{id}', [CommonController::class, 'getAlumni'])->name('alumni.profile.view');
            Route::post('/update/{id}', [CommonController::class, 'updateProfile'])->name('alumni.profile.update');
            Route::post('/edit-verify-otp', [CommonController::class, 'editVerifyOtp'])->name('alumni.edit.verify.otp');
        });
        Route::prefix('forums')->group(function () {
            Route::get('/', [ForumsController::class, 'index'])->name('alumni.forums');
            Route::get('/activity', [ForumsController::class, 'activity'])->name('alumni.forums.activity');
            Route::get('/data', [ForumsController::class, 'getData'])->name('alumni.forums.data');
            Route::get('/activity-data', [ForumsController::class, 'getActivityData'])->name('alumni.forums.activity.data');
            Route::get('/filter-options', [ForumsController::class, 'getFilterOptions'])->name('alumni.forums.filter-options');
            Route::post('/create-post', [ForumsController::class, 'createPost'])->name('alumni.create.post');
            Route::post('/update-post', [ForumsController::class, 'updatePost'])->name('alumni.forums.update.post');
            Route::post('/create-reply', [ForumsController::class, 'createReply'])->name('alumni.create.reply');
            Route::post('/like', [ForumsController::class, 'toggleLike'])->name('alumni.like.post');
            Route::post('/pinned', [ForumsController::class, 'pinnedPost'])->name('alumni.pinned.post');
            Route::post('/update-status', [ForumsController::class, 'updateStatus'])->name('alumni.update.status');
            Route::get('/view-thread/{id}', [ForumsController::class, 'viewThread'])->name('alumni.view.thread');
            Route::get('/get-labels', [ForumsController::class, 'getLabels'])->name('alumni.forums.labels');
            Route::post('/report-post', [ForumsController::class, 'reportPost'])->name('alumni.forums.report');
        });

    });
    Route::get('/states', [CommonController::class, 'getStates'])->name('alumni.states');
    Route::get('/cities-by-state/{stateId}', [CommonController::class, 'getCitiesByState'])->name('alumni.cities');
    Route::get('/pincodes-by-city/{cityId}', [CommonController::class, 'getPincodesByCity'])->name('alumni.pincodes');
    Route::get('/center-locations-by-pincode/{pincodeId}', [CommonController::class, 'getCenterLocationsByPincode'])->name('alumni.center.locations');
});
