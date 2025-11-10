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
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\OrganizationController;
use App\Http\Controllers\Admin\TaskCategoryController;
use App\Http\Controllers\Admin\RoleManagementController;
use App\Http\Controllers\Admin\TaskController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\SettingController;

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

        Route::prefix('masters')->group(function () {
            #..branch ...
            Route::prefix('branch')->controller(BranchController::class)->group(function () {
                Route::get('/', 'index')->name('branch.index')->middleware('checkAccess:master.view');
                Route::get('/testMail', 'testMail')->name('branch.testMail');
                Route::post('save', 'save')->name('branch.save');
                Route::post('update/{id}', 'update')->name('branch.update')->middleware('checkAccess:master.edit');
                Route::get('/{id}', 'get')->name('branch.get');
                Route::get('/get-child-branches/{id}', [BranchController::class, 'getChildBranches']);
                Route::delete('/{id}', 'delete')->name('branch.delete')->middleware('checkAccess:master.delete');
            });

            #..department ...
            Route::prefix('department')->controller(DepartmentController::class)->group(function () {
                Route::get('/', 'index')->name('department.index')->middleware('checkAccess:master.view');
                Route::post('save', 'save')->name('department.save');
                Route::post('update/{id}', 'update')->name('department.update')->middleware('checkAccess:master.edit');
                Route::get('/{id}', 'get')->name('department.get');
                Route::delete('/{id}', 'delete')->name('department.delete')->middleware('checkAccess:master.delete');
            });

            #..location ...
            Route::prefix('location')->controller(LocationController::class)->group(function () {
                Route::get('/', 'index')->name('location.index')->middleware('checkAccess:master.view');
                Route::post('save', 'save')->name('location.save');
                Route::post('update/{id}', 'update')->name('location.update')->middleware('checkAccess:master.edit');
                Route::get('/{id}', 'get')->name('location.get');
                Route::delete('/{id}', 'delete')->name('location.delete')->middleware('checkAccess:master.delete');
            });

            #..designation ...
            Route::prefix('designation')->controller(DesignationController::class)->group(function () {
                Route::get('/', 'index')->name('designation.index')->middleware('checkAccess:master.view');
                Route::post('save', 'save')->name('designation.save');
                Route::post('update/{id}', 'update')->name('designation.update')->middleware('checkAccess:master.edit');
                Route::get('/{id}', 'get')->name('designation.get');
                Route::delete('/{id}', 'delete')->name('designation.delete')->middleware('checkAccess:master.delete');
            });

            #..task category ...
            Route::prefix('task-category')->controller(TaskCategoryController::class)->group(function () {
                Route::get('/', 'index')->name('task-category.index')->middleware('checkAccess:master.view');
                Route::post('save', 'save')->name('task-category.save');
                Route::post('update/{id}', 'update')->name('task-category.update')->middleware('checkAccess:master.edit');
                Route::get('/{id}', 'get')->name('task-category.get');
                Route::delete('/{id}', 'delete')->name('task-category.delete')->middleware('checkAccess:master.delete');
            });
        });

        #..employee ...
        Route::prefix('employee')->controller(EmployeeController::class)->group(function () {
            Route::get('create', 'create')->name('employee.create')->middleware('checkAccess:employee.create');
            Route::get('/', 'index')->name('employee.index')->middleware('checkAccess:employee.view');
            Route::post('save', 'save')->name('employee.save');
            Route::post('update/{id}', 'update')->name('employee.update')->middleware('checkAccess:employee.edit');
            Route::get('edit/{id}', 'get')->name('employee.get');
            Route::delete('/{id}', 'delete')->name('employee.delete')->middleware('checkAccess:employee.delete');
            Route::get('send-employee-mail/{id}', 'sendEmployeeMail')->name('employee.sendEmployeeMail');
            Route::get('employee_task/{id}', 'employee_task')->name('employee.employee_task')->middleware('checkAccess:employee.delete');
        });

        Route::prefix('organization')->controller(OrganizationController::class)->group(function () {
            Route::get('create', 'create')->name('organization.create')->middleware('checkAccess:organization.create');
            Route::get('/', 'index')->name('organization.index')->middleware('checkAccess:organization.view');
            Route::post('save', 'save')->name('organization.save');
            Route::get('/export', 'export')->name('organization.export');
            Route::post('update/{id}', 'update')->name('organization.update')->middleware('checkAccess:organization.edit');
            Route::get('edit/{id}', 'get')->name('organization.get');
            Route::delete('/{id}', 'delete')->name('organization.delete')->middleware('checkAccess:organization.delete');
        });

        #..task ...
        Route::prefix('task')->controller(TaskController::class)->group(function () {
            Route::get('/', 'index')->name('task.index');
            Route::get('view/{id}', 'view')->name('task.view');
            // Route::get('edit/{id}', 'get')->name('task.get');
            // Route::delete('/{id}', 'delete')->name('task.delete');
            Route::get('import-view', 'importView')->name('task.import-view')->middleware('checkAccess:bulk_upload.view');
            Route::post('import', 'import')->name('task.import')->middleware('checkAccess:bulk_upload.view');
            Route::get('export-template', 'exportTaskTemplate')->name('task.export-template')->middleware('checkAccess:bulk_upload.view');
        });

        #..role ...
        Route::prefix('role')->controller(RoleManagementController::class)->group(function () {
            Route::get('/', 'index')->name('role.index')->middleware('checkAccess:role.view');
            Route::get('/add_edit', 'create')->name('role.create')->middleware('checkAccess:role.create');
            Route::post('/save', 'save')->name('role.save');
            Route::get('/{id}', 'edit')->name('role.edit')->middleware('checkAccess:role.edit');
            Route::delete('/{id}', 'delete')->name('role.delete')->middleware('checkAccess:role.delete');
            Route::get('role_user/{id}', 'role_user')->name('role.role_user')->middleware('checkAccess:role.delete');
        });

        #..role ...
        Route::prefix('settings')->controller(SettingController::class)->group(function () {
            Route::get('/', 'index')->name('settings.index');
            Route::post('/create', 'create')->name('settings.create');
            // Route::post('/save', 'save')->name('role.save');
            // Route::get('/{id}', 'edit')->name('role.edit')->middleware('checkAccess:role.edit');
            // Route::delete('/{id}','delete')->name('role.delete')->middleware('checkAccess:role.delete');
            // Route::get('role_user/{id}','role_user')->name('role.role_user')->middleware('checkAccess:role.delete');
        });


        #..user ...
        Route::prefix('user')->controller(UserManagementController::class)->group(function () {
            Route::get('/', 'index')->name('user.index')->middleware('checkAccess:user.view');
            Route::get('/add_edit', 'create')->name('user.create')->middleware('checkAccess:user.create');
            Route::post('/save', 'save')->name('user.save');
            Route::get('/{id}', 'edit')->name('user.edit')->middleware('checkAccess:user.edit');
            Route::delete('/{id}', 'delete')->name('user.delete')->middleware('checkAccess:user.delete');
        });

        Route::prefix('reports')->controller(ReportController::class)->group(function () {
            Route::get('/raw', 'rawReport')->name('reports.raw');
            Route::get('/overdue', 'overdueReport')->name('reports.overdue');
            Route::get('/export', 'export')->name('reports.export');
            Route::get('/ialertExport', 'ialertExport')->name('reports.ialertExport');
            Route::get('/ialertReport', 'ialertReport')->name('reports.ialertReport');
            Route::get('/overdueExport', 'overdueExport')->name('reports.overdueExport');
            Route::get('/monthly', 'monthlyReport')->name('reports.monthly');
            Route::get('/monthlyExport', 'monthlyExport')->name('reports.monthlyExport');
        });
    });
