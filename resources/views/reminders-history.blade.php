<!DOCTYPE html>
<html lang="ms">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>RakanKampus - History</title>
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
    padding: 20px 18px 100px;
    position: relative;
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

  .stats-row {
    display: flex;
    gap: 8px;
    margin-bottom: 16px;
  }

  .stat-card {
    flex: 1;
    text-align: left;
    background: rgba(255,255,255,0.12);
    border: 1.5px solid transparent;
    border-radius: 12px;
    padding: 10px 12px;
    cursor: pointer;
  }

  .stat-card.active-all { background: rgba(255,255,255,0.24); border-color: #fff; }
  .stat-card.active-completed { background: rgba(22,163,74,0.35); border-color: #4ade80; }
  .stat-card.active-deleted { background: rgba(220,38,38,0.35); border-color: #f87171; }

  .stat-num { font-size: 18px; font-weight: 800; color: #fff; margin: 0; }
  .stat-label { font-size: 10px; color: #bfe9ea; margin: 2px 0 0; font-weight: 600; }

  .toolbar-row {
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 0 0 16px;
  }

  .filter-wrap { position: relative; flex: 1; }

  .filter-btn, .select-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    width: 100%;
    box-sizing: border-box;
    background: rgba(255,255,255,0.12);
    border: 1px solid rgba(255,255,255,0.25);
    color: #bfe9ea;
    border-radius: 8px;
    padding: 9px 10px;
    font-size: 11.5px;
    font-weight: 700;
    cursor: pointer;
  }

  .filter-btn svg, .select-btn svg { width: 11px; height: 11px; }

  .select-btn { flex: 1; width: auto; }

  .select-btn.active { background: #dc2626; border-color: #dc2626; color: #fff; }

  .filter-dropdown {
    position: absolute;
    top: calc(100% + 6px);
    left: 0;
    z-index: 40;
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 10px 26px rgba(0,0,0,0.3);
    padding: 6px;
    display: none;
    flex-direction: column;
    gap: 2px;
    min-width: 120px;
  }

  .filter-dropdown.open { display: flex; }

  .filter-opt {
    text-align: left;
    background: #fff;
    color: #334155;
    border: none;
    border-radius: 6px;
    padding: 8px 10px;
    font-size: 12.5px;
    font-weight: 700;
    cursor: pointer;
  }

  .filter-opt.active { background: #0d9488; color: #fff; }

  .select-all-row {
    display: none;
    align-items: center;
    gap: 8px;
    width: 100%;
    background: none;
    border: none;
    padding: 0 2px 10px;
    cursor: pointer;
  }

  .select-all-row.open { display: flex; }

  .select-all-row input { width: 16px; height: 16px; accent-color: #dc2626; cursor: pointer; }
  .select-all-row span { font-size: 12px; font-weight: 700; color: #bfe9ea; }

  .history-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
  }

  .history-card {
    background: rgba(255,255,255,0.96);
    border: 1.5px solid transparent;
    border-radius: 14px;
    padding: 13px 14px;
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .history-card.selected { background: #fef2f2; border-color: #fca5a5; }

  .history-select { width: 16px; height: 16px; accent-color: #dc2626; cursor: pointer; flex-shrink: 0; display: none; }
  .history-select.open { display: block; }

  .history-icon {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .history-icon svg { width: 14px; height: 14px; }

  .history-body { flex: 1; min-width: 0; }

  .history-type {
    display: inline-block;
    font-size: 9.5px;
    font-weight: 700;
    border-radius: 5px;
    padding: 1.5px 6px;
    margin-bottom: 3px;
  }

  .history-subject {
    font-size: 13px;
    font-weight: 700;
    color: #14213d;
    margin: 0 0 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .history-when { font-size: 11px; color: #64748b; margin: 0; }

  .history-status { font-size: 10.5px; font-weight: 700; flex-shrink: 0; }

  .empty-state {
    text-align: center;
    padding: 34px 18px;
    background: rgba(255,255,255,0.1);
    border-radius: 16px;
    color: #bfe9ea;
    font-size: 13px;
  }

  .bulk-delete-btn {
    display: none;
    align-items: center;
    justify-content: center;
    gap: 8px;
    position: fixed;
    left: 20px;
    right: 20px;
    bottom: 24px;
    max-width: 600px;
    margin: 0 auto;
    background: #dc2626;
    color: #fff;
    border: none;
    border-radius: 14px;
    padding: 14px;
    font-size: 13.5px;
    font-weight: 800;
    cursor: pointer;
    box-shadow: 0 10px 24px rgba(220,38,38,0.4);
    z-index: 32;
  }

  .bulk-delete-btn.open { display: flex; }
  .bulk-delete-btn svg { width: 15px; height: 15px; }

  @media (max-width: 860px) {
    .bulk-delete-btn { bottom: 88px; }
  }

  .back-link-desktop {
    display: none;
    align-items: center;
    gap: 6px;
    font-size: 11.5px;
    font-weight: 700;
    color: #0d9488;
    text-decoration: none;
    margin-bottom: 14px;
  }

  .back-link-desktop svg { width: 13px; height: 13px; }

  @media (min-width: 861px) {
    body { background: #f0fafa; }

    .container { max-width: 780px; margin: 0; padding: 36px 44px 90px; }

    .back-btn { display: none; }
    .back-link-desktop { display: inline-flex; }
    .header { padding: 0 0 18px; }
    .header-title { color: #14213d; font-size: 22px; }
    .header-sub { color: #0d9488; font-size: 13px; }

    .search-stats-toolbar-wrap { display: flex; flex-wrap: wrap; align-items: center; gap: 10px; }

    .search-row { background: #ffffff; border: 1px solid #dbeeee; flex: 1 1 260px; margin-bottom: 0; order: 1; }
    .search-row svg { stroke: #64748b; }
    .search-row input { color: #14213d; }
    .search-row input::placeholder { color: #94a3b8; }

    .toolbar-row { margin: 0; order: 2; }

    .stats-row { flex: 0 0 100%; order: 3; }

    .stat-card { background: #ffffff; border-color: #dbeeee; }
    .stat-card.active-all { background: #e6fbf9; border-color: #0d9488; }
    .stat-card.active-completed { background: #dcfce7; border-color: #4ade80; }
    .stat-card.active-deleted { background: #fee2e2; border-color: #f87171; }
    .stat-num { color: #14213d; }
    .stat-label { color: #0d9488; }
    #statCompleted .stat-label { color: #16a34a; }
    #statDeleted .stat-label { color: #dc2626; }

    .filter-wrap { flex: 0 0 auto; }
    .filter-btn, .select-btn { width: auto; background: #ffffff; border: 1px solid #dbeeee; color: #0d9488; }
    .select-btn { flex: 0 0 auto; }
    .select-btn.active { background: #dc2626; border-color: #dc2626; color: #fff; }

    .select-all-row span { color: #0d9488; }

    .history-card { background: #ffffff; border-color: #dbeeee; }
    .history-card.selected { background: #fef2f2; border-color: #fca5a5; }
    .history-when { color: #64748b; }

    .empty-state { background: #ffffff; border: 1px solid #dbeeee; color: #64748b; }

    .bulk-delete-btn { left: 264px; right: 44px; max-width: none; margin: 0; }
  }
</style>
</head>
<body>

@include('partials.app-nav', ['active' => 'reminders', 'user' => $user])

<div class="container" id="pageContainer">
  <a href="{{ route('student.reminders') }}" class="back-link-desktop">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"></path><path d="m11 18-6-6 6-6"></path></svg>
    Back to Reminders
  </a>
  <div class="header">
    <a href="{{ route('student.reminders') }}" class="back-btn" aria-label="Back">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"></path></svg>
    </a>
    <div>
      <p class="header-title">History</p>
      <p class="header-sub">Past exams, assignments &amp; deadlines</p>
    </div>
  </div>

  <div class="search-stats-toolbar-wrap">
  <div class="search-row">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
    <input type="text" id="searchInput" placeholder="Search history..." oninput="onSearchChange()">
  </div>

  <div class="stats-row">
    <button type="button" class="stat-card" id="statAll" onclick="setStatusFilter('all')">
      <p class="stat-num" id="totalCount">0</p>
      <p class="stat-label">Total Reminders</p>
    </button>
    <button type="button" class="stat-card" id="statCompleted" onclick="setStatusFilter('completed')">
      <p class="stat-num" id="completedCount">0</p>
      <p class="stat-label">Completed</p>
    </button>
    <button type="button" class="stat-card" id="statDeleted" onclick="setStatusFilter('deleted')">
      <p class="stat-num" id="deletedCount">0</p>
      <p class="stat-label">Deleted</p>
    </button>
  </div>

  <div class="toolbar-row">
    <div class="filter-wrap">
      <button type="button" class="filter-btn" onclick="toggleFilterOpen()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
        <span id="filterDaysLabel">30 Days</span>
      </button>
      <div class="filter-dropdown" id="filterDropdown">
        @foreach([90, 30, 14, 7] as $d)
          <button type="button" class="filter-opt" data-days="{{ $d }}" onclick="setFilterDays({{ $d }})">{{ $d }} Days</button>
        @endforeach
      </div>
    </div>
    <button type="button" class="select-btn" id="selectModeBtn" onclick="toggleSelectMode()">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="4"></rect><path d="m8 12 3 3 5-6"></path></svg>
      <span id="selectModeLabel">Select</span>
    </button>
  </div>
  </div>

  <button type="button" class="select-all-row" id="selectAllRow" onclick="toggleSelectAll()">
    <input type="checkbox" id="selectAllCheckbox" style="pointer-events: none;">
    <span>Select All</span>
  </button>

  <div class="history-list" id="historyList"></div>
  <div class="empty-state" id="emptyState" style="display:none;">Tiada sejarah reminder lagi.</div>
</div>

<button type="button" class="bulk-delete-btn" id="bulkDeleteBtn" onclick="deleteSelected()">
  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
  Delete · <span id="selectedCount">0</span>
</button>

<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

let items = @json($items);
let searchQuery = '';
let filterDays = 30;
let statusFilter = 'all';
let selectMode = false;
let selectedIds = [];

function typeStyle(type) {
  if (type === 'Assignment') return { color: '#0d9488', bg: '#f0fdfa' };
  if (type === 'Quiz') return { color: '#7c3aed', bg: '#f5f3ff' };
  if (type === 'Other') return { color: '#64748b', bg: '#f1f5f9' };
  return { color: '#6366f1', bg: '#eef2ff' };
}

function daysAgoLabel(dueMs) {
  const days = Math.max(1, Math.round((Date.now() - dueMs) / (24 * 3600 * 1000)));
  return days === 1 ? '1 day ago' : days + ' days ago';
}

function formatWhen(dueMs) {
  const d = new Date(dueMs);
  return d.toLocaleDateString(undefined, { day: 'numeric', month: 'short' });
}

function escapeHtml(s) {
  return String(s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
}

function render() {
  const cutoff = Date.now() - filterDays * 24 * 3600 * 1000;
  const q = searchQuery.trim().toLowerCase();
  const inRange = items
    .filter(it => new Date(it.due_at).getTime() >= cutoff)
    .filter(it => !q || it.subject.toLowerCase().includes(q));

  document.getElementById('totalCount').textContent = inRange.length;
  document.getElementById('completedCount').textContent = inRange.filter(it => it.status === 'completed').length;
  document.getElementById('deletedCount').textContent = inRange.filter(it => it.status === 'deleted').length;

  document.getElementById('statAll').className = 'stat-card' + (statusFilter === 'all' ? ' active-all' : '');
  document.getElementById('statCompleted').className = 'stat-card' + (statusFilter === 'completed' ? ' active-completed' : '');
  document.getElementById('statDeleted').className = 'stat-card' + (statusFilter === 'deleted' ? ' active-deleted' : '');

  const visible = inRange
    .filter(it => statusFilter === 'all' || it.status === statusFilter)
    .sort((a, b) => new Date(b.due_at) - new Date(a.due_at));

  document.getElementById('emptyState').style.display = visible.length === 0 ? 'block' : 'none';

  const list = document.getElementById('historyList');
  list.innerHTML = visible.map(it => {
    const dueMs = new Date(it.due_at).getTime();
    const ts = typeStyle(it.type);
    const isCompleted = it.status === 'completed';
    const isSelected = selectedIds.includes(it.id);
    const iconBg = isCompleted ? '#dcfce7' : '#fee2e2';
    const iconStroke = isCompleted ? '#16a34a' : '#dc2626';
    const statusColor = isCompleted ? '#16a34a' : '#dc2626';
    const statusText = isCompleted ? 'Completed' : 'Deleted';
    const icon = isCompleted
      ? `<svg viewBox="0 0 24 24" fill="none" stroke="${iconStroke}" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg>`
      : `<svg viewBox="0 0 24 24" fill="none" stroke="${iconStroke}" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>`;
    return `
      <div class="history-card ${isSelected ? 'selected' : ''}" data-id="${it.id}">
        <input type="checkbox" class="history-select ${selectMode ? 'open' : ''}" ${isSelected ? 'checked' : ''} onchange="toggleSelect(${it.id})">
        <div class="history-icon" style="background: ${iconBg};">${icon}</div>
        <div class="history-body">
          <span class="history-type" style="color: ${ts.color}; background: ${ts.bg};">${escapeHtml(it.type)}</span>
          <p class="history-subject">${escapeHtml(it.subject)}</p>
          <p class="history-when">${formatWhen(dueMs)} · ${daysAgoLabel(dueMs)}</p>
        </div>
        <span class="history-status" style="color: ${statusColor};">${statusText}</span>
      </div>`;
  }).join('');

  document.getElementById('selectAllRow').classList.toggle('open', selectMode);
  const allSelected = visible.length > 0 && visible.every(it => selectedIds.includes(it.id));
  document.getElementById('selectAllCheckbox').checked = allSelected;

  document.getElementById('bulkDeleteBtn').classList.toggle('open', selectedIds.length > 0);
  document.getElementById('selectedCount').textContent = selectedIds.length;

  window._visibleIds = visible.map(it => it.id);
}

function onSearchChange() {
  searchQuery = document.getElementById('searchInput').value;
  render();
}

function setStatusFilter(status) {
  statusFilter = status;
  render();
}

function toggleFilterOpen() {
  document.getElementById('filterDropdown').classList.toggle('open');
}

function setFilterDays(days) {
  filterDays = days;
  document.getElementById('filterDaysLabel').textContent = days + ' Days';
  document.querySelectorAll('.filter-opt').forEach(b => b.classList.toggle('active', parseInt(b.dataset.days) === days));
  document.getElementById('filterDropdown').classList.remove('open');
  render();
}

function toggleSelectMode() {
  selectMode = !selectMode;
  if (!selectMode) selectedIds = [];
  document.getElementById('selectModeBtn').classList.toggle('active', selectMode);
  document.getElementById('selectModeLabel').textContent = selectMode ? 'Cancel' : 'Select';
  render();
}

function toggleSelect(id) {
  if (selectedIds.includes(id)) {
    selectedIds = selectedIds.filter(x => x !== id);
  } else {
    selectedIds.push(id);
  }
  render();
}

function toggleSelectAll() {
  const visibleIds = window._visibleIds || [];
  const allSelected = visibleIds.length > 0 && visibleIds.every(id => selectedIds.includes(id));
  selectedIds = allSelected ? [] : visibleIds.slice();
  render();
}

function deleteSelected() {
  const ids = selectedIds.slice();
  if (ids.length === 0) return;
  if (!confirm('Padam ' + ids.length + ' rekod ni secara kekal?')) return;
  fetch('/reminders/history/bulk-delete', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
    body: JSON.stringify({ ids }),
  }).then(res => res.json()).then(() => {
    items = items.filter(it => !ids.includes(it.id));
    selectedIds = [];
    render();
  }).catch(err => console.error('Bulk delete failed', err));
}

// Page-level swipe back to Reminders
(function () {
  const el = document.getElementById('pageContainer');
  let startX = null;
  el.addEventListener('pointerdown', (e) => {
    if (e.target.closest('.history-card')) return;
    startX = e.clientX;
  });
  el.addEventListener('pointerup', (e) => {
    if (startX === null) return;
    const delta = e.clientX - startX;
    startX = null;
    if (delta > 70) window.location.href = "{{ route('student.reminders') }}";
  });
})();

render();
</script>

</body>
</html>
