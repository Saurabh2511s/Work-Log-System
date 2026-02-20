<?php

namespace App\Http\Controllers;

use App\Models\TimeLog;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $uid = $request->user()->id;

        $timeLogCount = TimeLog::where('user_id', $uid)->count();
        $leaveCount = LeaveRequest::where('user_id', $uid)->whereIn('status', ['pending','approved'])->count();

        return view('dashboard', compact('timeLogCount', 'leaveCount'));
    }
}