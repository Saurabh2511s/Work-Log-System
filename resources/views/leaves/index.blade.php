@extends('layouts.app')
@section('title','Leaves')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h2>Leave Management</h2>
  <a href="{{ route('leaves.create') }}" class="btn btn-primary">Apply Leave</a>
</div>

<div class="card shadow-sm">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-striped mb-0">
        <thead class="table-light">
          <tr>
            <th>Start</th>
            <th>End</th>
            <th>Status</th>
            <th>Reason</th>
            <th style="width:160px;">Action</th>
          </tr>
        </thead>
        <tbody>
          @forelse($leaves as $leave)
            <tr>
              <td>{{ $leave->start_date->toDateString() }}</td>
              <td>{{ $leave->end_date->toDateString() }}</td>
              <td>
                <span class="badge bg-warning text-dark">{{ ucfirst($leave->status) }}</span>
              </td>
              <td>{{ $leave->reason }}</td>
              <td>
                @if($leave->status === 'pending')
                  <a href="{{ route('leaves.edit',$leave) }}" class="btn btn-sm btn-warning">Edit</a>

                  <form action="{{ route('leaves.destroy',$leave) }}" method="POST" class="d-inline"
                        onsubmit="return confirm('Delete this leave?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                  </form>
                @else
                  <small class="text-muted">Locked</small>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="text-center p-4 text-muted">No leave requests found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

<div class="d-flex justify-content-between align-items-center mt-3">
  <small class="text-muted">
    Showing {{ $leaves->firstItem() ?? 0 }} to {{ $leaves->lastItem() ?? 0 }} of {{ $leaves->total() }} entries
  </small>
  <div>
    {{ $leaves->onEachSide(1)->links() }}
  </div>
</div>
@endsection