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
        $alumniId = session('alumni.id');
        $alumni = Alumnis::findOrFail($alumniId);

        return view('alumni.dashboard.index', compact('alumni'));
    }
}
