<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
@include('partials.pwa-head')
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ __('RakanKampus - History') }}</title>
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
<style>
  /* ---- History (same look as Reminders) ---- */
  .section-row .back-link { display: inline-flex; align-items: center; gap: 4px; font-size: 12px; font-weight: 800; letter-spacing: .06em; text-transform: uppercase; color: #0f766e; text-decoration: none; }
  .section-row .back-link svg { width: 14px; height: 14px; }
  .section-row .back-link:hover { text-decoration: underline; }
  .status-chips { display: flex; gap: 8px; margin: 0 0 12px; }
  .status-chip { border: 1.5px solid #dbeeee; background: #ffffff; color: #475569; border-radius: 99px; padding: 6px 12px; font-size: 12px; font-weight: 700; cursor: pointer; font-family: inherit; display: inline-flex; gap: 6px; align-items: center; }
  .status-chip b { font-weight: 800; color: #14213d; }
  .status-chip.active { background: #14213d; border-color: #14213d; color: #fff; }
  .status-chip.active b { color: #5eead4; }
  .status-chip.c.active { background: #16a34a; border-color: #16a34a; } .status-chip.c.active b { color: #dcfce7; }
  .status-chip.d.active { background: #dc2626; border-color: #dc2626; } .status-chip.d.active b { color: #fee2e2; }
  .reminder-card.past { background: #f6f8fa; }
  .reminder-card.past .reminder-subject { color: #334155; }
  .reminder-card.past.deleted .reminder-subject { text-decoration: line-through; text-decoration-color: rgba(220,38,38,.5); }
  .reminder-action-btn.restore { color: #0f766e; }
  @media (max-width: 860px) { .section-row .back-link { color: #5eead4; } .status-chip { background: rgba(255,255,255,.1); border-color: rgba(255,255,255,.18); color: #e2e8f0; } .status-chip b { color: #fff; } .reminder-card.past { background: #f3f6f9; } }
  html[data-theme="dark"] .status-chip { background: #141c28; border-color: #263244; color: #aab6c3; }
  html[data-theme="dark"] .status-chip b { color: #e2e8f0; }
  html[data-theme="dark"] .status-chip.active { background: #1de2d3; border-color: #1de2d3; color: #0b1520; }
  html[data-theme="dark"] .status-chip.active b { color: #0b1520; }
  html[data-theme="dark"] .status-chip.c.active { background: #22c55e; border-color: #22c55e; } html[data-theme="dark"] .status-chip.d.active { background: #ef4444; border-color: #ef4444; color: #fff; }
  html[data-theme="dark"] .reminder-card.past { background: #182130 !important; }
  html[data-theme="dark"] .reminder-card.past .reminder-subject { color: #cbd5e1; }
  html[data-theme="dark"] .section-row .back-link { color: #5eead4; }
  html[data-theme="dark"] .reminder-action-btn.restore { color: #5eead4; }
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
html[data-theme="dark"] .section-row .back-link { color: #2fe5d6; }
html[data-theme="dark"] .status-chip { border: 1.5px solid #284848; background: #17202d; color: #d0d4da; }
html[data-theme="dark"] .status-chip b { color: #dee1e9; }
html[data-theme="dark"] .status-chip.active b { color: #9cf2e4; }
html[data-theme="dark"] .reminder-card.past { background: #121923; }
html[data-theme="dark"] .reminder-card.past .reminder-subject { color: #d6dae1; }
html[data-theme="dark"] .reminder-card.past.deleted .reminder-subject { text-decoration-color: rgba(239, 158, 158, 0.5); }
html[data-theme="dark"] .reminder-action-btn.restore { color: #2fe5d6; }
@media (max-width: 860px) {
  html[data-theme="dark"] .section-row .back-link { color: #9cf2e4; }
  html[data-theme="dark"] .status-chip { border-color: rgba(42, 51, 65, 0.18); }
  html[data-theme="dark"] .reminder-card.past { background: #111720; }
}
</style>
</head>
<body>

@include('partials.app-nav', ['active' => 'reminders', 'user' => $user])

<div class="container" id="pageContainer">
  <div class="header">
    <div>
      <p class="header-title">{{ __('History') }}</p>
      <p class="header-sub">{{ __('Past exams, assignments & deadlines') }}</p>
    </div>
  </div>

  <div class="search-toolbar-wrap">
  <div class="search-row">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
    <input type="text" id="searchInput" placeholder="{{ __('Search history...') }}" oninput="onSearchChange()">
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

  <!-- Mirror of the Reminders page: "‹ Upcoming (n)" goes back, "History (n)" is where you are -->
  <div class="section-row">
    <a href="{{ route('student.reminders') }}" class="back-link">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"></path></svg>
      {{ __('Upcoming') }} ({{ $upcomingCount }})
    </a>
    <p class="section-label" id="countLabel">{{ __('HISTORY (:count)', ['count' => 0]) }}</p>
  </div>

  <div class="status-chips">
    <button type="button" class="status-chip" id="chipAll" onclick="setStatusFilter('all')">{{ __('All') }} <b id="totalCount">0</b></button>
    <button type="button" class="status-chip c" id="chipCompleted" onclick="setStatusFilter('completed')">{{ __('Completed') }} <b id="completedCount">0</b></button>
    <button type="button" class="status-chip d" id="chipDeleted" onclick="setStatusFilter('deleted')">{{ __('Deleted') }} <b id="deletedCount">0</b></button>
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

  <div class="reminder-list" id="historyList"></div>
  <div class="empty-state" id="emptyState" style="display:none;">{{ __('No reminder history yet.') }}</div>
</div>

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
  const days = Math.round((Date.now() - dueMs) / (24 * 3600 * 1000));
  if (dueMs > Date.now()) { const left = Math.max(1, Math.ceil((dueMs - Date.now()) / 86400000)); return t('was due in :count d', {count: left}); }
  if (days <= 0) return t('Today');
  return days === 1 ? t('1 day ago') : t(':count days ago', {count: days});
}
function formatWhen(dueMs) {
  const d = new Date(dueMs);
  const loc = ({ ms: 'ms-MY', zh: 'zh-CN', ta: 'ta-IN' })[window.APP_LOCALE] || 'en-GB';
  return d.toLocaleDateString(loc, { day: 'numeric', month: 'short' }) + ' · ' + d.toLocaleTimeString(loc, { hour: '2-digit', minute: '2-digit' });
}
function escapeHtml(s) {
  return String(s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
}

const ICON_DONE = '<svg viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg>';
const ICON_BIN = '<svg viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>';

function render() {
  const cutoff = Date.now() - filterDays * 24 * 3600 * 1000;
  const q = searchQuery.trim().toLowerCase();
  const inRange = items
    .filter(it => new Date(it.due_at).getTime() >= cutoff)
    .filter(it => !q || it.subject.toLowerCase().includes(q));

  document.getElementById('totalCount').textContent = inRange.length;
  document.getElementById('completedCount').textContent = inRange.filter(it => it.status === 'completed').length;
  document.getElementById('deletedCount').textContent = inRange.filter(it => it.status === 'deleted').length;
  document.getElementById('chipAll').classList.toggle('active', statusFilter === 'all');
  document.getElementById('chipCompleted').classList.toggle('active', statusFilter === 'completed');
  document.getElementById('chipDeleted').classList.toggle('active', statusFilter === 'deleted');

  const visible = inRange
    .filter(it => statusFilter === 'all' || it.status === statusFilter)
    .sort((a, b) => new Date(b.due_at) - new Date(a.due_at));

  document.getElementById('countLabel').textContent = t('HISTORY (:count)', {count: visible.length});
  document.getElementById('emptyState').style.display = visible.length === 0 ? 'block' : 'none';

  document.getElementById('historyList').innerHTML = visible.map(it => {
    const dueMs = new Date(it.due_at).getTime();
    const ts = typeStyle(it.type);
    const done = it.status === 'completed';
    const isSelected = selectedIds.includes(it.id);
    return `
      <div class="reminder-card past ${done ? '' : 'deleted'} ${isSelected ? 'selected' : ''}" data-id="${it.id}">
        <input type="checkbox" class="reminder-select ${selectMode ? 'open' : ''}" ${isSelected ? 'checked' : ''} onchange="toggleSelect(${it.id})">
        <div class="reminder-dot" style="background: ${done ? '#dcfce7' : '#fee2e2'};">${done ? ICON_DONE : ICON_BIN}</div>
        <div class="reminder-body">
          <span class="reminder-type" style="color: ${ts.color}; background: ${ts.bg};">${escapeHtml(t(it.type))}</span>
          <p class="reminder-subject">${escapeHtml(it.subject)}</p>
          <p class="reminder-when">${formatWhen(dueMs)}</p>
          <p class="reminder-status" style="color: ${done ? '#16a34a' : '#dc2626'};">${done ? t('Completed') : t('Deleted')} · ${daysAgoLabel(dueMs)}</p>
        </div>
        ${!selectMode ? `
        <div class="reminder-actions">
          ${done ? '' : `<button type="button" class="reminder-action-btn restore" aria-label="${t('Restore')}" title="${t('Restore')}" onclick="restoreOne(${it.id})">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 3-6.7L3 8"></path><path d="M3 3v5h5"></path></svg></button>`}
          <button type="button" class="reminder-action-btn" aria-label="${t('Delete')}" onclick="deleteIds([${it.id}])">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
          </button>
        </div>` : ''}
      </div>`;
  }).join('');

  document.getElementById('selectAllRow').classList.toggle('open', selectMode);
  document.getElementById('selectAllCheckbox').checked = visible.length > 0 && visible.every(it => selectedIds.includes(it.id));
  document.getElementById('bulkDeleteBtn').disabled = selectedIds.length === 0;
  document.getElementById('selectedCount').textContent = selectedIds.length;
  window._visibleIds = visible.map(it => it.id);
}

function onSearchChange() { searchQuery = document.getElementById('searchInput').value; render(); }
function setStatusFilter(status) { statusFilter = status; render(); }
function toggleFilterOpen() { document.getElementById('filterDropdown').classList.toggle('open'); }
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
  selectedIds = selectedIds.includes(id) ? selectedIds.filter(x => x !== id) : selectedIds.concat(id);
  render();
}
function toggleSelectAll() {
  const ids = window._visibleIds || [];
  selectedIds = ids.length > 0 && ids.every(id => selectedIds.includes(id)) ? [] : ids.slice();
  render();
}
function deleteSelected() { deleteIds(selectedIds.slice()); }

// Permanent delete (partials/rk-dialog: the robot carries the old records to the bin)
async function deleteIds(ids) {
  if (ids.length === 0) return;
  const ok = await RKDialog.confirm({
    scene: 'trash',
    title: t('Permanently delete :count record(s)?', {count: ids.length}),
    message: t('They will be removed from your history for good.'),
    list: items.filter(it => ids.includes(it.id)).map(it => ({ label: it.subject, meta: t(it.status === 'completed' ? 'Completed' : 'Deleted') })),
    warn: t('This cannot be undone.'),
    confirmText: t('Delete'),
  });
  if (!ok) return;
  fetch('/reminders/history/bulk-delete', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
    body: JSON.stringify({ ids }),
  }).then(res => res.json()).then(() => {
    items = items.filter(it => !ids.includes(it.id));
    selectedIds = selectedIds.filter(id => !ids.includes(id));
    render();
    RKToast.show({ text: t(':count record(s) deleted', {count: ids.length}) });
  }).catch(err => console.error('Bulk delete failed', err));
}

// Bring a deleted (not yet due) reminder back to the Reminders list
function restoreOne(id) {
  fetch('/reminders/restore', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
    body: JSON.stringify({ ids: [id] }),
  }).then(res => res.json()).then((data) => {
    if (!data || !data.success) throw new Error('restore failed');
    const it = items.find(x => x.id === id);
    const upcoming = it && new Date(it.due_at).getTime() > Date.now();
    if (upcoming) items = items.filter(x => x.id !== id);   // it's back in Upcoming
    else it.status = 'completed';                              // past due: it now shows as completed
    render();
    RKToast.show({ text: t('Restored'), sub: upcoming ? t('Back in your Reminders') : t('Moved to Completed') });
  }).catch(() => RKDialog.alert({ scene: 'oops', title: t('Oops!'), message: t('Connection problem. Please try again.') }));
}

// Swipe right anywhere on the page to go back to Reminders
(function () {
  const el = document.getElementById('pageContainer');
  let startX = null;
  el.addEventListener('pointerdown', (e) => { if (!e.target.closest('.reminder-card')) startX = e.clientX; });
  el.addEventListener('pointerup', (e) => {
    if (startX === null) return;
    const delta = e.clientX - startX; startX = null;
    if (delta > 70) window.location.href = "{{ route('student.reminders') }}";
  });
})();

render();
</script>

</body>
</html>
