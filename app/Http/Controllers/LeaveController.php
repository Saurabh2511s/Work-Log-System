<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\TimeLog;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LeaveController extends Controller
{
    public function index(Request $request)
    {
        $leaves = LeaveRequest::where('user_id', $request->user()->id)
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('leaves.index', compact('leaves'));
    }

    public function create()
    {
        return view('leaves.form', [
            'leave' => null
        ]);
    }

    public function store(Request $request)
    {
        $uid = $request->user()->id;

        $data = $request->validate([
            'start_date' => ['required','date','before_or_equal:end_date'],
            'end_date'   => ['required','date','after_or_equal:start_date'],
            'reason'     => ['nullable','string','max:500'],
        ]);

        $this->validateLeaveRules($uid, $data['start_date'], $data['end_date'], null);

        LeaveRequest::create([
            'user_id' => $uid,
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'reason' => $data['reason'] ?? null,
            'status' => 'pending',
        ]);

        return redirect()->route('leaves.index')->with('success', 'Leave request submitted.');
    }

    public function edit(LeaveRequest $leave, Request $request)
    {
        abort_if($leave->user_id !== $request->user()->id, 403);

        // You can restrict editing to pending only
        if ($leave->status !== 'pending') {
            return redirect()->route('leaves.index')->with('error', 'Only pending leaves can be edited.');
        }

        return view('leaves.form', compact('leave'));
    }

    public function update(LeaveRequest $leave, Request $request)
    {
        abort_if($leave->user_id !== $request->user()->id, 403);

        if ($leave->status !== 'pending') {
            return redirect()->route('leaves.index')->with('error', 'Only pending leaves can be edited.');
        }

        $data = $request->validate([
            'start_date' => ['required','date','before_or_equal:end_date'],
            'end_date'   => ['required','date','after_or_equal:start_date'],
            'reason'     => ['nullable','string','max:500'],
        ]);

        $this->validateLeaveRules($request->user()->id, $data['start_date'], $data['end_date'], $leave->id);

        $leave->update([
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'reason' => $data['reason'] ?? null,
        ]);

        return redirect()->route('leaves.index')->with('success', 'Leave updated successfully.');
    }

    public function destroy(LeaveRequest $leave, Request $request)
    {
        abort_if($leave->user_id !== $request->user()->id, 403);

        if ($leave->status !== 'pending') {
            return redirect()->route('leaves.index')->with('error', 'Only pending leaves can be deleted.');
        }

        $leave->delete();
        return redirect()->route('leaves.index')->with('success', 'Leave deleted successfully.');
    }

    /**
     * Senior-level validation for overlaps & conflicts
     */
    private function validateLeaveRules(int $userId, string $start, string $end, ?int $ignoreId): void
    {
        // 1) Block if work report exists in range
        $hasLogs = TimeLog::where('user_id', $userId)
            ->whereDate('work_date', '>=', $start)
            ->whereDate('work_date', '<=', $end)
            ->exists();

        if ($hasLogs) {
            throw ValidationException::withMessages([
                'start_date' => 'Cannot apply leave: Work report exists within selected range.',
            ]);
        }

        // 2) Prevent exact duplicate leave
        $duplicate = LeaveRequest::where('user_id', $userId)
            ->whereIn('status', ['pending','approved'])
            ->whereDate('start_date', $start)
            ->whereDate('end_date', $end);

        if ($ignoreId) $duplicate->where('id', '!=', $ignoreId);

        if ($duplicate->exists()) {
            throw ValidationException::withMessages([
                'start_date' => 'Same date leave already exists.',
            ]);
        }

        // 3) Prevent overlapping ranges (classic overlap formula)
        // overlap if: existing.start <= new.end AND existing.end >= new.start
        $overlap = LeaveRequest::where('user_id', $userId)
            ->whereIn('status', ['pending','approved'])
            ->whereDate('start_date', '<=', $end)
            ->whereDate('end_date', '>=', $start);

        if ($ignoreId) $overlap->where('id', '!=', $ignoreId);

        if ($overlap->exists()) {
            throw ValidationException::withMessages([
                'start_date' => 'Leave overlaps with an existing leave request.',
            ]);
        }
    }
}