@extends('layouts.app')
@section('title','Dashboard')

@section('content')
<div class="mb-4">
  <h3>Welcome, {{ auth()->user()->name }}!</h3>
  <p class="text-muted mb-0">Manage your work logs and leave requests from here.</p>
</div>

<div class="row g-3">
  <div class="col-md-6">
    <div class="card shadow-sm">
      <div class="card-body">
        <h5 class="card-title">Log Time</h5>
        <p class="text-muted">Record your daily tasks and hours.</p>
        <div class="d-flex justify-content-between">
          <span>Total Logs:</span>
          <b>{{ $timeLogCount }}</b>
        </div>
        <a href="{{ route('time_logs.index') }}" class="btn btn-primary mt-3">Open</a>
      </div>
    </div>
  </div>

  <div class="col-md-6">
    <div class="card shadow-sm">
      <div class="card-body">
        <h5 class="card-title">Apply For Leave</h5>
        <p class="text-muted">Request time off or check your leave status.</p>
        <div class="d-flex justify-content-between">
          <span>Pending/Approved:</span>
          <b>{{ $leaveCount }}</b>
        </div>
        <a href="{{ route('leaves.index') }}" class="btn btn-primary mt-3">Open</a>
      </div>
    </div>
  </div>
</div>
@endsection