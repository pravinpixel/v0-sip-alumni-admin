<?php

namespace App\Http\Controllers\Alumni;

use App\Http\Controllers\Controller;
use App\Models\Alumnis;
use App\Models\MobileOtp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $id = session('alumni.id');

        // Load fresh data from database
        $alumni = Alumnis::find($id) ?? null;
        $city = $alumni ? $alumni->city : null;
        $state = $city ? $city->state : null;
        $occupation = $alumni ? $alumni->occupation : null;

        return view('alumni.dashboard.index', compact('alumni', 'city', 'state', 'occupation'));
    }
}
