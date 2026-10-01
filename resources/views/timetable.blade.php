<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
@include('partials.pwa-head')
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ __('RakanKampus - Timetable') }}</title>
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


  .header-title { font-size: 18px; font-weight: 800; color: #fff; margin: 0; }
  .header-sub { font-size: 12.5px; color: #bfe9ea; margin: 2px 0 0; }

  /* Same layout as the Reminders page: big "Add Class" button, then a small
     toolbar with the Select toggle (it turns into a red Cancel while selecting). */
  .add-btn {
    width: 100%; box-sizing: border-box;
    background: linear-gradient(120deg, #14213d, #2ec4c6);
    color: #fff; border: none; border-radius: 12px; padding: 14px;
    font-size: 14px; font-weight: 800;
    display: flex; align-items: center; justify-content: center; gap: 8px;
    cursor: pointer; margin-bottom: 18px;
    box-shadow: 0 10px 22px rgba(0,0,0,0.22);
  }
  .add-btn svg { width: 16px; height: 16px; }

  .heading-row { display: flex; align-items: flex-end; justify-content: space-between; gap: 12px; margin-bottom: 16px; }
  .heading-row .today-heading { margin-bottom: 0; }
  .heading-row .select-btn { margin-left: auto; flex-shrink: 0; }
  .select-btn {
    display: flex; align-items: center; justify-content: center; gap: 5px;
    background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.25); color: #bfe9ea;
    border-radius: 8px; padding: 9px 14px; font-size: 11.5px; font-weight: 700; cursor: pointer;
  }
  .select-btn svg { width: 11px; height: 11px; }
  .select-btn.active { background: #dc2626; border-color: #dc2626; color: #fff; }
  .select-btn.hidden { display: none; }

  /* Select mode — same as the Reminders page: a "Select all" row above the
     list and a red "Delete · n" bar pinned to the bottom, as wide as the list. */
  .select-bar { display: flex; align-items: center; justify-content: space-between; gap: 10px; padding: 0 2px 12px; }
  .select-bar.hidden { display: none; }
  .select-all-label { display: flex; align-items: center; gap: 8px; font-size: 12px; font-weight: 700; color: #bfe9ea; cursor: pointer; }
  .select-all-label input { width: 16px; height: 16px; accent-color: #dc2626; cursor: pointer; }
  .delete-week-link { background: none; border: none; padding: 0; font-size: 11.5px; font-weight: 700; color: #fca5a5; text-decoration: underline; cursor: pointer; }
  .delete-week-link.hidden { display: none; }

  /* Select mode: small red "Delete (n)" button at the right end of the Select All row */
  .bulk-delete-btn {
    display: inline-flex; align-items: center; gap: 6px; flex-shrink: 0;
    background: #dc2626; color: #fff; border: 1px solid #dc2626; border-radius: 8px;
    padding: 7px 12px; font-size: 11.5px; font-weight: 700; cursor: pointer;
    transition: opacity .15s, background .15s;
  }
  .bulk-delete-btn:hover:not(:disabled) { background: #b91c1c; }
  .bulk-delete-btn:disabled { opacity: .45; cursor: default; }
  .bulk-delete-btn svg { width: 12px; height: 12px; }
  .select-bar-actions { display: flex; align-items: center; gap: 14px; }

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
  /* Picked in select mode — same pink as a selected reminder */
  .timeline-card:has(input[type=checkbox]:checked) { background: #fef2f2 !important; border-color: #fca5a5; }
  .timeline-card input[type=checkbox] { accent-color: #dc2626; }

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

  /* RakanKampus Assistant: the robot logo in a soft tile, gently bobbing */
  .modal-icon.rk-brand { width: 38px; height: 38px; border-radius: 12px; background: #e8f7f5; border: 1px solid #c9ece7; }
  /* the robot stands still in the tile and waves hello (no strolling) */
  .modal-icon.rk-brand { overflow: visible; }
  .modal-icon.rk-brand .tile-bot { width: 50px; height: 34px; margin: 0 -6px -1px; flex-shrink: 0; }
  .modal-icon.rk-brand .tile-bot .rb-walk, .modal-icon.rk-brand .tile-bot .rb-face { animation: none; }
  .modal-icon.rk-brand .tile-bot .rb-wave { animation-duration: .9s; }
  .modal-icon.rk-brand .tile-bot .rb-legL, .modal-icon.rk-brand .tile-bot .rb-legR { animation: none; }
  .modal-icon.rk-brand .tile-bot .rb-bob, .modal-icon.rk-brand .tile-bot .rb-shadow { animation-duration: 1.4s; }
  html[data-theme="dark"] .modal-icon.rk-brand { background: #e8f7f5 !important; border-color: #9fd9d1 !important; }
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

  .ai-desc { font-size: 13px; color: #334155; margin: 0 0 8px; line-height: 1.45; font-weight: 600; }
  .ai-tips { margin: 0 0 16px; padding-left: 18px; font-size: 12px; color: #64748b; line-height: 1.5; }
  .ai-tips li { margin-bottom: 2px; }
  .ai-tips b { color: #0d9488; }
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

  .ai-preview { display: none; margin-top: 16px; }
  .ai-preview.open { display: block; }
  .ai-preview-note { font-size: 12px; color: #475569; margin: 0 0 10px; line-height: 1.45; }
  .ai-preview-day { font-size: 11px; font-weight: 800; color: #0d9488; letter-spacing: 0.06em; text-transform: uppercase; margin: 14px 0 6px; }
  .ai-pv-card { border: 1.5px solid #e2e8f0; border-radius: 12px; padding: 10px; margin-bottom: 8px; position: relative; }
  .ai-pv-card.warn { border-color: #f59e0b; background: #fffbeb; }
  .ai-pv-card.new { border-color: #2ec4c6; background: #f0fdfa; }
  .ai-pv-card .ai-pv-remove { position: absolute; top: 8px; right: 8px; width: 24px; height: 24px; border: none; border-radius: 7px; background: #f1f5f9; color: #64748b; cursor: pointer; font-size: 14px; }
  .ai-pv-warn { font-size: 11px; color: #b45309; margin: 0 30px 6px 0; line-height: 1.35; }
  .ai-pv-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 6px; }
  .ai-pv-grid .full { grid-column: 1 / -1; padding-right: 30px; }
  .modal .ai-pv-grid input[type="text"], .modal .ai-pv-grid input[type="time"], .modal .ai-pv-grid select { padding: 7px 9px; font-size: 12.5px; border-radius: 8px; }
  .ai-pv-add { width: 100%; margin-top: 4px; padding: 10px; border: 1.5px dashed #94a3b8; border-radius: 12px; background: #f8fafc; color: #0d9488; font-size: 13px; font-weight: 800; cursor: pointer; font-family: inherit; }
  .ai-pv-add:hover { border-color: #0d9488; background: #f0fdfa; }
  .ai-pv-actions { display: flex; gap: 8px; margin-top: 12px; }
  .ai-pv-actions button { flex: 1; border: none; border-radius: 12px; padding: 12px; font-size: 13px; font-weight: 800; cursor: pointer; font-family: inherit; }
  .ai-pv-save { background: linear-gradient(120deg, #14213d, #2ec4c6); color: #fff; }
  .ai-pv-save:disabled { opacity: 0.5; cursor: default; }
  .ai-pv-cancel { background: #f1f5f9; color: #475569; }


  /* ---------- One-off programmes (camp, workshop, orientation week...) ---------- */
  .mode-seg { display: grid; grid-template-columns: 1fr 1fr; background: #f1f5f9; border-radius: 13px; padding: 4px; margin-bottom: 14px; }
  .mode-seg button { border: none; background: none; border-radius: 10px; padding: 9px 6px; font-size: 13px; font-weight: 800; color: #64748b; cursor: pointer; font-family: inherit; display: flex; align-items: center; justify-content: center; gap: 6px; transition: background .15s, color .15s; }
  .mode-seg button.on { background: #fff; color: #14213d; box-shadow: 0 2px 6px rgba(20,33,61,.12); }
  .mode-seg.hidden { display: none; }
  #progTimeField[hidden] { display: none; }
  .prog-dates { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
  .prog-dates .rkp-field { margin: 0; }
  .prog-chips { display: flex; flex-wrap: wrap; gap: 6px; margin: 8px 0 4px; }
  .prog-chips button { border: none; background: #f1f5f9; color: #475569; border-radius: 99px; padding: 7px 12px; font-size: 12px; font-weight: 800; cursor: pointer; font-family: inherit; }
  .prog-chips button.on { background: #14213d; color: #fff; }
  .prog-days-note { font-size: 11.5px; font-weight: 700; color: #0d9488; margin: 4px 0 0; min-height: 15px; }
  .allday-row { display: flex; align-items: center; justify-content: space-between; border: 1.5px solid #e2e8f0; border-radius: 10px; padding: 10px 12px; font-size: 13.5px; font-weight: 700; color: #14213d; cursor: pointer; margin-bottom: 8px; }
  .allday-row input { display: none; }
  .switch { width: 40px; height: 23px; border-radius: 99px; background: #cbd5e1; position: relative; transition: background .2s; flex-shrink: 0; }
  .switch::after { content: ''; position: absolute; left: 3px; top: 3px; width: 17px; height: 17px; border-radius: 50%; background: #fff; transition: transform .2s; box-shadow: 0 1px 3px rgba(0,0,0,.2); }
  .allday-row input:checked + .switch { background: #14b8a6; }
  .allday-row input:checked + .switch::after { transform: translateX(17px); }
  .prog-colors { display: flex; gap: 10px; }
  .prog-colors button { width: 28px; height: 28px; border-radius: 50%; border: none; cursor: pointer; padding: 0; transition: transform .12s; }
  .prog-colors button.on { box-shadow: 0 0 0 2px #fff, 0 0 0 4px #14213d; transform: scale(1.05); }
  .c-amber { --pc: #f59e0b; --pc2: #f97316; --pbg: #fff7ed; --pbd: #fed7aa; --ptx: #c2410c; }
  .c-violet { --pc: #8b5cf6; --pc2: #a855f7; --pbg: #f5f3ff; --pbd: #ddd6fe; --ptx: #6d28d9; }
  .c-pink { --pc: #ec4899; --pc2: #f43f5e; --pbg: #fdf2f8; --pbd: #fbcfe8; --ptx: #be185d; }
  .c-green { --pc: #22c55e; --pc2: #10b981; --pbg: #f0fdf4; --pbd: #bbf7d0; --ptx: #15803d; }
  .c-blue { --pc: #3b82f6; --pc2: #0ea5e9; --pbg: #eff6ff; --pbd: #bfdbfe; --ptx: #1d4ed8; }
  .prog-colors button { background: linear-gradient(135deg, var(--pc), var(--pc2)); }

  .prog-card {
    display: flex; align-items: center; gap: 12px; border-radius: 16px; padding: 12px 12px 12px 14px; margin-bottom: 14px; cursor: pointer;
    background: var(--pbg); border: 1.5px solid var(--pbd); position: relative; overflow: hidden; animation: progIn .35s ease;
  }
  .prog-card::before { content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 4px; background: linear-gradient(var(--pc), var(--pc2)); }
  .prog-ic { width: 40px; height: 40px; border-radius: 12px; display: grid; place-items: center; flex-shrink: 0; color: #fff; background: linear-gradient(135deg, var(--pc), var(--pc2)); box-shadow: 0 6px 14px rgba(0,0,0,.12); }
  .prog-ic svg { width: 20px; height: 20px; }
  .prog-main { flex: 1; min-width: 0; }
  .prog-title { margin: 0; font-size: 13.5px; font-weight: 800; color: #14213d; }
  .prog-meta { margin: 2px 0 0; font-size: 11.5px; color: var(--ptx); font-weight: 600; }
  .prog-day { flex-shrink: 0; font-size: 10.5px; font-weight: 800; color: var(--ptx); background: #fff; border: 1px solid var(--pbd); border-radius: 99px; padding: 5px 9px; white-space: nowrap; }
  .prog-bar { height: 4px; border-radius: 9px; background: rgba(0,0,0,.06); margin-top: 7px; overflow: hidden; }
  .prog-bar i { display: block; height: 100%; border-radius: 9px; background: linear-gradient(90deg, var(--pc), var(--pc2)); }
  @keyframes progIn { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: none; } }

  /* coloured line under days that have a programme */
  .day-picker-item { position: relative; }
  .dp-prog { display: flex; gap: 2px; height: 4px; width: 70%; justify-content: center; }
  .dp-prog i { flex: 1; max-width: 18px; border-radius: 4px; background: linear-gradient(90deg, var(--pc), var(--pc2)); }
  .dp-prog.empty { visibility: hidden; }

  .upcoming { margin-top: 6px; }
  .upcoming-title { font-size: 11.5px; font-weight: 800; color: #bfe9ea; text-transform: uppercase; letter-spacing: .04em; margin: 0 0 8px; }
  .up-row { display: flex; align-items: center; gap: 10px; padding: 9px 10px; border-radius: 14px; background: rgba(255,255,255,.1); margin-bottom: 8px; cursor: pointer; }
  .up-date { width: 42px; text-align: center; border-radius: 11px; padding: 5px 0; color: #fff; flex-shrink: 0; background: linear-gradient(135deg, var(--pc), var(--pc2)); }
  .up-date small { display: block; font-size: 9.5px; font-weight: 800; opacity: .9; text-transform: uppercase; }
  .up-date b { font-size: 16px; }
  .up-main { flex: 1; min-width: 0; }
  .up-main p { margin: 0; font-size: 13px; font-weight: 800; color: #fff; }
  .up-main span { font-size: 11px; color: #bfe9ea; }
  .up-left { font-size: 10.5px; font-weight: 800; border-radius: 99px; padding: 4px 8px; background: rgba(255,255,255,.16); color: #fff; white-space: nowrap; }
  @media (min-width: 861px) {
    .upcoming-title { color: #64748b; }
    .up-row { background: #fff; border: 1.5px solid rgba(20,33,61,0.08); }
    .up-main p { color: #14213d; }
    .up-main span { color: #64748b; }
    .up-left { background: var(--pbg); color: var(--ptx); }
  }
  @media (prefers-reduced-motion: reduce) { .prog-card { animation: none; } }
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
    .header-title { color: #14213d; }
    .header-sub { color: #64748b; }
    .add-btn { width: auto; padding: 12px 22px; }
    .select-btn { background: #ffffff; border: 1px solid #dbeeee; color: #0d9488; }
    .select-btn.active { background: #dc2626; border-color: #dc2626; color: #fff; }
    .select-all-label { color: #0d9488; }
    .delete-week-link { color: #dc2626; }
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
<style id="rk-dark-theme">
/* Dark theme — generated by scripts/dark-theme/generate.py, do not edit by hand.
   Hand-made fixes go in resources/views/partials/dark-fixes.blade.php */
html[data-theme="dark"] body { background-image: linear-gradient(160deg, #0c1320, #112031 55%, #1d6869); }
html[data-theme="dark"] .add-btn { box-shadow: 0 10px 22px rgba(0, 0, 0, 0.45); }
html[data-theme="dark"] .select-btn { border: 1px solid rgba(42, 51, 65, 0.25); }
html[data-theme="dark"] .class-card { background: #39231b; }
html[data-theme="dark"] .class-time { color: #dee1e9; background: #17202d; }
html[data-theme="dark"] .class-subject { color: #dee1e9; }
html[data-theme="dark"] .class-meta { color: #b0b6be; }
html[data-theme="dark"] .class-action-btn { color: #dee1e9; }
html[data-theme="dark"] .day-picker-item.active { background: #17202d; }
html[data-theme="dark"] .day-picker-item.active .dp-label { color: #dee1e9; }
html[data-theme="dark"] .timeline-card.t1 { background: #1c3b39; }
html[data-theme="dark"] .timeline-card.t2 { background: #1c253b; }
html[data-theme="dark"] .timeline-card.t3 { background: #1e3e30; }
html[data-theme="dark"] .timeline-card.t4 { background: #1c2d3b; }
html[data-theme="dark"] .timeline-card:has(input[type=checkbox]:checked) { background: #371a1a !important; border-color: #482828; }
html[data-theme="dark"] .timeline-subject { color: #dee1e9; }
html[data-theme="dark"] .timeline-meta { color: #d0d4da; }
html[data-theme="dark"] .timeline-lecturer { color: #b0b6be; }
html[data-theme="dark"] .timeline-menu-btn { color: #dee1e9; }
html[data-theme="dark"] .timeline-menu { background: #17202d; box-shadow: 0 10px 26px rgba(0, 0, 0, 0.41); }
html[data-theme="dark"] .timeline-menu button { color: #dee1e9; }
html[data-theme="dark"] .timeline-menu button.danger { color: #ef9e9e; }
html[data-theme="dark"] .timeline-menu button:hover { background: #10161f; }
html[data-theme="dark"] .ai-fab { background: #17202d; box-shadow: 0 10px 24px rgba(0, 0, 0, 0.59); }
html[data-theme="dark"] .ai-fab span { color: #dee1e9; }
html[data-theme="dark"] .modal { background: #17202d; box-shadow: 0 24px 60px rgba(0, 0, 0, 0.68); }
html[data-theme="dark"] .modal-icon.rk-brand { background: #1e3d39; border: 1px solid #284843; }
html[data-theme="dark"] .modal-title { color: #dee1e9; }
html[data-theme="dark"] .modal-close { background: #10161f; color: #b0b6be; }
html[data-theme="dark"] .field-label { color: #b0b6be; }
html[data-theme="dark"] .modal input[type="text"], html[data-theme="dark"] .modal input[type="time"], html[data-theme="dark"] .modal select { border: 1.5px solid #283648; color: #dee1e9; }
html[data-theme="dark"] .delete-btn { background: #371a1a; color: #ef9e9e; border: 1.5px solid #482828; }
html[data-theme="dark"] .delete-btn:hover { background: #3d1d1d; }
html[data-theme="dark"] .form-error { color: #ef9e9e; }
html[data-theme="dark"] .ai-desc { color: #d6dae1; }
html[data-theme="dark"] .ai-tips { color: #b0b6be; }
html[data-theme="dark"] .ai-tips b { color: #41eedf; }
html[data-theme="dark"] .ai-upload-label { background: #1b2238; border: 1.5px dashed #282e48; color: #aba6e7; }
html[data-theme="dark"] .ai-scanning { color: #aba6e7; }
html[data-theme="dark"] .ai-success { background: #1b3824; border: 1px solid #284833; }
html[data-theme="dark"] .ai-success p { color: #44e07e; }
html[data-theme="dark"] .ai-success button { color: #44e07e; }
html[data-theme="dark"] .ai-error { background: #371a1a; border: 1px solid #482828; }
html[data-theme="dark"] .ai-error p { color: #eb7979; }
html[data-theme="dark"] .ai-error button { color: #eb7979; }
html[data-theme="dark"] .ai-preview-note { color: #d0d4da; }
html[data-theme="dark"] .ai-preview-day { color: #41eedf; }
html[data-theme="dark"] .ai-pv-card { border: 1.5px solid #283648; }
html[data-theme="dark"] .ai-pv-card.warn { background: #39331b; }
html[data-theme="dark"] .ai-pv-card.new { background: #1b3831; }
html[data-theme="dark"] .ai-pv-card .ai-pv-remove { background: #10161f; color: #b0b6be; }
html[data-theme="dark"] .ai-pv-warn { color: #f79b55; }
html[data-theme="dark"] .ai-pv-add { background: #141b26; color: #41eedf; }
html[data-theme="dark"] .ai-pv-add:hover { background: #1b3831; }
html[data-theme="dark"] .ai-pv-cancel { background: #10161f; color: #d0d4da; }
html[data-theme="dark"] .grid-day-head { color: #b0b6be; }
html[data-theme="dark"] .grid-hour-label { color: #ced3d9; }
html[data-theme="dark"] .grid-day-column { background-color: #17202d; border: 1px solid #284848; }
html[data-theme="dark"] .grid-block { box-shadow: 0 3px 8px rgba(0, 0, 0, 0.37); }
html[data-theme="dark"] .grid-block:hover { box-shadow: 0 6px 14px rgba(0, 0, 0, 0.55); }
@media (min-width: 861px) {
  html[data-theme="dark"] body { background: #10161f; }
  html[data-theme="dark"] .header-title { color: #dee1e9; }
  html[data-theme="dark"] .header-sub { color: #b0b6be; }
  html[data-theme="dark"] .select-btn { background: #17202d; border: 1px solid #284848; color: #41eedf; }
  html[data-theme="dark"] .select-all-label { color: #41eedf; }
  html[data-theme="dark"] .delete-week-link { color: #ef9e9e; }
  html[data-theme="dark"] .day-label { color: #b0b6be; }
  html[data-theme="dark"] .class-card { background: #17202d; border: 1.5px solid #284848; }
  html[data-theme="dark"] .class-meta { color: #b0b6be; }
  html[data-theme="dark"] .empty-day { color: #ced3d9; }
  html[data-theme="dark"] .today-date { color: #41eedf; }
  html[data-theme="dark"] .today-title { color: #dee1e9; }
  html[data-theme="dark"] .day-picker-item { background: #1d2f3c; }
  html[data-theme="dark"] .dp-label { color: #b0b6be; }
  html[data-theme="dark"] .dp-date { color: #dee1e9; }
  html[data-theme="dark"] .day-picker-item.active .dp-date { background: #17202d; color: #dee1e9; }
  html[data-theme="dark"] .timeline-time span { color: #ced3d9; }
  html[data-theme="dark"] .break-pill span { color: #b0b6be; background: #1d2f3c; }
}

/* programmes — dark */
html[data-theme="dark"] .mode-seg { background: #10161f; }
html[data-theme="dark"] .mode-seg button { color: #94a3b8; }
html[data-theme="dark"] .mode-seg button.on { background: #1f2a37; color: #e2e8f0; }
html[data-theme="dark"] .prog-chips button { background: #10161f; color: #cbd5e1; }
html[data-theme="dark"] .prog-chips button.on { background: #e2e8f0; color: #14213d; }
html[data-theme="dark"] .allday-row { border-color: #283648; color: #dee1e9; }
html[data-theme="dark"] .prog-colors button.on { box-shadow: 0 0 0 2px #17202d, 0 0 0 4px #e2e8f0; }
html[data-theme="dark"] .prog-card { background: #1f2430; border-color: #343c4c; }
html[data-theme="dark"] .prog-title { color: #e5e7eb; }
html[data-theme="dark"] .prog-meta { color: #cbd5e1; }
html[data-theme="dark"] .prog-day { background: #10161f; border-color: #343c4c; color: #e5e7eb; }
@media (min-width: 861px) {
  html[data-theme="dark"] .upcoming-title { color: #b0b6be; }
  html[data-theme="dark"] .up-row { background: #17202d; border-color: #284848; }
  html[data-theme="dark"] .up-main p { color: #dee1e9; }
  html[data-theme="dark"] .up-main span { color: #b0b6be; }
  html[data-theme="dark"] .up-left { background: #10161f; color: #e5e7eb; }
}
</style>
</head>
<body>

@include('partials.app-nav', ['active' => 'timetable', 'user' => $user])

<div class="container" id="pageContainer">
  <div class="header">
    <div>
      <p class="header-title">{{ __('Timetable') }}</p>
      <p class="header-sub">{{ __('Your weekly class schedule') }}</p>
    </div>
  </div>

  <button type="button" class="add-btn" onclick="openAddModal()">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
    {{ __('Add class / programme') }}
  </button>

  <div class="heading-row">
    <div class="today-heading" id="todayHeading">
      <p class="today-date" id="todayDateLine"></p>
      <p class="today-title" id="todayTitleLine"></p>
    </div>
    <button type="button" class="select-btn hidden" id="selectToggleBtn" onclick="toggleSelectMode()">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="4"></rect><path d="m8 12 3 3 5-6"></path></svg>
      <span id="selectToggleLabel">{{ __('Select') }}</span>
    </button>
  </div>

  <div class="day-picker" id="dayPicker"></div>

  <div class="select-bar hidden" id="selectBar">
    <label class="select-all-label">
      <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll(this.checked)">
      {{ __('Select All') }}
    </label>
    <div class="select-bar-actions">
      <button type="button" class="delete-week-link hidden" id="deleteAllBtn" onclick="deleteAllSchedules()">{{ __('Delete whole timetable') }}</button>
      <button type="button" class="bulk-delete-btn" id="selectDeleteBtn" onclick="deleteSelectedSchedules()" disabled>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
        <span>{{ __('Delete') }} (<span id="selectedCount">0</span>)</span>
      </button>
    </div>
  </div>

  <div id="scheduleList"></div>
  <div class="upcoming" id="upcomingPrograms"></div>

  <div class="grid-wrap" id="scheduleGridWrap">
    <div class="grid-header" id="gridHeaderRow"></div>
    <div class="grid-body">
      <div class="grid-gutter" id="gridGutter"></div>
      <div class="grid-columns" id="gridColumns"></div>
    </div>
  </div>
</div>

<x-rakankampus-fab onclick="openAiModal()" />

<div class="overlay" id="overlay" onclick="closeModals()"></div>

<div class="modal" id="aiModal">
  <div class="modal-head">
    <div class="modal-title-row">
      <div class="modal-icon rk-brand"><x-brand-bot-animated :size="34" class="tile-bot" /></div>
      <p class="modal-title">{{ __('RakanKampus Assistant') }}</p>
    </div>
    <button type="button" class="modal-close" aria-label="{{ __('Close') }}" onclick="closeModals()">×</button>
  </div>
  <p class="ai-desc">{{ __('Upload your class timetable and we\'ll fill in your classes for you.') }}</p>
  <ul class="ai-tips">
    <li><b>{{ __('PDF (recommended)') }}</b> {{ __('— the official timetable PDF is read exactly, 100% accurate.') }}</li>
    <li><b>{{ __('Photo / screenshot') }}</b> {{ __('— read by AI, so a few details may need fixing.') }}</li>
    <li>{{ __('You\'ll get a preview to check and edit before anything is saved.') }}</li>
  </ul>
  <label class="ai-upload-label">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2Z"></path><circle cx="12" cy="13" r="4"></circle></svg>
    {{ __('Upload PDF / Photo') }}
    <input type="file" accept="application/pdf,.pdf,image/*" onchange="handlePhotoSelected(event)">
  </label>
  <p class="ai-scanning" id="aiScanning">{{ __('🔎 Reading timetable and detecting classes...') }}</p>
  <div class="ai-success" id="aiSuccess">
    <p id="aiSuccessMsg"></p>
    <button type="button" aria-label="{{ __('Dismiss') }}" onclick="dismissAiMsg()">×</button>
  </div>
  <div class="ai-error" id="aiError">
    <p id="aiErrorMsg"></p>
    <button type="button" aria-label="{{ __('Dismiss') }}" onclick="dismissAiError()">×</button>
  </div>
  <div class="ai-preview" id="aiPreview">
    <p class="ai-preview-note">{{ __('Check the classes below. Fix anything that is wrong, remove what you don\'t need, then press Save.') }} <b>{{ __('Yellow cards need checking.') }}</b></p>
    <div id="aiPreviewList"></div>
    <button type="button" class="ai-pv-add" onclick="addAiPreview()">{{ __('+ Add class') }}</button>
    <div class="ai-pv-actions">
      <button type="button" class="ai-pv-cancel" onclick="cancelAiPreview()">{{ __('Cancel') }}</button>
      <button type="button" class="ai-pv-save" id="aiPreviewSaveBtn" onclick="saveAiPreview()">{{ __('Save') }}</button>
    </div>
  </div>
</div>

<div class="modal" id="modal">
  <div class="modal-head">
    <p class="modal-title" id="modalTitle">{{ __('Add Class') }}</p>
    <button type="button" class="modal-close" aria-label="{{ __('Close') }}" onclick="closeModals()">×</button>
  </div>

  <div class="mode-seg" id="modeSeg">
    <button type="button" data-mode="class" onclick="setMode('class')">📚 {{ __('Weekly class') }}</button>
    <button type="button" data-mode="program" onclick="setMode('program')">🎯 {{ __('Programme') }}</button>
  </div>

  <input type="hidden" id="scheduleId">
  <input type="hidden" id="programId">
  <input type="text" id="subjectInput" placeholder="{{ __('e.g. Database Systems') }}">

  <div id="classFields">
  <p class="field-label">{{ __('Day') }}</p>
  <select id="dayInput" hidden>
    @foreach($days as $d)
      <option value="{{ $d }}">{{ __($d) }}</option>
    @endforeach
  </select>
  <div class="rkp-days" id="dayChips" style="margin-bottom:12px">
    @foreach($days as $d)
      <button type="button" data-day="{{ $d }}" onclick="pickDay('{{ $d }}')">{{ __(substr($d, 0, 3)) }}</button>
    @endforeach
  </div>

  <p class="field-label">{{ __('Time') }}</p>
  <input type="hidden" id="startInput">
  <input type="hidden" id="endInput">
  <button type="button" class="rkp-field empty" id="timeField" onclick="openClassTimePicker()" style="margin-bottom:12px">
    <span><small>{{ __('Start – End') }}</small><b id="timeFieldText">{{ __('Pick class time') }}</b></span>
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v5l3 3"></path></svg>
  </button>

  <p class="field-label">{{ __('Room (optional)') }}</p>
  <input type="text" id="roomInput" placeholder="{{ __('e.g. Bilik Kuliah 3') }}">

  <p class="field-label">{{ __('Lecturer (optional)') }}</p>
  <input type="text" id="lecturerInput" placeholder="{{ __('e.g. En. Ahmad') }}">
  </div>

  <div id="programFields" hidden>
    <p class="field-label">{{ __('Dates') }}</p>
    <input type="hidden" id="progStart"><input type="hidden" id="progEnd">
    <div class="prog-dates">
      <button type="button" class="rkp-field empty" id="progStartField" onclick="pickProgDate('start')"><span><small>{{ __('Start') }}</small><b>{{ __('Pick date') }}</b></span>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="16" rx="2"></rect><path d="M16 3v4M8 3v4M3 10h18"></path></svg></button>
      <button type="button" class="rkp-field empty" id="progEndField" onclick="pickProgDate('end')"><span><small>{{ __('End') }}</small><b>{{ __('Pick date') }}</b></span>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="16" rx="2"></rect><path d="M16 3v4M8 3v4M3 10h18"></path></svg></button>
    </div>
    <div class="prog-chips" id="progChips">
      <button type="button" data-n="1" onclick="setProgLength(1)">{{ __('1 day') }}</button>
      <button type="button" data-n="2" onclick="setProgLength(2)">{{ __('2 days') }}</button>
      <button type="button" data-n="3" onclick="setProgLength(3)">{{ __('3 days') }}</button>
      <button type="button" data-n="7" onclick="setProgLength(7)">{{ __('A week') }}</button>
    </div>
    <p class="prog-days-note" id="progDaysNote"></p>

    <p class="field-label">{{ __('Time') }}</p>
    <label class="allday-row"><span>{{ __('All day') }}</span><input type="checkbox" id="progAllDay" checked onchange="syncProgramFields()"><span class="switch"></span></label>
    <input type="hidden" id="progStartTime"><input type="hidden" id="progEndTime">
    <button type="button" class="rkp-field empty" id="progTimeField" onclick="openProgTimePicker()" hidden>
      <span><small>{{ __('Start – End') }}</small><b id="progTimeText">{{ __('Pick time') }}</b></span>
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v5l3 3"></path></svg>
    </button>

    <p class="field-label">{{ __('Place (optional)') }}</p>
    <input type="text" id="progPlace" placeholder="{{ __('e.g. Dewan Besar PUO') }}">

    <p class="field-label">{{ __('Colour') }}</p>
    <div class="prog-colors" id="progColors">
      @foreach(['amber', 'violet', 'pink', 'green', 'blue'] as $c)
        <button type="button" class="c-{{ $c }}" data-color="{{ $c }}" aria-label="{{ $c }}" onclick="pickProgColor('{{ $c }}')"></button>
      @endforeach
    </div>
  </div>

  <p class="form-error" id="formError">{{ __('Please fill in subject, day and time.') }}</p>

  <button type="button" class="save-btn" id="saveBtn" onclick="saveSchedule()">{{ __('Save') }}</button>
  <button type="button" class="delete-btn" id="deleteScheduleBtn" onclick="confirmDeleteFromModal()">{{ __('Delete Class') }}</button>
</div>


@include('partials.rk-picker')

<script>
let schedules = @json($schedules);
let programs = @json($programs);
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
  if (mins < 60) return mins + ' ' + t('min');
  const h = Math.floor(mins / 60);
  const m = mins % 60;
  return h + t('h') + (m > 0 ? ' ' + m + t('min') : '');
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
  dateLine.textContent = d.getDate() + ' ' + t(MONTH_NAMES[d.getMonth()]);
  titleLine.textContent = selectedDay === todayName ? t('Today') : t(selectedDay);
}

function renderDayPicker() {
  const picker = document.getElementById('dayPicker');
  if (!picker) return;
  picker.innerHTML = DAYS.map((day, i) => {
    const d = weekDates[i];
    const progs = programsOn(isoOf(d)).slice(0, 3);
    const marks = `<span class="dp-prog ${progs.length ? '' : 'empty'}">${progs.map(p => `<i class="c-${p.color}"></i>`).join('')}</span>`;
    return `<button type="button" class="day-picker-item ${day === selectedDay ? 'active' : ''}" onclick="selectDay('${day}')" ${progs.length ? `title="${escapeHtml(progs.map(p => p.title).join(', '))}"` : ''}>
        <span class="dp-label">${t(day.slice(0, 3))}</span>
        <span class="dp-date">${d.getDate()}</span>
        ${marks}
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

  const dayIso = isoOf(weekDates[DAYS.indexOf(selectedDay)]);
  const progHtml = programsOn(dayIso).map(p => programCardHtml(p, dayIso)).join('');

  if (items.length === 0) {
    list.innerHTML = progHtml + `<p class="empty-day">${selectedDay === todayName ? t('No classes today') : t('No classes on :day', {day: t(selectedDay)})}</p>`;
    return;
  }

  let html = progHtml + '<div class="timeline">';
  items.forEach((s, idx) => {
    if (idx > 0) {
      const gap = toMinutes(s.start_time) - toMinutes(items[idx - 1].end_time);
      if (gap > 0) {
        html += `<div class="break-pill"><span>${t('Break')} · ${formatDuration(gap)}</span></div>`;
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
            <button type="button" class="timeline-menu-btn" aria-label="${t('More options')}" onclick="toggleTimelineMenu(event, ${s.id})">⋮</button>
            <div class="timeline-menu" id="timelineMenu-${s.id}">
              <button type="button" onclick="closeTimelineMenus(); openEditModal(${s.id})">${t('Edit')}</button>
              <button type="button" class="danger" onclick="closeTimelineMenus(); removeSchedule(${s.id})">${t('Delete')}</button>
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

  headerRow.innerHTML = '<div></div>' + DAYS.map(d => `<div class="grid-day-head">${t(d.slice(0, 3))}</div>`).join('');

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
  renderUpcomingPrograms();
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

function syncSelectButton() {
  const btn = document.getElementById('selectToggleBtn');
  btn.classList.toggle('active', selectMode);
  document.getElementById('selectToggleLabel').textContent = selectMode ? t('Cancel') : t('Select');
}

function updateSelectBar() {
  syncSelectButton();
  const selectAllCheckbox = document.getElementById('selectAllCheckbox');
  const deleteBtn = document.getElementById('selectDeleteBtn');
  if (!selectAllCheckbox || !deleteBtn) return;

  const dayIds = schedules.filter(s => s.day_of_week === selectedDay).map(s => s.id);
  const selectedOnDay = dayIds.filter(id => selectedIds.has(id));

  selectAllCheckbox.checked = dayIds.length > 0 && selectedOnDay.length === dayIds.length;
  document.getElementById('selectedCount').textContent = selectedOnDay.length;
  deleteBtn.disabled = selectedOnDay.length === 0;
}


// ---------- One-off programmes ----------
let modalMode = 'class';
let progColor = 'amber';
const PROG_ICON = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 21V4"/><path d="M5 4h11l-2 4 2 4H5"/></svg>';

function isoOf(d) { return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`; }
function dateOf(iso) { const [y, m, d] = iso.split('-').map(Number); return new Date(y, m - 1, d); }
function addDays(iso, n) { const d = dateOf(iso); d.setDate(d.getDate() + n); return isoOf(d); }
function dayCount(a, b) { return Math.round((dateOf(b) - dateOf(a)) / 86400000) + 1; }
function shortDate(iso) { return RKPicker.formatDate(iso); }
function daysLabel(n) { return n === 1 ? t('1 day') : t(':n days', {n}); }
function programsOn(iso) { return programs.filter(p => p.start_date <= iso && p.end_date >= iso); }
function programWhen(p) {
  return p.all_day ? t('All day') : `${formatTime12(p.start_time)} – ${formatTime12(p.end_time)}`;
}
function programRange(p) {
  const n = dayCount(p.start_date, p.end_date);
  return n === 1 ? shortDate(p.start_date) : `${shortDate(p.start_date)} – ${shortDate(p.end_date)} · ${daysLabel(n)}`;
}

function programCardHtml(p, iso) {
  const total = dayCount(p.start_date, p.end_date);
  const nth = dayCount(p.start_date, iso);
  const meta = [programWhen(p), p.place ? escapeHtml(p.place) : ''].filter(Boolean).join(' · ');
  return `<div class="prog-card c-${p.color}" onclick="openProgramModal(${p.id})" role="button" tabindex="0">
      <div class="prog-ic">${PROG_ICON}</div>
      <div class="prog-main">
        <p class="prog-title">${escapeHtml(p.title)}</p>
        <p class="prog-meta">${meta}</p>
        ${total > 1 ? `<div class="prog-bar"><i style="width:${Math.round(nth / total * 100)}%"></i></div>` : ''}
      </div>
      ${total > 1 ? `<span class="prog-day">${t('Day :n / :total', {n: nth, total})}</span>` : `<span class="prog-day">${t('Programme')}</span>`}
    </div>`;
}

// Programmes after this week (this week's ones already show on their days)
function renderUpcomingPrograms() {
  const box = document.getElementById('upcomingPrograms');
  if (!box) return;
  const weekEnd = isoOf(weekDates[6]);
  const today = isoOf(new Date());
  const list = programs.filter(p => p.start_date > weekEnd).slice(0, 4);
  if (!list.length) { box.innerHTML = ''; return; }
  box.innerHTML = `<p class="upcoming-title">${t('Upcoming programmes')}</p>` + list.map(p => {
    const d = dateOf(p.start_date);
    const left = dayCount(today, p.start_date) - 1;
    return `<div class="up-row c-${p.color}" onclick="openProgramModal(${p.id})" role="button" tabindex="0">
        <div class="up-date"><small>${t(MONTH_NAMES[d.getMonth()]).slice(0, 3)}</small><b>${d.getDate()}</b></div>
        <div class="up-main"><p>${escapeHtml(p.title)}</p><span>${programRange(p)}${p.place ? ' · ' + escapeHtml(p.place) : ''}</span></div>
        <span class="up-left">${left === 1 ? t('Tomorrow') : t('in :count days', {count: left})}</span>
      </div>`;
  }).join('');
}

function setMode(mode) {
  modalMode = mode;
  const isProg = mode === 'program';
  document.querySelectorAll('#modeSeg button').forEach(b => b.classList.toggle('on', b.dataset.mode === mode));
  document.getElementById('classFields').hidden = isProg;
  document.getElementById('programFields').hidden = !isProg;
  document.getElementById('subjectInput').placeholder = isProg ? t('e.g. Kem Kepimpinan JTMK') : t('e.g. Database Systems');
  document.getElementById('formError').style.display = 'none';
  const editing = isProg ? !!document.getElementById('programId').value : !!document.getElementById('scheduleId').value;
  document.getElementById('modalTitle').textContent = isProg ? (editing ? t('Edit Programme') : t('Add Programme')) : (editing ? t('Edit Class') : t('Add Class'));
  document.getElementById('saveBtn').textContent = isProg ? t('Save programme') : t('Save');
  document.getElementById('deleteScheduleBtn').textContent = isProg ? t('Delete Programme') : t('Delete Class');
  if (isProg) syncProgramFields();
}

function resetProgramFields(startIso) {
  document.getElementById('programId').value = '';
  document.getElementById('progStart').value = startIso || '';
  document.getElementById('progEnd').value = startIso || '';
  document.getElementById('progAllDay').checked = true;
  document.getElementById('progStartTime').value = '';
  document.getElementById('progEndTime').value = '';
  document.getElementById('progPlace').value = '';
  progColor = 'amber';
}

function syncProgramFields() {
  const a = document.getElementById('progStart').value, b = document.getElementById('progEnd').value;
  const setField = (id, iso) => {
    const el = document.getElementById(id);
    el.classList.toggle('empty', !iso);
    el.querySelector('b').textContent = iso ? shortDate(iso) : t('Pick date');
  };
  setField('progStartField', a); setField('progEndField', b);
  const n = a && b ? dayCount(a, b) : 0;
  document.querySelectorAll('#progChips button').forEach(c => c.classList.toggle('on', +c.dataset.n === n));
  document.getElementById('progDaysNote').textContent = n > 0 ? `✓ ${daysLabel(n)} · ${shortDate(a)}${n > 1 ? ' – ' + shortDate(b) : ''}` : '';
  const allDay = document.getElementById('progAllDay').checked;
  const tf = document.getElementById('progTimeField');
  tf.hidden = allDay;
  const ts = document.getElementById('progStartTime').value, te = document.getElementById('progEndTime').value;
  tf.classList.toggle('empty', !(ts && te));
  document.getElementById('progTimeText').textContent = ts && te ? `${RKPicker.formatTime(ts)} – ${RKPicker.formatTime(te)}` : t('Pick time');
  document.querySelectorAll('#progColors button').forEach(c => c.classList.toggle('on', c.dataset.color === progColor));
}

function pickProgDate(which) {
  const a = document.getElementById('progStart').value, b = document.getElementById('progEnd').value;
  RKPicker.date({
    title: which === 'start' ? t('Start date') : t('End date'),
    subtitle: document.getElementById('subjectInput').value.trim(),
    date: which === 'start' ? a : (b || a),
    min: which === 'end' ? a : '',
    range: [a, b],
    onDone(iso) {
      if (which === 'start') {
        // keep the same length when the start moves
        const len = a && b ? dayCount(a, b) : 1;
        document.getElementById('progStart').value = iso;
        document.getElementById('progEnd').value = addDays(iso, len - 1);
      } else {
        document.getElementById('progEnd').value = iso < a ? a : iso;
      }
      syncProgramFields();
    },
  });
}

function setProgLength(n) {
  let a = document.getElementById('progStart').value;
  if (!a) { a = isoOf(weekDates[DAYS.indexOf(selectedDay)]); document.getElementById('progStart').value = a; }
  document.getElementById('progEnd').value = addDays(a, n - 1);
  syncProgramFields();
}

function pickProgColor(c) { progColor = c; syncProgramFields(); }

function openProgTimePicker() {
  RKPicker.timeRange({
    title: t('Programme time'),
    subtitle: document.getElementById('subjectInput').value.trim(),
    start: document.getElementById('progStartTime').value,
    end: document.getElementById('progEndTime').value,
    onDone(start, end) {
      document.getElementById('progStartTime').value = start;
      document.getElementById('progEndTime').value = end;
      syncProgramFields();
    },
  });
}

function openProgramModal(id) {
  const p = programs.find(x => x.id === id);
  if (!p) return;
  resetProgramFields();
  document.getElementById('programId').value = p.id;
  document.getElementById('scheduleId').value = '';
  document.getElementById('subjectInput').value = p.title;
  document.getElementById('progStart').value = p.start_date;
  document.getElementById('progEnd').value = p.end_date;
  document.getElementById('progAllDay').checked = !!p.all_day;
  document.getElementById('progStartTime').value = p.start_time || '';
  document.getElementById('progEndTime').value = p.end_time || '';
  document.getElementById('progPlace').value = p.place || '';
  progColor = p.color || 'amber';
  document.getElementById('modeSeg').classList.add('hidden');
  document.getElementById('deleteScheduleBtn').classList.add('open');
  showModal('modal');
  setMode('program');
}

function saveProgram() {
  const id = document.getElementById('programId').value;
  const err = document.getElementById('formError');
  const title = document.getElementById('subjectInput').value.trim();
  const start_date = document.getElementById('progStart').value;
  const end_date = document.getElementById('progEnd').value;
  const all_day = document.getElementById('progAllDay').checked;
  const start_time = document.getElementById('progStartTime').value;
  const end_time = document.getElementById('progEndTime').value;
  const showErr = (msg) => { err.textContent = msg; err.style.display = 'block'; };

  if (!title || !start_date || !end_date) return showErr(t('Please fill in the programme name and dates.'));
  if (!all_day && !(start_time && end_time)) return showErr(t('Pick a time, or switch on "All day".'));
  if (dayCount(start_date, end_date) > 31) return showErr(t('A programme can be at most :days days long.', {days: 31}));

  const payload = { title, start_date, end_date, all_day, start_time: all_day ? null : start_time, end_time: all_day ? null : end_time,
    place: document.getElementById('progPlace').value.trim() || null, color: progColor };
  const btn = document.getElementById('saveBtn');
  btn.disabled = true;
  fetch(id ? `/programs/${id}` : '/programs', {
    method: id ? 'PUT' : 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
    body: JSON.stringify(payload),
  })
    .then(safeJson)
    .then(({ ok, status, data }) => {
      if (!ok || !data || !data.success) {
        if (status === 419) return oops(t('Your session has expired. Please refresh the page and try again.'));
        const first = data && data.errors ? Object.values(data.errors)[0][0] : null;
        return showErr(first || t('Could not save. Please try again.'));
      }
      programs = id ? programs.map(p => p.id === parseInt(id, 10) ? data.program : p) : programs.concat(data.program);
      programs.sort((a, b) => a.start_date.localeCompare(b.start_date));
      closeModals();
      // jump to the programme's first day when it's in this week
      const wk = weekDates.map(isoOf);
      const i = wk.indexOf(data.program.start_date);
      if (!id && i >= 0) selectedDay = DAYS[i];
      render();
    })
    .catch(() => showErr(t('Could not save. Please try again.')))
    .finally(() => { btn.disabled = false; });
}

async function deleteProgram(id) {
  const p = programs.find(x => x.id === id);
  if (!p) return;
  const ok = await RKDialog.confirm({
    scene: 'timetable',
    title: t('Delete this programme?'),
    message: t('It will be removed from your timetable.'),
    list: [{ label: p.title, meta: programRange(p) }],
    warn: t('This cannot be undone.'),
    confirmText: t('Delete'),
  });
  if (!ok) return;
  closeModals();
  fetch(`/programs/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' } })
    .then(safeJson)
    .then(({ ok }) => {
      if (!ok) return oops(t('Could not delete. Please try again.'));
      programs = programs.filter(x => x.id !== id);
      render();
    });
}

function showModal(id) {
  const fe = document.getElementById('formError');
  fe.style.display = 'none';
  fe.textContent = t('Please fill in subject, day and time.');
  document.getElementById('overlay').classList.add('open');
  document.getElementById(id).classList.add('open');
}

function closeModals() {
  document.getElementById('overlay').classList.remove('open');
  document.getElementById('modal').classList.remove('open');
  document.getElementById('aiModal').classList.remove('open');
}

function openAddModal() {
  document.getElementById('modalTitle').textContent = t('Add Class');
  document.getElementById('scheduleId').value = '';
  document.getElementById('subjectInput').value = '';
  document.getElementById('dayInput').value = selectedDay;
  document.getElementById('startInput').value = '';
  document.getElementById('endInput').value = '';
  document.getElementById('roomInput').value = '';
  document.getElementById('lecturerInput').value = '';
  document.getElementById('deleteScheduleBtn').classList.remove('open');
  resetProgramFields(isoOf(weekDates[DAYS.indexOf(selectedDay)]));
  document.getElementById('modeSeg').classList.remove('hidden');
  syncClassFields();
  showModal('modal');
  setMode('class');
}

function openEditModal(id) {
  const s = schedules.find(x => x.id === id);
  if (!s) return;
  document.getElementById('modalTitle').textContent = t('Edit Class');
  document.getElementById('scheduleId').value = s.id;
  document.getElementById('subjectInput').value = s.subject;
  document.getElementById('dayInput').value = s.day_of_week;
  document.getElementById('startInput').value = s.start_time;
  document.getElementById('endInput').value = s.end_time;
  document.getElementById('roomInput').value = s.room || '';
  document.getElementById('lecturerInput').value = s.lecturer || '';
  document.getElementById('deleteScheduleBtn').classList.add('open');
  document.getElementById('programId').value = '';
  document.getElementById('modeSeg').classList.add('hidden');
  syncClassFields();
  showModal('modal');
  setMode('class');
}

// Day chips + "Start – End" field → clock picker (partials/rk-picker)
function pickDay(day) {
  document.getElementById('dayInput').value = day;
  syncClassFields();
}

function syncClassFields() {
  const day = document.getElementById('dayInput').value;
  document.querySelectorAll('#dayChips [data-day]').forEach(b => b.classList.toggle('on', b.dataset.day === day));
  const a = document.getElementById('startInput').value, b = document.getElementById('endInput').value;
  document.getElementById('timeField').classList.toggle('empty', !(a && b));
  document.getElementById('timeFieldText').textContent = (a && b)
    ? `${RKPicker.formatTime(a)} – ${RKPicker.formatTime(b)}`
    : t('Pick class time');
}

function openClassTimePicker() {
  RKPicker.timeRange({
    title: t('Class time'),
    subtitle: [document.getElementById('subjectInput').value.trim(), t(document.getElementById('dayInput').value)].filter(Boolean).join(' · '),
    start: document.getElementById('startInput').value,
    end: document.getElementById('endInput').value,
    onDone(start, end) {
      document.getElementById('startInput').value = start;
      document.getElementById('endInput').value = end;
      syncClassFields();
    },
  });
}

// Custom confirm (partials/rk-dialog): the robot carries the class card to the bin.
function classListFor(ids) {
  return schedules.filter(s => ids.includes(s.id)).map(s => ({
    label: s.subject,
    meta: `${t(s.day_of_week)} · ${RKPicker.formatTime(String(s.start_time).slice(0, 5))}`,
  }));
}
function oops(message) {
  RKDialog.alert({ scene: 'oops', title: t('Oops!'), message });
}

async function confirmDeleteFromModal() {
  if (modalMode === 'program') return deleteProgram(parseInt(document.getElementById('programId').value, 10));
  const id = parseInt(document.getElementById('scheduleId').value, 10);
  if (!id) return;
  const ok = await RKDialog.confirm({
    scene: 'timetable',
    title: t('Delete this class?'),
    message: t('It will be removed from your timetable.'),
    list: classListFor([id]),
    warn: t('This cannot be undone.'),
    confirmText: t('Delete'),
  });
  if (!ok) return;
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
  if (modalMode === 'program') return saveProgram();
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
          oops(t('Your session has expired. Please refresh the page and try again.'));
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
        RKToast.show({ text: t('Class deleted'), sub: t('Removed from your timetable') });
        return;
      }
      if (status === 419) {
        oops(t('Your session has expired. Please refresh the page and try again.'));
      } else {
        oops(t('Could not delete this class right now. Please try again.'));
      }
    })
    .catch((err) => {
      console.error('Delete failed', err);
      oops(t('Connection problem. Please try again.'));
    });
}

async function deleteAllSchedules() {
  if (schedules.length === 0) return;
  const ok = await RKDialog.confirm({
    scene: 'timetable',
    title: t('Delete whole timetable?'),
    message: t('Every class in your timetable will be removed.'),
    pill: t(':count class(es)', {count: schedules.length}),
    warn: t('This cannot be undone.'),
    confirmText: t('Delete all'),
  });
  if (!ok) return;

  fetch('{{ route('timetable.destroyAll') }}', {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': csrfToken },
  })
    .then(safeJson)
    .then(({ ok, status }) => {
      if (ok) {
        const n = schedules.length;
        schedules = [];
        render();
        RKToast.show({ text: t('Timetable cleared'), sub: t(':count class(es) deleted', {count: n}) });
        return;
      }
      if (status === 419) {
        oops(t('Your session has expired. Please refresh the page and try again.'));
      } else {
        oops(t('Could not delete your classes right now. Please try again.'));
      }
    })
    .catch((err) => {
      console.error('Delete all failed', err);
      oops(t('Connection problem. Please try again.'));
    });
}

async function deleteSelectedSchedules() {
  const ids = Array.from(selectedIds);
  if (ids.length === 0) return;
  const ok = await RKDialog.confirm({
    scene: 'timetable',
    title: t('Delete :count class(es)?', {count: ids.length}),
    message: t('These classes will be removed from your timetable.'),
    list: classListFor(ids),
    warn: t('This cannot be undone.'),
    confirmText: t('Delete'),
  });
  if (!ok) return;

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
        RKToast.show({ text: t(':count class(es) deleted', {count: ids.length}), sub: t('Removed from your timetable') });
        return;
      }
      if (status === 419) {
        oops(t('Your session has expired. Please refresh the page and try again.'));
      } else {
        oops(t('Could not delete the selected classes right now. Please try again.'));
      }
    })
    .catch((err) => {
      console.error('Bulk delete failed', err);
      oops(t('Connection problem. Please try again.'));
    });
}

function openAiModal() {
  if (aiPreview.length === 0) document.getElementById('aiPreview').classList.remove('open');
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
    headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
    body: formData,
  })
    .then(safeJson)
    .then(({ ok, status, data }) => {
      document.getElementById('aiScanning').classList.remove('open');
      e.target.value = '';

      if (!ok || !data || !data.success) {
        const msg = (data && (data.error || data.message))
          || (status === 419 ? t('Your session has expired. Please refresh the page and try again.')
          : status === 413 ? t('File is too large.')
          : t('Could not process that file (error :status). Please try again.', {status}));
        document.getElementById('aiErrorMsg').textContent = '⚠️ ' + msg;
        document.getElementById('aiError').classList.add('open');
        return;
      }

      aiPreview = data.classes.map(c => ({
        subject: c.subject || '',
        day_of_week: c.day_of_week,
        start_time: c.start_time,
        end_time: c.end_time,
        room: c.room || '',
        lecturer: c.lecturer || '',
        warnings: c.warnings || [],
      }));
      renderAiPreview();
    })
    .catch(err => {
      console.error('AI capture failed', err);
      document.getElementById('aiScanning').classList.remove('open');
      document.getElementById('aiErrorMsg').textContent = '⚠️ ' + t('Connection problem. Please try again.');
      document.getElementById('aiError').classList.add('open');
      e.target.value = '';
    });
}

// ---- AI capture preview (nothing is saved until the student confirms) ----
let aiPreview = [];

function renderAiPreview() {
  const wrap = document.getElementById('aiPreview');
  const list = document.getElementById('aiPreviewList');
  const saveBtn = document.getElementById('aiPreviewSaveBtn');

  if (aiPreview.length === 0) {
    wrap.classList.remove('open');
    list.innerHTML = '';
    return;
  }

  const order = aiPreview
    .map((c, i) => i)
    .sort((a, b) => (DAYS.indexOf(aiPreview[a].day_of_week) - DAYS.indexOf(aiPreview[b].day_of_week))
      || String(aiPreview[a].start_time).localeCompare(String(aiPreview[b].start_time)));

  let html = '';
  let lastDay = null;
  order.forEach(i => {
    const c = aiPreview[i];
    if (c.day_of_week !== lastDay) {
      html += `<p class="ai-preview-day">${escapeHtml(t(c.day_of_week))}</p>`;
      lastDay = c.day_of_week;
    }
    const warn = c.warnings && c.warnings.length > 0;
    html += `<div class="ai-pv-card${warn ? ' warn' : ''}${c.isNew ? ' new' : ''}" data-idx="${i}">
      <button type="button" class="ai-pv-remove" aria-label="${t('Remove')}" onclick="removeAiPreview(${i})">×</button>
      ${warn ? c.warnings.map(w => `<p class="ai-pv-warn">⚠️ ${escapeHtml(w)}</p>`).join('') : ''}
      <div class="ai-pv-grid">
        <input class="full" type="text" value="${escapeHtml(c.subject)}" placeholder="${t('Subject')}" oninput="editAiPreview(${i}, 'subject', this.value)">
        <select onchange="editAiPreview(${i}, 'day_of_week', this.value); renderAiPreview();">
          ${DAYS.map(d => `<option value="${d}"${d === c.day_of_week ? ' selected' : ''}>${t(d)}</option>`).join('')}
        </select>
        <div style="display:flex; gap:6px;">
          <input type="time" value="${escapeHtml(c.start_time)}" onchange="editAiPreview(${i}, 'start_time', this.value)">
          <input type="time" value="${escapeHtml(c.end_time)}" onchange="editAiPreview(${i}, 'end_time', this.value)">
        </div>
        <input type="text" value="${escapeHtml(c.room)}" placeholder="${t('Room')}" oninput="editAiPreview(${i}, 'room', this.value)">
        <input type="text" value="${escapeHtml(c.lecturer)}" placeholder="${t('Lecturer')}" oninput="editAiPreview(${i}, 'lecturer', this.value)">
      </div>
    </div>`;
  });

  list.innerHTML = html;
  saveBtn.textContent = t('Save :count classes', {count: aiPreview.length});
  saveBtn.disabled = false;
  wrap.classList.add('open');
}

function editAiPreview(i, field, value) {
  if (aiPreview[i]) aiPreview[i][field] = value;
}

// Add a blank class the AI missed. Defaults to the day currently open on the
// timetable (or Monday), and focuses its subject box so the student can type straight away.
function addAiPreview() {
  const day = DAYS.includes(selectedDay) ? selectedDay : DAYS[0];
  aiPreview.push({ subject: '', day_of_week: day, start_time: '', end_time: '', room: '', lecturer: '', warnings: [], isNew: true });
  renderAiPreview();
  const subjectBox = document.querySelector(`#aiPreviewList [data-idx="${aiPreview.length - 1}"] input.full`);
  if (subjectBox) {
    subjectBox.focus();
    subjectBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
  }
}

function removeAiPreview(i) {
  aiPreview.splice(i, 1);
  renderAiPreview();
}

function cancelAiPreview() {
  aiPreview = [];
  renderAiPreview();
}

function saveAiPreview() {
  const bad = aiPreview.find(c => !c.subject.trim() || !c.start_time || !c.end_time || c.end_time <= c.start_time);
  if (bad) {
    document.getElementById('aiErrorMsg').textContent = '⚠️ ' + t('Some classes have no subject or end before they start. Please fix them first.');
    document.getElementById('aiError').classList.add('open');
    return;
  }

  const saveBtn = document.getElementById('aiPreviewSaveBtn');
  saveBtn.disabled = true;
  saveBtn.textContent = t('Saving...');

  const payload = aiPreview.map(c => ({
    subject: c.subject.trim(),
    day_of_week: c.day_of_week,
    start_time: c.start_time,
    end_time: c.end_time,
    room: c.room.trim() || null,
    lecturer: c.lecturer.trim() || null,
  }));

  fetch('{{ route('timetable.bulkStore') }}', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
    body: JSON.stringify({ classes: payload }),
  })
    .then(safeJson)
    .then(({ ok, status, data }) => {
      if (!ok || !data || !data.success) {
        saveBtn.disabled = false;
        saveBtn.textContent = t('Save :count classes', {count: aiPreview.length});
        document.getElementById('aiErrorMsg').textContent = status === 419
          ? '⚠️ ' + t('Your session has expired. Please refresh the page and try again.')
          : '⚠️ ' + t('Could not save. Check all fields and try again.');
        document.getElementById('aiError').classList.add('open');
        return;
      }
      schedules = schedules.concat(data.schedules);
      aiPreview = [];
      renderAiPreview();
      document.getElementById('aiSuccessMsg').textContent = '✅ ' + t(':count classes saved to your timetable!', {count: data.schedules.length});
      document.getElementById('aiSuccess').classList.add('open');
      render();
    })
    .catch(err => {
      console.error('AI preview save failed', err);
      saveBtn.disabled = false;
      saveBtn.textContent = t('Save :count classes', {count: aiPreview.length});
      document.getElementById('aiErrorMsg').textContent = '⚠️ ' + t('Connection problem. Please try again.');
      document.getElementById('aiError').classList.add('open');
    });
}

render();
</script>

</body>
</html>
