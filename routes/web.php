<?php

use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\TestController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\DesignationController;
use App\Http\Controllers\Admin\DirectoryController;
use App\Http\Controllers\Admin\OrganizationController;
use App\Http\Controllers\Admin\TaskCategoryController;
use App\Http\Controllers\Admin\RoleManagementController;
use App\Http\Controllers\Admin\TaskController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\ForumsController;

/*
|--------------------------------------------------------------------------
| Web Routes //
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::get('mail', [TestController::class, 'mail']);
Route::middleware(['route.access:admin'])->group(function () {

Route::controller(AuthController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('login', 'index')->name('index.login');
    Route::post('login_check', 'login_check')->name('login.check');
    Route::get('logout', 'logout')->name('logout');
    Route::get('/forgot-password', 'forgot_password')->name('forgot_password');
    Route::post('/password_reset_mail', 'password_reset_mail')->name('password_reset_mail');
    Route::get('/reset-forgot-password/{email}/{token}', 'reset_forgot_password')->name('reset_forgot_password');
    Route::post('/reset-forgot-password-submit', 'reset_forgot_password_submit')->name('reset_forgot_password_submit');
});

Route::group(['middleware' => ['auth']], function () {

    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard.view');
    Route::get('profile', [ProfileController::class, 'index']);
    Route::post('profile-update', [ProfileController::class, 'update']);
    Route::get('change-password', [ProfileController::class, 'changePassword']);
    Route::post('password-update', [ProfileController::class, 'updatePassword']);

    #..directory ...
    Route::prefix('directory')->controller(DirectoryController::class)->group(function () {
        Route::get('/', 'index')->name('admin.directory.index')->middleware('checkAccess:directory.view');
        Route::get('/get-data', [DirectoryController::class, 'getData'])->name('admin.directory.data');
        Route::get('/filter-options', [DirectoryController::class, 'getFilterOptions'])->name('admin.directory.filter.options');
        Route::get('/connections/{id}', [DirectoryController::class, 'connectionViewPage'])->name('admin.directory.view.connections.page');
        Route::get('/connections-list/{id}', [DirectoryController::class, 'viewConnectionList'])->name('admin.directory.view.connections.list');
        Route::post('/update-status', 'updateStatus')->name('directory.update.status');
        Route::get('/view-profile/{id}', 'viewProfileDetails')->name('admin.directory.view.profile');
        Route::get('/export', [DirectoryController::class, 'export'])->name('admin.directory.export');
    });

    Route::prefix('forums')->controller(ForumsController::class)->group(function () {
        Route::get('/', 'index')->name('admin.forums.index')->middleware('checkAccess:forum.view');
        Route::get('/get-data', 'getData')->name('admin.forums.data');
        Route::get('/filter-options', 'getFilterOptions')->name('admin.forums.filter.options');
        Route::get('/post-details/{id}', 'getPostDetails')->name('admin.forums.post.details');
        Route::get('/comments/{id}', 'viewComments')->name('admin.forums.comments');
        Route::get('/comments-data/{id}', 'getCommentsData')->name('admin.forums.comments.data');
        Route::get('/comment-replies/{id}', 'getCommentReplies')->name('admin.forums.comment.replies');
        Route::delete('/comment/{id}', 'deleteComment')->name('admin.forums.comment.delete');
        Route::post('/change-status', 'changeStatus')->name('forums.change.status');
        Route::get('/export', [ForumsController::class, 'export'])->name('admin.forums.export');
    });

    #..role ...
    Route::prefix('role')->controller(RoleManagementController::class)->group(function () {
        Route::get('/', 'index')->name('role.index')->middleware('checkAccess:role.view');
        Route::get('/add_edit', 'create')->name('role.create')->middleware('checkAccess:role.create');
        Route::post('/save', 'save')->name('role.save');
        Route::post('/toggle-status', 'toggleStatus')->name('role.toggle-status')->middleware('checkAccess:role.edit');
        Route::get('/{id}', 'edit')->name('role.edit')->middleware('checkAccess:role.edit');
        Route::delete('/{id}', 'delete')->name('role.delete')->middleware('checkAccess:role.delete');
        Route::get('role_user/{id}', 'role_user')->name('role.role_user')->middleware('checkAccess:role.delete');
    });


    #..user ...
    Route::prefix('user')->controller(UserManagementController::class)->group(function () {
        Route::get('/', 'index')->name('user.index')->middleware('checkAccess:user.view');
        Route::get('/add_edit', 'create')->name('user.create')->middleware('checkAccess:user.create');
        Route::post('/save', 'save')->name('user.save');
        Route::post('/toggle-status', 'toggleStatus')->name('user.toggle-status')->middleware('checkAccess:user.edit');
        Route::get('/{id}', 'edit')->name('user.edit')->middleware('checkAccess:user.edit');
        Route::delete('/{id}', 'delete')->name('user.delete')->middleware('checkAccess:user.delete');
    });
});
});

