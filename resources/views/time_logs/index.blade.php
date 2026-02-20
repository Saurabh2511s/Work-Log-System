@extends('layouts.app')
@section('title','Time Logs')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h2 class="mb-0">Time Log Management</h2>
    <small class="text-muted">Manage your daily work log entries</small>
  </div>

  <a href="{{ route('time_logs.create', ['work_date' => $date]) }}" class="btn btn-primary">
    + Add New Log
  </a>
</div>

<div class="card shadow-sm mb-3">
  <div class="card-body">
    <form method="GET" action="{{ route('time_logs.index') }}" class="row g-2 align-items-end">
      <div class="col-auto">
        <label class="form-label mb-1">Filter by Date</label>
        <input type="date"
               name="work_date"
               class="form-control"
               value="{{ $date }}"
               max="{{ now()->toDateString() }}">
      </div>

      <div class="col-auto">
        <button class="btn btn-secondary">Filter</button>
      </div>

      <div class="col ms-auto text-end">
        <span class="badge bg-info text-dark p-2">
          Total:
          {{ str_pad(intdiv($totalMinutes,60),2,'0',STR_PAD_LEFT) }}h
          {{ str_pad($totalMinutes%60,2,'0',STR_PAD_LEFT) }}m
          / 10h 00m
        </span>
      </div>
    </form>
  </div>
</div>

<div class="card shadow-sm">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-striped mb-0">
        <thead class="table-light">
          <tr>
            <th>Date</th>
            <th>Project</th>
            <th>Task</th>
            <th>Duration</th>
            <th class="text-end" style="width:170px;">Action</th>
          </tr>
        </thead>

        <tbody>
          @forelse($logs as $log)
            @php
              $h = intdiv($log->minutes, 60);
              $m = $log->minutes % 60;
            @endphp
            <tr>
              <td>{{ \Illuminate\Support\Carbon::parse($log->work_date)->toDateString() }}</td>
              <td>{{ $log->project?->name }}</td>
              <td>{{ $log->description }}</td>
              <td>{{ str_pad($h,2,'0',STR_PAD_LEFT) }}h {{ str_pad($m,2,'0',STR_PAD_LEFT) }}m</td>
              <td class="text-end">
                <a href="{{ route('time_logs.edit',$log) }}" class="btn btn-sm btn-warning">Edit</a>

                <form action="{{ route('time_logs.destroy',$log) }}"
                      method="POST" class="d-inline"
                      onsubmit="return confirm('Delete this log?')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="text-center p-4 text-muted">
                No time logs found for selected date.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

<div class="d-flex justify-content-between align-items-center mt-3">
  <small class="text-muted">
    Showing {{ $logs->firstItem() ?? 0 }} to {{ $logs->lastItem() ?? 0 }}
    of {{ $logs->total() }} entries
  </small>

  <div>
    {{ $logs->onEachSide(1)->links() }}
  </div>
</div>
@endsection