<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Alumni\AuthCheckController;
use Illuminate\Support\Facades\Auth;

Route::controller(AuthCheckController::class)->group(function () {
    Route::get('/',  'index')->name('index');
});