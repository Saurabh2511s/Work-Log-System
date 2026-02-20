@extends('layouts.app')
@section('title','Apply Leave')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3>Apply Leave</h3>
  <a href="{{ route('leaves.index') }}" class="btn btn-secondary">Back</a>
</div>

<div class="card shadow-sm">
  <div class="card-body">
    <form method="POST" action="{{ route('leaves.store') }}" id="leaveForm">
      @csrf

      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label">Start Date</label>
          <input type="date" name="start_date" class="form-control" max="{{ now()->toDateString() }}" required>
        </div>

        <div class="col-md-4">
          <label class="form-label">End Date</label>
          <input type="date" name="end_date" class="form-control" max="{{ now()->toDateString() }}" required>
        </div>

        <div class="col-md-12">
          <label class="form-label">Reason (optional)</label>
          <textarea name="reason" class="form-control" maxlength="500"></textarea>
        </div>
      </div>

      <button class="btn btn-success mt-3">Submit Request</button>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
(function(){
  const f = document.getElementById('leaveForm');
  f.addEventListener('submit', (e)=>{
    const s = f.querySelector('[name="start_date"]').value;
    const en = f.querySelector('[name="end_date"]').value;
    if(s && en && en < s){
      e.preventDefault();
      alert('End date must be same or after start date.');
    }
  });
})();
</script>
@endpush