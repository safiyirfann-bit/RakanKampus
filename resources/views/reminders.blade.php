<!DOCTYPE html>
<html lang="ms">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
@include('partials.pwa-head')
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
    padding: 20px 18px 110px;
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

  .add-btn {
    width: 100%;
    box-sizing: border-box;
    background: linear-gradient(120deg, #14213d, #2ec4c6);
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
    margin-bottom: 14px;
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

  .toolbar-row {
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 0 0 14px;
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

  .history-link {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: #fff;
    text-decoration: none;
  }

  .history-link svg { width: 10px; height: 10px; }

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

  .reminder-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
  }

  .swipe-wrap {
    position: relative;
    border-radius: 16px;
    overflow: hidden;
  }

  .swipe-delete-panel {
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 88px;
    background: #dc2626;
    border: none;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 5px;
    cursor: pointer;
    color: #fff;
  }

  .swipe-delete-panel svg { width: 18px; height: 18px; }
  .swipe-delete-panel span { font-size: 11px; font-weight: 700; }

  .reminder-card {
    position: relative;
    background: #fdf2ee;
    border: 1.5px solid transparent;
    border-radius: 16px;
    padding: 13px 14px;
    display: flex;
    align-items: flex-start;
    gap: 12px;
    transition: transform 0.2s ease, background 0.15s ease, border-color 0.15s ease;
    touch-action: pan-y;
  }

  .reminder-card.selected { background: #fef2f2; border-color: #fca5a5; }

  .reminder-select {
    width: 16px;
    height: 16px;
    accent-color: #dc2626;
    cursor: pointer;
    flex-shrink: 0;
    margin-top: 13px;
    display: none;
  }

  .reminder-select.open { display: block; }

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

  .notify-banner {
    display: none;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    background: #fff7ed;
    border: 1px solid #fed7aa;
    color: #9a3412;
    border-radius: 12px;
    padding: 10px 14px;
    margin-bottom: 12px;
    font-size: 12.5px;
  }
  .notify-banner.open { display: flex; }
  .notify-banner button {
    background: #ea580c;
    color: #ffffff;
    border: 0;
    border-radius: 8px;
    padding: 7px 12px;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    flex-shrink: 0;
  }

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

  .ai-fab {
    position: fixed;
    right: max(20px, calc(50% - 300px));
    bottom: 24px;
    z-index: 30;
    display: flex;
    align-items: center;
    background: linear-gradient(90deg, #22d3ee, #34d399 50%, #facc15);
    padding: 2px;
    border-radius: 999px;
    border: none;
    cursor: pointer;
    box-shadow: 0 8px 18px rgba(0,0,0,0.28);
  }

  .ai-fab-inner {
    display: flex;
    align-items: center;
    gap: 6px;
    background: #fff;
    border-radius: 999px;
    padding: 9px 14px 9px 10px;
  }

  .ai-fab-inner svg { width: 14px; height: 14px; }
  .ai-fab-inner span { font-size: 11.5px; font-weight: 800; color: #14213d; letter-spacing: 0.01em; }

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

  .modal-title-row { display: flex; align-items: center; gap: 8px; min-width: 0; }

  .modal-icon {
    width: 30px;
    height: 30px;
    border-radius: 9px;
    background: linear-gradient(120deg, #14213d, #2ec4c6);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .modal-icon svg { width: 16px; height: 16px; }

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
    flex-shrink: 0;
  }

  .field-label { font-size: 11px; font-weight: 700; color: #64748b; margin: 0 0 6px; display: block; }

  .modal input[type="text"],
  .modal input[type="date"],
  .modal input[type="time"],
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

  .date-time-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
  }

  .date-time-row input { margin-bottom: 12px; }

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

  @media (max-width: 860px) {
    .ai-fab { bottom: 88px; }
    .bulk-delete-btn { bottom: 88px; }
  }

  @media (min-width: 861px) {
    body { background: #f0fafa; }

    .container { max-width: 760px; margin: 0; padding: 36px 44px 90px; }

    .back-btn { display: none; }
    .header { padding: 0 0 18px; }
    .header-title { color: #14213d; font-size: 22px; }
    .header-sub { color: #0d9488; font-size: 13px; }

    .add-btn { width: auto; }

    .search-toolbar-wrap { display: flex; align-items: center; gap: 10px; margin-bottom: 16px; }

    .search-row { background: #ffffff; border: 1px solid #dbeeee; flex: 1; margin-bottom: 0; }
    .search-row svg { stroke: #64748b; }
    .search-row input { color: #14213d; }
    .search-row input::placeholder { color: #94a3b8; }

    .toolbar-row { margin: 0; }

    .filter-wrap { flex: 0 0 auto; }
    .filter-btn, .select-btn {
      width: auto;
      background: #ffffff;
      border: 1px solid #dbeeee;
      color: #0d9488;
    }
    .select-btn { flex: 0 0 auto; }
    .select-btn.active { background: #dc2626; border-color: #dc2626; color: #fff; }

    .section-label { color: #64748b; }
    .history-link { color: #0d9488; }
    .select-all-row span { color: #0d9488; }

    .reminder-card { background: #ffffff; border-color: #dbeeee; }
    .reminder-card.selected { background: #fef2f2; border-color: #fca5a5; }
    .reminder-when { color: #64748b; }

    .empty-state { background: #ffffff; border: 1px solid #dbeeee; color: #64748b; }

    .ai-fab { right: 40px; bottom: 30px; }
    .bulk-delete-btn { left: 264px; right: 44px; max-width: none; margin: 0; }
  }

  .ai-desc { font-size: 12.5px; color: #64748b; margin: 0 0 16px; line-height: 1.4; }

  .ai-upload-label {
    width: 100%;
    box-sizing: border-box;
    background: #eef2ff;
    border: 1.5px dashed #a5b4fc;
    color: #4338ca;
    border-radius: 10px;
    padding: 16px;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
  }

  .ai-upload-label svg { width: 16px; height: 16px; }
  .ai-upload-label input { display: none; }

  .ai-scanning { font-size: 12px; color: #4338ca; margin: 14px 0 0; text-align: center; display: none; }
  .ai-scanning.open { display: block; }

  .ai-success {
    margin-top: 14px;
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    border-radius: 10px;
    padding: 10px 12px;
    display: none;
    align-items: flex-start;
    justify-content: space-between;
    gap: 8px;
  }

  .ai-success.open { display: flex; }
  .ai-success p { font-size: 11.5px; color: #15803d; margin: 0; line-height: 1.4; }
  .ai-success button { background: none; border: none; color: #15803d; font-size: 14px; cursor: pointer; padding: 0; flex-shrink: 0; }
</style>
</head>
<body>

@include('partials.app-nav', ['active' => 'reminders', 'user' => $user])

<div class="container" id="pageContainer">
  <div class="header">
    <a href="{{ route('student.home') }}" class="back-btn" aria-label="Back">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"></path></svg>
    </a>
    <div>
      <p class="header-title">Reminders</p>
      <p class="header-sub">For exams, assignments &amp; deadlines</p>
    </div>
  </div>

  <div class="notify-banner" id="notifyBanner">
    <span>🔔 Turn on notifications to get alerted before your deadlines.</span>
    <button type="button" onclick="enableNotifications()">Enable</button>
  </div>

  <button type="button" class="add-btn" onclick="openAddModal()">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
    Add Reminder
  </button>

  <div class="search-toolbar-wrap">
  <div class="search-row">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
    <input type="text" id="searchInput" placeholder="Search reminders..." oninput="onSearchChange()">
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

  <div class="section-row">
    <p class="section-label" id="countLabel">UPCOMING (0)</p>
    <a href="{{ route('student.reminders.history') }}" class="history-link">
      History ({{ $historyCount }})
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"></path></svg>
    </a>
  </div>

  <button type="button" class="select-all-row" id="selectAllRow" onclick="toggleSelectAll()">
    <input type="checkbox" id="selectAllCheckbox" style="pointer-events: none;">
    <span>Select All</span>
  </button>

  <div class="reminder-list" id="reminderList"></div>
  <div class="empty-state" id="emptyState" style="display:none;">No reminders yet — tap "Add Reminder" to add your first exam, assignment or deadline.</div>
</div>

<button type="button" class="bulk-delete-btn" id="bulkDeleteBtn" onclick="deleteSelected()">
  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
  Delete · <span id="selectedCount">0</span>
</button>

<button type="button" class="ai-fab" aria-label="AI Assistant" onclick="openAiModal()">
  <span class="ai-fab-inner">
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
  <p class="ai-desc">Upload a photo of your exam slip, timetable or assignment brief — AI will read it and add the reminder automatically.</p>
  <label class="ai-upload-label">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2Z"></path><circle cx="12" cy="13" r="4"></circle></svg>
    Upload Photo
    <input type="file" accept="image/*" onchange="handlePhotoSelected(event)">
  </label>
  <p class="ai-scanning" id="aiScanning">🔎 Reading image and detecting details...</p>
  <div class="ai-success" id="aiSuccess">
    <p id="aiSuccessMsg"></p>
    <button type="button" aria-label="Dismiss" onclick="dismissAiMsg()">×</button>
  </div>
</div>

<div class="modal" id="modal">
  <div class="modal-head">
    <p class="modal-title" id="modalTitle">Add Reminder</p>
    <button type="button" class="modal-close" aria-label="Close" onclick="closeModals()">×</button>
  </div>
  <input type="hidden" id="reminderId">
  <input type="text" id="subjectInput" placeholder="e.g. Software Engineering Assignment 2">
  <p class="field-label">Type</p>
  <div class="type-row" id="typeRow">
    @foreach(['Exam' => '#6366f1', 'Assignment' => '#0d9488', 'Quiz' => '#7c3aed', 'Other' => '#64748b'] as $t => $color)
      <button type="button" class="type-opt" data-type="{{ $t }}" data-color="{{ $color }}" onclick="selectType('{{ $t }}')">{{ $t }}</button>
    @endforeach
  </div>
  <div class="date-time-row">
    <input type="date" id="dateInput">
    <input type="time" id="timeInput">
  </div>
  <p class="field-label">Notify me how many hours before?</p>
  <input type="number" id="leadInput" min="0" step="0.5" value="1">
  <p class="hint">Type any number of hours — use 0.5 for 30 minutes.</p>
  <p id="formError" style="color:#e11d48; font-size: 11.5px; display:none; margin: -6px 0 10px;">Please fill in a subject and date.</p>
  <button type="button" class="modal-save" onclick="saveReminder()">Save Reminder</button>
</div>

<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

let reminders = @json($reminders);
let historyCount = {{ $historyCount }};
let selectedType = 'Exam';
let searchQuery = '';
let filterDays = 30;
let selectMode = false;
let selectedIds = [];
let dragId = null;
let dragStartX = null;
let dragOffset = 0;
let pageDragStartX = null;

function typeStyle(type) {
  if (type === 'Assignment') return { color: '#0d9488', bg: '#f0fdfa' };
  if (type === 'Quiz') return { color: '#7c3aed', bg: '#f5f3ff' };
  if (type === 'Other') return { color: '#64748b', bg: '#f1f5f9' };
  return { color: '#6366f1', bg: '#eef2ff' };
}

function computeStatus(dueMs, leadHours) {
  const now = Date.now();
  const diffMs = dueMs - now;
  const diffHours = diffMs / 3600000;
  if (diffMs <= 0) return { text: 'Passed', color: '#94a3b8', dotBg: '#e2e8f0', dotStroke: '#94a3b8' };
  if (diffHours <= leadHours) {
    const h = Math.floor(diffHours);
    const m = Math.floor((diffHours - h) * 60);
    return { text: '🔔 Notified · ' + h + 'h ' + m + 'm left', color: '#ea580c', dotBg: '#ffedd5', dotStroke: '#ea580c' };
  }
  if (diffHours <= 24) return { text: 'Upcoming · in ' + Math.floor(diffHours) + 'h', color: '#2563eb', dotBg: '#dbeafe', dotStroke: '#2563eb' };
  return { text: 'Upcoming · in ' + Math.floor(diffHours / 24) + 'd', color: '#0d9488', dotBg: '#ccfbf1', dotStroke: '#0d9488' };
}

function leadLabel(hours) {
  if (hours < 1) return Math.round(hours * 60) + ' min';
  if (hours === 1) return '1 hour';
  return (Math.round(hours * 10) / 10) + ' hours';
}

function formatWhen(dueMs) {
  const d = new Date(dueMs);
  return d.toLocaleDateString(undefined, { day: 'numeric', month: 'short' }) + ' · ' + d.toLocaleTimeString(undefined, { hour: '2-digit', minute: '2-digit' });
}

function pad(n) { return n < 10 ? '0' + n : '' + n; }
function toDateStr(ms) { const d = new Date(ms); return d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate()); }
function toTimeStr(ms) { const d = new Date(ms); return pad(d.getHours()) + ':' + pad(d.getMinutes()); }

function escapeHtml(s) {
  return String(s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
}

function render() {
  const q = searchQuery.trim().toLowerCase();
  const cutoff = Date.now() + filterDays * 24 * 3600 * 1000;
  const visible = reminders
    .filter(r => !q || r.subject.toLowerCase().includes(q))
    .filter(r => new Date(r.due_at).getTime() <= cutoff)
    .sort((a, b) => new Date(a.due_at) - new Date(b.due_at));

  document.getElementById('countLabel').textContent = 'UPCOMING (' + visible.length + ')';
  document.getElementById('emptyState').style.display = visible.length === 0 ? 'block' : 'none';

  const list = document.getElementById('reminderList');
  list.innerHTML = visible.map(r => {
    const dueMs = new Date(r.due_at).getTime();
    const status = computeStatus(dueMs, r.lead_hours);
    const ts = typeStyle(r.type);
    const isSelected = selectedIds.includes(r.id);
    const offset = dragId === r.id ? dragOffset : 0;
    return `
      <div class="swipe-wrap">
        <button type="button" class="swipe-delete-panel" onclick="removeReminder(${r.id})" aria-label="Delete">
          <svg viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
          <span>Delete</span>
        </button>
        <div class="reminder-card ${isSelected ? 'selected' : ''}" data-id="${r.id}"
             style="transform: translateX(${offset}px);"
             onpointerdown="startRowDrag(event, ${r.id})" onpointermove="moveRowDrag(event)" onpointerup="endRowDrag(event)" onpointerleave="endRowDrag(event)">
          <input type="checkbox" class="reminder-select ${selectMode ? 'open' : ''}" ${isSelected ? 'checked' : ''} onchange="toggleSelect(${r.id})" onclick="event.stopPropagation()">
          <div class="reminder-dot" style="background: ${status.dotBg};">
            <svg viewBox="0 0 24 24" fill="none" stroke="${status.dotStroke}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v5l3 3"></path></svg>
          </div>
          <div class="reminder-body">
            <span class="reminder-type" style="color: ${ts.color}; background: ${ts.bg};">${escapeHtml(r.type)}</span>
            <p class="reminder-subject">${escapeHtml(r.subject)}</p>
            <p class="reminder-when">${formatWhen(dueMs)} · notify ${leadLabel(r.lead_hours)} before</p>
            <p class="reminder-status" style="color: ${status.color};">${status.text}</p>
          </div>
          ${!selectMode ? `
          <div class="reminder-actions">
            <button type="button" class="reminder-action-btn" aria-label="Edit" onclick="event.stopPropagation(); openEditModal(${r.id})">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.12 2.12 0 0 1 3 3L12 15l-4 1 1-4Z"></path></svg>
            </button>
            <button type="button" class="reminder-action-btn" aria-label="Delete" onclick="event.stopPropagation(); removeReminder(${r.id})">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
          </div>` : ''}
        </div>
      </div>`;
  }).join('');

  document.getElementById('selectAllRow').classList.toggle('open', selectMode);
  const allSelected = visible.length > 0 && visible.every(r => selectedIds.includes(r.id));
  document.getElementById('selectAllCheckbox').checked = allSelected;

  document.getElementById('bulkDeleteBtn').classList.toggle('open', selectedIds.length > 0);
  document.getElementById('selectedCount').textContent = selectedIds.length;

  window._visibleIds = visible.map(r => r.id);
}

function onSearchChange() {
  searchQuery = document.getElementById('searchInput').value;
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
  fetch('/reminders/bulk-delete', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
    body: JSON.stringify({ ids }),
  }).then(res => res.json()).then(() => {
    reminders = reminders.filter(r => !ids.includes(r.id));
    historyCount += ids.length;
    selectedIds = [];
    render();
  }).catch(err => console.error('Bulk delete failed', err));
}

// Row swipe-to-delete
function startRowDrag(e, id) {
  if (selectMode) return;
  dragId = id;
  dragStartX = e.clientX;
  dragOffset = 0;
}

function moveRowDrag(e) {
  if (dragId === null || dragStartX === null) return;
  let delta = e.clientX - dragStartX;
  if (delta < 0) delta = 0;
  if (delta > 88) delta = 88;
  dragOffset = delta;
  const card = document.querySelector(`.reminder-card[data-id="${dragId}"]`);
  if (card) card.style.transform = `translateX(${dragOffset}px)`;
}

function endRowDrag() {
  if (dragId === null) return;
  const id = dragId;
  const shouldDelete = dragOffset > 44;
  dragId = null;
  dragStartX = null;
  dragOffset = 0;
  if (shouldDelete) {
    removeReminder(id);
  } else {
    render();
  }
}

// Page-level swipe to History
(function () {
  const el = document.getElementById('pageContainer');
  let startX = null;
  el.addEventListener('pointerdown', (e) => {
    if (e.target.closest('.reminder-card') || e.target.closest('.swipe-delete-panel')) return;
    startX = e.clientX;
  });
  el.addEventListener('pointerup', (e) => {
    if (startX === null) return;
    const delta = e.clientX - startX;
    startX = null;
    if (delta < -70) window.location.href = "{{ route('student.reminders.history') }}";
  });
})();

// Type select (add/edit modal)
let activeForm = 'add';

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

function openAddModal() {
  document.getElementById('modalTitle').textContent = 'Add Reminder';
  document.getElementById('reminderId').value = '';
  document.getElementById('subjectInput').value = '';
  document.getElementById('dateInput').value = '';
  document.getElementById('timeInput').value = '';
  document.getElementById('leadInput').value = '1';
  selectType('Exam');
  showModal('modal');
}

function openEditModal(id) {
  const r = reminders.find(x => x.id === id);
  if (!r) return;
  const dueMs = new Date(r.due_at).getTime();
  document.getElementById('modalTitle').textContent = 'Edit Reminder';
  document.getElementById('reminderId').value = r.id;
  document.getElementById('subjectInput').value = r.subject;
  document.getElementById('dateInput').value = toDateStr(dueMs);
  document.getElementById('timeInput').value = toTimeStr(dueMs);
  document.getElementById('leadInput').value = r.lead_hours;
  selectType(r.type);
  showModal('modal');
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

function saveReminder() {
  const id = document.getElementById('reminderId').value;
  const subject = document.getElementById('subjectInput').value.trim();
  const date = document.getElementById('dateInput').value;
  const time = document.getElementById('timeInput').value || '09:00';
  const lead = parseFloat(document.getElementById('leadInput').value) || 1;

  if (!subject || !date) {
    document.getElementById('formError').style.display = 'block';
    return;
  }

  const due = date + 'T' + time;
  const url = id ? `/reminders/${id}` : '/reminders';
  const method = id ? 'PUT' : 'POST';

  fetch(url, {
    method,
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
    body: JSON.stringify({ subject, type: selectedType, due_at: due, lead_hours: lead }),
  })
    .then(res => res.json())
    .then(data => {
      if (id) {
        reminders = reminders.map(r => r.id === parseInt(id) ? data.reminder : r);
      } else {
        reminders.push(data.reminder);
      }
      closeModals();
      render();
    })
    .catch(err => console.error('Save failed', err));
}

function removeReminder(id) {
  fetch(`/reminders/${id}`, {
    method: 'DELETE',
    headers: { 'X-CSRF-TOKEN': csrfToken },
  })
    .then(res => res.json())
    .then(() => {
      reminders = reminders.filter(r => r.id !== id);
      selectedIds = selectedIds.filter(x => x !== id);
      historyCount += 1;
      if (dragId === id) { dragId = null; dragOffset = 0; }
      render();
    })
    .catch(err => console.error('Delete failed', err));
}

// AI Assistant simulation (matches prototype: fake scan, then real create)
function openAiModal() {
  document.getElementById('aiSuccess').classList.remove('open');
  document.getElementById('aiScanning').classList.remove('open');
  showModal('aiModal');
}

function dismissAiMsg() {
  document.getElementById('aiSuccess').classList.remove('open');
}

function handlePhotoSelected(e) {
  const file = e.target.files && e.target.files[0];
  if (!file) return;
  document.getElementById('aiScanning').classList.add('open');
  document.getElementById('aiSuccess').classList.remove('open');

  const pool = [
    { subject: 'Database Systems Final Exam', type: 'Exam', daysFromNow: 3, time: '09:00', leadHours: 3 },
    { subject: 'Mobile App Development Assignment 3', type: 'Assignment', daysFromNow: 2, time: '23:59', leadHours: 6 },
    { subject: 'Discrete Mathematics Quiz 2', type: 'Quiz', daysFromNow: 1, time: '14:00', leadHours: 1 },
  ];
  const pick = pool[Math.floor(Math.random() * pool.length)];

  setTimeout(() => {
    const d = new Date(Date.now() + pick.daysFromNow * 24 * 3600 * 1000);
    const dateStr = toDateStr(d.getTime());
    const due = dateStr + 'T' + pick.time;

    fetch('/reminders', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
      body: JSON.stringify({ subject: pick.subject, type: pick.type, due_at: due, lead_hours: pick.leadHours }),
    })
      .then(res => res.json())
      .then(data => {
        reminders.push(data.reminder);
        document.getElementById('aiScanning').classList.remove('open');
        document.getElementById('aiSuccessMsg').textContent = '✅ Detected "' + pick.subject + '" — reminder added automatically!';
        document.getElementById('aiSuccess').classList.add('open');
        e.target.value = '';
        render();
      })
      .catch(err => console.error('AI add failed', err));
  }, 1400);
}

// Browser notifications: fire once per reminder when the lead time is reached (works while this page/tab is open)
const NOTIFIED_KEY = 'reminders_notified_v1';

function getNotified() {
  try { return JSON.parse(localStorage.getItem(NOTIFIED_KEY)) || {}; } catch (e) { return {}; }
}

function markNotified(key) {
  try {
    const all = getNotified();
    all[key] = Date.now();
    localStorage.setItem(NOTIFIED_KEY, JSON.stringify(all));
  } catch (e) {}
}

function updateNotifyBanner() {
  const supported = 'Notification' in window;
  document.getElementById('notifyBanner').classList.toggle('open', supported && Notification.permission === 'default');
}

// Web Push: lets the server notify even when this page is closed.
const VAPID_PUBLIC_KEY = @json(config('webpush.vapid.public_key'));
let pushActive = false;

function urlBase64ToUint8Array(base64) {
  const padding = '='.repeat((4 - base64.length % 4) % 4);
  const raw = atob((base64 + padding).replace(/-/g, '+').replace(/_/g, '/'));
  return Uint8Array.from(raw, c => c.charCodeAt(0));
}

async function ensurePushSubscription() {
  if (!VAPID_PUBLIC_KEY || !('serviceWorker' in navigator) || !('PushManager' in window)) return;
  if (Notification.permission !== 'granted') return;
  try {
    const reg = await navigator.serviceWorker.register('/sw.js');
    await navigator.serviceWorker.ready;
    let sub = await reg.pushManager.getSubscription();
    if (!sub) {
      sub = await reg.pushManager.subscribe({
        userVisibleOnly: true,
        applicationServerKey: urlBase64ToUint8Array(VAPID_PUBLIC_KEY),
      });
    }
    const res = await fetch('/push/subscribe', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
      body: JSON.stringify(sub.toJSON()),
    });
    pushActive = res.ok;
  } catch (e) {
    console.error('Push subscribe failed', e);
    pushActive = false;
  }
}

function enableNotifications() {
  if (!('Notification' in window)) return;
  Notification.requestPermission().then(async () => {
    updateNotifyBanner();
    await ensurePushSubscription();
    checkDueReminders();
  });
}

function checkDueReminders() {
  // When push is active the server sends the notification, so skip the in-page one to avoid duplicates
  if (pushActive) return;
  if (!('Notification' in window) || Notification.permission !== 'granted') return;
  const now = Date.now();
  reminders.forEach(r => {
    const dueMs = new Date(r.due_at).getTime();
    if (dueMs <= now) return;
    if (dueMs - r.lead_hours * 3600000 > now) return;
    const key = r.id + '@' + r.due_at + '@' + r.lead_hours;
    if (getNotified()[key]) return;
    markNotified(key);
    const mins = Math.max(1, Math.round((dueMs - now) / 60000));
    const left = mins >= 60 ? Math.floor(mins / 60) + 'h ' + (mins % 60) + 'm' : mins + ' min';
    new Notification(r.type + ' due in ' + left, {
      body: r.subject + ' · ' + formatWhen(dueMs),
      tag: 'reminder-' + r.id,
    });
  });
}

updateNotifyBanner();
ensurePushSubscription().then(checkDueReminders);
setInterval(checkDueReminders, 30000);
setInterval(render, 60000);

render();
</script>

</body>
</html>
