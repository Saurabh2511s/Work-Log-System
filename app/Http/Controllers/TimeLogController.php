<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\TimeLog;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TimeLogController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->get('work_date', now()->toDateString());

        $logs = TimeLog::with('project')
            ->where('user_id', $request->user()->id)
            ->whereDate('work_date', $date)
            ->orderByDesc('id')
            ->paginate(5)
            ->withQueryString();

        $totalMinutes = TimeLog::where('user_id', $request->user()->id)
            ->whereDate('work_date', $date)
            ->sum('minutes');

        return view('time_logs.index', compact('logs','date','totalMinutes'));
    }

    public function create(Request $request)
    {
        $projects = Project::where('is_active', true)->orderBy('name')->get();

        // ✅ default date from query OR today
        $workDate = $request->get('work_date', now()->toDateString());

        // ✅ existing time already logged for that date
        $existingMinutes = TimeLog::where('user_id', $request->user()->id)
            ->whereDate('work_date', $workDate)
            ->sum('minutes');

        return view('time_logs.bulk_create', compact('projects','workDate','existingMinutes'));
    }

    public function store(Request $request)
    {
        $uid = $request->user()->id;

        $validated = $request->validate([
            'work_date' => ['required','date','before_or_equal:today'],
            'tasks' => ['required','array','min:1'],
            'tasks.*.project_id' => ['required','exists:projects,id'],
            'tasks.*.description' => ['required','string','max:500'],
            'tasks.*.hours' => ['required','integer','min:0','max:10'],
            'tasks.*.minutes' => ['required','integer','min:0','max:59'],
        ]);

        $workDate = $validated['work_date'];

        // Block if leave exists for the date
        $hasLeave = LeaveRequest::where('user_id', $uid)
            ->whereIn('status', ['pending','approved'])
            ->whereDate('start_date', '<=', $workDate)
            ->whereDate('end_date', '>=', $workDate)
            ->exists();

        if ($hasLeave) {
            throw ValidationException::withMessages([
                'work_date' => 'Cannot log work: A leave is already submitted for this date.',
            ]);
        }

        $rows = [];
        $newTotal = 0;

        foreach ($validated['tasks'] as $i => $t) {
            $hours = (int) $t['hours'];
            $mins  = (int) $t['minutes'];
            $minutes = ($hours * 60) + $mins;

            if ($minutes <= 0) {
                throw ValidationException::withMessages([
                    "tasks.$i.hours" => 'Duration must be greater than 00:00.',
                ]);
            }

            if ($minutes > 600) {
                throw ValidationException::withMessages([
                    "tasks.$i.hours" => 'A task cannot exceed 10:00 hours.',
                ]);
            }

            $newTotal += $minutes;

            $rows[] = [
                'user_id' => $uid,
                'work_date' => $workDate,
                'project_id' => $t['project_id'],
                'description' => $t['description'],
                'minutes' => $minutes,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // ✅ existing + new
        $existing = TimeLog::where('user_id', $uid)
            ->whereDate('work_date', $workDate)
            ->sum('minutes');

        if (($existing + $newTotal) > 600) {
            throw ValidationException::withMessages([
                'tasks' => 'Daily work limit reached. Total cannot exceed 10 hours.',
            ]);
        }

        DB::transaction(fn() => TimeLog::insert($rows));

        return redirect()
            ->route('time_logs.index', ['work_date' => $workDate])
            ->with('success', 'Logs saved successfully.');
    }

    public function edit(TimeLog $timeLog, Request $request)
    {
        abort_if($timeLog->user_id !== $request->user()->id, 403);

        $projects = Project::where('is_active', true)->orderBy('name')->get();

        $editHours = intdiv($timeLog->minutes, 60);
        $editMinutes = $timeLog->minutes % 60;

        return view('time_logs.form', compact('projects','timeLog','editHours','editMinutes'));
    }

    public function update(TimeLog $timeLog, Request $request)
    {
        abort_if($timeLog->user_id !== $request->user()->id, 403);

        $data = $request->validate([
            'work_date' => ['required','date','before_or_equal:today'],
            'project_id' => ['required','exists:projects,id'],
            'description' => ['required','string','max:500'],
            'hours' => ['required','integer','min:0','max:10'],
            'minutes' => ['required','integer','min:0','max:59'],
        ]);

        $uid = $request->user()->id;
        $workDate = $data['work_date'];

        $hasLeave = LeaveRequest::where('user_id', $uid)
            ->whereIn('status', ['pending','approved'])
            ->whereDate('start_date', '<=', $workDate)
            ->whereDate('end_date', '>=', $workDate)
            ->exists();

        if ($hasLeave) {
            throw ValidationException::withMessages([
                'work_date' => 'Cannot log work: A leave is already submitted for this date.',
            ]);
        }

        $minutes = ((int)$data['hours'] * 60) + (int)$data['minutes'];

        if ($minutes <= 0) {
            throw ValidationException::withMessages([
                'hours' => 'Duration must be greater than 00:00.',
            ]);
        }

        if ($minutes > 600) {
            throw ValidationException::withMessages([
                'hours' => 'A task cannot exceed 10:00 hours.',
            ]);
        }

        $other = TimeLog::where('user_id', $uid)
            ->whereDate('work_date', $workDate)
            ->where('id', '!=', $timeLog->id)
            ->sum('minutes');

        if (($other + $minutes) > 600) {
            throw ValidationException::withMessages([
                'hours' => 'Daily work limit reached. Total cannot exceed 10 hours.',
            ]);
        }

        $timeLog->update([
            'work_date' => $workDate,
            'project_id' => $data['project_id'],
            'description' => $data['description'],
            'minutes' => $minutes,
        ]);

        return redirect()
            ->route('time_logs.index', ['work_date' => $workDate])
            ->with('success', 'Log updated successfully.');
    }

    public function destroy(TimeLog $timeLog, Request $request)
    {
        abort_if($timeLog->user_id !== $request->user()->id, 403);

        $date = $timeLog->work_date->toDateString();
        $timeLog->delete();

        return redirect()
            ->route('time_logs.index', ['work_date' => $date])
            ->with('success', 'Log deleted.');
    }
}