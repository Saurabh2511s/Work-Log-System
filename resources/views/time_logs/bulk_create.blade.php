@extends('layouts.app')
@section('title','Add Time Logs')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3>Log Daily Tasks</h3>
  <a href="{{ route('time_logs.index', ['work_date'=>$workDate]) }}" class="btn btn-secondary">Back</a>
</div>

<div class="card shadow-sm">
  <div class="card-body">
    <form method="POST" action="{{ route('time_logs.store') }}" id="bulkForm">
      @csrf

      <div class="mb-3">
        <label class="form-label">Work Date</label>
        <input type="date" name="work_date" id="work_date"
               max="{{ now()->toDateString() }}"
               value="{{ old('work_date', $workDate) }}"
               class="form-control" style="max-width: 260px;" required>
        <small class="text-muted">Changing date updates “Already logged” time.</small>
      </div>

      <div class="d-flex justify-content-between align-items-center mb-2">
        <h5 class="mb-0">Tasks</h5>
        <button type="button" class="btn btn-outline-primary" id="addRowBtn">+ Add Task Row</button>
      </div>

      <div class="table-responsive">
        <table class="table table-bordered" id="taskTable">
          <thead class="table-light">
            <tr>
              <th style="min-width:200px;">Project</th>
              <th>Description</th>
              <th style="min-width:240px;">Duration (HH:MM)</th>
              <th style="width:90px;">Remove</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>

      <div class="d-flex justify-content-between align-items-center">
        <div class="alert alert-info py-2 mb-0" style="min-width:420px;">
          <div><b>Already Logged:</b> <span id="alreadyDisplay">00h 00m</span></div>
          <div><b>New Entries:</b> <span id="newDisplay">00h 00m</span></div>
          <div class="mt-1"><b>Grand Total:</b> <span id="grandDisplay">00h 00m</span> / 10h 00m</div>
        </div>

        <button class="btn btn-success" id="saveBtn">Save All Logs</button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
(function(){
  const projects = @json($projects->map(fn($p)=>['id'=>$p->id,'name'=>$p->name])->values());

  // ✅ existing time from server
  const existingMinutes = parseInt(@json($existingMinutes), 10) || 0;

  const tbody = document.querySelector('#taskTable tbody');

  const alreadyDisplay = document.getElementById('alreadyDisplay');
  const newDisplay     = document.getElementById('newDisplay');
  const grandDisplay   = document.getElementById('grandDisplay');
  const saveBtn        = document.getElementById('saveBtn');
  const workDateInput  = document.getElementById('work_date');

  function minutesToHuman(mins){
    const h = Math.floor(mins/60);
    const m = mins%60;
    return String(h).padStart(2,'0')+'h '+String(m).padStart(2,'0')+'m';
  }

  function calcNewMinutes(){
    let total = 0;
    tbody.querySelectorAll('tr.task-row').forEach(row => {
      const h = parseInt(row.querySelector('.hour-select').value || '0', 10);
      const m = parseInt(row.querySelector('.minute-select').value || '0', 10);
      total += (h * 60 + m);
    });
    return total;
  }

  function recalc(){
    const newM = calcNewMinutes();
    const grand = existingMinutes + newM;

    alreadyDisplay.textContent = minutesToHuman(existingMinutes);
    newDisplay.textContent     = minutesToHuman(newM);
    grandDisplay.textContent   = minutesToHuman(grand);

    if(grand > 600){
      grandDisplay.classList.add('text-danger');
      saveBtn.disabled = true;
    }else{
      grandDisplay.classList.remove('text-danger');
      saveBtn.disabled = false;
    }
  }

  function reindex(){
    [...tbody.querySelectorAll('tr.task-row')].forEach((row,i)=>{
      row.querySelector('select.project-select').name = `tasks[${i}][project_id]`;
      row.querySelector('input.desc-input').name     = `tasks[${i}][description]`;
      row.querySelector('select.hour-select').name   = `tasks[${i}][hours]`;
      row.querySelector('select.minute-select').name = `tasks[${i}][minutes]`;
    });
  }

  function addRow(){
    const idx = tbody.children.length;
    const tr = document.createElement('tr');
    tr.className = 'task-row';

    tr.innerHTML = `
      <td>
        <select class="form-select project-select" name="tasks[${idx}][project_id]" required>
          <option value="">Select</option>
          ${projects.map(p=>`<option value="${p.id}">${p.name}</option>`).join('')}
        </select>
      </td>

      <td>
        <input class="form-control desc-input" name="tasks[${idx}][description]" maxlength="500" required placeholder="Task details...">
      </td>

      <td>
        <div class="d-flex gap-2 align-items-center">
          <select class="form-select hour-select" name="tasks[${idx}][hours]" style="width:90px" required>
            ${Array.from({length: 11}, (_,i)=> `<option value="${i}">${String(i).padStart(2,'0')}</option>`).join('')}
          </select>
          <span>:</span>
          <select class="form-select minute-select" name="tasks[${idx}][minutes]" style="width:90px" required>
            ${[0,5,10,15,20,25,30,35,40,45,50,55].map(v=> `<option value="${v}">${String(v).padStart(2,'0')}</option>`).join('')}
          </select>
          <small class="text-muted ms-2">0:00 not allowed</small>
        </div>
        <small class="text-danger time-error"></small>
      </td>

      <td class="text-center">
        <button type="button" class="btn btn-sm btn-outline-danger removeBtn">X</button>
      </td>
    `;

    tbody.appendChild(tr);
    recalc();
  }

  // ✅ if user changes date, reload page so existingMinutes updates
  workDateInput.addEventListener('change', ()=>{
    const d = workDateInput.value;
    if(!d) return;
    const url = new URL(window.location.href);
    url.searchParams.set('work_date', d);
    window.location.href = url.toString();
  });

  document.getElementById('addRowBtn').addEventListener('click', () => {
    addRow();
    reindex();
  });

  tbody.addEventListener('change', recalc);

  tbody.addEventListener('click', (e)=>{
    if(e.target.classList.contains('removeBtn')){
      e.target.closest('tr').remove();
      reindex();
      recalc();
    }
  });

  document.getElementById('bulkForm').addEventListener('submit', (e)=>{
    if(tbody.children.length === 0){
      e.preventDefault();
      alert('Add at least one task row.');
      return;
    }

    let newTotal = 0;

    for(const row of [...tbody.querySelectorAll('tr.task-row')]){
      const h = parseInt(row.querySelector('.hour-select').value || '0', 10);
      const m = parseInt(row.querySelector('.minute-select').value || '0', 10);
      const mins = (h*60 + m);

      row.querySelector('.time-error').textContent = '';

      if(mins <= 0){
        e.preventDefault();
        row.querySelector('.time-error').textContent = '0:00 duration not allowed';
        alert('Time must be greater than 00:00');
        return;
      }

      if(mins > 600){
        e.preventDefault();
        row.querySelector('.time-error').textContent = 'Single task cannot exceed 10:00 hours';
        alert('Single task cannot exceed 10:00 hours');
        return;
      }

      newTotal += mins;
    }

    if(existingMinutes + newTotal > 600){
      e.preventDefault();
      alert('Daily work limit reached. Existing + new cannot exceed 10 hours.');
      return;
    }
  });

  // default row
  addRow();
  alreadyDisplay.textContent = minutesToHuman(existingMinutes);
  recalc();
})();
</script>
@endpush