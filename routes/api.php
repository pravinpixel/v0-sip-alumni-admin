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
    'prefix' => 'ialert',
    'as' => 'ialert.'
], function () {
    Route::post('save', [IallertController::class, 'save'])->name('iallert.save');
    Route::post('upload', [IallertController::class, 'upload'])->name('iallert.upload');
});

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


        Route::group([
            'prefix' => 'task',
            'as' => 'task.'
        ], function () {
            Route::get('/', [TaskController::class, 'index'])->name('task.index');
            Route::get('view/{id}', [TaskController::class, 'view'])->name('task.view');
            Route::post('save', [TaskController::class, 'save'])->name('task.save');
            Route::post('save1', [TaskController::class, 'save1'])->name('task.save1');
            Route::post('update/{id}', [TaskController::class, 'update'])->name('task.update');
            Route::post('statusupdate/{id}', [TaskController::class, 'taskcompleteStatusUpdate'])->name('task.taskcompleteStatusUpdate');
            Route::post('taskcancell/{id}', [TaskController::class, 'cancellStatusUpdate'])->name('task.taskcancell');
            Route::post('taskclose/{id}', [TaskController::class, 'closeStatusUpdate'])->name('task.taskclose');
            Route::delete('delete/{id}/{entire?}', [TaskController::class, 'delete'])->name('task.delete');
            // Route::delete('delete/{id}', [TaskController::class, 'delete'])->name('task.delete');
            Route::get('essential', [EssentialController::class, 'Essential'])->name('task.essential');
        });

        Route::group([
            'prefix' => 'task-comment',
            'as' => 'task-comment.'
        ], function () {
            Route::get('/{id}', [TaskCommentController::class, 'index'])->name('task-comment.index');
            Route::post('save', [TaskCommentController::class, 'save'])->name('task-comment.save')->middleware('throttle:100,1');
        });

        // Route::group([
        //     'prefix' => 'mention',
        //     'as' => 'mention.'
        // ], function () {
        //     Route::get('mentionList', [TaskController::class, 'mentionList'])->name('mention-List.mentionList');
        // });

        Route::group([
            'prefix' => 'export',
            'as' => 'export.'
        ], function () {
            Route::get('/{type}', [ReportController::class, 'export'])->name('tasks.export');
        });

        Route::group([
            'prefix' => 'dashboard',
            'as' => 'dashboard.'
        ], function () {
            Route::get('myTask/{type?}', [DashboardController::class, 'myTask'])->name('dashboard.myTask');
            Route::get('notification', [NotificationController::class, 'index'])->name('dashboard.index');
            Route::delete('notification/delete', [NotificationController::class, 'alldelete'])->name('notification.alldelete');
            Route::delete('notification/delete/{id}', [NotificationController::class, 'delete'])->name('notification.delete');
            Route::put('notification/seen/{id}', [NotificationController::class, 'seenUpdate'])->name('notification.seenUpdate');
            Route::get('assignedTask/{type?}', [DashboardController::class, 'assignedTask'])->name('dashboard.assignedTask');
            Route::get('empDirectory', [DashboardController::class, 'empDirectory'])->name('dashboard.empDirectory');
            Route::get('ialertGraph', [DashboardController::class, 'ialertGraph'])->name('dashboard.ialertGraph');
        });

        Route::group([
            'prefix' => 'ialert',
            'as' => 'ialert.'
        ], function () {
            Route::get('index', [IallertController::class, 'index'])->name('iallert.index');
            Route::get('view/{id}', [IallertController::class, 'view'])->name('iallert.view');
            Route::get('essential', [IallertController::class, 'essential'])->name('iallert.essential');
            Route::get('commentList/{id}', [IallertController::class, 'commentList'])->name('iallert.commentList');
            Route::post('comment', [IallertController::class, 'comment'])->name('iallert.comment');
            Route::post('update/{id}', [IallertController::class, 'update'])->name('iallert.update');
            Route::post('store-organization', [IallertController::class, 'storeOrganization'])->name('iallert.store-organization');
            
        });

        Route::post('mail', [TestController::class, 'mail']);
    });
});
