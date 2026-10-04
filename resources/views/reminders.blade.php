<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
@include('partials.font')
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
@include('partials.pwa-head')
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ __('RakanKampus - Reminders') }}</title>
<style>
  * { box-sizing: border-box; }

  html, body {
    margin: 0;
    min-height: 100%;
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
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

  .select-all-bar { display: none; align-items: center; justify-content: space-between; gap: 10px; padding: 0 2px 10px; }
  .select-all-bar.open { display: flex; }

  .select-all-row {
    display: flex;
    align-items: center;
    gap: 8px;
    background: none;
    border: none;
    padding: 0;
    cursor: pointer;
  }

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

  .repeat-toggle-row { margin: 4px 0 12px; }

  .repeat-toggle-label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 12.5px;
    font-weight: 700;
    color: #334155;
    cursor: pointer;
  }

  .repeat-toggle-label input { width: 16px; height: 16px; accent-color: #2ec4c6; cursor: pointer; }

  .repeat-options-wrap { margin: -2px 0 12px; }

  .repeat-options-wrap.hidden { display: none; }

  .repeat-chip-row { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 4px; }

  .repeat-chip {
    background: #fff;
    border: 1px solid #dbe4ea;
    color: #334155;
    border-radius: 999px;
    padding: 7px 12px;
    font-size: 11px;
    font-weight: 700;
    cursor: pointer;
  }

  .repeat-chip.active {
    background: linear-gradient(120deg, #14213d, #2ec4c6);
    color: #fff;
    border-color: transparent;
  }

  @media (max-width: 860px) {
    .ai-fab { bottom: 88px; }
  }

  @media (min-width: 861px) {
    body { background: #f0fafa; }

    .container { max-width: 1080px; margin: 0; padding: 36px 44px 90px; }

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

  .ai-error {
    margin-top: 14px;
    background: #fef2f2;
    border: 1px solid #fecaca;
    border-radius: 10px;
    padding: 10px 12px;
    display: none;
    align-items: flex-start;
    justify-content: space-between;
    gap: 8px;
  }

  .ai-error.open { display: flex; }
  .ai-error p { font-size: 11.5px; color: #b91c1c; margin: 0; line-height: 1.4; }
  .ai-error button { background: none; border: none; color: #b91c1c; font-size: 14px; cursor: pointer; padding: 0; flex-shrink: 0; }

  /* AI reminder preview */
  .ai-rem-preview { display: none; margin-top: 16px; }
  .ai-rem-preview.open { display: block; }
  .ai-rem-note { font-size: 12px; color: #475569; margin: 0 0 10px; line-height: 1.45; }
  .ai-rem-card { position: relative; border: 1.5px solid #e2e8f0; border-radius: 12px; padding: 10px; margin-bottom: 8px; }
  .ai-rem-card.warn { border-color: #f59e0b; background: #fffbeb; }
  .ai-rem-card.new { border-color: #2ec4c6; background: #f0fdfa; }
  .ai-rem-remove { position: absolute; top: 8px; right: 8px; width: 24px; height: 24px; border: none; border-radius: 7px; background: #f1f5f9; color: #64748b; cursor: pointer; font-size: 14px; }
  .ai-rem-warn { font-size: 11px; color: #b45309; margin: 0 30px 6px 0; line-height: 1.35; }
  .modal .ai-rem-card input[type="text"], .modal .ai-rem-card input[type="date"], .modal .ai-rem-card input[type="time"] { margin-bottom: 0; padding: 7px 9px; font-size: 12.5px; border-radius: 8px; }
  .modal .ai-rem-card .ai-rem-subject { width: calc(100% - 30px); margin-bottom: 6px; }
  .ai-rem-row { display: grid; grid-template-columns: 1fr 1.2fr 0.9fr; gap: 6px; }
  .ai-rem-row select { width: 100%; box-sizing: border-box; border: 1px solid #dbe4ea; border-radius: 8px; padding: 7px 6px; font-size: 12.5px; color: #14213d; background: #fff; font-family: inherit; }
  .ai-rem-add { width: 100%; margin-top: 4px; padding: 10px; border: 1.5px dashed #94a3b8; border-radius: 12px; background: #f8fafc; color: #0d9488; font-size: 13px; font-weight: 800; cursor: pointer; font-family: inherit; }
  .ai-rem-actions { display: flex; gap: 8px; margin-top: 12px; }
  .ai-rem-actions button { flex: 1; border: none; border-radius: 12px; padding: 12px; font-size: 13px; font-weight: 800; cursor: pointer; font-family: inherit; }
  .ai-rem-save { background: linear-gradient(120deg, #14213d, #2ec4c6); color: #fff; }
  .ai-rem-save:disabled { opacity: 0.5; cursor: default; }
  .ai-rem-cancel { background: #f1f5f9; color: #475569; }
</style>
<style id="rk-dark-theme">
/* Dark theme — generated by scripts/dark-theme/generate.py, do not edit by hand.
   Hand-made fixes go in resources/views/partials/dark-fixes.blade.php */
html[data-theme="dark"] body { background-image: linear-gradient(160deg, #0c1320, #112031 55%, #1d6869); }
html[data-theme="dark"] .add-btn { box-shadow: 0 10px 22px rgba(0, 0, 0, 0.45); }
html[data-theme="dark"] .filter-btn, html[data-theme="dark"] .select-btn { border: 1px solid rgba(42, 51, 65, 0.25); }
html[data-theme="dark"] .filter-dropdown { background: #17202d; box-shadow: 0 10px 26px rgba(0, 0, 0, 0.59); }
html[data-theme="dark"] .filter-opt { background: #17202d; color: #d6dae1; }
html[data-theme="dark"] .reminder-card { background: #39231b; }
html[data-theme="dark"] .reminder-card.selected { background: #371a1a; border-color: #482828; }
html[data-theme="dark"] .reminder-subject { color: #dee1e9; }
html[data-theme="dark"] .reminder-when { color: #ced3d9; }
html[data-theme="dark"] .notify-banner { background: #382b1b; border: 1px solid #483928; color: #eb7750; }
html[data-theme="dark"] .reminder-action-btn { color: #ced3d9; }
html[data-theme="dark"] .reminder-action-btn:hover { color: #9361e0; }
html[data-theme="dark"] .ai-fab { box-shadow: 0 8px 18px rgba(0, 0, 0, 0.55); }
html[data-theme="dark"] .ai-fab-inner { background: #17202d; }
html[data-theme="dark"] .ai-fab-inner span { color: #dee1e9; }
html[data-theme="dark"] .modal { background: #17202d; box-shadow: 0 24px 60px rgba(0, 0, 0, 0.68); }
html[data-theme="dark"] .modal-icon.rk-brand { background: #1e3d39; border: 1px solid #284843; }
html[data-theme="dark"] .modal-title { color: #dee1e9; }
html[data-theme="dark"] .modal-close { background: #10161f; color: #b0b6be; }
html[data-theme="dark"] .field-label { color: #b0b6be; }
html[data-theme="dark"] .modal input[type="text"], html[data-theme="dark"] .modal input[type="date"], html[data-theme="dark"] .modal input[type="time"], html[data-theme="dark"] .modal input[type="number"] { border: 1px solid #283b48; color: #dee1e9; }
html[data-theme="dark"] .type-opt { background: #17202d; border: 1px solid #283b48; color: #d6dae1; }
html[data-theme="dark"] .hint { color: #ced3d9; }
html[data-theme="dark"] .repeat-toggle-label { color: #d6dae1; }
html[data-theme="dark"] .repeat-chip { background: #17202d; border: 1px solid #283b48; color: #d6dae1; }
@media (min-width: 861px) {
  html[data-theme="dark"] body { background: #10161f; }
  html[data-theme="dark"] .header-title { color: #dee1e9; }
  html[data-theme="dark"] .header-sub { color: #41eedf; }
  html[data-theme="dark"] .search-row { background: #17202d; border: 1px solid #284848; }
  html[data-theme="dark"] .search-row svg { stroke: #b0b6be; }
  html[data-theme="dark"] .search-row input { color: #dee1e9; }
  html[data-theme="dark"] .search-row input::placeholder { color: #ced3d9; }
  html[data-theme="dark"] .filter-btn, html[data-theme="dark"] .select-btn { background: #17202d; border: 1px solid #284848; color: #41eedf; }
  html[data-theme="dark"] .section-label { color: #b0b6be; }
  html[data-theme="dark"] .history-link { color: #41eedf; }
  html[data-theme="dark"] .select-all-row span { color: #41eedf; }
  html[data-theme="dark"] .reminder-card { background: #17202d; border-color: #284848; }
  html[data-theme="dark"] .reminder-card.selected { background: #371a1a; border-color: #482828; }
  html[data-theme="dark"] .reminder-when { color: #b0b6be; }
  html[data-theme="dark"] .empty-state { background: #17202d; border: 1px solid #284848; color: #b0b6be; }
}
html[data-theme="dark"] .ai-desc { color: #b0b6be; }
html[data-theme="dark"] .ai-upload-label { background: #1b2238; border: 1.5px dashed #282e48; color: #aba6e7; }
html[data-theme="dark"] .ai-scanning { color: #aba6e7; }
html[data-theme="dark"] .ai-success { background: #1b3824; border: 1px solid #284833; }
html[data-theme="dark"] .ai-success p { color: #44e07e; }
html[data-theme="dark"] .ai-success button { color: #44e07e; }
html[data-theme="dark"] .ai-error { background: #371a1a; border: 1px solid #482828; }
html[data-theme="dark"] .ai-error p { color: #eb7979; }
html[data-theme="dark"] .ai-error button { color: #eb7979; }
html[data-theme="dark"] .ai-rem-note { color: #d0d4da; }
html[data-theme="dark"] .ai-rem-card { border: 1.5px solid #283648; }
html[data-theme="dark"] .ai-rem-card.warn { background: #39331b; }
html[data-theme="dark"] .ai-rem-card.new { background: #1b3831; }
html[data-theme="dark"] .ai-rem-remove { background: #10161f; color: #b0b6be; }
html[data-theme="dark"] .ai-rem-warn { color: #f79b55; }
html[data-theme="dark"] .ai-rem-row select { border: 1px solid #283b48; color: #dee1e9; background: #17202d; }
html[data-theme="dark"] .ai-rem-add { background: #141b26; color: #41eedf; }
html[data-theme="dark"] .ai-rem-cancel { background: #10161f; color: #d0d4da; }
</style>
<style id="rm-b">
/* Reminders "B": list + next-deadline countdown + calendar */
.rm-layout { display: block; }
.rm-side { display: none; }
.rm-next { position: relative; overflow: hidden; border-radius: 20px; padding: 15px 16px; color: #fff; margin-bottom: 14px;
  background: linear-gradient(135deg, #be123c, #f43f5e 60%, #fb923c); box-shadow: 0 12px 26px rgba(244,63,94,.28); }
.rm-next::after { content: ""; position: absolute; right: -40px; top: -40px; width: 140px; height: 140px; border-radius: 50%; background: rgba(255,255,255,.12); }
.rm-next.none { background: linear-gradient(135deg, #0f766e, #14b8a6); box-shadow: 0 12px 26px rgba(20,184,166,.25); }
.rm-next-label { margin: 0; font-size: 10.5px; font-weight: 800; letter-spacing: .8px; opacity: .9; }
.rm-next-title { display: block; font-size: 16px; font-weight: 800; margin-top: 3px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; position: relative; z-index: 1; }
.rm-next-when { margin: 2px 0 0; font-size: 11.5px; opacity: .85; }
.rm-cd { display: flex; gap: 8px; margin-top: 10px; position: relative; z-index: 1; }
.rm-cd div { flex: 1; max-width: 64px; background: rgba(255,255,255,.2); border-radius: 12px; padding: 6px 4px; text-align: center; font-size: 9.5px; font-weight: 700; }
.rm-cd b { display: block; font-size: 19px; font-weight: 800; font-variant-numeric: tabular-nums; }

.rm-week { display: flex; gap: 6px; margin-bottom: 14px; }
.rm-week button { flex: 1; min-width: 0; border: 1px solid rgba(255,255,255,.35); background: rgba(255,255,255,.14); color: #e6fbfb; border-radius: 14px; padding: 6px 0 9px; font: 700 10px inherit; font-family: inherit; cursor: pointer; position: relative; }
.rm-week button b { display: block; font-size: 15px; font-weight: 800; color: #fff; }
.rm-week button.today { border-color: #fff; }
.rm-week button.on { background: #fff; color: #0f766e; } .rm-week button.on b { color: #0f2747; }
.rm-dots { position: absolute; left: 0; right: 0; bottom: 3px; display: flex; justify-content: center; gap: 2px; }
.rm-dots i { width: 5px; height: 5px; border-radius: 50%; }

.rm-types { display: flex; gap: 6px; overflow-x: auto; scrollbar-width: none; margin: 0 0 10px; }
.rm-types::-webkit-scrollbar { display: none; }
.rm-type { flex: none; border: 1px solid rgba(255,255,255,.35); background: rgba(255,255,255,.14); color: #fff; font: 700 12px inherit; font-family: inherit; padding: 7px 13px; border-radius: 999px; cursor: pointer; }
.rm-type.on { background: #fff; color: #0f2747; border-color: #fff; }
.rm-dayfilter { display: none; align-items: center; justify-content: space-between; gap: 8px; background: rgba(255,255,255,.9); color: #0f2747; border-radius: 12px; padding: 8px 12px; font-size: 12.5px; font-weight: 700; margin-bottom: 10px; }
.rm-dayfilter.open { display: flex; }
.rm-dayfilter button { border: 0; background: #f0fdfa; color: #0f766e; font: 800 11.5px inherit; font-family: inherit; padding: 5px 10px; border-radius: 999px; cursor: pointer; }
.reminder-card { border-left: 4px solid var(--tc, transparent) !important; }

.rm-cal { background: #fff; border: 1px solid #dbeeee; border-radius: 20px; padding: 14px 16px 12px; }
.rm-cal-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
.rm-cal-head b { font-size: 14.5px; color: #14213d; }
.rm-cal-head button { width: 28px; height: 28px; border: 0; border-radius: 9px; background: #f1f5f9; color: #475569; font-size: 16px; cursor: pointer; margin-left: 4px; }
.rm-cal-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 3px; text-align: center; }
.rm-cal-grid .h { font-size: 10px; font-weight: 800; color: #94a3b8; padding: 4px 0; }
.rm-cal-grid button { position: relative; border: 0; background: none; font: 600 12px inherit; font-family: inherit; color: #334155; padding: 8px 0 10px; border-radius: 10px; cursor: default; }
.rm-cal-grid button.has { cursor: pointer; font-weight: 800; color: #0f2747; }
.rm-cal-grid button.has:hover { background: #f0fdfa; }
.rm-cal-grid button.today { background: #0f2747; color: #fff; }
.rm-cal-grid button.on { background: #14b8a6; color: #fff; }
.rm-cal-grid button .rm-dots { bottom: 2px; }
.rm-cal-legend { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 10px; font-size: 10.5px; font-weight: 700; color: #64748b; }
.rm-cal-legend span::before { content: "●"; color: var(--c); margin-right: 3px; }

@media (min-width: 861px) {
  .rm-layout { display: grid; grid-template-columns: minmax(0, 1fr) 320px; gap: 22px; align-items: start; }
  .rm-side { display: block; position: sticky; top: 24px; }
  .rm-next-m, .rm-week { display: none; }
  .rm-type { border-color: #dbeeee; background: #fff; color: #0f766e; }
  .rm-type.on { background: #0f2747; color: #fff; border-color: #0f2747; }
  .rm-dayfilter { background: #fff; border: 1px solid #dbeeee; }
}
html[data-theme="dark"] .rm-cal { background: #17202d; border-color: #284848; }
html[data-theme="dark"] .rm-cal-head b, html[data-theme="dark"] .rm-cal-grid button.has { color: #dee1e9; }
html[data-theme="dark"] .rm-cal-grid button { color: #b0b6be; }
html[data-theme="dark"] .rm-cal-head button { background: #1f2a39; color: #ced3d9; }
html[data-theme="dark"] .rm-cal-grid button.has:hover { background: #1d3d3b; }
html[data-theme="dark"] .rm-cal-grid button.today { background: #41eedf; color: #0b1626; }
@media (min-width: 861px) {
  html[data-theme="dark"] .rm-type { background: #17202d; border-color: #284848; color: #41eedf; }
  html[data-theme="dark"] .rm-type.on { background: #41eedf; color: #0b1626; }
  html[data-theme="dark"] .rm-dayfilter { background: #17202d; border-color: #284848; color: #dee1e9; }
}
html[data-theme="dark"] .rm-dayfilter button { background: #1d3d3b; color: #41eedf; }
</style>
</head>
<body>

@include('partials.app-nav', ['active' => 'reminders', 'user' => $user])

<div class="container" id="pageContainer">
  <div class="header">
    <div>
      <p class="header-title">{{ __('Reminders') }}</p>
      <p class="header-sub">{{ __('For exams, assignments & deadlines') }}</p>
    </div>
  </div>

  <div class="rm-layout">
  <div class="rm-main">

  {{-- Next deadline countdown (phones show it here; PC shows it in the side column) --}}
  <div class="rm-next rm-next-m" data-next></div>

  {{-- Phones: this week's 7 days, dot = has a reminder, tap to show only that day --}}
  <div class="rm-week" id="rmWeek"></div>

  <div class="notify-banner" id="notifyBanner">
    <span>{{ __('Turn on notifications to get alerted before your deadlines.') }}</span>
    <button type="button" onclick="enableNotifications()">{{ __('Enable') }}</button>
  </div>

  <button type="button" class="add-btn" onclick="openAddModal()">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
    {{ __('Add Reminder') }}
  </button>

  <div class="search-toolbar-wrap">
  <div class="search-row">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
    <input type="text" id="searchInput" placeholder="{{ __('Search reminders...') }}" oninput="onSearchChange()">
  </div>

  <div class="toolbar-row">
    <div class="filter-wrap">
      <button type="button" class="filter-btn" onclick="toggleFilterOpen()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
        <span id="filterDaysLabel">{{ __(':count Days', ['count' => 30]) }}</span>
      </button>
      <div class="filter-dropdown" id="filterDropdown">
        @foreach([90, 30, 14, 7] as $d)
          <button type="button" class="filter-opt" data-days="{{ $d }}" onclick="setFilterDays({{ $d }})">{{ __(':count Days', ['count' => $d]) }}</button>
        @endforeach
      </div>
    </div>
    <button type="button" class="select-btn" id="selectModeBtn" onclick="toggleSelectMode()">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="4"></rect><path d="m8 12 3 3 5-6"></path></svg>
      <span id="selectModeLabel">{{ __('Select') }}</span>
    </button>
  </div>
  </div>

  <div class="rm-types" id="rmTypes">
    <button type="button" class="rm-type on" data-type="">{{ __('All') }}</button>
    <button type="button" class="rm-type" data-type="Exam">🎓 {{ __('Exam') }}</button>
    <button type="button" class="rm-type" data-type="Assignment">📦 {{ __('Assignment') }}</button>
    <button type="button" class="rm-type" data-type="Quiz">📝 {{ __('Quiz') }}</button>
    <button type="button" class="rm-type" data-type="Other">📌 {{ __('Other') }}</button>
  </div>
  <div class="rm-dayfilter" id="rmDayFilter"><span id="rmDayText"></span><button type="button" onclick="setDayFilter(null)">{{ __('Show all') }} ×</button></div>

  <div class="section-row">
    <p class="section-label" id="countLabel">{{ __('UPCOMING (:count)', ['count' => 0]) }}</p>
    <a href="{{ route('student.reminders.history') }}" class="history-link">
      {{ __('History') }} ({{ $historyCount }})
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"></path></svg>
    </a>
  </div>

  <div class="select-all-bar" id="selectAllRow">
    <button type="button" class="select-all-row" onclick="toggleSelectAll()">
      <input type="checkbox" id="selectAllCheckbox" style="pointer-events: none;">
      <span>{{ __('Select All') }}</span>
    </button>
    <button type="button" class="bulk-delete-btn" id="bulkDeleteBtn" onclick="deleteSelected()" disabled>
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
      <span>{{ __('Delete') }} (<span id="selectedCount">0</span>)</span>
    </button>
  </div>

  <div class="reminder-list" id="reminderList"></div>
  <div class="empty-state" id="emptyState" style="display:none;">{{ __('No reminders yet — tap "Add Reminder" to add your first exam, assignment or deadline.') }}</div>
  </div>{{-- /rm-main --}}

  {{-- PC side column: next deadline countdown + month calendar --}}
  <aside class="rm-side">
    <div class="rm-next" data-next></div>
    <div class="rm-cal">
      <div class="rm-cal-head">
        <b id="rmCalTitle"></b>
        <span>
          <button type="button" onclick="shiftCal(-1)" aria-label="{{ __('Previous month') }}">‹</button>
          <button type="button" onclick="shiftCal(1)" aria-label="{{ __('Next month') }}">›</button>
        </span>
      </div>
      <div class="rm-cal-grid" id="rmCalGrid"></div>
      <div class="rm-cal-legend"><span style="--c:#6366f1">{{ __('Exam') }}</span><span style="--c:#0d9488">{{ __('Assignment') }}</span><span style="--c:#7c3aed">{{ __('Quiz') }}</span><span style="--c:#64748b">{{ __('Other') }}</span></div>
    </div>
  </aside>
  </div>{{-- /rm-layout --}}
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
  <p class="ai-desc">{{ __('Upload a photo or PDF of your exam slip, exam timetable or assignment brief — AI will find every date in it. You\'ll get a list to check before anything is saved.') }}</p>
  <label class="ai-upload-label">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2Z"></path><circle cx="12" cy="13" r="4"></circle></svg>
    {{ __('Upload PDF / Photo') }}
    <input type="file" accept="image/*,application/pdf,.pdf" onchange="handlePhotoSelected(event)">
  </label>
  <p class="ai-scanning" id="aiScanning">{{ __('🔎 Reading image and detecting details...') }}</p>
  <div class="ai-success" id="aiSuccess">
    <p id="aiSuccessMsg"></p>
    <button type="button" aria-label="{{ __('Dismiss') }}" onclick="dismissAiMsg()">×</button>
  </div>
  <div class="ai-error" id="aiError">
    <p id="aiErrorMsg"></p>
    <button type="button" aria-label="{{ __('Dismiss') }}" onclick="dismissAiError()">×</button>
  </div>
  <div class="ai-rem-preview" id="aiRemPreview">
    <p class="ai-rem-note">{{ __('Check the reminders below. Fix anything that is wrong, remove what you don\'t need, then press Save.') }} <b>{{ __('Yellow cards need checking.') }}</b></p>
    <div id="aiRemPreviewList"></div>
    <button type="button" class="ai-rem-add" onclick="addAiReminder()">{{ __('+ Add reminder') }}</button>
    <div class="ai-rem-actions">
      <button type="button" class="ai-rem-cancel" onclick="cancelAiReminderPreview()">{{ __('Cancel') }}</button>
      <button type="button" class="ai-rem-save" id="aiRemSaveBtn" onclick="saveAiReminderPreview()">{{ __('Save') }}</button>
    </div>
  </div>
</div>

<div class="modal" id="modal">
  <div class="modal-head">
    <p class="modal-title" id="modalTitle">{{ __('Add Reminder') }}</p>
    <button type="button" class="modal-close" aria-label="{{ __('Close') }}" onclick="closeModals()">×</button>
  </div>
  <input type="hidden" id="reminderId">
  <input type="text" id="subjectInput" placeholder="{{ __('e.g. Software Engineering Assignment 2') }}">
  <p class="field-label">{{ __('Type') }}</p>
  <div class="type-row" id="typeRow">
    @foreach(['Exam' => '#6366f1', 'Assignment' => '#0d9488', 'Quiz' => '#7c3aed', 'Other' => '#64748b'] as $t => $color)
      <button type="button" class="type-opt" data-type="{{ $t }}" data-color="{{ $color }}" onclick="selectType('{{ $t }}')">{{ __($t) }}</button>
    @endforeach
  </div>
  <p class="field-label">{{ __('Due date & time') }}</p>
  <input type="hidden" id="dateInput">
  <input type="hidden" id="timeInput">
  <button type="button" class="rkp-field empty" id="dueField" onclick="openDuePicker()" style="margin-bottom:12px">
    <span><small>{{ __('Due') }}</small><b id="dueFieldText">{{ __('Pick date & time') }}</b></span>
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="17" rx="2"></rect><path d="M3 9h18M8 2v4M16 2v4"></path></svg>
  </button>
  <p class="field-label">{{ __('Notify me how many hours before?') }}</p>
  <input type="number" id="leadInput" min="0" step="0.5" value="1">
  <p class="hint">{{ __('Type any number of hours — use 0.5 for 30 minutes.') }}</p>

  <div class="repeat-toggle-row">
    <label class="repeat-toggle-label">
      <input type="checkbox" id="repeatToggle" onchange="toggleRepeatOptions()">
      {{ __('Remind me more than once') }}
    </label>
  </div>
  <div class="repeat-options-wrap hidden" id="repeatOptionsWrap">
    <p class="field-label">{{ __('Also remind me at (pick as many as you like)') }}</p>
    <div class="repeat-chip-row" id="repeatChipRow">
      <button type="button" class="repeat-chip" data-hours="72" onclick="toggleRepeatChip(this)">{{ __('3 days before') }}</button>
      <button type="button" class="repeat-chip" data-hours="24" onclick="toggleRepeatChip(this)">{{ __('1 day before') }}</button>
      <button type="button" class="repeat-chip" data-hours="3" onclick="toggleRepeatChip(this)">{{ __('3 hours before') }}</button>
      <button type="button" class="repeat-chip" data-hours="1" onclick="toggleRepeatChip(this)">{{ __('1 hour before') }}</button>
      <button type="button" class="repeat-chip" data-hours="0.5" onclick="toggleRepeatChip(this)">{{ __('30 min before') }}</button>
    </div>
    <p class="hint">{{ __('On top of the main notification above.') }}</p>
  </div>

  <p id="formError" style="color:#e11d48; font-size: 11.5px; display:none; margin: -6px 0 10px;">{{ __('Please fill in a subject and date.') }}</p>
  <button type="button" class="modal-save" onclick="saveReminder()">{{ __('Save Reminder') }}</button>
</div>

@include('partials.rk-picker')

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
let selectedRepeatHours = [];
let typeFilter = '';
let dayFilter = null; // 'YYYY-MM-DD'
let calMonth = (() => { const d = new Date(); return new Date(d.getFullYear(), d.getMonth(), 1); })();

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
  if (diffMs <= 0) return { text: t('Passed'), color: '#94a3b8', dotBg: '#e2e8f0', dotStroke: '#94a3b8' };
  if (diffHours <= leadHours) {
    const h = Math.floor(diffHours);
    const m = Math.floor((diffHours - h) * 60);
    return { text: t('Notified') + ' · ' + t(':h h :m m left', {h, m}), color: '#ea580c', dotBg: '#ffedd5', dotStroke: '#ea580c' };
  }
  if (diffHours <= 24) return { text: t('Upcoming') + ' · ' + t('in :count h', {count: Math.floor(diffHours)}), color: '#2563eb', dotBg: '#dbeafe', dotStroke: '#2563eb' };
  return { text: t('Upcoming') + ' · ' + t('in :count d', {count: Math.floor(diffHours / 24)}), color: '#0d9488', dotBg: '#ccfbf1', dotStroke: '#0d9488' };
}

function leadLabel(hours) {
  if (hours < 1) return Math.round(hours * 60) + ' ' + t('min');
  if (hours === 1) return t('1 hour');
  return t(':count hours', {count: Math.round(hours * 10) / 10});
}

function formatWhen(dueMs) {
  const d = new Date(dueMs);
  const loc = ({ ms: 'ms-MY', zh: 'zh-CN', ta: 'ta-IN' })[window.APP_LOCALE] || 'en-GB';
  return d.toLocaleDateString(loc, { day: 'numeric', month: 'short' }) + ' · ' + d.toLocaleTimeString(loc, { hour: '2-digit', minute: '2-digit' });
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
    .filter(r => new Date(r.due_at).getTime() > Date.now())
    .filter(r => dayFilter ? toDateStr(new Date(r.due_at).getTime()) === dayFilter : new Date(r.due_at).getTime() <= cutoff)
    .filter(r => !typeFilter || r.type === typeFilter)
    .sort((a, b) => new Date(a.due_at) - new Date(b.due_at));

  document.getElementById('countLabel').textContent = t('UPCOMING (:count)', {count: visible.length});
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
        <button type="button" class="swipe-delete-panel" onclick="removeReminder(${r.id})" aria-label="${t('Delete')}">
          <svg viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
          <span>${t('Delete')}</span>
        </button>
        <div class="reminder-card ${isSelected ? 'selected' : ''}" data-id="${r.id}"
             style="transform: translateX(${offset}px); --tc: ${ts.color};"
             onpointerdown="startRowDrag(event, ${r.id})" onpointermove="moveRowDrag(event)" onpointerup="endRowDrag(event)" onpointerleave="endRowDrag(event)">
          <input type="checkbox" class="reminder-select ${selectMode ? 'open' : ''}" ${isSelected ? 'checked' : ''} onchange="toggleSelect(${r.id})" onclick="event.stopPropagation()">
          <div class="reminder-dot" style="background: ${status.dotBg};">
            <svg viewBox="0 0 24 24" fill="none" stroke="${status.dotStroke}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v5l3 3"></path></svg>
          </div>
          <div class="reminder-body">
            <span class="reminder-type" style="color: ${ts.color}; background: ${ts.bg};">${escapeHtml(t(r.type))}</span>
            <p class="reminder-subject">${escapeHtml(r.subject)}</p>
            <p class="reminder-when">${formatWhen(dueMs)} · ${t('notify :time before', {time: leadLabel(r.lead_hours)})}</p>
            <p class="reminder-status" style="color: ${status.color};">${status.text}</p>
          </div>
          ${!selectMode ? `
          <div class="reminder-actions">
            <button type="button" class="reminder-action-btn" aria-label="${t('Edit')}" onclick="event.stopPropagation(); openEditModal(${r.id})">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.12 2.12 0 0 1 3 3L12 15l-4 1 1-4Z"></path></svg>
            </button>
            <button type="button" class="reminder-action-btn" aria-label="${t('Delete')}" onclick="event.stopPropagation(); removeReminder(${r.id})">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
          </div>` : ''}
        </div>
      </div>`;
  }).join('');

  document.getElementById('selectAllRow').classList.toggle('open', selectMode);
  const allSelected = visible.length > 0 && visible.every(r => selectedIds.includes(r.id));
  document.getElementById('selectAllCheckbox').checked = allSelected;

  document.getElementById('bulkDeleteBtn').disabled = selectedIds.length === 0;
  document.getElementById('selectedCount').textContent = selectedIds.length;

  window._visibleIds = visible.map(r => r.id);
  renderSide();
}

// ---------- next deadline, week strip, month calendar ----------
const RM_LOC = ({ ms: 'ms-MY', zh: 'zh-CN', ta: 'ta-IN' })[window.APP_LOCALE] || 'en-GB';
const RM_TYPE_C = { Exam: '#6366f1', Assignment: '#0d9488', Quiz: '#7c3aed', Other: '#64748b' };
function upcomingAll() {
  return reminders.filter(r => new Date(r.due_at).getTime() > Date.now()).sort((a, b) => new Date(a.due_at) - new Date(b.due_at));
}
function dotsByDay() {
  const m = {};
  upcomingAll().forEach(r => { const k = toDateStr(new Date(r.due_at).getTime()); (m[k] = m[k] || []).push(RM_TYPE_C[r.type] || '#64748b'); });
  return m;
}
function renderNext() {
  const n = upcomingAll()[0];
  document.querySelectorAll('[data-next]').forEach(el => {
    if (!n) {
      el.classList.add('none');
      el.innerHTML = `<p class="rm-next-label">⏰ ${t('NEXT DEADLINE')}</p><b class="rm-next-title">${t('Nothing due — enjoy! 🎉')}</b>`;
      return;
    }
    el.classList.remove('none');
    let left = Math.max(0, new Date(n.due_at).getTime() - Date.now()) / 1000;
    const d = Math.floor(left / 86400); left -= d * 86400;
    const h = Math.floor(left / 3600); left -= h * 3600;
    const m = Math.floor(left / 60); const sec = Math.floor(left - m * 60);
    const box = (v, l) => `<div><b>${pad(v)}</b>${l}</div>`;
    el.innerHTML = `<p class="rm-next-label">⏰ ${t('NEXT DEADLINE')}</p>
      <b class="rm-next-title">${escapeHtml(n.subject)}</b>
      <p class="rm-next-when">${escapeHtml(t(n.type))} · ${formatWhen(new Date(n.due_at).getTime())}</p>
      <div class="rm-cd">${box(d, t('days'))}${box(h, t('hrs'))}${box(m, t('min'))}${box(sec, t('sec'))}</div>`;
  });
}
function renderWeek() {
  const el = document.getElementById('rmWeek'); if (!el) return;
  const dots = dotsByDay(), start = new Date(); start.setHours(0, 0, 0, 0);
  let html = '';
  for (let i = 0; i < 7; i++) {
    const d = new Date(start); d.setDate(start.getDate() + i);
    const k = toDateStr(d.getTime());
    html += `<button type="button" class="${dayFilter === k ? 'on' : ''} ${i === 0 ? 'today' : ''}" onclick="setDayFilter('${k}')">
      ${d.toLocaleDateString(RM_LOC, { weekday: 'narrow' })}<b>${d.getDate()}</b>
      <span class="rm-dots">${(dots[k] || []).slice(0, 3).map(c => `<i style="background:${c}"></i>`).join('')}</span></button>`;
  }
  el.innerHTML = html;
}
function renderCal() {
  const grid = document.getElementById('rmCalGrid'); if (!grid) return;
  document.getElementById('rmCalTitle').textContent = calMonth.toLocaleDateString(RM_LOC, { month: 'long', year: 'numeric' });
  const dots = dotsByDay(), first = new Date(calMonth), today = toDateStr(Date.now());
  const lead = (first.getDay() + 6) % 7, days = new Date(first.getFullYear(), first.getMonth() + 1, 0).getDate();
  let html = '';
  const ref = new Date(2024, 0, 1); // a Monday
  for (let i = 0; i < 7; i++) { const d = new Date(ref); d.setDate(1 + i); html += `<span class="h">${d.toLocaleDateString(RM_LOC, { weekday: 'narrow' })}</span>`; }
  for (let i = 0; i < lead; i++) html += '<span></span>';
  for (let n = 1; n <= days; n++) {
    const k = first.getFullYear() + '-' + pad(first.getMonth() + 1) + '-' + pad(n);
    const has = dots[k];
    html += `<button type="button" class="${k === today ? 'today' : ''} ${dayFilter === k ? 'on' : ''} ${has ? 'has' : ''}" ${has ? `onclick="setDayFilter('${k}')"` : 'disabled'}>${n}
      <span class="rm-dots">${(has || []).slice(0, 3).map(c => `<i style="background:${c}"></i>`).join('')}</span></button>`;
  }
  grid.innerHTML = html;
}
function renderSide() { renderNext(); renderWeek(); renderCal(); }
function shiftCal(n) { calMonth = new Date(calMonth.getFullYear(), calMonth.getMonth() + n, 1); renderCal(); }
function setDayFilter(k) {
  dayFilter = (k && dayFilter !== k) ? k : null;
  const bar = document.getElementById('rmDayFilter');
  bar.classList.toggle('open', !!dayFilter);
  if (dayFilter) {
    const [y, mo, d] = dayFilter.split('-').map(Number);
    document.getElementById('rmDayText').textContent = t('Showing :day', { day: new Date(y, mo - 1, d).toLocaleDateString(RM_LOC, { weekday: 'short', day: 'numeric', month: 'short' }) });
  }
  render();
}
document.getElementById('rmTypes').addEventListener('click', e => {
  const b = e.target.closest('.rm-type'); if (!b) return;
  typeFilter = b.dataset.type;
  document.querySelectorAll('.rm-type').forEach(x => x.classList.toggle('on', x === b));
  render();
});
setInterval(renderNext, 1000);

function onSearchChange() {
  searchQuery = document.getElementById('searchInput').value;
  render();
}

function toggleFilterOpen() {
  document.getElementById('filterDropdown').classList.toggle('open');
}

function setFilterDays(days) {
  filterDays = days;
  document.getElementById('filterDaysLabel').textContent = t(':count Days', {count: days});
  document.querySelectorAll('.filter-opt').forEach(b => b.classList.toggle('active', parseInt(b.dataset.days) === days));
  document.getElementById('filterDropdown').classList.remove('open');
  render();
}

function toggleSelectMode() {
  selectMode = !selectMode;
  if (!selectMode) selectedIds = [];
  document.getElementById('selectModeBtn').classList.toggle('active', selectMode);
  document.getElementById('selectModeLabel').textContent = selectMode ? t('Cancel') : t('Select');
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

// Custom confirm (partials/rk-dialog): the robot files the reminders into History.
async function deleteSelected() {
  const ids = selectedIds.slice();
  if (ids.length === 0) return;
  const ok = await RKDialog.confirm({
    scene: 'reminder',
    title: t('Delete :count reminder(s)?', {count: ids.length}),
    message: t('They will be moved to History.'),
    list: reminders.filter(r => ids.includes(r.id)).map(r => ({ label: r.subject, meta: t(r.type) })),
    confirmText: t('Delete'),
  });
  if (!ok) return;
  fetch('/reminders/bulk-delete', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
    body: JSON.stringify({ ids }),
  }).then(res => res.json()).then(() => {
    reminders = reminders.filter(r => !ids.includes(r.id));
    historyCount += ids.length;
    selectedIds = [];
    render();
    showDeletedToast(ids);
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
  const moved = dragOffset > 0;
  const shouldDelete = dragOffset > 44;
  dragId = null;
  dragStartX = null;
  dragOffset = 0;
  if (shouldDelete) {
    removeReminder(id);
  } else if (moved) {
    // Only snap the card back if it was actually dragged. Calling render()
    // here unconditionally (even for a plain tap with zero movement) replaces
    // the card's DOM node between pointerup and the browser's click event,
    // which silently swallows clicks on the Edit/Delete buttons inside it.
    const card = document.querySelector(`.reminder-card[data-id="${id}"]`);
    if (card) card.style.transform = 'translateX(0px)';
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

function toggleRepeatOptions() {
  document.getElementById('repeatOptionsWrap').classList.toggle('hidden', !document.getElementById('repeatToggle').checked);
}

function toggleRepeatChip(btn) {
  const hours = parseFloat(btn.getAttribute('data-hours'));
  btn.classList.toggle('active');
  if (btn.classList.contains('active')) {
    if (!selectedRepeatHours.includes(hours)) selectedRepeatHours.push(hours);
  } else {
    selectedRepeatHours = selectedRepeatHours.filter(h => h !== hours);
  }
}

function resetRepeatOptions(presetHours) {
  selectedRepeatHours = Array.isArray(presetHours) ? presetHours.slice() : [];
  document.getElementById('repeatToggle').checked = selectedRepeatHours.length > 0;
  document.getElementById('repeatOptionsWrap').classList.toggle('hidden', selectedRepeatHours.length === 0);
  document.querySelectorAll('.repeat-chip').forEach(chip => {
    const hours = parseFloat(chip.getAttribute('data-hours'));
    chip.classList.toggle('active', selectedRepeatHours.includes(hours));
  });
}

function openAddModal() {
  document.getElementById('modalTitle').textContent = t('Add Reminder');
  document.getElementById('reminderId').value = '';
  document.getElementById('subjectInput').value = '';
  document.getElementById('dateInput').value = '';
  document.getElementById('timeInput').value = '';
  document.getElementById('leadInput').value = '1';
  selectType('Exam');
  resetRepeatOptions([]);
  syncDueField();
  showModal('modal');
}

function openEditModal(id) {
  const r = reminders.find(x => x.id === id);
  if (!r) return;
  const dueMs = new Date(r.due_at).getTime();
  document.getElementById('modalTitle').textContent = t('Edit Reminder');
  document.getElementById('reminderId').value = r.id;
  document.getElementById('subjectInput').value = r.subject;
  document.getElementById('dateInput').value = toDateStr(dueMs);
  document.getElementById('timeInput').value = toTimeStr(dueMs);
  document.getElementById('leadInput').value = r.lead_hours;
  selectType(r.type);
  resetRepeatOptions(r.repeat_lead_hours || []);
  syncDueField();
  showModal('modal');
}

// Due date & time field → calendar + clock picker (partials/rk-picker)
function syncDueField() {
  const d = document.getElementById('dateInput').value;
  const tm = document.getElementById('timeInput').value;
  const field = document.getElementById('dueField');
  field.classList.toggle('empty', !d);
  document.getElementById('dueFieldText').textContent = d
    ? `${RKPicker.formatDate(d, true)} · ${RKPicker.formatTime(tm || '09:00')}`
    : t('Pick date & time');
}

function openDuePicker() {
  const marks = reminders.map(r => toDateStr(new Date(r.due_at).getTime()));
  RKPicker.dateTime({
    title: t('Due date & time'),
    subtitle: document.getElementById('subjectInput').value.trim(),
    date: document.getElementById('dateInput').value,
    time: document.getElementById('timeInput').value || '09:00',
    marks,
    onDone(date, time) {
      document.getElementById('dateInput').value = date;
      document.getElementById('timeInput').value = time;
      syncDueField();
    },
  });
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

  const repeatLeadHours = document.getElementById('repeatToggle').checked ? selectedRepeatHours : [];

  fetch(url, {
    method,
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
    body: JSON.stringify({ subject, type: selectedType, due_at: due, lead_hours: lead, repeat_lead_hours: repeatLeadHours }),
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
      showDeletedToast([id]);
    })
    .catch(err => console.error('Delete failed', err));
}

// Toast after a delete, with UNDO (reminders are only soft-deleted into History).
function showDeletedToast(ids) {
  RKToast.show({
    text: ids.length === 1 ? t('Reminder deleted') : t(':count reminders deleted', {count: ids.length}),
    sub: t('Moved to History'),
    undo: () => {
      fetch('/reminders/restore', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({ ids }),
      }).then(res => res.json()).then((data) => {
        const back = (data && data.reminders) || [];
        reminders = reminders.concat(back.filter(b => !reminders.some(r => r.id === b.id)));
        historyCount = Math.max(0, historyCount - back.length);
        render();
        RKToast.show({ text: t('Restored'), sub: t(':count reminder(s) back in your list', {count: back.length}) });
      }).catch(err => console.error('Restore failed', err));
    },
  });
}

// AI Assistant: real image capture — upload photo, AI reads it, reminder created automatically
function openAiModal() {
  if (aiReminderPreview.length === 0) document.getElementById('aiRemPreview').classList.remove('open');
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

  fetch('{{ route('reminders.aiCapture') }}', {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
    body: formData,
  })
    .then(res => res.json().catch(() => null).then(data => ({ ok: res.ok, data })))
    .then(({ ok, data }) => {
      document.getElementById('aiScanning').classList.remove('open');
      e.target.value = '';

      if (!ok || !data || !data.success) {
        document.getElementById('aiErrorMsg').textContent = '⚠️ ' + ((data && (data.error || data.message)) || t('Could not process that image. Please try again.'));
        document.getElementById('aiError').classList.add('open');
        return;
      }

      aiReminderPreview = data.items.map(it => ({ ...it, warnings: it.warnings || [] }));
      renderAiReminderPreview();
    })
    .catch(err => {
      console.error('AI capture failed', err);
      document.getElementById('aiScanning').classList.remove('open');
      document.getElementById('aiErrorMsg').textContent = '⚠️ ' + t('Connection problem. Please try again.');
      document.getElementById('aiError').classList.add('open');
      e.target.value = '';
    });
}

// ---- AI reminder preview: one photo can hold many reminders; nothing is saved until confirmed ----
let aiReminderPreview = [];
const REMINDER_TYPES = ['Exam', 'Assignment', 'Quiz', 'Other'];

function renderAiReminderPreview() {
  const wrap = document.getElementById('aiRemPreview');
  const list = document.getElementById('aiRemPreviewList');
  const saveBtn = document.getElementById('aiRemSaveBtn');

  if (aiReminderPreview.length === 0) {
    wrap.classList.remove('open');
    list.innerHTML = '';
    return;
  }

  list.innerHTML = aiReminderPreview.map((r, i) => {
    const warn = r.warnings && r.warnings.length > 0;
    return `<div class="ai-rem-card${warn ? ' warn' : ''}${r.isNew ? ' new' : ''}" data-idx="${i}">
      <button type="button" class="ai-rem-remove" aria-label="${t('Remove')}" onclick="removeAiReminder(${i})">×</button>
      ${warn ? r.warnings.map(w => `<p class="ai-rem-warn">⚠️ ${escapeHtml(w)}</p>`).join('') : ''}
      <input class="ai-rem-subject" type="text" value="${escapeHtml(r.subject)}" placeholder="${t('Subject')}" oninput="editAiReminder(${i}, 'subject', this.value)">
      <div class="ai-rem-row">
        <select onchange="editAiReminder(${i}, 'type', this.value)">
          ${REMINDER_TYPES.map(ty => `<option value="${ty}"${ty === r.type ? ' selected' : ''}>${t(ty)}</option>`).join('')}
        </select>
        <input type="date" value="${escapeHtml(r.due_date)}" onchange="editAiReminder(${i}, 'due_date', this.value)">
        <input type="time" value="${escapeHtml(r.due_time)}" onchange="editAiReminder(${i}, 'due_time', this.value)">
      </div>
    </div>`;
  }).join('');

  saveBtn.textContent = t('Save :count reminders', {count: aiReminderPreview.length});
  saveBtn.disabled = false;
  wrap.classList.add('open');
}

function editAiReminder(i, field, value) {
  if (aiReminderPreview[i]) aiReminderPreview[i][field] = value;
}

function removeAiReminder(i) {
  aiReminderPreview.splice(i, 1);
  renderAiReminderPreview();
}

function addAiReminder() {
  aiReminderPreview.push({ subject: '', type: 'Exam', due_date: '', due_time: '09:00', lead_hours: 3, warnings: [], isNew: true });
  renderAiReminderPreview();
  const box = document.querySelector(`#aiRemPreviewList [data-idx="${aiReminderPreview.length - 1}"] .ai-rem-subject`);
  if (box) { box.focus(); box.scrollIntoView({ behavior: 'smooth', block: 'center' }); }
}

function cancelAiReminderPreview() {
  aiReminderPreview = [];
  renderAiReminderPreview();
}

function saveAiReminderPreview() {
  const bad = aiReminderPreview.find(r => !r.subject.trim() || !r.due_date || !r.due_time);
  if (bad) {
    document.getElementById('aiErrorMsg').textContent = '⚠️ ' + t('Some reminders have no subject, date or time. Please fix them first.');
    document.getElementById('aiError').classList.add('open');
    return;
  }

  const saveBtn = document.getElementById('aiRemSaveBtn');
  saveBtn.disabled = true;
  saveBtn.textContent = t('Saving...');

  const payload = aiReminderPreview.map(r => ({
    subject: r.subject.trim(),
    type: r.type,
    due_date: r.due_date,
    due_time: r.due_time,
    lead_hours: r.lead_hours ?? (r.type === 'Exam' ? 3 : r.type === 'Assignment' ? 6 : 1),
  }));

  fetch('{{ route('reminders.bulkStore') }}', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
    body: JSON.stringify({ reminders: payload }),
  })
    .then(res => res.json().catch(() => null).then(data => ({ ok: res.ok, status: res.status, data })))
    .then(({ ok, status, data }) => {
      if (!ok || !data || !data.success) {
        saveBtn.disabled = false;
        saveBtn.textContent = t('Save :count reminders', {count: aiReminderPreview.length});
        document.getElementById('aiErrorMsg').textContent = '⚠️ ' + (status === 419
          ? t('Your session has expired. Please refresh the page and try again.')
          : t('Could not save. Check all fields and try again.'));
        document.getElementById('aiError').classList.add('open');
        return;
      }
      reminders = reminders.concat(data.reminders);
      aiReminderPreview = [];
      renderAiReminderPreview();
      document.getElementById('aiSuccessMsg').textContent = '✅ ' + t(':count reminders added!', {count: data.reminders.length});
      document.getElementById('aiSuccess').classList.add('open');
      render();
    })
    .catch(err => {
      console.error('AI reminder save failed', err);
      saveBtn.disabled = false;
      saveBtn.textContent = t('Save :count reminders', {count: aiReminderPreview.length});
      document.getElementById('aiErrorMsg').textContent = '⚠️ ' + t('Connection problem. Please try again.');
      document.getElementById('aiError').classList.add('open');
    });
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
    const left = mins >= 60 ? Math.floor(mins / 60) + 'h ' + (mins % 60) + 'm' : mins + ' ' + t('min');
    new Notification(t(':type due in :time', {type: t(r.type), time: left}), {
      body: r.subject + ' · ' + formatWhen(dueMs),
      tag: 'reminder-' + r.id,
    });
  });
}

// Opened from the chat's "Set reminder" button: start a new reminder with the topic filled in
(function () {
  const q = new URLSearchParams(location.search);
  if (q.get('add') !== '1') return;
  openAddModal();
  const subject = (q.get('subject') || '').slice(0, 120);
  if (subject) document.getElementById('subjectInput').value = subject;
  if (['Exam', 'Assignment', 'Quiz', 'Other'].includes(q.get('type'))) selectType(q.get('type'));
  history.replaceState(null, '', location.pathname);
})();

updateNotifyBanner();
ensurePushSubscription().then(checkDueReminders);
setInterval(checkDueReminders, 30000);
setInterval(render, 60000);

render();
</script>

</body>
</html>
