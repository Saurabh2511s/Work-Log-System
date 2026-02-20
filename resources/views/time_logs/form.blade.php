@extends('layouts.app')
@section('title','Edit Time Log')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3>Edit Log</h3>
  <a href="{{ route('time_logs.index', ['work_date'=>$timeLog->work_date->toDateString()]) }}" class="btn btn-secondary">Back</a>
</div>

<div class="card shadow-sm">
  <div class="card-body">
    <form method="POST" action="{{ route('time_logs.update', $timeLog) }}" id="editForm">
      @csrf @method('PUT')

      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label">Work Date</label>
          <input type="date" name="work_date" class="form-control"
                 max="{{ now()->toDateString() }}"
                 value="{{ old('work_date',$timeLog->work_date->toDateString()) }}" required>
        </div>

        <div class="col-md-4">
          <label class="form-label">Project</label>
          <select name="project_id" class="form-select" required>
            <option value="">Select</option>
            @foreach($projects as $p)
              <option value="{{ $p->id }}" @selected(old('project_id',$timeLog->project_id)==$p->id)>
                {{ $p->name }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="col-md-4">
          <label class="form-label">Duration (HH:MM)</label>
          <div class="d-flex gap-2 align-items-center">
            <select name="hours" class="form-select" style="width:90px" required>
              @for($i=0;$i<=10;$i++)
                <option value="{{ $i }}" @selected((int)old('hours',$editHours)===$i)>{{ str_pad($i,2,'0',STR_PAD_LEFT) }}</option>
              @endfor
            </select>
            <span>:</span>
            <select name="minutes" class="form-select" style="width:90px" required>
              @foreach([0,5,10,15,20,25,30,35,40,45,50,55] as $m)
                <option value="{{ $m }}" @selected((int)old('minutes',$editMinutes)===$m)>{{ str_pad($m,2,'0',STR_PAD_LEFT) }}</option>
              @endforeach
            </select>
          </div>
          <small class="text-muted">0:00 not allowed</small>
        </div>

        <div class="col-md-12">
          <label class="form-label">Description</label>
          <textarea name="description" class="form-control" maxlength="500" required>{{ old('description',$timeLog->description) }}</textarea>
        </div>
      </div>

      <button class="btn btn-success mt-3">Update</button>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
(function(){
  const f = document.getElementById('editForm');

  f.addEventListener('submit', (e)=>{
    const h = parseInt(f.querySelector('[name="hours"]').value || '0', 10);
    const m = parseInt(f.querySelector('[name="minutes"]').value || '0', 10);
    const mins = h*60+m;

    if(mins <= 0){
      e.preventDefault();
      alert('Duration must be greater than 00:00');
      return;
    }
    if(mins > 600){
      e.preventDefault();
      alert('Task cannot exceed 10:00 hours');
      return;
    }
  });
})();
</script>
@endpush