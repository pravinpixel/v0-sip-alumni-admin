<?php

namespace App\Http\Controllers\Alumni;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthCheckController extends Controller
{
    public function index(Request $request)
    {
         return view('alumni.login.login');
    }
}