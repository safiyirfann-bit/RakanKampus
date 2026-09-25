<!DOCTYPE html>
<html lang="en">
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

  .action-row { display: flex; gap: 10px; margin-bottom: 20px; }

  .add-btn {
    flex: 1; box-sizing: border-box;
    background: linear-gradient(120deg, #14213d, #2ec4c6);
    color: #fff; border: none; border-radius: 12px; padding: 14px;
    font-size: 14px; font-weight: 800;
    display: flex; align-items: center; justify-content: center; gap: 8px;
    cursor: pointer;
    box-shadow: 0 10px 22px rgba(0,0,0,0.22);
  }
  .add-btn svg { width: 16px; height: 16px; }

  .delete-all-btn {
    flex-shrink: 0; box-sizing: border-box;
    background: rgba(254,242,242,0.95); color: #dc2626; border: 1.5px solid rgba(254,202,202,0.9);
    border-radius: 12px; padding: 14px 16px;
    font-size: 13px; font-weight: 800;
    display: flex; align-items: center; justify-content: center; gap: 6px;
    cursor: pointer;
  }
  .delete-all-btn svg { width: 15px; height: 15px; }
  .delete-all-btn:hover { background: #fee2e2; }
  .delete-all-btn.hidden { display: none; }

  .select-toggle-btn {
    flex-shrink: 0; box-sizing: border-box;
    background: rgba(255,255,255,0.12); color: #fff; border: 1.5px solid rgba(255,255,255,0.28);
    border-radius: 12px; padding: 14px 16px;
    font-size: 13px; font-weight: 800;
    display: flex; align-items: center; justify-content: center; gap: 6px;
    cursor: pointer;
  }
  .select-toggle-btn svg { width: 15px; height: 15px; }
  .select-toggle-btn:hover { background: rgba(255,255,255,0.2); }
  .select-toggle-btn.hidden { display: none; }

  /* Bulk-select bar — shown instead of the day-picker while picking classes
     to delete, so "select all" only ever applies to what's on screen. */
  .select-bar {
    display: flex; align-items: center; justify-content: space-between; gap: 10px;
    background: rgba(255,255,255,0.1); border-radius: 14px; padding: 11px 14px; margin-bottom: 18px;
  }
  .select-bar.hidden { display: none; }
  .select-all-label { display: flex; align-items: center; gap: 8px; font-size: 12.5px; font-weight: 700; color: #fff; cursor: pointer; }
  .select-all-label input { width: 16px; height: 16px; accent-color: #2ec4c6; cursor: pointer; }
  .select-bar-actions { display: flex; gap: 8px; }
  .select-cancel-btn {
    background: none; border: 1.5px solid rgba(255,255,255,0.32); color: #fff;
    border-radius: 10px; padding: 8px 14px; font-size: 12px; font-weight: 700; cursor: pointer;
  }
  .select-delete-btn {
    background: #dc2626; color: #fff; border: none;
    border-radius: 10px; padding: 8px 14px; font-size: 12px; font-weight: 800; cursor: pointer;
  }
  .select-delete-btn:disabled { opacity: 0.45; cursor: not-allowed; }

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

  /* Mobile "Today" heading — replaces the plain header title on small screens,
     hidden again on desktop where the original header text is shown instead. */
  .today-heading { display: none; margin-bottom: 16px; }
  .today-date { font-size: 12px; font-weight: 700; color: #bfe9ea; margin: 0 0 2px; }
  .today-title { font-size: 23px; font-weight: 900; color: #fff; margin: 0; }

  /* Mon-Sun day-picker strip with real calendar dates for the current week */
  .day-picker { display: flex; gap: 7px; overflow-x: auto; padding-bottom: 2px; margin-bottom: 18px; }
  .day-picker-item {
    flex: 1; min-width: 40px; border: none; background: rgba(255,255,255,0.1); border-radius: 14px;
    padding: 9px 4px; display: flex; flex-direction: column; align-items: center; gap: 5px; cursor: pointer;
  }
  .dp-label { font-size: 10px; font-weight: 800; color: #bfe9ea; text-transform: uppercase; letter-spacing: 0.02em; }
  .dp-date {
    font-size: 12.5px; font-weight: 800; color: #fff; width: 22px; height: 22px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
  }
  .day-picker-item.active { background: #fff; }
  .day-picker-item.active .dp-label { color: #14213d; }
  .day-picker-item.active .dp-date { background: #14213d; color: #fff; }

  /* Single-day timeline (mobile "Today" view) */
  .timeline { display: flex; flex-direction: column; }

  .timeline-row { display: flex; gap: 12px; align-items: flex-start; }

  .timeline-time {
    flex-shrink: 0; width: 50px; padding-top: 14px;
    display: flex; flex-direction: column; align-items: center; gap: 7px;
  }
  .timeline-time span { font-size: 10.5px; font-weight: 800; color: #e6fbfa; text-align: center; line-height: 1.2; }
  .timeline-dot {
    width: 10px; height: 10px; border-radius: 50%; background: #2ec4c6;
    border: 2px solid #14213d; box-shadow: 0 0 0 2px rgba(255,255,255,0.25);
  }

  .timeline-card {
    flex: 1; min-width: 0; border-radius: 16px; padding: 13px 12px 13px 15px;
    display: flex; align-items: flex-start; justify-content: space-between; gap: 8px;
    margin-bottom: 14px; position: relative; border-left: 4px solid transparent;
  }
  .timeline-card.t1 { background: #eafbfa; border-left-color: #2ec4c6; }
  .timeline-card.t2 { background: #eef1f8; border-left-color: #14213d; }
  .timeline-card.t3 { background: #e8f6f0; border-left-color: #17a589; }
  .timeline-card.t4 { background: #eaf3fb; border-left-color: #1b6ea6; }

  .timeline-card-main { flex: 1; min-width: 0; }
  .timeline-subject { font-size: 13.5px; font-weight: 800; color: #14213d; margin: 0 0 3px; }
  .timeline-meta { font-size: 11.5px; color: #475569; margin: 0 0 3px; }
  .timeline-lecturer { font-size: 11px; color: #64748b; margin: 0; }

  .timeline-checkbox { flex-shrink: 0; display: flex; align-items: center; padding-top: 3px; }
  .timeline-checkbox input { width: 18px; height: 18px; accent-color: #2ec4c6; cursor: pointer; }

  .timeline-menu-wrap { position: relative; flex-shrink: 0; }
  .timeline-menu-btn {
    width: 26px; height: 26px; border-radius: 8px; border: none;
    background: rgba(20,33,61,0.07); color: #14213d; font-size: 15px; font-weight: 900;
    display: flex; align-items: center; justify-content: center; cursor: pointer; line-height: 1;
  }
  .timeline-menu {
    display: none; position: absolute; right: 0; top: 30px; z-index: 10;
    background: #fff; border-radius: 10px; box-shadow: 0 10px 26px rgba(0,0,0,0.2);
    overflow: hidden; min-width: 108px;
  }
  .timeline-menu.open { display: block; }
  .timeline-menu button {
    display: block; width: 100%; text-align: left; padding: 10px 14px; border: none;
    background: none; font-size: 12.5px; font-weight: 700; color: #14213d; cursor: pointer;
  }
  .timeline-menu button.danger { color: #dc2626; }
  .timeline-menu button:hover { background: #f1f5f9; }

  .break-pill { display: flex; align-items: center; margin: 0 0 14px 62px; }
  .break-pill span {
    font-size: 10.5px; font-weight: 700; color: #bfe9ea; background: rgba(255,255,255,0.1);
    padding: 4px 10px; border-radius: 999px;
  }

  @media (max-width: 860px) {
    .today-heading { display: block; }
  }

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

  .delete-btn {
    width: 100%; margin-top: 10px; background: #fef2f2; color: #dc2626;
    border: 1.5px solid #fecaca; border-radius: 12px; padding: 12px; font-size: 13.5px;
    font-weight: 800; cursor: pointer; display: none;
  }
  .delete-btn.open { display: block; }
  .delete-btn:hover { background: #fee2e2; }

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

  /* Desktop weekly grid — hidden on mobile, shown instead of the day-list at >=861px */
  .grid-wrap { display: none; }

  .grid-header {
    display: grid;
    grid-template-columns: 56px repeat(7, minmax(96px, 1fr));
    gap: 8px;
    margin-bottom: 8px;
  }

  .grid-day-head {
    font-size: 11px; font-weight: 800; letter-spacing: 0.04em; text-transform: uppercase;
    color: #64748b; text-align: center; padding-bottom: 4px;
  }

  .grid-body { display: grid; grid-template-columns: 56px 1fr; gap: 8px; overflow-x: auto; }

  .grid-gutter { position: relative; }

  .grid-hour-label {
    position: absolute; right: 8px; transform: translateY(-50%);
    font-size: 10.5px; font-weight: 700; color: #94a3b8; white-space: nowrap;
  }

  .grid-columns {
    position: relative;
    display: grid;
    grid-template-columns: repeat(7, minmax(96px, 1fr));
    gap: 8px;
  }

  .grid-day-column {
    position: relative;
    background-color: #ffffff;
    border: 1px solid #dbeeee;
    border-radius: 10px;
  }

  .grid-block {
    position: absolute; left: 3px; right: 3px; min-height: 26px;
    background: linear-gradient(120deg, #14213d, #2ec4c6);
    border-radius: 8px; padding: 5px 7px; overflow: hidden; cursor: pointer;
    box-shadow: 0 3px 8px rgba(20,33,61,0.18);
    transition: transform 0.12s ease, box-shadow 0.12s ease;
  }
  .grid-block:hover { transform: translateY(-1px); box-shadow: 0 6px 14px rgba(20,33,61,0.28); }

  .grid-block-time { font-size: 9.5px; font-weight: 800; color: #bfe9ea; margin: 0; line-height: 1.2; }
  .grid-block-subject { font-size: 11px; font-weight: 800; color: #fff; margin: 1px 0 0; line-height: 1.25; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; }
  .grid-block-meta { font-size: 9.5px; color: #bfe9ea; margin: 2px 0 0; line-height: 1.2; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

  @media (min-width: 861px) {
    body { background: #f0fafa; }
    .container { max-width: 1080px; margin: 0; padding: 36px 44px 90px; }
    .back-btn { display: none; }
    .header-title { color: #14213d; }
    .header-sub { color: #64748b; }
    .action-row { justify-content: flex-start; }
    .add-btn { flex: 0 0 auto; width: auto; padding: 12px 22px; }
    .delete-all-btn { padding: 12px 18px; }
    .select-toggle-btn { padding: 12px 18px; background: #eef2f5; color: #14213d; border-color: transparent; }
    .select-toggle-btn:hover { background: #e2e8f0; }
    .select-bar { background: #eef2f5; }
    .select-all-label { color: #14213d; }
    .select-cancel-btn { color: #14213d; border-color: rgba(20,33,61,0.2); }
    .day-label { color: #64748b; }
    .class-card { background: #ffffff; border: 1.5px solid #dbeeee; }
    .class-meta { color: #64748b; }
    .empty-day { color: #94a3b8; }
    .ai-fab { right: 40px; bottom: 30px; }

    /* Keep the same day-picker + single-day timeline layout used on mobile,
       just recolored to the site's light desktop palette instead of the
       dark gradient body. The weekly grid stays hidden (default). */
    .today-heading { display: block; }
    .today-date { color: #0d9488; }
    .today-title { color: #14213d; }

    .day-picker-item { background: #eef2f5; }
    .dp-label { color: #64748b; }
    .dp-date { color: #14213d; }
    .day-picker-item.active { background: #14213d; }
    .day-picker-item.active .dp-label { color: #bfe9ea; }
    .day-picker-item.active .dp-date { background: #fff; color: #14213d; }

    .timeline-time span { color: #94a3b8; }
    .timeline-card { border: 1.5px solid rgba(20,33,61,0.08); }
    .break-pill span { color: #64748b; background: #eef2f5; }
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

  <div class="action-row">
    <button type="button" class="add-btn" onclick="openAddModal()">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
      Add Class
    </button>
    <button type="button" class="delete-all-btn hidden" id="deleteAllBtn" onclick="deleteAllSchedules()">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"></path><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
      Delete All
    </button>
    <button type="button" class="select-toggle-btn hidden" id="selectToggleBtn" onclick="toggleSelectMode()">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m3 7 2 2 4-4"></path><path d="M11 7h10"></path><path d="m3 17 2 2 4-4"></path><path d="M11 17h10"></path></svg>
      Select
    </button>
  </div>

  <div class="select-bar hidden" id="selectBar">
    <label class="select-all-label">
      <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll(this.checked)">
      Select all
    </label>
    <div class="select-bar-actions">
      <button type="button" class="select-cancel-btn" onclick="toggleSelectMode()">Cancel</button>
      <button type="button" class="select-delete-btn" id="selectDeleteBtn" onclick="deleteSelectedSchedules()" disabled>Delete</button>
    </div>
  </div>

  <div class="today-heading" id="todayHeading">
    <p class="today-date" id="todayDateLine"></p>
    <p class="today-title" id="todayTitleLine"></p>
  </div>

  <div class="day-picker" id="dayPicker"></div>

  <div id="scheduleList"></div>

  <div class="grid-wrap" id="scheduleGridWrap">
    <div class="grid-header" id="gridHeaderRow"></div>
    <div class="grid-body">
      <div class="grid-gutter" id="gridGutter"></div>
      <div class="grid-columns" id="gridColumns"></div>
    </div>
  </div>
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
  <button type="button" class="delete-btn" id="deleteScheduleBtn" onclick="confirmDeleteFromModal()">Delete Class</button>
</div>

<script>
let schedules = @json($schedules);
const DAYS = @json($days);
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

// Bulk-select state for deleting a mix of classes at once — scoped to the
// currently viewed day (the only list the user can actually see), so it's
// reset whenever the selected day changes rather than growing invisibly.
let selectMode = false;
let selectedIds = new Set();

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

function toMinutes(hhmm) {
  const [h, m] = hhmm.split(':').map(Number);
  return h * 60 + m;
}

function formatDuration(mins) {
  if (mins < 60) return mins + ' min';
  const h = Math.floor(mins / 60);
  const m = mins % 60;
  return h + 'h' + (m > 0 ? ' ' + m + 'min' : '');
}

const MONTH_NAMES = ['January','February','March','April','May','June','July','August','September','October','November','December'];

// Real Mon-Sun dates for the current calendar week, matching DAYS order (Monday-first).
function getWeekDates() {
  const now = new Date();
  const jsDay = now.getDay(); // 0 = Sunday ... 6 = Saturday
  const mondayOffset = jsDay === 0 ? -6 : 1 - jsDay;
  const monday = new Date(now.getFullYear(), now.getMonth(), now.getDate() + mondayOffset);
  return DAYS.map((_, i) => new Date(monday.getFullYear(), monday.getMonth(), monday.getDate() + i));
}

const weekDates = getWeekDates();
const todayName = DAYS[(new Date().getDay() + 6) % 7]; // convert JS Sunday-first index to Monday-first
let selectedDay = todayName;

function renderTodayHeading() {
  const dateLine = document.getElementById('todayDateLine');
  const titleLine = document.getElementById('todayTitleLine');
  if (!dateLine || !titleLine) return;
  const d = weekDates[DAYS.indexOf(selectedDay)];
  dateLine.textContent = d.getDate() + ' ' + MONTH_NAMES[d.getMonth()];
  titleLine.textContent = selectedDay === todayName ? 'Today' : selectedDay;
}

function renderDayPicker() {
  const picker = document.getElementById('dayPicker');
  if (!picker) return;
  picker.innerHTML = DAYS.map((day, i) => {
    const d = weekDates[i];
    return `<button type="button" class="day-picker-item ${day === selectedDay ? 'active' : ''}" onclick="selectDay('${day}')">
        <span class="dp-label">${day.slice(0, 3)}</span>
        <span class="dp-date">${d.getDate()}</span>
      </button>`;
  }).join('');
}

function selectDay(day) {
  selectedDay = day;
  selectedIds.clear();
  selectMode = false;
  document.getElementById('selectBar').classList.add('hidden');
  renderDayPicker();
  renderTodayHeading();
  renderMobileTimeline();
  updateSelectToggleVisibility();
  updateSelectBar();
}

function toggleTimelineMenu(e, id) {
  e.stopPropagation();
  const menu = document.getElementById('timelineMenu-' + id);
  if (!menu) return;
  const wasOpen = menu.classList.contains('open');
  closeTimelineMenus();
  if (!wasOpen) menu.classList.add('open');
}

function closeTimelineMenus() {
  document.querySelectorAll('.timeline-menu.open').forEach(m => m.classList.remove('open'));
}

document.addEventListener('click', closeTimelineMenus);

const TIMELINE_TINTS = ['t1', 't2', 't3', 't4'];

function renderMobileTimeline() {
  const list = document.getElementById('scheduleList');
  if (!list) return;

  const items = schedules
    .filter(s => s.day_of_week === selectedDay)
    .sort((a, b) => a.start_time.localeCompare(b.start_time));

  if (items.length === 0) {
    list.innerHTML = `<p class="empty-day">No classes ${selectedDay === todayName ? 'today' : 'on ' + selectedDay}</p>`;
    return;
  }

  let html = '<div class="timeline">';
  items.forEach((s, idx) => {
    if (idx > 0) {
      const gap = toMinutes(s.start_time) - toMinutes(items[idx - 1].end_time);
      if (gap > 0) {
        html += `<div class="break-pill"><span>Break · ${formatDuration(gap)}</span></div>`;
      }
    }

    const tint = TIMELINE_TINTS[idx % TIMELINE_TINTS.length];
    html += `
      <div class="timeline-row">
        <div class="timeline-time">
          <span>${formatTime12(s.start_time)}</span>
          <span class="timeline-dot"></span>
        </div>
        <div class="timeline-card ${tint}" data-id="${s.id}">
          ${selectMode ? `
          <label class="timeline-checkbox">
            <input type="checkbox" data-id="${s.id}" ${selectedIds.has(s.id) ? 'checked' : ''} onchange="toggleScheduleSelected(${s.id}, this.checked)">
          </label>` : ''}
          <div class="timeline-card-main">
            <p class="timeline-subject">${escapeHtml(s.subject)}</p>
            <p class="timeline-meta">${formatTime12(s.start_time)} - ${formatTime12(s.end_time)}${s.room ? ' · ' + escapeHtml(s.room) : ''}</p>
            ${s.lecturer ? `<p class="timeline-lecturer">${escapeHtml(s.lecturer)}</p>` : ''}
          </div>
          ${!selectMode ? `
          <div class="timeline-menu-wrap">
            <button type="button" class="timeline-menu-btn" aria-label="More options" onclick="toggleTimelineMenu(event, ${s.id})">⋮</button>
            <div class="timeline-menu" id="timelineMenu-${s.id}">
              <button type="button" onclick="closeTimelineMenus(); openEditModal(${s.id})">Edit</button>
              <button type="button" class="danger" onclick="closeTimelineMenus(); removeSchedule(${s.id})">Delete</button>
            </div>
          </div>` : ''}
        </div>
      </div>`;
  });
  html += '</div>';
  list.innerHTML = html;
}

const GRID_HOUR_HEIGHT = 56; // px per hour in the desktop weekly grid

function renderDesktopGrid() {
  const headerRow = document.getElementById('gridHeaderRow');
  const gutter = document.getElementById('gridGutter');
  const columns = document.getElementById('gridColumns');
  if (!headerRow || !gutter || !columns) return;

  // Default window 7 AM - 6 PM, widened to fit any class outside that range,
  // rounded to whole hours so gridlines/labels land cleanly.
  let minStart = 7 * 60;
  let maxEnd = 18 * 60;
  schedules.forEach(s => {
    minStart = Math.min(minStart, toMinutes(s.start_time));
    maxEnd = Math.max(maxEnd, toMinutes(s.end_time));
  });
  minStart = Math.floor(minStart / 60) * 60;
  maxEnd = Math.ceil(maxEnd / 60) * 60;
  const totalMinutes = Math.max(maxEnd - minStart, 60);
  const totalHeight = (totalMinutes / 60) * GRID_HOUR_HEIGHT;

  headerRow.innerHTML = '<div></div>' + DAYS.map(d => `<div class="grid-day-head">${d.slice(0, 3)}</div>`).join('');

  gutter.style.height = totalHeight + 'px';
  let hourLabels = '';
  for (let m = minStart; m <= maxEnd; m += 60) {
    const top = ((m - minStart) / totalMinutes) * 100;
    const hh = String(Math.floor(m / 60)).padStart(2, '0');
    hourLabels += `<div class="grid-hour-label" style="top:${top}%;">${formatTime12(hh + ':00')}</div>`;
  }
  gutter.innerHTML = hourLabels;

  columns.style.height = totalHeight + 'px';
  columns.style.backgroundImage = `repeating-linear-gradient(to bottom, #e7f1f1 0, #e7f1f1 1px, transparent 1px, transparent ${GRID_HOUR_HEIGHT}px)`;

  columns.innerHTML = DAYS.map(day => {
    const items = schedules.filter(s => s.day_of_week === day);

    const blocks = items.map(s => {
      const start = toMinutes(s.start_time);
      const realEnd = toMinutes(s.end_time);
      // Actual short classes (a 15-min quiz slot, say) still get a real duration-
      // proportioned block, but never so thin the time/subject text has no room —
      // stretch the drawn box to a readable minimum without touching the saved data.
      const durationMinutes = Math.max(realEnd - start, 1);
      const drawnEnd = Math.max(realEnd, start + 40);
      const top = ((start - minStart) / totalMinutes) * 100;
      const height = ((drawnEnd - start) / totalMinutes) * 100;
      const meta = [s.room, s.lecturer].filter(Boolean).map(escapeHtml).join(' · ');

      return `<div class="grid-block" style="top:${top}%; height:${height}%;" onclick="openEditModal(${s.id})" title="${escapeHtml(s.subject)}">
          <p class="grid-block-time">${formatTime12(s.start_time)}</p>
          <p class="grid-block-subject">${escapeHtml(s.subject)}</p>
          ${meta && durationMinutes >= 45 ? `<p class="grid-block-meta">${meta}</p>` : ''}
        </div>`;
    }).join('');

    return `<div class="grid-day-column">${blocks}</div>`;
  }).join('');
}

function render() {
  renderTodayHeading();
  renderDayPicker();
  renderMobileTimeline();
  renderDesktopGrid();
  document.getElementById('deleteAllBtn').classList.toggle('hidden', schedules.length === 0);
  updateSelectToggleVisibility();
  updateSelectBar();
}

// Shows/hides the "Select" entry point based on whether the currently viewed
// day has any classes, and backs selection mode out on its own if that day
// just emptied (e.g. its last class was deleted another way). Called both
// after a full render() and after switching days, since selectDay() doesn't
// otherwise go through render().
function updateSelectToggleVisibility() {
  const dayHasClasses = schedules.some(s => s.day_of_week === selectedDay);
  document.getElementById('selectToggleBtn').classList.toggle('hidden', !dayHasClasses);
  if (selectMode && !dayHasClasses) {
    selectMode = false;
    selectedIds.clear();
    document.getElementById('selectBar').classList.add('hidden');
    renderMobileTimeline();
  }
}

function toggleSelectMode() {
  selectMode = !selectMode;
  selectedIds.clear();
  document.getElementById('selectBar').classList.toggle('hidden', !selectMode);
  renderMobileTimeline();
  updateSelectBar();
}

function toggleScheduleSelected(id, checked) {
  if (checked) {
    selectedIds.add(id);
  } else {
    selectedIds.delete(id);
  }
  updateSelectBar();
}

function toggleSelectAll(checked) {
  const dayIds = schedules.filter(s => s.day_of_week === selectedDay).map(s => s.id);
  if (checked) {
    dayIds.forEach(id => selectedIds.add(id));
  } else {
    selectedIds.clear();
  }
  renderMobileTimeline();
  updateSelectBar();
}

function updateSelectBar() {
  const selectAllCheckbox = document.getElementById('selectAllCheckbox');
  const deleteBtn = document.getElementById('selectDeleteBtn');
  if (!selectAllCheckbox || !deleteBtn) return;

  const dayIds = schedules.filter(s => s.day_of_week === selectedDay).map(s => s.id);
  const selectedOnDay = dayIds.filter(id => selectedIds.has(id));

  selectAllCheckbox.checked = dayIds.length > 0 && selectedOnDay.length === dayIds.length;
  deleteBtn.disabled = selectedOnDay.length === 0;
  deleteBtn.textContent = selectedOnDay.length > 0 ? `Delete (${selectedOnDay.length})` : 'Delete';
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
  document.getElementById('dayInput').value = selectedDay;
  document.getElementById('startInput').value = '';
  document.getElementById('endInput').value = '';
  document.getElementById('roomInput').value = '';
  document.getElementById('lecturerInput').value = '';
  document.getElementById('deleteScheduleBtn').classList.remove('open');
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
  document.getElementById('deleteScheduleBtn').classList.add('open');
  showModal('modal');
}

function confirmDeleteFromModal() {
  const id = parseInt(document.getElementById('scheduleId').value, 10);
  if (!id) return;
  if (!confirm('Delete this class?')) return;
  closeModals();
  removeSchedule(id);
}

// Reads the response body as text first, then tries to parse JSON — a failed
// request (expired session -> 419, route/record issue -> 404, etc.) often comes
// back as an HTML error page, and calling res.json() directly on that throws and
// gets swallowed by a bare .catch(), which is why "nothing happens" when it fails.
function safeJson(res) {
  return res.text().then((text) => {
    let data = null;
    try { data = JSON.parse(text); } catch (e) { /* not JSON, e.g. an HTML error page */ }
    return { ok: res.ok, status: res.status, data };
  });
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
    .then(safeJson)
    .then(({ ok, status, data }) => {
      if (!ok || !data || !data.success) {
        if (status === 419) {
          alert('Your session has expired. Please refresh the page and try again.');
        } else {
          document.getElementById('formError').style.display = 'block';
        }
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
    .catch((err) => {
      console.error('Save failed', err);
      document.getElementById('formError').style.display = 'block';
    });
}

function removeSchedule(id) {
  fetch(`/timetable/${id}`, {
    method: 'DELETE',
    headers: { 'X-CSRF-TOKEN': csrfToken },
  })
    .then(safeJson)
    .then(({ ok, status }) => {
      // A 404 means this class is already gone on the server (stale local copy) —
      // still remove it from the visible list instead of leaving a dead card the
      // user can never clear. Any other failure gets an actual message, not silence.
      if (ok || status === 404) {
        schedules = schedules.filter(s => s.id !== id);
        render();
        return;
      }
      if (status === 419) {
        alert('Your session has expired. Please refresh the page and try again.');
      } else {
        alert('Could not delete this class right now. Please try again.');
      }
    })
    .catch((err) => {
      console.error('Delete failed', err);
      alert('Ada masalah sambungan. Cuba lagi.');
    });
}

function deleteAllSchedules() {
  if (schedules.length === 0) return;
  if (!confirm(`Delete all ${schedules.length} class(es) from your timetable? This cannot be undone.`)) return;

  fetch('{{ route('timetable.destroyAll') }}', {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': csrfToken },
  })
    .then(safeJson)
    .then(({ ok, status }) => {
      if (ok) {
        schedules = [];
        render();
        return;
      }
      if (status === 419) {
        alert('Your session has expired. Please refresh the page and try again.');
      } else {
        alert('Could not delete your classes right now. Please try again.');
      }
    })
    .catch((err) => {
      console.error('Delete all failed', err);
      alert('Ada masalah sambungan. Cuba lagi.');
    });
}

function deleteSelectedSchedules() {
  const ids = Array.from(selectedIds);
  if (ids.length === 0) return;
  if (!confirm(`Delete ${ids.length} selected class(es)? This cannot be undone.`)) return;

  fetch('{{ route('timetable.bulkDestroy') }}', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
    body: JSON.stringify({ ids }),
  })
    .then(safeJson)
    .then(({ ok, status }) => {
      if (ok) {
        schedules = schedules.filter(s => !selectedIds.has(s.id));
        selectMode = false;
        selectedIds.clear();
        document.getElementById('selectBar').classList.add('hidden');
        render();
        return;
      }
      if (status === 419) {
        alert('Your session has expired. Please refresh the page and try again.');
      } else {
        alert('Could not delete the selected classes right now. Please try again.');
      }
    })
    .catch((err) => {
      console.error('Bulk delete failed', err);
      alert('Ada masalah sambungan. Cuba lagi.');
    });
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
        document.getElementById('aiErrorMsg').textContent = '⚠️ ' + (data.error || 'Could not process that image. Please try again.');
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
