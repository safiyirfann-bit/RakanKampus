<!DOCTYPE html>
<html lang="ms">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
@include('partials.pwa-head')
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>RakanKampus - Timetable</title>
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
    padding: 20px 18px 110px;
    position: relative;
  }

  .header { display: flex; align-items: center; gap: 12px; padding: 8px 0 18px; }

  .back-btn {
    width: 38px; height: 38px; min-width: 38px; border-radius: 50%;
    background: rgba(255,255,255,0.12);
    display: flex; align-items: center; justify-content: center;
    color: #fff; text-decoration: none;
  }
  .back-btn svg { width: 18px; height: 18px; stroke: currentColor; }

  .header-title { font-size: 18px; font-weight: 800; color: #fff; margin: 0; }
  .header-sub { font-size: 12.5px; color: #bfe9ea; margin: 2px 0 0; }

  .add-btn {
    width: 100%; box-sizing: border-box;
    background: linear-gradient(120deg, #14213d, #2ec4c6);
    color: #fff; border: none; border-radius: 12px; padding: 14px;
    font-size: 14px; font-weight: 800;
    display: flex; align-items: center; justify-content: center; gap: 8px;
    cursor: pointer; margin-bottom: 20px;
    box-shadow: 0 10px 22px rgba(0,0,0,0.22);
  }
  .add-btn svg { width: 16px; height: 16px; }

  .day-section { margin-bottom: 18px; }
  .day-label {
    font-size: 11.5px; font-weight: 800; letter-spacing: 0.06em;
    color: #bfe9ea; text-transform: uppercase; margin: 0 0 8px 2px;
  }

  .class-list { display: flex; flex-direction: column; gap: 10px; }

  .class-card {
    background: #fdf2ee; border-radius: 16px; padding: 13px 14px;
    display: flex; align-items: flex-start; gap: 12px;
  }

  .class-time {
    flex-shrink: 0; width: 62px; text-align: center;
    font-size: 11.5px; font-weight: 800; color: #14213d;
    background: #ffffff; border-radius: 10px; padding: 6px 4px; line-height: 1.35;
  }

  .class-body { flex: 1; min-width: 0; }
  .class-subject { font-size: 13.5px; font-weight: 800; color: #14213d; margin: 0 0 2px; }
  .class-meta { font-size: 11.5px; color: #64748b; margin: 0; }

  .class-actions { display: flex; gap: 6px; flex-shrink: 0; }
  .class-action-btn {
    width: 28px; height: 28px; border-radius: 8px; border: none;
    background: rgba(20,33,61,0.06); color: #14213d;
    display: flex; align-items: center; justify-content: center; cursor: pointer;
  }
  .class-action-btn svg { width: 13px; height: 13px; stroke: currentColor; }

  .empty-day { font-size: 12px; color: rgba(255,255,255,0.55); padding: 2px 2px 0; }

  .ai-fab {
    position: fixed; right: 20px; bottom: 88px; z-index: 30;
    background: #ffffff; border: none; border-radius: 999px;
    padding: 11px 16px 11px 12px; display: flex; align-items: center; gap: 7px;
    box-shadow: 0 10px 24px rgba(0,0,0,0.3); cursor: pointer;
  }
  .ai-fab svg { width: 16px; height: 16px; }
  .ai-fab span { font-size: 11.5px; font-weight: 800; color: #14213d; letter-spacing: 0.01em; }

  .overlay {
    position: fixed; inset: 0; background: rgba(15,23,42,0.5);
    opacity: 0; pointer-events: none; transition: opacity 0.2s ease; z-index: 40;
  }
  .overlay.open { opacity: 1; pointer-events: auto; }

  .modal {
    position: fixed; left: 16px; right: 16px; top: 50%; max-width: 420px; margin: 0 auto;
    transform: translateY(-50%) scale(0.94); opacity: 0; pointer-events: none;
    background: #fff; border-radius: 20px; padding: 22px 22px 24px;
    box-shadow: 0 24px 60px rgba(0,0,0,0.35); z-index: 41;
    max-height: 82vh; overflow-y: auto;
    transition: transform 0.2s ease, opacity 0.2s ease;
  }
  .modal.open { transform: translateY(-50%) scale(1); opacity: 1; pointer-events: auto; }

  .modal-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; }
  .modal-title-row { display: flex; align-items: center; gap: 10px; }
  .modal-icon {
    width: 30px; height: 30px; border-radius: 9px;
    background: linear-gradient(120deg, #14213d, #2ec4c6);
    display: flex; align-items: center; justify-content: center;
  }
  .modal-icon svg { width: 15px; height: 15px; }
  .modal-title { font-size: 15px; font-weight: 800; color: #14213d; margin: 0; }
  .modal-close { width: 26px; height: 26px; border: none; background: #f1f5f9; border-radius: 8px; color: #64748b; font-size: 15px; cursor: pointer; }

  .field-label { font-size: 11.5px; font-weight: 700; color: #64748b; margin: 12px 0 6px; }
  .modal input[type="text"], .modal input[type="time"], .modal select {
    width: 100%; box-sizing: border-box; border: 1.5px solid #e2e8f0; border-radius: 10px;
    padding: 11px 12px; font-size: 13.5px; color: #14213d; outline: none; font-family: inherit;
  }
  .modal input:focus, .modal select:focus { border-color: #2ec4c6; }

  .time-row { display: flex; gap: 10px; }
  .time-row > div { flex: 1; }

  .save-btn {
    width: 100%; margin-top: 18px; background: linear-gradient(120deg, #14213d, #2ec4c6);
    color: #fff; border: none; border-radius: 12px; padding: 13px; font-size: 13.5px;
    font-weight: 800; cursor: pointer;
  }

  .form-error { display: none; color: #dc2626; font-size: 11.5px; margin-top: 8px; }

  .ai-desc { font-size: 12.5px; color: #64748b; margin: 0 0 16px; line-height: 1.4; }
  .ai-upload-label {
    width: 100%; box-sizing: border-box; background: #eef2ff; border: 1.5px dashed #a5b4fc;
    color: #4338ca; border-radius: 10px; padding: 16px; font-size: 13px; font-weight: 700;
    display: flex; align-items: center; justify-content: center; gap: 8px; cursor: pointer;
  }
  .ai-upload-label svg { width: 16px; height: 16px; }
  .ai-upload-label input { display: none; }

  .ai-scanning { font-size: 12px; color: #4338ca; margin: 14px 0 0; text-align: center; display: none; }
  .ai-scanning.open { display: block; }

  .ai-success, .ai-error {
    margin-top: 14px; border-radius: 10px; padding: 10px 12px; display: none;
    align-items: flex-start; justify-content: space-between; gap: 8px;
  }
  .ai-success { background: #f0fdf4; border: 1px solid #bbf7d0; }
  .ai-success.open { display: flex; }
  .ai-success p { font-size: 11.5px; color: #15803d; margin: 0; line-height: 1.4; }
  .ai-success button { background: none; border: none; color: #15803d; font-size: 14px; cursor: pointer; padding: 0; flex-shrink: 0; }

  .ai-error { background: #fef2f2; border: 1px solid #fecaca; }
  .ai-error.open { display: flex; }
  .ai-error p { font-size: 11.5px; color: #b91c1c; margin: 0; line-height: 1.4; }
  .ai-error button { background: none; border: none; color: #b91c1c; font-size: 14px; cursor: pointer; padding: 0; flex-shrink: 0; }

  @media (min-width: 861px) {
    body { background: #f0fafa; }
    .container { max-width: 760px; margin: 0; padding: 36px 44px 90px; }
    .back-btn { display: none; }
    .header-title { color: #14213d; }
    .header-sub { color: #64748b; }
    .add-btn { width: auto; padding: 12px 22px; }
    .day-label { color: #64748b; }
    .class-card { background: #ffffff; border: 1.5px solid #dbeeee; }
    .class-meta { color: #64748b; }
    .empty-day { color: #94a3b8; }
    .ai-fab { right: 40px; bottom: 30px; }
  }
</style>
</head>
<body>

@include('partials.app-nav', ['active' => 'timetable', 'user' => $user])

<div class="container" id="pageContainer">
  <div class="header">
    <a href="{{ route('student.home') }}" class="back-btn" aria-label="Back">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"></path></svg>
    </a>
    <div>
      <p class="header-title">Timetable</p>
      <p class="header-sub">Your weekly class schedule</p>
    </div>
  </div>

  <button type="button" class="add-btn" onclick="openAddModal()">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
    Add Class
  </button>

  <div id="scheduleList"></div>
</div>

<button type="button" class="ai-fab" aria-label="AI Assistant" onclick="openAiModal()">
  <span style="display:flex;align-items:center;gap:7px;">
    <svg viewBox="0 0 24 24" fill="#0d9488" stroke="none"><path d="M12 2.5l1.7 5.3 5.3 1.7-5.3 1.7L12 16.5l-1.7-5.3-5.3-1.7 5.3-1.7L12 2.5z"></path><path d="M19.5 14l0.9 2.6 2.6 0.9-2.6 0.9-0.9 2.6-0.9-2.6-2.6-0.9 2.6-0.9z"></path></svg>
    <span>AI Assistant</span>
  </span>
</button>

<div class="overlay" id="overlay" onclick="closeModals()"></div>

<div class="modal" id="aiModal">
  <div class="modal-head">
    <div class="modal-title-row">
      <div class="modal-icon">
        <svg viewBox="0 0 24 24" fill="#ffffff" stroke="none"><path d="M12 2.5l1.7 5.3 5.3 1.7-5.3 1.7L12 16.5l-1.7-5.3-5.3-1.7 5.3-1.7L12 2.5z"></path></svg>
      </div>
      <p class="modal-title">AI Assistant</p>
    </div>
    <button type="button" class="modal-close" aria-label="Close" onclick="closeModals()">×</button>
  </div>
  <p class="ai-desc">Upload a photo of your class timetable — AI will read it and add every class automatically.</p>
  <label class="ai-upload-label">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2Z"></path><circle cx="12" cy="13" r="4"></circle></svg>
    Upload Photo
    <input type="file" accept="image/*" onchange="handlePhotoSelected(event)">
  </label>
  <p class="ai-scanning" id="aiScanning">🔎 Reading timetable and detecting classes...</p>
  <div class="ai-success" id="aiSuccess">
    <p id="aiSuccessMsg"></p>
    <button type="button" aria-label="Dismiss" onclick="dismissAiMsg()">×</button>
  </div>
  <div class="ai-error" id="aiError">
    <p id="aiErrorMsg"></p>
    <button type="button" aria-label="Dismiss" onclick="dismissAiError()">×</button>
  </div>
</div>

<div class="modal" id="modal">
  <div class="modal-head">
    <p class="modal-title" id="modalTitle">Add Class</p>
    <button type="button" class="modal-close" aria-label="Close" onclick="closeModals()">×</button>
  </div>
  <input type="hidden" id="scheduleId">
  <input type="text" id="subjectInput" placeholder="e.g. Database Systems">

  <p class="field-label">Day</p>
  <select id="dayInput">
    @foreach($days as $d)
      <option value="{{ $d }}">{{ $d }}</option>
    @endforeach
  </select>

  <p class="field-label">Time</p>
  <div class="time-row">
    <div><input type="time" id="startInput"></div>
    <div><input type="time" id="endInput"></div>
  </div>

  <p class="field-label">Room (optional)</p>
  <input type="text" id="roomInput" placeholder="e.g. Bilik Kuliah 3">

  <p class="field-label">Lecturer (optional)</p>
  <input type="text" id="lecturerInput" placeholder="e.g. En. Ahmad">

  <p class="form-error" id="formError">Please fill in subject, day and time.</p>

  <button type="button" class="save-btn" onclick="saveSchedule()">Save</button>
</div>

<script>
let schedules = @json($schedules);
const DAYS = @json($days);
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

function pad(n) { return n < 10 ? '0' + n : '' + n; }

function formatTime12(hhmm) {
  if (!hhmm) return '';
  const [h, m] = hhmm.split(':').map(Number);
  const period = h >= 12 ? 'PM' : 'AM';
  const h12 = h % 12 === 0 ? 12 : h % 12;
  return h12 + ':' + pad(m) + ' ' + period;
}

function escapeHtml(s) {
  return String(s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
}

function render() {
  const list = document.getElementById('scheduleList');
  list.innerHTML = DAYS.map(day => {
    const items = schedules
      .filter(s => s.day_of_week === day)
      .sort((a, b) => a.start_time.localeCompare(b.start_time));

    const body = items.length === 0
      ? `<p class="empty-day">No classes</p>`
      : `<div class="class-list">${items.map(s => `
          <div class="class-card" data-id="${s.id}">
            <div class="class-time">${formatTime12(s.start_time)}<br>${formatTime12(s.end_time)}</div>
            <div class="class-body">
              <p class="class-subject">${escapeHtml(s.subject)}</p>
              <p class="class-meta">${[s.room, s.lecturer].filter(Boolean).map(escapeHtml).join(' · ') || '&nbsp;'}</p>
            </div>
            <div class="class-actions">
              <button type="button" class="class-action-btn" aria-label="Edit" onclick="openEditModal(${s.id})">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.12 2.12 0 0 1 3 3L12 15l-4 1 1-4Z"></path></svg>
              </button>
              <button type="button" class="class-action-btn" aria-label="Delete" onclick="removeSchedule(${s.id})">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
              </button>
            </div>
          </div>
        `).join('')}</div>`;

    return `<div class="day-section"><p class="day-label">${day}</p>${body}</div>`;
  }).join('');
}

function showModal(id) {
  document.getElementById('formError').style.display = 'none';
  document.getElementById('overlay').classList.add('open');
  document.getElementById(id).classList.add('open');
}

function closeModals() {
  document.getElementById('overlay').classList.remove('open');
  document.getElementById('modal').classList.remove('open');
  document.getElementById('aiModal').classList.remove('open');
}

function openAddModal() {
  document.getElementById('modalTitle').textContent = 'Add Class';
  document.getElementById('scheduleId').value = '';
  document.getElementById('subjectInput').value = '';
  document.getElementById('dayInput').value = 'Monday';
  document.getElementById('startInput').value = '';
  document.getElementById('endInput').value = '';
  document.getElementById('roomInput').value = '';
  document.getElementById('lecturerInput').value = '';
  showModal('modal');
}

function openEditModal(id) {
  const s = schedules.find(x => x.id === id);
  if (!s) return;
  document.getElementById('modalTitle').textContent = 'Edit Class';
  document.getElementById('scheduleId').value = s.id;
  document.getElementById('subjectInput').value = s.subject;
  document.getElementById('dayInput').value = s.day_of_week;
  document.getElementById('startInput').value = s.start_time;
  document.getElementById('endInput').value = s.end_time;
  document.getElementById('roomInput').value = s.room || '';
  document.getElementById('lecturerInput').value = s.lecturer || '';
  showModal('modal');
}

function saveSchedule() {
  const id = document.getElementById('scheduleId').value;
  const subject = document.getElementById('subjectInput').value.trim();
  const day = document.getElementById('dayInput').value;
  const start = document.getElementById('startInput').value;
  const end = document.getElementById('endInput').value;
  const room = document.getElementById('roomInput').value.trim();
  const lecturer = document.getElementById('lecturerInput').value.trim();

  if (!subject || !day || !start || !end) {
    document.getElementById('formError').style.display = 'block';
    return;
  }

  const payload = { subject, day_of_week: day, start_time: start, end_time: end, room: room || null, lecturer: lecturer || null };
  const url = id ? `/timetable/${id}` : '/timetable';
  const method = id ? 'PUT' : 'POST';

  fetch(url, {
    method,
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
    body: JSON.stringify(payload),
  })
    .then(res => res.json())
    .then(data => {
      if (!data.success) {
        document.getElementById('formError').style.display = 'block';
        return;
      }
      if (id) {
        schedules = schedules.map(s => s.id === parseInt(id) ? data.schedule : s);
      } else {
        schedules.push(data.schedule);
      }
      closeModals();
      render();
    })
    .catch(err => console.error('Save failed', err));
}

function removeSchedule(id) {
  fetch(`/timetable/${id}`, {
    method: 'DELETE',
    headers: { 'X-CSRF-TOKEN': csrfToken },
  })
    .then(res => res.json())
    .then(() => {
      schedules = schedules.filter(s => s.id !== id);
      render();
    })
    .catch(err => console.error('Delete failed', err));
}

function openAiModal() {
  document.getElementById('aiSuccess').classList.remove('open');
  document.getElementById('aiScanning').classList.remove('open');
  document.getElementById('aiError').classList.remove('open');
  showModal('aiModal');
}

function dismissAiMsg() {
  document.getElementById('aiSuccess').classList.remove('open');
}

function dismissAiError() {
  document.getElementById('aiError').classList.remove('open');
}

function handlePhotoSelected(e) {
  const file = e.target.files && e.target.files[0];
  if (!file) return;

  document.getElementById('aiScanning').classList.add('open');
  document.getElementById('aiSuccess').classList.remove('open');
  document.getElementById('aiError').classList.remove('open');

  const formData = new FormData();
  formData.append('photo', file);

  fetch('{{ route('timetable.aiCapture') }}', {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': csrfToken },
    body: formData,
  })
    .then(res => res.json().then(data => ({ ok: res.ok, data })))
    .then(({ ok, data }) => {
      document.getElementById('aiScanning').classList.remove('open');
      e.target.value = '';

      if (!ok || !data.success) {
        document.getElementById('aiErrorMsg').textContent = '⚠️ ' + (data.error || 'Tak dapat proses gambar tu. Cuba lagi.');
        document.getElementById('aiError').classList.add('open');
        return;
      }

      schedules = schedules.concat(data.schedules);
      document.getElementById('aiSuccessMsg').textContent = '✅ Added ' + data.schedules.length + ' class(es) from your timetable!';
      document.getElementById('aiSuccess').classList.add('open');
      render();
    })
    .catch(err => {
      console.error('AI capture failed', err);
      document.getElementById('aiScanning').classList.remove('open');
      document.getElementById('aiErrorMsg').textContent = '⚠️ Ada masalah sambungan. Cuba lagi.';
      document.getElementById('aiError').classList.add('open');
      e.target.value = '';
    });
}

render();
</script>

</body>
</html>
