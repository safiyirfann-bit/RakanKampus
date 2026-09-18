<!DOCTYPE html>
<html lang="ms">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>RakanKampus - Reminders</title>
<style>
  * { box-sizing: border-box; }

  html, body {
    margin: 0;
    min-height: 100%;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
  }

  body {
    background: linear-gradient(160deg, #14213d, #1b3a5c 55%, #2ec4c6);
    background-attachment: fixed;
    min-height: 100vh;
  }

  .container {
    max-width: 640px;
    margin: 0 auto;
    padding: 20px 18px 80px;
  }

  .header {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 8px 0 18px;
  }

  .back-btn {
    width: 38px;
    height: 38px;
    min-width: 38px;
    border-radius: 50%;
    background: rgba(255,255,255,0.12);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    text-decoration: none;
  }

  .back-btn svg { width: 18px; height: 18px; stroke: currentColor; }

  .header-title { font-size: 18px; font-weight: 800; color: #fff; margin: 0; }
  .header-sub { font-size: 12.5px; color: #bfe9ea; margin: 2px 0 0; }

  .add-btn {
    width: 100%;
    box-sizing: border-box;
    background: linear-gradient(120deg, #a78bfa, #2ec4c6);
    color: #fff;
    border: none;
    border-radius: 12px;
    padding: 14px;
    font-size: 14px;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    cursor: pointer;
    margin-bottom: 16px;
    box-shadow: 0 10px 22px rgba(0,0,0,0.22);
  }

  .add-btn svg { width: 16px; height: 16px; }

  .search-row {
    display: flex;
    align-items: center;
    gap: 8px;
    background: rgba(255,255,255,0.12);
    border-radius: 12px;
    padding: 11px 14px;
    margin-bottom: 16px;
  }

  .search-row svg { width: 16px; height: 16px; stroke: #bfe9ea; flex-shrink: 0; }

  .search-row input {
    flex: 1;
    min-width: 0;
    background: none;
    border: none;
    outline: none;
    color: #fff;
    font-size: 13.5px;
  }

  .search-row input::placeholder { color: rgba(255,255,255,0.6); }

  .section-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin: 0 2px 12px;
  }

  .section-label {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.08em;
    color: #bfe9ea;
    text-transform: uppercase;
    margin: 0;
  }

  .reminder-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
  }

  .reminder-card {
    background: #fdf2ee;
    border-radius: 16px;
    padding: 14px 16px;
    display: flex;
    align-items: flex-start;
    gap: 12px;
    box-shadow: 0 4px 14px rgba(0,0,0,0.08);
  }

  .reminder-dot {
    width: 40px;
    height: 40px;
    min-width: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .reminder-dot svg { width: 18px; height: 18px; }

  .reminder-body { flex: 1; min-width: 0; }

  .reminder-type {
    display: inline-block;
    font-size: 10.5px;
    font-weight: 700;
    border-radius: 7px;
    padding: 2px 8px;
    margin-bottom: 6px;
  }

  .reminder-subject {
    font-size: 14px;
    font-weight: 800;
    color: #14213d;
    margin: 0 0 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .reminder-when { font-size: 11.5px; color: #94a3b8; margin: 0 0 4px; }
  .reminder-status { font-size: 11.5px; font-weight: 700; margin: 0; }

  .reminder-actions {
    display: flex;
    align-items: center;
    gap: 5px;
    flex-shrink: 0;
  }

  .reminder-action-btn {
    background: none;
    border: none;
    color: #94a3b8;
    cursor: pointer;
    padding: 3px;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .reminder-action-btn:hover { color: #4c1d95; }

  .reminder-action-btn svg { width: 14px; height: 14px; }

  .empty-state {
    text-align: center;
    padding: 34px 18px;
    background: rgba(255,255,255,0.1);
    border-radius: 16px;
    color: #bfe9ea;
    font-size: 13px;
  }

  /* Modal */
  .overlay {
    position: fixed;
    inset: 0;
    background: rgba(15,23,42,0.5);
    z-index: 40;
    display: none;
  }

  .overlay.open { display: block; }

  .modal {
    position: fixed;
    left: 16px;
    right: 16px;
    top: 50%;
    max-width: 420px;
    margin: 0 auto;
    transform: translateY(-50%) scale(0.94);
    opacity: 0;
    pointer-events: none;
    background: #fff;
    border-radius: 20px;
    padding: 22px 22px 24px;
    box-shadow: 0 24px 60px rgba(0,0,0,0.35);
    z-index: 41;
    max-height: 82vh;
    overflow-y: auto;
    transition: transform 0.2s ease, opacity 0.2s ease;
  }

  .modal.open {
    transform: translateY(-50%) scale(1);
    opacity: 1;
    pointer-events: auto;
  }

  .modal-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 16px;
  }

  .modal-title { font-size: 16px; font-weight: 800; color: #14213d; margin: 0; }

  .modal-close {
    background: #f1f5f9;
    border: none;
    border-radius: 50%;
    width: 28px;
    height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #64748b;
    cursor: pointer;
    font-size: 16px;
  }

  .field-label { font-size: 11px; font-weight: 700; color: #64748b; margin: 0 0 6px; display: block; }

  .modal input[type="text"],
  .modal input[type="datetime-local"],
  .modal input[type="number"] {
    width: 100%;
    box-sizing: border-box;
    border: 1px solid #dbe4ea;
    border-radius: 10px;
    padding: 11px 14px;
    font-size: 13.5px;
    color: #14213d;
    outline: none;
    margin-bottom: 12px;
  }

  .type-row {
    display: flex;
    gap: 8px;
    margin-bottom: 14px;
  }

  .type-opt {
    flex: 1;
    background: #fff;
    border: 1px solid #dbe4ea;
    color: #334155;
    border-radius: 8px;
    padding: 8px 4px;
    font-size: 11px;
    font-weight: 700;
    cursor: pointer;
  }

  .type-opt.active { color: #fff; border-color: transparent; }

  .modal-save {
    width: 100%;
    background: linear-gradient(120deg, #14213d, #2ec4c6);
    color: #fff;
    border: none;
    border-radius: 10px;
    padding: 13px;
    font-size: 13.5px;
    font-weight: 700;
    cursor: pointer;
  }

  .hint { font-size: 10.5px; color: #94a3b8; margin: -6px 0 12px; }
</style>
</head>
<body>

<div class="container">
  <div class="header">
    <a href="{{ route('student.home') }}" class="back-btn" aria-label="Back">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"></path></svg>
    </a>
    <div>
      <p class="header-title">Reminders</p>
      <p class="header-sub">For exams, assignments &amp; deadlines</p>
    </div>
  </div>

  <button type="button" class="add-btn" onclick="openAddModal()">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
    Add Reminder
  </button>

  <div class="search-row">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
    <input type="text" id="searchInput" placeholder="Search reminders..." oninput="filterReminders()">
  </div>

  <div class="section-row">
    <p class="section-label" id="countLabel">UPCOMING ({{ count($reminders) }})</p>
  </div>

  <div class="reminder-list" id="reminderList">
    @forelse($reminders as $r)
      <div class="reminder-card" data-id="{{ $r['id'] }}" data-search="{{ strtolower($r['subject']) }}">
        <div class="reminder-dot" style="background: {{ $r['dot_bg'] }};">
          <svg viewBox="0 0 24 24" fill="none" stroke="{{ $r['dot_stroke'] }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v5l3 3"></path></svg>
        </div>
        <div class="reminder-body">
          <span class="reminder-type" style="color: {{ $r['type_color'] }}; background: {{ $r['type_bg'] }};">{{ $r['type'] }}</span>
          <p class="reminder-subject">{{ $r['subject'] }}</p>
          <p class="reminder-when">{{ $r['when_text'] }} · notify {{ $r['lead_label'] }} before</p>
          <p class="reminder-status" style="color: {{ $r['status_color'] }};">{{ $r['status_text'] }}</p>
        </div>
        <div class="reminder-actions">
          <button type="button" class="reminder-action-btn" aria-label="Edit"
            onclick='openEditModal(@json($r))'>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.12 2.12 0 0 1 3 3L12 15l-4 1 1-4Z"></path></svg>
          </button>
          <button type="button" class="reminder-action-btn" aria-label="Delete" onclick="deleteReminder({{ $r['id'] }})">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
          </button>
        </div>
      </div>
    @empty
      <div class="empty-state" id="emptyState">No reminders yet — tap "Add Reminder" to add your first exam, assignment or deadline.</div>
    @endforelse
  </div>
</div>

<div class="overlay" id="overlay" onclick="closeModal()"></div>

<div class="modal" id="modal">
  <div class="modal-head">
    <p class="modal-title" id="modalTitle">Add Reminder</p>
    <button type="button" class="modal-close" aria-label="Close" onclick="closeModal()">×</button>
  </div>
  <input type="hidden" id="reminderId">
  <input type="text" id="subjectInput" placeholder="e.g. Software Engineering Assignment 2">
  <p class="field-label">Type</p>
  <div class="type-row" id="typeRow">
    @foreach(['Exam' => '#6366f1', 'Assignment' => '#0d9488', 'Quiz' => '#7c3aed', 'Other' => '#64748b'] as $t => $color)
      <button type="button" class="type-opt" data-type="{{ $t }}" data-color="{{ $color }}" onclick="selectType('{{ $t }}')">{{ $t }}</button>
    @endforeach
  </div>
  <input type="datetime-local" id="dueInput">
  <p class="field-label">Notify me how many hours before?</p>
  <input type="number" id="leadInput" min="0" step="0.5" value="1">
  <p class="hint">Type any number of hours — use 0.5 for 30 minutes.</p>
  <p id="formError" style="color:#e11d48; font-size: 11.5px; display:none; margin: -6px 0 10px;">Please fill in a subject and date.</p>
  <button type="button" class="modal-save" onclick="saveReminder()">Save Reminder</button>
</div>

<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
let selectedType = 'Exam';

function openAddModal() {
  document.getElementById('modalTitle').textContent = 'Add Reminder';
  document.getElementById('reminderId').value = '';
  document.getElementById('subjectInput').value = '';
  document.getElementById('dueInput').value = '';
  document.getElementById('leadInput').value = '1';
  selectType('Exam');
  showModal();
}

function openEditModal(reminder) {
  document.getElementById('modalTitle').textContent = 'Edit Reminder';
  document.getElementById('reminderId').value = reminder.id;
  document.getElementById('subjectInput').value = reminder.subject;
  document.getElementById('dueInput').value = reminder.due_at_input;
  document.getElementById('leadInput').value = reminder.lead_hours;
  selectType(reminder.type);
  showModal();
}

function selectType(type) {
  selectedType = type;
  document.querySelectorAll('.type-opt').forEach(btn => {
    if (btn.dataset.type === type) {
      btn.classList.add('active');
      btn.style.background = btn.dataset.color;
      btn.style.borderColor = btn.dataset.color;
    } else {
      btn.classList.remove('active');
      btn.style.background = '#fff';
      btn.style.borderColor = '#dbe4ea';
    }
  });
}

function showModal() {
  document.getElementById('formError').style.display = 'none';
  document.getElementById('overlay').classList.add('open');
  document.getElementById('modal').classList.add('open');
}

function closeModal() {
  document.getElementById('overlay').classList.remove('open');
  document.getElementById('modal').classList.remove('open');
}

function saveReminder() {
  const id = document.getElementById('reminderId').value;
  const subject = document.getElementById('subjectInput').value.trim();
  const due = document.getElementById('dueInput').value;
  const lead = document.getElementById('leadInput').value || '1';

  if (!subject || !due) {
    document.getElementById('formError').style.display = 'block';
    return;
  }

  const url = id ? `/reminders/${id}` : '/reminders';
  const method = id ? 'PUT' : 'POST';

  fetch(url, {
    method,
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': csrfToken,
    },
    body: JSON.stringify({
      subject,
      type: selectedType,
      due_at: due,
      lead_hours: parseFloat(lead) || 1,
    }),
  })
    .then(res => res.json())
    .then(() => {
      closeModal();
      window.location.reload();
    })
    .catch(err => console.error('Save failed', err));
}

function deleteReminder(id) {
  if (!confirm('Delete this reminder?')) return;

  fetch(`/reminders/${id}`, {
    method: 'DELETE',
    headers: { 'X-CSRF-TOKEN': csrfToken },
  })
    .then(res => res.json())
    .then(() => {
      const card = document.querySelector(`.reminder-card[data-id="${id}"]`);
      if (card) card.remove();
    })
    .catch(err => console.error('Delete failed', err));
}

function filterReminders() {
  const q = document.getElementById('searchInput').value.trim().toLowerCase();
  document.querySelectorAll('.reminder-card').forEach(card => {
    card.style.display = card.dataset.search.includes(q) ? '' : 'none';
  });
}
</script>

</body>
</html>
