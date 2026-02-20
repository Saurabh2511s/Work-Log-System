@extends('layouts.app')
@section('title', $leave ? 'Edit Leave' : 'Apply Leave')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h2>{{ $leave ? 'Edit Leave' : 'Apply Leave' }}</h2>
  <a href="{{ route('leaves.index') }}" class="btn btn-secondary">Back</a>
</div>

<div class="card shadow-sm">
  <div class="card-body">
    <form method="POST"
          action="{{ $leave ? route('leaves.update',$leave) : route('leaves.store') }}"
          id="leaveForm">
      @csrf
      @if($leave) @method('PUT') @endif

      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label">Start Date</label>
          <input type="date" name="start_date" id="start_date"
                 class="form-control"
                 value="{{ old('start_date', $leave?->start_date?->toDateString()) }}"
                 required>
        </div>

        <div class="col-md-4">
          <label class="form-label">End Date</label>
          <input type="date" name="end_date" id="end_date"
                 class="form-control"
                 value="{{ old('end_date', $leave?->end_date?->toDateString()) }}"
                 required>
        </div>

        <div class="col-md-12">
          <label class="form-label">Reason (optional)</label>
          <textarea name="reason" class="form-control" maxlength="500"
                    placeholder="Reason...">{{ old('reason', $leave?->reason) }}</textarea>
          <small class="text-muted">Max 500 chars</small>
        </div>
      </div>

      <button class="btn btn-primary mt-3">
        {{ $leave ? 'Update Leave' : 'Submit Request' }}
      </button>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
(function(){
  const form = document.getElementById('leaveForm');
  const start = document.getElementById('start_date');
  const end = document.getElementById('end_date');

  function validate(){
    if(!start.value || !end.value) return true;

    if(end.value < start.value){
      alert('End date cannot be before start date.');
      end.focus();
      return false;
    }
    return true;
  }

  form.addEventListener('submit', function(e){
    if(!validate()) e.preventDefault();
  });

  start.addEventListener('change', validate);
  end.addEventListener('change', validate);
})();
</script>
@endpush