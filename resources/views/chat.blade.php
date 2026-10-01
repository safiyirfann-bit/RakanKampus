<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
@include('partials.pwa-head')
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ __('RakanKampus - New Conversation') }}</title>
@php
  $initials = strtoupper(substr($user->first_name ?? $user->name ?? 'A', 0, 1) . substr($user->last_name ?? '', 0, 1));
  $displayName = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?: $user->name;
  $firstName = $user->first_name ?: strtok((string) $user->name, ' ');
  $suggestIcons = ['💬', '💳', '📍', '📚'];
@endphp
<style>
  :root {
    --navy: #0f1d2e;
    --navy-2: #16273b;
    --navy-3: #1d3149;
    --navy-line: #22384f;
    --teal: #14b8a6;
    --teal-soft: #e6f7f5;
    --ink: #1f2a37;
    --muted: #64748b;
    --faint: #94a3b8;
    --line: #e5e9ee;
    --user-bubble: #e8f1f8;
    --col: 760px;
  }
  * { box-sizing: border-box; }
  html, body { margin: 0; height: 100%; }
  body {
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    background: #fff;
    color: var(--ink);
    overflow: hidden;
  }
  button { font: inherit; }

  .app { display: flex; height: 100vh; height: 100dvh; }

  /* ---------- Sidebar ---------- */
  .sidebar {
    width: 280px; min-width: 280px;
    background: var(--navy);
    color: #cbd6e2;
    display: flex; flex-direction: column;
    padding: 12px 10px;
    transition: margin-left .25s ease, transform .25s ease;
    z-index: 30;
  }
  .app.sidebar-collapsed .sidebar { margin-left: -280px; }
  .side-top { display: flex; align-items: center; justify-content: space-between; padding: 4px 6px 12px; }
  .brand { display: flex; align-items: center; gap: 9px; color: #fff; font-weight: 700; font-size: 15px; text-decoration: none; }
  .brand-logo { width: 30px; height: 30px; border-radius: 9px; background: #fff; display: flex; align-items: center; justify-content: center; }
  .icon-btn {
    width: 34px; height: 34px; border-radius: 8px; border: none; background: none;
    color: #9fb0c2; cursor: pointer; display: flex; align-items: center; justify-content: center; text-decoration: none;
  }
  .icon-btn:hover { background: var(--navy-3); color: #fff; }
  .icon-btn svg { width: 19px; height: 19px; }
  .side-btn {
    display: flex; align-items: center; gap: 10px; width: 100%;
    padding: 10px 12px; border-radius: 10px; border: 1px solid var(--navy-line);
    background: none; color: #fff; font-size: 14px; font-weight: 600; cursor: pointer; text-align: left;
    text-decoration: none;
  }
  .side-btn:hover { background: var(--navy-2); }
  .side-btn svg { width: 17px; height: 17px; flex-shrink: 0; }
  .side-link { border-color: transparent; font-weight: 500; color: #cbd6e2; margin-top: 2px; }
  .side-search { border-color: transparent; font-weight: 500; color: #cbd6e2; }
  .side-search kbd { margin-left: auto; font-family: inherit; font-size: 11px; line-height: 1; color: #6f8399; border: 1px solid var(--navy-line); border-radius: 5px; padding: 3px 5px; }

  .recent-list { flex: 1; overflow-y: auto; margin: 4px -4px 0; padding: 0 4px; }
  .recent-list::-webkit-scrollbar { width: 6px; }
  .recent-list::-webkit-scrollbar-thumb { background: var(--navy-line); border-radius: 6px; }
  .grp { font-size: 11.5px; font-weight: 600; color: #6f8399; padding: 16px 12px 6px; }
  .recent-item {
    position: relative; display: flex; align-items: center; gap: 6px;
    padding: 8px 8px 8px 12px; border-radius: 8px; cursor: pointer; font-size: 13.5px; color: #cbd6e2;
  }
  .recent-item:hover { background: var(--navy-2); }
  .recent-item.active { background: var(--navy-3); color: #fff; }
  .recent-title { flex: 1; min-width: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .dots-btn {
    width: 26px; height: 26px; border-radius: 6px; border: none; background: none; color: #9fb0c2;
    cursor: pointer; display: flex; align-items: center; justify-content: center; opacity: 0; flex-shrink: 0;
  }
  .recent-item:hover .dots-btn, .recent-item.active .dots-btn, .dots-btn[aria-expanded="true"] { opacity: 1; }
  .dots-btn:hover { background: rgba(255,255,255,.08); color: #fff; }
  @media (hover: none) { .dots-btn { opacity: 1; } }
  .item-menu {
    position: absolute; right: 6px; top: calc(100% - 2px); z-index: 40;
    background: #fff; border-radius: 10px; padding: 5px; min-width: 150px;
    box-shadow: 0 12px 30px rgba(15,29,46,.25);
  }
  .item-menu button {
    display: flex; align-items: center; gap: 10px; width: 100%; padding: 8px 10px; border: none; background: none;
    border-radius: 7px; font-size: 13.5px; color: var(--ink); cursor: pointer; text-align: left;
  }
  .item-menu button:hover { background: #f1f5f9; }
  .item-menu button.danger { color: #dc2626; }
  .item-menu svg { width: 15px; height: 15px; }
  .recent-empty { font-size: 13px; color: #6f8399; padding: 14px 12px; }

  .side-user {
    display: flex; align-items: center; gap: 10px; padding: 10px; margin-top: 8px;
    border-top: 1px solid var(--navy-line); text-decoration: none; border-radius: 10px;
  }
  .side-user:hover { background: var(--navy-2); }
  .avatar {
    width: 32px; height: 32px; border-radius: 50%; background: var(--teal); color: #fff;
    font-size: 12px; font-weight: 700; display: flex; align-items: center; justify-content: center; overflow: hidden; flex-shrink: 0;
  }
  .avatar img { width: 100%; height: 100%; object-fit: cover; }
  .side-user b { color: #fff; font-size: 13.5px; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .side-user span { font-size: 11.5px; color: #7f93a8; }
  .side-user .avatar { color: #fff; font-size: 12px; }

  .sidebar-backdrop { display: none; }

  /* ---------- Main ---------- */
  .main { flex: 1; min-width: 0; display: flex; flex-direction: column; position: relative; }
  .topbar {
    height: 56px; flex-shrink: 0; display: flex; align-items: center; gap: 8px;
    padding: 0 14px; border-bottom: 1px solid #eef1f4; background: #fff;
  }
  .topbar .icon-btn { color: var(--muted); }
  .topbar .icon-btn:hover { background: #f1f5f9; color: var(--ink); }
  .topbar-title { flex: 1; min-width: 0; font-size: 15px; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .topbar-title span { color: var(--faint); font-weight: 400; font-size: 13px; margin-left: 6px; }
  .app:not(.sidebar-collapsed) .show-when-collapsed { display: none; }

  .chat-area { flex: 1; overflow-y: auto; padding: 28px 16px 40px; scroll-behavior: smooth; }
  .col { max-width: var(--col); margin: 0 auto; }

  .message-row { display: flex; margin: 22px 0; animation: rise .25s ease both; }
  @keyframes rise { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: none; } }
  .message-row.user { justify-content: flex-end; }
  .message-row.user .message {
    background: var(--user-bubble); color: #0f2742; padding: 11px 16px; border-radius: 18px;
    max-width: min(560px, 85%); font-size: 15px; line-height: 1.55; white-space: pre-wrap; word-break: break-word;
  }
  .message-row.bot { gap: 14px; }
  .bot-avatar {
    width: 32px; height: 32px; border-radius: 50%; background: var(--teal-soft); border: 1px solid #cdeee9;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
  }
  .bot-body { flex: 1; min-width: 0; padding-top: 4px; }
  .message-row.bot .message { font-size: 15px; line-height: 1.7; word-break: break-word; }
  .message-row.bot .message.plain { white-space: pre-wrap; }
  .message-row.bot .message p { margin: 0 0 10px; }
  .message-row.bot .message > :last-child { margin-bottom: 0; }
  .message-row.bot .message a { color: #0d9488; text-decoration: underline; text-decoration-color: rgba(13,148,136,.35); text-underline-offset: 2px; word-break: break-all; }
  .message-row.bot .message a:hover { text-decoration-color: currentColor; }
  /* Numbered answers: step badges joined by a thin line, like a timeline */
  .steps { list-style: none; margin: 6px 0 14px; padding: 0; }
  .steps li { position: relative; padding: 0 0 14px 42px; min-height: 28px; }
  .steps li::before {
    content: attr(data-n); position: absolute; left: 0; top: 0;
    width: 28px; height: 28px; border-radius: 50%;
    background: var(--teal-soft); color: #0f766e; border: 1px solid #b7e4dd;
    font-size: 13px; font-weight: 700; display: flex; align-items: center; justify-content: center;
  }
  .steps li::after { content: ''; position: absolute; left: 13.5px; top: 32px; bottom: 4px; width: 1px; background: #d5ece8; }
  .steps li:last-child { padding-bottom: 0; }
  .steps li:last-child::after { display: none; }
  .steps .st { display: block; padding-top: 2px; }
  /* a numbered item that has its own bullet points: title in bold, points tucked under it */
  .steps li.has-sub .st { font-weight: 700; color: var(--navy); }
  .steps li.has-sub .st b { font-weight: 700; }
  .steps .sub { list-style: none; margin: 4px 0 0; padding: 8px 12px 8px 6px; background: #f7fbfa; border: 1px solid #e3f1ee; border-radius: 12px; }
  .steps .sub li { padding: 0 0 0 20px; margin: 3px 0; min-height: 0; color: #475569; font-size: 14.5px; }
  .steps .sub li::after { display: none; }
  .steps .sub li::before { content: ''; left: 6px; top: .72em; width: 6px; height: 6px; border: 0; background: var(--teal); }
  .steps .note { display: block; margin-top: 4px; color: #475569; font-size: 14.5px; }
  .steps b, .bullets b { color: var(--navy); font-weight: 600; }
  .bullets { margin: 4px 0 12px; padding-left: 4px; list-style: none; }
  .bullets li { position: relative; padding-left: 20px; margin: 4px 0; }
  .bullets li::before { content: ''; position: absolute; left: 4px; top: .7em; width: 6px; height: 6px; border-radius: 50%; background: var(--teal); }
  .message-row.bot .message h4 { font-size: 15.5px; font-weight: 700; color: var(--navy); margin: 12px 0 6px; }
  /* Action bar under each bot answer (copy, rate, share, regenerate, more) */
  .msg-actions { position: relative; display: flex; align-items: center; gap: 2px; margin: 6px 0 0 -6px; opacity: 0; transition: opacity .15s; }
  .message-row.bot:hover .msg-actions, .message-row.bot:last-child .msg-actions, .msg-actions.pinned { opacity: 1; }
  @media (hover: none) { .msg-actions { opacity: 1; } }
  .msg-actions button {
    position: relative; width: 32px; height: 32px; padding: 0; border-radius: 8px; border: none; background: none; color: var(--faint);
    cursor: pointer; display: grid; place-items: center; transition: background .15s, color .15s, transform .1s;
  }
  .msg-actions button:hover { background: #f1f5f9; color: var(--ink); }
  .msg-actions button:active { transform: scale(.9); }
  .msg-actions button.on { color: var(--ink); }
  .msg-actions button.on svg { fill: currentColor; }
  .msg-actions button.gone { display: none; }
  .msg-actions button.pop svg { animation: actPop .35s cubic-bezier(.2,1.6,.4,1); }
  .msg-actions svg { width: 17px; height: 17px; }
  .msg-actions [data-regen].spin svg { animation: actSpin .8s linear infinite; }
  .message-row.bot:not(:last-child) [data-regen] { display: none; }
  .msg-actions button[data-tip]::after {
    content: attr(data-tip); position: absolute; bottom: calc(100% + 6px); left: 50%; transform: translateX(-50%) translateY(3px);
    background: #0f172a; color: #fff; font-size: 11.5px; font-weight: 500; white-space: nowrap; padding: 5px 8px; border-radius: 7px;
    opacity: 0; pointer-events: none; transition: opacity .12s, transform .12s; z-index: 5;
  }
  @media (hover: hover) { .msg-actions button[data-tip]:hover::after { opacity: 1; transform: translateX(-50%); } }
  .msg-actions .act-note { font-size: 12px; color: var(--faint); margin-left: 6px; animation: actFade 2.2s ease forwards; }
  .act-menu {
    position: absolute; left: 150px; bottom: calc(100% + 6px); min-width: 170px; padding: 6px; border-radius: 14px; z-index: 20;
    background: #fff; border: 1px solid #e2e8f0; box-shadow: 0 12px 30px rgba(15,23,42,.14); animation: actMenu .14s ease-out;
  }
  .act-menu button { width: 100%; height: auto; display: flex; align-items: center; gap: 10px; padding: 9px 10px; border-radius: 9px; color: var(--ink); font-size: 14px; }
  .act-menu button:hover { background: #f1f5f9; }
  .msg-actions .act-menu button::after { display: none; }
  @keyframes actPop { 0% { transform: scale(1); } 45% { transform: scale(1.35) rotate(-8deg); } 100% { transform: scale(1); } }
  @keyframes actSpin { to { transform: rotate(360deg); } }
  @keyframes actFade { 0%, 75% { opacity: 1; } 100% { opacity: 0; } }
  @keyframes actMenu { from { opacity: 0; transform: translateY(4px); } to { opacity: 1; transform: none; } }


  /* ---------- Read aloud: floating talking mascot ---------- */
  .rk-mascot {
    position: absolute; right: 22px; bottom: 108px; z-index: 30; display: flex; align-items: flex-end;
    pointer-events: none; animation: mascotIn .5s cubic-bezier(.2,1.5,.4,1);
  }
  .rk-mascot.out { animation: mascotOut .3s ease-in forwards; }
  .rk-mascot > * { pointer-events: auto; }
  .rk-mascot .m-bot { width: 86px; height: 96px; flex: none; filter: drop-shadow(0 10px 16px rgba(15,29,46,.28)); animation: mascotHover 2.4s ease-in-out infinite; }
  .rk-mascot .m-say {
    position: relative; margin: 0 4px 64px 0; max-width: 250px; padding: 10px 14px 11px; border-radius: 18px 18px 4px 18px;
    background: var(--navy); color: #fff; font-size: 13px; line-height: 1.5; box-shadow: 0 12px 28px rgba(15,29,46,.28);
  }
  .rk-mascot .m-top { display: flex; align-items: center; gap: 7px; font-size: 10.5px; font-weight: 800; letter-spacing: .5px; color: #99f6e4; margin-bottom: 3px; text-transform: uppercase; }
  .rk-mascot .m-eq { display: flex; align-items: center; gap: 2px; height: 12px; }
  .rk-mascot .m-eq i { width: 2.5px; height: 3px; border-radius: 2px; background: #2dd4bf; }
  .rk-mascot.talking .m-eq i { animation: mEq .8s ease-in-out infinite; }
  .rk-mascot .m-eq i:nth-child(2) { animation-delay: -.3s; } .rk-mascot .m-eq i:nth-child(3) { animation-delay: -.55s; } .rk-mascot .m-eq i:nth-child(4) { animation-delay: -.15s; }
  .rk-mascot .m-text { display: block; max-height: 4.5em; overflow: hidden; animation: mText .3s ease; }
  .rk-mascot .m-stop {
    position: absolute; right: -9px; top: -9px; width: 26px; height: 26px; border-radius: 50%; border: 2px solid #fff;
    background: #ef4444; color: #fff; display: grid; place-items: center; cursor: pointer; padding: 0; box-shadow: 0 4px 10px rgba(239,68,68,.4);
  }
  .rk-mascot .m-stop svg { width: 11px; height: 11px; }
  .rk-mascot .m-stop:hover { transform: scale(1.08); }
  /* robot parts */
  .m-bot .eyes { transform-box: fill-box; transform-origin: center; animation: mBlink 3.4s infinite; }
  .m-bot .tongue { opacity: 0; }
  .rk-mascot.talking .m-bot .mouth { transform-box: fill-box; transform-origin: center; animation: mTalk 1.1s steps(1) infinite; }
  .rk-mascot.talking .m-bot .tongue { transform-box: fill-box; transform-origin: center; animation: mTongue 1.1s steps(1) infinite; }
  .rk-mascot.talking .m-bot .ant { animation: mAnt .55s ease-in-out infinite alternate; }
  .rk-mascot.talking .m-bot .chest { animation: mChest .4s ease-in-out infinite alternate; }
  .rk-mascot.talking .m-bot .head { transform-box: view-box; transform-origin: 100px 94px; animation: mNod 1.6s ease-in-out infinite; }
  .rk-mascot.talking .m-bot .arm { transform-box: view-box; transform-origin: 58px 118px; animation: mGest 1.3s ease-in-out infinite; }
  .rk-mascot.loading .m-bot .head { transform-box: view-box; transform-origin: 100px 94px; animation: mThink 1.4s ease-in-out infinite; }
  /* the sentence being read, underlined inside the answer */
  ::highlight(rk-reading) { background-color: rgba(45,212,191,.28); }
  @keyframes mascotIn { from { transform: translateY(50px) scale(.6); opacity: 0; } to { transform: none; opacity: 1; } }
  @keyframes mascotOut { to { transform: translateY(40px) scale(.7); opacity: 0; } }
  @keyframes mascotHover { 50% { transform: translateY(-5px); } }
  @keyframes mEq { 0%,100% { height: 3px; } 50% { height: 12px; } }
  @keyframes mText { from { opacity: .35; transform: translateY(3px); } to { opacity: 1; transform: none; } }
  @keyframes mBlink { 0%,92%,100% { transform: scaleY(1); } 95% { transform: scaleY(.1); } }
  @keyframes mTalk { 0% { transform: scaleY(.35); } 12% { transform: scaleY(1.25) scaleX(.85); } 24% { transform: scaleY(.6); } 36% { transform: scaleY(1.45) scaleX(.8); } 48% { transform: scaleY(.3) scaleX(1.1); } 60% { transform: scaleY(1.1); } 72% { transform: scaleY(.5) scaleX(1.05); } 84% { transform: scaleY(1.35) scaleX(.85); } 100% { transform: scaleY(.35); } }
  @keyframes mTongue { 0%,24%,48%,72% { opacity: 0; } 12%,36%,60%,84% { opacity: 1; } }
  @keyframes mAnt { to { fill: #f472b6; } }
  @keyframes mChest { from { opacity: .5; } to { opacity: 1; } }
  @keyframes mNod { 0%,100% { transform: rotate(0); } 25% { transform: rotate(-4deg); } 75% { transform: rotate(3deg); } }
  @keyframes mGest { 0%,100% { transform: rotate(0); } 50% { transform: rotate(-14deg); } }
  @keyframes mThink { 0%,100% { transform: rotate(-6deg); } 50% { transform: rotate(6deg); } }
  @media (max-width: 760px) {
    .rk-mascot { right: 10px; bottom: 96px; }
    .rk-mascot .m-bot { width: 66px; height: 74px; }
    .rk-mascot .m-say { max-width: min(220px, 60vw); margin-bottom: 50px; font-size: 12.5px; }
  }
  @media (prefers-reduced-motion: reduce) { .rk-mascot, .rk-mascot * { animation: none !important; } }
  /* ---------- Robot "searching" animation ---------- */
  .searching { display: flex; align-items: center; gap: 14px; padding-top: 2px; }
  .search-bot { width: 84px; height: 56px; flex-shrink: 0; overflow: visible; }
  .search-bot .bob { animation: bob 1.6s ease-in-out infinite; transform-origin: center; }
  .search-bot .eyes { animation: look 2.4s ease-in-out infinite; }
  .search-bot .antenna { animation: blink 1s steps(2, start) infinite; }
  .search-bot .lens { animation: scan 2.4s ease-in-out infinite; transform-origin: 52px 44px; }
  .search-bot .doc { animation: docs 2.4s ease-in-out infinite; opacity: 0; }
  .search-bot .doc.d2 { animation-delay: .8s; }
  .search-bot .doc.d3 { animation-delay: 1.6s; }
  @keyframes bob { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-2px); } }
  @keyframes look { 0%,100% { transform: translateX(-2.5px); } 50% { transform: translateX(2.5px); } }
  @keyframes blink { to { opacity: .25; } }
  @keyframes scan { 0%,100% { transform: translate(-2px, 3px) rotate(-6deg); } 50% { transform: translate(12px, -4px) rotate(6deg); } }
  @keyframes docs { 0% { opacity: 0; transform: translateY(6px); } 25%,60% { opacity: 1; transform: translateY(0); } 100% { opacity: 0; transform: translateY(-6px); } }
  .search-text { font-size: 14px; color: var(--muted); }
  .search-text b { display: block; color: var(--ink); font-weight: 600; font-size: 14.5px; margin-bottom: 2px; }
  .search-status { display: inline-block; animation: fadeStatus .35s ease; }
  @keyframes fadeStatus { from { opacity: 0; transform: translateY(3px); } to { opacity: 1; transform: none; } }
  .dots::after { content: ''; animation: dots 1.2s steps(4, end) infinite; }
  @keyframes dots { 0% { content: ''; } 25% { content: '.'; } 50% { content: '..'; } 75%,100% { content: '...'; } }
  .stopped { font-size: 13px; color: var(--faint); font-style: italic; }

  /* ---------- Empty state ---------- */
  .empty-state { min-height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; padding: 20px 0 40px; }
  .hero-bot { width: 60px; height: 60px; border-radius: 50%; background: var(--teal-soft); border: 1px solid #cdeee9; display: flex; align-items: center; justify-content: center; margin-bottom: 14px; }
  .empty-state h2 { font-size: 28px; font-weight: 600; color: var(--navy); margin: 0 0 6px; }
  .empty-state p { color: var(--muted); margin: 0 0 26px; }
  .suggestions { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; width: 100%; max-width: var(--col); margin-top: 16px; text-align: left; }
  .suggestion {
    border: 1px solid var(--line); border-radius: 14px; padding: 12px 16px; background: #fff; cursor: pointer;
    font-size: 14px; color: #334155; text-align: left; transition: border-color .15s, background .15s, transform .15s;
    display: flex; gap: 10px; align-items: flex-start;
  }
  .suggestion:hover { border-color: #b7e4dd; background: #f7fcfb; transform: translateY(-1px); }
  .suggestion small { display: block; color: var(--faint); font-size: 12px; margin-top: 2px; }

  /* ---------- Composer ---------- */
  .composer { padding: 0 16px calc(14px + env(safe-area-inset-bottom, 0px)); background: linear-gradient(to top, #fff 75%, rgba(255,255,255,0)); }
  .app.is-empty .composer { display: none; }
  .composer-box {
    max-width: var(--col); margin: 0 auto; border: 1px solid #dfe5eb; border-radius: 24px;
    padding: 12px 10px 10px 18px; background: #fff; box-shadow: 0 6px 24px rgba(15,29,46,.08);
    display: flex; align-items: flex-end; gap: 8px; transition: border-color .15s, box-shadow .15s;
  }
  .composer-box:focus-within { border-color: #b7e4dd; box-shadow: 0 6px 24px rgba(20,184,166,.12); }
  #messageInput, .hero-input {
    flex: 1; border: none; outline: none; resize: none; font: inherit; font-size: 15px; line-height: 1.5;
    color: var(--ink); background: transparent; max-height: 160px; padding: 6px 0; overflow-y: hidden;
  }
  #messageInput::placeholder, .hero-input::placeholder { color: var(--faint); }
  #messageInput.voice-live, .hero-input.voice-live { color: var(--teal); }
  .round-btn {
    width: 38px; height: 38px; border-radius: 50%; border: none; cursor: pointer; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center; transition: background .15s, transform .1s;
  }
  .round-btn:active { transform: scale(.94); }
  .round-btn svg { width: 18px; height: 18px; }
  .mic-btn { background: none; color: var(--muted); }
  .mic-btn:hover { background: #f1f5f9; color: var(--ink); }
  .mic-btn svg { fill: none; stroke: currentColor; }
  .mic-btn.listening { background: #fee2e2; color: #dc2626; animation: pulse 1.2s ease-in-out infinite; }
  @keyframes pulse { 0%,100% { box-shadow: 0 0 0 0 rgba(220,38,38,.35); } 50% { box-shadow: 0 0 0 7px rgba(220,38,38,0); } }
  .send-btn { background: var(--navy); color: #fff; }
  .send-btn:hover { background: #1d3149; }
  .send-btn:disabled { background: #cbd5e1; cursor: default; }
  .stop-btn { background: var(--navy); color: #fff; }
  .hidden { display: none !important; }
  .disclaimer { text-align: center; font-size: 11.5px; color: var(--faint); margin-top: 8px; }


  /* ---------- Collapsed icon rail (desktop, like ChatGPT) ---------- */
  .rail { display: none; }
  @media (min-width: 861px) {
    .app.sidebar-collapsed .rail {
      display: flex; flex-direction: column; align-items: center; gap: 4px;
      width: 58px; min-width: 58px; background: var(--navy); padding: 10px 0 12px; z-index: 31;
    }
    #menuBtn { display: none; }
    /* New chat + Home already live in the sidebar / rail on desktop; the topbar keeps them on mobile only */
    .topbar .mobile-only { display: none; }
  }
  .rail-btn {
    position: relative; width: 40px; height: 40px; border-radius: 10px; border: none; background: none;
    color: #9fb0c2; cursor: pointer; display: flex; align-items: center; justify-content: center; text-decoration: none;
    transition: background .15s, color .15s;
  }
  .rail-btn:hover, .rail-btn.open { background: var(--navy-3); color: #fff; }
  .rail-btn svg { width: 20px; height: 20px; }
  .rail-logo { margin-bottom: 10px; }
  .rail-logo .logo-face { width: 32px; height: 32px; border-radius: 9px; background: #fff; display: flex; align-items: center; justify-content: center; }
  .rail-logo .logo-hover { display: none; }
  .rail-logo:hover .logo-face { display: none; }
  .rail-logo:hover .logo-hover { display: block; }
  .rail-spacer { flex: 1; }
  .rail .avatar { width: 30px; height: 30px; font-size: 11px; }
  .rail-btn[data-tip]:hover::after {
    content: attr(data-tip); position: absolute; left: calc(100% + 10px); top: 50%; transform: translateY(-50%);
    background: #0b1522; color: #fff; font-size: 12.5px; font-weight: 600; padding: 6px 10px; border-radius: 8px;
    white-space: nowrap; pointer-events: none; box-shadow: 0 6px 16px rgba(0,0,0,.25); z-index: 70;
    animation: tipIn .12s ease;
  }
  .rail-btn.open[data-tip]::after { display: none; }
  @keyframes tipIn { from { opacity: 0; transform: translate(-3px, -50%); } }

  /* "Chats" flyout next to the rail */
  .flyout {
    position: fixed; left: 66px; z-index: 65; width: 290px; max-height: min(460px, calc(100vh - 40px)); overflow-y: auto;
    background: var(--navy-2); border: 1px solid var(--navy-line); border-radius: 16px; padding: 10px 6px;
    box-shadow: 0 18px 40px rgba(0,0,0,.35); animation: flyIn .15s ease;
  }
  @keyframes flyIn { from { opacity: 0; transform: translateX(-6px); } }
  .fly-title { font-size: 12.5px; font-weight: 600; color: #7f93a8; padding: 4px 12px 8px; }
  .fly-item {
    display: block; width: 100%; text-align: left; border: none; background: none; color: #e2e8f0; cursor: pointer;
    padding: 9px 12px; border-radius: 9px; font-size: 14px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
  }
  .fly-item:hover { background: var(--navy-3); }
  .fly-item.active { background: var(--navy-3); color: #fff; }
  .fly-empty { color: #7f93a8; font-size: 13px; padding: 8px 12px; }

  /* Search chats pop-up */
  dialog.search-modal {
    border: none; padding: 0; border-radius: 18px; width: min(640px, calc(100vw - 32px)); max-height: min(560px, calc(100vh - 80px));
    background: #fff; color: var(--ink); box-shadow: 0 30px 70px rgba(15,29,46,.35); overflow: hidden; margin-top: 10vh;
  }
  dialog.search-modal[open] { display: flex; flex-direction: column; animation: popIn .16s ease; }
  @keyframes popIn { from { opacity: 0; transform: translateY(8px) scale(.98); } }
  dialog.search-modal::backdrop { background: rgba(15,29,46,.45); }
  .sm-head { display: flex; align-items: center; gap: 12px; padding: 16px 16px 14px 22px; border-bottom: 1px solid #eef1f4; }
  .sm-head svg { width: 19px; height: 19px; color: var(--faint); flex-shrink: 0; }
  #searchModalInput { flex: 1; border: none; outline: none; font: inherit; font-size: 16px; color: var(--ink); background: none; }
  #searchModalInput::placeholder { color: var(--faint); }
  .sm-close { width: 34px; height: 34px; border-radius: 50%; border: none; background: none; color: var(--muted); cursor: pointer; display: flex; align-items: center; justify-content: center; }
  .sm-close:hover { background: #f1f5f9; color: var(--ink); }
  .sm-body { overflow-y: auto; padding: 8px 10px 14px; }
  .sm-label { font-size: 12.5px; font-weight: 600; color: var(--muted); padding: 10px 12px 6px; }
  .sm-item {
    display: flex; align-items: center; gap: 14px; width: 100%; border: none; background: none; text-align: left;
    padding: 11px 12px; border-radius: 10px; cursor: pointer; color: var(--ink); font-size: 14.5px;
  }
  .sm-item svg { width: 18px; height: 18px; color: var(--muted); flex-shrink: 0; }
  .sm-item:hover, .sm-item.focus { background: #f1f5f9; }
  .sm-item .sm-text { min-width: 0; flex: 1; }
  .sm-item .sm-text b { display: block; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .sm-item .sm-text small { display: block; color: var(--faint); font-size: 12.5px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-top: 1px; }
  .sm-item .sm-when { font-size: 12px; color: var(--faint); flex-shrink: 0; }
  .sm-new svg { color: var(--teal); }
  .sm-empty { color: var(--faint); font-size: 14px; padding: 20px 12px; text-align: center; }
  mark { background: #ccfbf1; color: inherit; border-radius: 3px; padding: 0 1px; }

  /* ---------- Mobile ---------- */
  @media (max-width: 860px) {
    .sidebar { position: fixed; top: 0; bottom: 0; left: 0; margin-left: 0 !important; transform: translateX(0); box-shadow: 10px 0 30px rgba(0,0,0,.25); }
    .app.sidebar-collapsed .sidebar { transform: translateX(-100%); box-shadow: none; }
    .sidebar-backdrop { display: block; position: fixed; inset: 0; background: rgba(15,29,46,.45); z-index: 25; transition: opacity .25s; }
    .app.sidebar-collapsed .sidebar-backdrop { opacity: 0; pointer-events: none; }
    .app:not(.sidebar-collapsed) .show-when-collapsed { display: flex; }
    .topbar-title span { display: none; }
    .suggestions { grid-template-columns: 1fr; }
    .empty-state h2 { font-size: 23px; }
    .chat-area { padding: 18px 14px 30px; }
    .message-row.bot { gap: 10px; }
  }
</style>
<style id="rk-dark-theme">
/* Dark theme — generated by scripts/dark-theme/generate.py, do not edit by hand.
   Hand-made fixes go in resources/views/partials/dark-fixes.blade.php */
html[data-theme="dark"] body { background: #17202d; color: #dee2e8; }
html[data-theme="dark"] .brand-logo { background: #17202d; }
html[data-theme="dark"] .icon-btn { color: #ced3d9; }
html[data-theme="dark"] .side-search kbd { color: #bec3ca; }
html[data-theme="dark"] .grp { color: #bec3ca; }
html[data-theme="dark"] .dots-btn { color: #ced3d9; }
html[data-theme="dark"] .item-menu { background: #17202d; box-shadow: 0 12px 30px rgba(0, 0, 0, 0.5); }
html[data-theme="dark"] .item-menu button { color: #dee2e8; }
html[data-theme="dark"] .item-menu button:hover { background: #10161f; }
html[data-theme="dark"] .item-menu button.danger { color: #ef9e9e; }
html[data-theme="dark"] .recent-empty { color: #bec3ca; }
html[data-theme="dark"] .side-user span { color: #ced3d8; }
html[data-theme="dark"] .topbar { border-bottom: 1px solid #2a3341; background: #17202d; }
html[data-theme="dark"] .topbar .icon-btn { color: #b0b6be; }
html[data-theme="dark"] .topbar .icon-btn:hover { background: #10161f; color: #dee2e8; }
html[data-theme="dark"] .topbar-title span { color: #ced3d9; }
html[data-theme="dark"] .message-row.user .message { background: #1d2f3d; color: #dee3e9; }
html[data-theme="dark"] .bot-avatar { background: #1e3e3a; border: 1px solid #284843; }
html[data-theme="dark"] .message-row.bot .message a { color: #41eedf; text-decoration-color: rgba(65, 238, 223, 0.35); }
html[data-theme="dark"] .steps li::before { background: #1e3e3a; color: #2fe5d6; border: 1px solid #284843; }
html[data-theme="dark"] .steps li::after { background: #234a43; }
html[data-theme="dark"] .steps li.has-sub .st { color: #e1e6ec; }
html[data-theme="dark"] .steps .sub { background: #131a25; border: 1px solid #284841; }
html[data-theme="dark"] .steps .sub li { color: #d0d4da; }
html[data-theme="dark"] .steps .note { color: #d0d4da; }
html[data-theme="dark"] .steps b, html[data-theme="dark"] .bullets b { color: #e1e6ec; }
html[data-theme="dark"] .message-row.bot .message h4 { color: #e1e6ec; }
html[data-theme="dark"] .msg-actions button { color: #ced3d9; }
html[data-theme="dark"] .msg-actions button:hover { background: #10161f; color: #dee2e8; }
html[data-theme="dark"] .msg-actions button.on { color: #dee2e8; }
html[data-theme="dark"] .rk-mascot .m-say { background: #1f2a37; border: 1px solid #334155; }
html[data-theme="dark"] .rk-mascot .m-bot { filter: drop-shadow(0 10px 16px rgba(0,0,0,.5)); }
html[data-theme="dark"] ::highlight(rk-reading) { background-color: rgba(45,212,191,.22); }
html[data-theme="dark"] .act-menu { background: #1a212b; border-color: #2a3340; box-shadow: 0 12px 30px rgba(0,0,0,.45); }
html[data-theme="dark"] .act-menu button { color: #dee2e8; }
html[data-theme="dark"] .act-menu button:hover { background: #10161f; }
html[data-theme="dark"] .msg-actions button[data-tip]::after { background: #f1f5f9; color: #0f172a; }
html[data-theme="dark"] .search-text { color: #b0b6be; }
html[data-theme="dark"] .search-text b { color: #dee2e8; }
html[data-theme="dark"] .stopped { color: #ced3d9; }
html[data-theme="dark"] .hero-bot { background: #1e3e3a; border: 1px solid #284843; }
html[data-theme="dark"] .empty-state h2 { color: #e1e6ec; }
html[data-theme="dark"] .empty-state p { color: #b0b6be; }
html[data-theme="dark"] .suggestion { border: 1px solid #2a3341; background: #17202d; color: #d6dae1; }
html[data-theme="dark"] .suggestion:hover { border-color: #284843; background: #131b25; }
html[data-theme="dark"] .suggestion small { color: #ced3d9; }
html[data-theme="dark"] .composer { background: linear-gradient(to top, #17202d 75%, rgba(255,255,255,0)); }
html[data-theme="dark"] .composer-box { border: 1px solid #2a3341; background: #17202d; box-shadow: 0 6px 24px rgba(0, 0, 0, 0.19); }
html[data-theme="dark"] .composer-box:focus-within { border-color: #284843; box-shadow: 0 6px 24px rgba(0, 0, 0, 0.27); }
html[data-theme="dark"] #messageInput, html[data-theme="dark"] .hero-input { color: #dee2e8; }
html[data-theme="dark"] #messageInput::placeholder, html[data-theme="dark"] .hero-input::placeholder { color: #ced3d9; }
html[data-theme="dark"] #messageInput.voice-live, html[data-theme="dark"] .hero-input.voice-live { color: #6cefe1; }
html[data-theme="dark"] .mic-btn { color: #b0b6be; }
html[data-theme="dark"] .mic-btn:hover { background: #10161f; color: #dee2e8; }
html[data-theme="dark"] .mic-btn.listening { background: #3d1d1d; color: #ef9e9e; }
html[data-theme="dark"] .send-btn:disabled { background: #283b52; }
html[data-theme="dark"] .disclaimer { color: #ced3d9; }
html[data-theme="dark"] .rail-btn { color: #ced3d9; }
html[data-theme="dark"] .rail-logo .logo-face { background: #17202d; }
html[data-theme="dark"] .rail-btn[data-tip]:hover::after { box-shadow: 0 6px 16px rgba(0, 0, 0, 0.5); }
html[data-theme="dark"] .flyout { box-shadow: 0 18px 40px rgba(0, 0, 0, 0.68); }
html[data-theme="dark"] .fly-title { color: #ced3d8; }
html[data-theme="dark"] .fly-empty { color: #ced3d8; }
html[data-theme="dark"] dialog.search-modal { background: #17202d; color: #dee2e8; box-shadow: 0 30px 70px rgba(0, 0, 0, 0.68); }
html[data-theme="dark"] .sm-head { border-bottom: 1px solid #2a3341; }
html[data-theme="dark"] .sm-head svg { color: #ced3d9; }
html[data-theme="dark"] #searchModalInput { color: #dee2e8; }
html[data-theme="dark"] #searchModalInput::placeholder { color: #ced3d9; }
html[data-theme="dark"] .sm-close { color: #b0b6be; }
html[data-theme="dark"] .sm-close:hover { background: #10161f; color: #dee2e8; }
html[data-theme="dark"] .sm-label { color: #b0b6be; }
html[data-theme="dark"] .sm-item { color: #dee2e8; }
html[data-theme="dark"] .sm-item svg { color: #b0b6be; }
html[data-theme="dark"] .sm-item:hover, html[data-theme="dark"] .sm-item.focus { background: #10161f; }
html[data-theme="dark"] .sm-item .sm-text small { color: #ced3d9; }
html[data-theme="dark"] .sm-item .sm-when { color: #ced3d9; }
html[data-theme="dark"] .sm-new svg { color: #6cefe1; }
html[data-theme="dark"] .sm-empty { color: #ced3d9; }
html[data-theme="dark"] mark { background: #22473f; }
@media (max-width: 860px) {
  html[data-theme="dark"] .sidebar { box-shadow: 10px 0 30px rgba(0, 0, 0, 0.5); }
}
</style>
</head>
<body>

<div class="app sidebar-collapsed is-empty" id="app">
  <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

  <!-- Collapsed rail (desktop): logo opens the sidebar, like ChatGPT -->
  <nav class="rail" id="rail" aria-label="{{ __('Chat navigation') }}">
    <button class="rail-btn rail-logo" id="railOpen" data-tip="{{ __('Open sidebar') }}" aria-label="{{ __('Open sidebar') }}">
      <span class="logo-face"><x-brand-logo size="22" /></span>
      <span class="logo-hover"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="16" rx="2"></rect><path d="M9 4v16"></path></svg></span>
    </button>
    <button class="rail-btn" id="railNew" data-tip="{{ __('New Chat') }}" aria-label="{{ __('New Chat') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"></path><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"></path></svg></button>
    <button class="rail-btn" id="railSearch" data-tip="{{ __('Search chats') }}" aria-label="{{ __('Search chats') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7"></circle><path d="m20 20-3.5-3.5"></path></svg></button>
    <button class="rail-btn" id="railChats" data-tip="{{ __('Chats') }}" aria-label="{{ __('Chats') }}" aria-expanded="false"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.4 8.4 0 0 1-9 8.4 8.8 8.8 0 0 1-3.8-.9L3 21l1.9-5A8.4 8.4 0 0 1 12 3a8.5 8.5 0 0 1 9 8.5z"></path></svg></button>
    <div class="rail-spacer"></div>
    <a class="rail-btn" href="{{ route('student.home') }}" data-tip="{{ __('Back to Home') }}" aria-label="{{ __('Back to Home') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 11.5 12 4l9 7.5"></path><path d="M5 10v9a1 1 0 0 0 1 1h4v-6h4v6h4a1 1 0 0 0 1-1v-9"></path></svg></a>
    <a class="rail-btn" href="{{ route('student.profile') }}" data-tip="{{ __('Profile') }}" aria-label="{{ __('Profile') }}">
      <span class="avatar">@if($user->photo_data)<img src="{{ $user->photo_data }}" alt="">@else{{ $initials }}@endif</span>
    </a>
  </nav>
  <div class="flyout" id="chatsFlyout" hidden>
    <div class="fly-title">{{ __('Recents') }}</div>
    <div id="flyList"></div>
  </div>

  <!-- Sidebar -->
  <aside class="sidebar" id="sidebar">
    <div class="side-top">
      <a href="{{ route('student.home') }}" class="brand">
        <span class="brand-logo"><x-brand-logo size="22" /></span>
        RakanKampus
      </a>
      <button class="icon-btn" id="sidebarCloseBtn" aria-label="{{ __('Close sidebar') }}" title="{{ __('Close sidebar') }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="16" rx="2"></rect><path d="M9 4v16"></path></svg>
      </button>
    </div>

    <button class="side-btn" id="newChatBtn">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"></path><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"></path></svg>
      {{ __('New Chat') }}
    </button>
    <a class="side-btn side-link" href="{{ route('student.home') }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 11.5 12 4l9 7.5"></path><path d="M5 10v9a1 1 0 0 0 1 1h4v-6h4v6h4a1 1 0 0 0 1-1v-9"></path></svg>
      {{ __('Back to Home') }}
    </a>

    <button class="side-btn side-search" id="sideSearchBtn" type="button">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7"></circle><path d="m20 20-3.5-3.5"></path></svg>
      {{ __('Search chats') }}
      <kbd>Ctrl K</kbd>
    </button>

    <div class="recent-list" id="recentList"></div>

    <a href="{{ route('student.profile') }}" class="side-user">
      <span class="avatar">@if($user->photo_data)<img src="{{ $user->photo_data }}" alt="">@else{{ $initials }}@endif</span>
      <span style="min-width:0">
        <b>{{ $displayName }}</b>
        <span>{{ $user->student_id }}</span>
      </span>
    </a>
  </aside>

  <!-- Main -->
  <div class="main">
    <div class="topbar">
      <button class="icon-btn" id="menuBtn" aria-label="{{ __('Toggle sidebar') }}" title="{{ __('Toggle sidebar') }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="16" rx="2"></rect><path d="M9 4v16"></path></svg>
      </button>
      <div class="topbar-title" id="topbarTitle">RakanKampus AI<span>· {{ __('Politeknik Assistant') }}</span></div>
      <button class="icon-btn mobile-only" id="topNewChatBtn" aria-label="{{ __('New Chat') }}" title="{{ __('New Chat') }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"></path><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"></path></svg>
      </button>
      <a class="icon-btn mobile-only" href="{{ route('student.home') }}" aria-label="{{ __('Home') }}" title="{{ __('Home') }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 11.5 12 4l9 7.5"></path><path d="M5 10v9a1 1 0 0 0 1 1h4v-6h4v6h4a1 1 0 0 0 1-1v-9"></path></svg>
      </a>
    </div>

    <template id="mascotTpl"><div class="rk-mascot" role="status" aria-live="polite">
      <div class="m-say"><div class="m-top"><span class="m-eq"><i></i><i></i><i></i><i></i></span><span class="m-label"></span></div><span class="m-text"></span>
        <button type="button" class="m-stop" aria-label="{{ __('Stop reading') }}" title="{{ __('Stop reading') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.2" stroke-linecap="round"><path d="M6 6l12 12M18 6 6 18"/></svg></button></div>
      <svg class="m-bot" viewBox="0 0 200 222" fill="none" aria-hidden="true">
        <line x1="100" y1="14" x2="100" y2="30" stroke="#14213d" stroke-width="5" stroke-linecap="round"/><circle class="ant" cx="100" cy="12" r="6" fill="#2ec4c6"/>
        <g class="head"><rect x="56" y="30" width="88" height="64" rx="24" fill="#14213d"/><circle cx="54" cy="58" r="14" fill="#2ec4c6"/><circle cx="146" cy="58" r="14" fill="#2ec4c6"/>
          <rect x="72" y="44" width="56" height="38" rx="14" fill="#ffffff"/><g class="eyes"><circle cx="90" cy="60" r="6" fill="#14213d"/><circle cx="110" cy="60" r="6" fill="#14213d"/></g>
          <ellipse class="mouth" cx="100" cy="74" rx="9" ry="4.5" fill="#14213d"/><ellipse class="tongue" cx="100" cy="76.5" rx="5" ry="1.8" fill="#f472b6"/></g>
        <rect x="52" y="96" width="96" height="86" rx="26" fill="#ffffff" stroke="#14213d" stroke-width="4"/><circle class="chest" cx="100" cy="128" r="7" fill="#2ec4c6"/>
        <g class="arm"><path d="M58 118 Q26 108 22 76" stroke="#14213d" stroke-width="20" stroke-linecap="round" fill="none"/><circle cx="24" cy="80" r="10" fill="#2ec4c6"/><circle cx="22" cy="64" r="15" fill="#ffffff" stroke="#14213d" stroke-width="4"/>
          <line x1="22" y1="50" x2="14" y2="38" stroke="#14213d" stroke-width="4" stroke-linecap="round"/><line x1="22" y1="49" x2="22" y2="36" stroke="#14213d" stroke-width="4" stroke-linecap="round"/><line x1="22" y1="50" x2="30" y2="38" stroke="#14213d" stroke-width="4" stroke-linecap="round"/></g>
        <path d="M142 118 Q160 130 158 152" stroke="#14213d" stroke-width="18" stroke-linecap="round" fill="none"/><circle cx="158" cy="158" r="12" fill="#14213d"/>
        <rect x="68" y="176" width="20" height="32" rx="9" fill="#14213d"/><rect x="112" y="176" width="20" height="32" rx="9" fill="#14213d"/>
        <ellipse cx="78" cy="214" rx="17" ry="8" fill="#14213d"/><ellipse cx="122" cy="214" rx="17" ry="8" fill="#14213d"/>
      </svg></div></template>
    <template id="botAvatarTpl"><div class="bot-avatar"><x-brand-logo size="20" /></div></template>

    <div class="chat-area" id="chatArea"></div>

    <div class="composer">
      <div class="composer-box">
        <textarea id="messageInput" rows="1" placeholder="{{ __('Ask me anything about Politeknik...') }}"></textarea>
        <button type="button" class="round-btn mic-btn hidden" id="micBtn" aria-label="{{ __('Voice input') }}">
          <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 15a3 3 0 0 0 3-3V6a3 3 0 0 0-6 0v6a3 3 0 0 0 3 3z"></path><path d="M19 11a7 7 0 0 1-14 0"></path><line x1="12" y1="18" x2="12" y2="22"></line></svg>
        </button>
        <button class="round-btn send-btn" id="sendBtn" aria-label="{{ __('Send') }}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M12 19V5"></path><path d="m5 12 7-7 7 7"></path></svg>
        </button>
        <button class="round-btn stop-btn hidden" id="stopBtn" aria-label="{{ __('Stop') }}">
          <svg viewBox="0 0 24 24"><rect x="7" y="7" width="10" height="10" rx="2" fill="currentColor"></rect></svg>
        </button>
      </div>
      <div class="disclaimer">{{ __('RakanKampus AI can make mistakes. Check important info with PUO.') }}</div>
    </div>
  </div>
</div>

{{-- Search chats pop-up --}}
<dialog class="search-modal" id="searchModal" aria-label="{{ __('Search chats') }}">
  <div class="sm-head">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7"></circle><path d="m20 20-3.5-3.5"></path></svg>
    <input type="text" id="searchModalInput" placeholder="{{ __('Search chats...') }}" autocomplete="off">
    <button class="sm-close" type="button" id="searchModalClose" aria-label="{{ __('Close') }}">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M18 6 6 18M6 6l12 12"></path></svg>
    </button>
  </div>
  <div class="sm-body" id="searchResults"></div>
</dialog>

{{-- Empty state (new chat) --}}
<template id="emptyTpl">
  <div class="empty-state" id="emptyState">
    <div class="hero-bot"><x-brand-logo size="34" /></div>
    <h2 id="greeting"></h2>
    <p>{{ __('What can I help you with today?') }}</p>
    <div class="composer-box" style="width:100%">
      <textarea class="hero-input" id="heroInput" rows="1" placeholder="{{ __('Ask me anything about Politeknik...') }}"></textarea>
      <button type="button" class="round-btn mic-btn hidden" id="heroMicBtn" aria-label="{{ __('Voice input') }}">
        <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 15a3 3 0 0 0 3-3V6a3 3 0 0 0-6 0v6a3 3 0 0 0 3 3z"></path><path d="M19 11a7 7 0 0 1-14 0"></path><line x1="12" y1="18" x2="12" y2="22"></line></svg>
      </button>
      <button class="round-btn send-btn" id="heroSendBtn" aria-label="{{ __('Send') }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M12 19V5"></path><path d="m5 12 7-7 7 7"></path></svg>
      </button>
    </div>
    <div class="suggestions">
      @foreach($quickQuestions as $i => $q)
        @php($label = $q['popular'] ? $q['text'] : __($q['text']))
        <button type="button" class="suggestion" data-question="{{ $label }}">
          <span>{{ $suggestIcons[$i % count($suggestIcons)] }}</span>
          <span>{{ $label }}@if($q['popular'])<small>🔥 {{ __('Popular') }}</small>@endif</span>
        </button>
      @endforeach
    </div>
  </div>
</template>

{{-- Robot searching animation, shown while waiting for the bot --}}
<template id="searchingTpl">
  <div class="searching">
    <svg class="search-bot" viewBox="0 0 96 64" aria-hidden="true">
      <!-- documents being flipped through -->
      <g class="doc d1"><rect x="58" y="8" width="20" height="25" rx="3" fill="#fff" stroke="#9ad8cf" stroke-width="1.6"/><path d="M62 15h12M62 20h12M62 25h8" stroke="#9ad8cf" stroke-width="1.8" stroke-linecap="round"/></g>
      <g class="doc d2"><rect x="70" y="16" width="20" height="25" rx="3" fill="#fff" stroke="#9ad8cf" stroke-width="1.6"/><path d="M74 23h12M74 28h12M74 33h8" stroke="#9ad8cf" stroke-width="1.8" stroke-linecap="round"/></g>
      <g class="doc d3"><rect x="62" y="28" width="20" height="25" rx="3" fill="#fff" stroke="#9ad8cf" stroke-width="1.6"/><path d="M66 35h12M66 40h12M66 45h8" stroke="#9ad8cf" stroke-width="1.8" stroke-linecap="round"/></g>
      <!-- robot (same character as the RakanKampus logo) -->
      <g class="bob">
        <line x1="24" y1="4" x2="24" y2="11" stroke="#14213d" stroke-width="2.6" stroke-linecap="round"/>
        <circle class="antenna" cx="24" cy="4" r="3.2" fill="#2ec4c6"/>
        <circle cx="9" cy="23" r="5.5" fill="#2ec4c6"/>
        <circle cx="39" cy="23" r="5.5" fill="#2ec4c6"/>
        <rect x="9" y="11" width="30" height="24" rx="10" fill="#14213d"/>
        <rect x="14" y="16" width="20" height="14" rx="6" fill="#fff"/>
        <g class="eyes"><circle cx="20.5" cy="23" r="2.4" fill="#14213d"/><circle cx="27.5" cy="23" r="2.4" fill="#14213d"/></g>
        <rect x="11" y="37" width="26" height="22" rx="9" fill="#fff" stroke="#14213d" stroke-width="2.2"/>
        <circle cx="24" cy="46" r="2.6" fill="#2ec4c6"/>
        <!-- arm reaching out to hold the magnifier -->
        <path d="M35 43 Q44 42 49 36" stroke="#14213d" stroke-width="4.5" stroke-linecap="round" fill="none"/>
      </g>
      <!-- magnifying glass sweeping across the documents -->
      <g class="lens">
        <line x1="55" y1="40" x2="49" y2="47" stroke="#14213d" stroke-width="4" stroke-linecap="round"/>
        <circle cx="62" cy="31" r="10" fill="rgba(46,196,198,.22)" stroke="#14213d" stroke-width="3"/>
        <path d="M56.5 28a6 6 0 0 1 4.5-3.5" stroke="#fff" stroke-width="2" stroke-linecap="round" fill="none"/>
      </g>
    </svg>
    <div class="search-text">
      <b>{{ __('RakanKampus is searching') }}<span class="dots"></span></b>
      <span class="search-status" data-status></span>
    </div>
  </div>
</template>

<script>
const app = document.getElementById('app');
const chatArea = document.getElementById('chatArea');
const recentList = document.getElementById('recentList');
const messageInput = document.getElementById('messageInput');
const sendBtn = document.getElementById('sendBtn');
const stopBtn = document.getElementById('stopBtn');
const micBtn = document.getElementById('micBtn');
const topbarTitle = document.getElementById('topbarTitle');
const DEFAULT_TITLE = topbarTitle.innerHTML;
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
const FIRST_NAME = @json($firstName);
const isMobile = () => window.matchMedia('(max-width: 860px)').matches;

let currentConversationId = null;
let currentController = null;
let conversationsCache = [];
let busy = false;

/* ---------- Sidebar ---------- */
function setSidebarOpen(open) {
  app.classList.toggle('sidebar-collapsed', !open);
  try { if (!isMobile()) localStorage.setItem('rk.chatSidebar', open ? '1' : '0'); } catch (e) {}
}
// Desktop: remember open/closed (open by default, like ChatGPT). Mobile: always starts closed.
(function () {
  let saved = null;
  try { saved = localStorage.getItem('rk.chatSidebar'); } catch (e) {}
  setSidebarOpen(!isMobile() && saved !== '0');
})();
document.getElementById('menuBtn').addEventListener('click', () => setSidebarOpen(app.classList.contains('sidebar-collapsed')));
document.getElementById('sidebarCloseBtn').addEventListener('click', () => setSidebarOpen(false));
document.getElementById('sidebarBackdrop').addEventListener('click', () => setSidebarOpen(false));

/* ---------- Input ---------- */
function autoResize(el) {
  el.style.height = 'auto';
  const max = parseFloat(getComputedStyle(el).maxHeight) || 160;
  el.style.height = Math.min(el.scrollHeight, max) + 'px';
  el.style.overflowY = el.scrollHeight > max ? 'auto' : 'hidden';
}
messageInput.addEventListener('input', () => autoResize(messageInput));
messageInput.addEventListener('keydown', (e) => {
  if (e.key === 'Enter' && !e.shiftKey && !e.isComposing) { e.preventDefault(); sendMessage(); }
});
sendBtn.addEventListener('click', () => sendMessage());
stopBtn.addEventListener('click', () => { if (currentController) currentController.abort(); });

/* ---------- Voice input ----------
   Browsers: Web Speech API. RakanKampus Android app: the phone's own recogniser via the
   RKAppVoice bridge (WebView's webkitSpeechRecognition never hears anything).
   Works in both the new-chat box and the normal composer; words appear live while you
   talk and it keeps listening until ~3 s of silence or you tap the mic again. */
const IN_APP = /RakanKampusApp/.test(navigator.userAgent);
const APP_VOICE = (() => { try { return !!(window.RKAppVoice && RKAppVoice.available()); } catch (e) { return false; } })();
const VOICE_LANG = ({ zh: 'zh-CN', ta: 'ta-IN' })[window.APP_LOCALE] || 'ms-MY';
const SpeechRecognitionAPI = IN_APP ? null : (window.SpeechRecognition || window.webkitSpeechRecognition);
const VOICE_OK = !!(SpeechRecognitionAPI || APP_VOICE);
const VOICE_SILENCE_MS = 3000;
let recognition = null, isListening = false, voiceBaseText = '', voiceSilenceTimer = null, voiceInput = null, voiceBtn = null;

function voiceTarget() {
  return (app.classList.contains('is-empty') && document.getElementById('heroInput')) || messageInput;
}
function showVoiceText(spoken) {
  if (!voiceInput) return;
  spoken = (spoken || '').trim();
  voiceInput.value = [voiceBaseText, spoken].filter(Boolean).join(' ');
  autoResize(voiceInput);
  voiceInput.scrollTop = voiceInput.scrollHeight;
}
function resetVoiceSilenceTimer() { clearTimeout(voiceSilenceTimer); voiceSilenceTimer = setTimeout(stopVoice, VOICE_SILENCE_MS); }
function startVoice(btn) {
  voiceInput = voiceTarget();
  voiceBtn = btn;
  voiceBaseText = voiceInput.value.trim();
  isListening = true;
  btn.classList.add('listening');
  voiceInput.classList.add('voice-live');
  resetVoiceSilenceTimer();
  if (APP_VOICE) { try { RKAppVoice.start(VOICE_LANG); } catch (e) { stopVoice(); } }
  else { try { recognition.start(); } catch (e) {} }
}
function stopVoice() {
  if (!isListening) return;
  isListening = false;
  clearTimeout(voiceSilenceTimer);
  if (voiceBtn) voiceBtn.classList.remove('listening');
  if (voiceInput) voiceInput.classList.remove('voice-live');
  if (APP_VOICE) { try { RKAppVoice.stop(); } catch (e) {} }
  else { try { recognition.stop(); } catch (e) {} }
  if (voiceInput && document.body.contains(voiceInput)) voiceInput.focus();
}
function voiceBlocked() {
  stopVoice();
  RKDialog.alert({ scene: 'oops', title: t('Microphone blocked'), message: t('Please allow microphone access to use voice input.') });
}
function wireMic(btn) {
  if (!btn || !VOICE_OK) return;
  btn.classList.remove('hidden');
  btn.addEventListener('click', () => { isListening ? stopVoice() : startVoice(btn); });
}

if (SpeechRecognitionAPI) {
  recognition = new SpeechRecognitionAPI();
  recognition.lang = VOICE_LANG;
  recognition.interimResults = true;
  recognition.continuous = true;
  recognition.maxAlternatives = 1;
  recognition.onstart = () => { if (voiceInput) voiceBaseText = voiceInput.value.trim(); };
  recognition.onresult = (event) => {
    if (!isListening) return;
    resetVoiceSilenceTimer();
    let spoken = '';
    for (let i = 0; i < event.results.length; i++) spoken += event.results[i][0].transcript;
    showVoiceText(spoken);
  };
  recognition.onerror = (event) => {
    if (event.error === 'not-allowed' || event.error === 'service-not-allowed') voiceBlocked();
  };
  recognition.onend = () => {
    if (isListening) setTimeout(() => { if (isListening) { try { recognition.start(); } catch (e) {} } }, 150);
  };
} else if (APP_VOICE) {
  // Called by the app. Partial text streams in while talking; after each pause the phone
  // sends a final result, so keep what was said and listen again (like the web version).
  window.RKVoice = {
    onStart() {},
    onPartial(text) { if (!isListening) return; resetVoiceSilenceTimer(); showVoiceText(text); },
    onFinal(text) {
      if (!isListening) return;
      showVoiceText(text);
      voiceBaseText = voiceInput.value.trim();
      try { RKAppVoice.start(VOICE_LANG); } catch (e) { stopVoice(); }
    },
    onError(code) {
      if (!isListening) return;
      if (code === 'not-allowed') return voiceBlocked();
      if (code === 'no-speech') { try { RKAppVoice.start(VOICE_LANG); } catch (e) { stopVoice(); } return; } // silence timer ends it
      stopVoice();
    },
  };
}
wireMic(micBtn);

/* ---------- Messages ---------- */
function ensureCol() {
  let col = chatArea.querySelector('.col');
  if (!col) { col = document.createElement('div'); col.className = 'col'; chatArea.appendChild(col); }
  return col;
}
function scrollToBottom() { chatArea.scrollTop = chatArea.scrollHeight; }

function greetingText() {
  const h = new Date().getHours();
  const g = h < 12 ? t('Good morning') : h < 19 ? t('Good afternoon') : t('Good evening');
  return FIRST_NAME ? `${g}, ${FIRST_NAME} 👋` : `${g} 👋`;
}

function showEmptyState() {
  if (isListening) stopVoice();
  app.classList.add('is-empty');
  chatArea.innerHTML = '';
  const node = document.getElementById('emptyTpl').content.cloneNode(true);
  node.getElementById('greeting').textContent = greetingText();
  chatArea.appendChild(node);
  topbarTitle.innerHTML = DEFAULT_TITLE;

  const heroInput = document.getElementById('heroInput');
  heroInput.addEventListener('input', () => autoResize(heroInput));
  heroInput.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' && !e.shiftKey && !e.isComposing) { e.preventDefault(); sendMessage(heroInput.value); }
  });
  document.getElementById('heroSendBtn').addEventListener('click', () => sendMessage(heroInput.value));
  wireMic(document.getElementById('heroMicBtn'));
  chatArea.querySelectorAll('.suggestion').forEach(b => b.addEventListener('click', () => sendMessage(b.dataset.question)));
  if (!isMobile()) heroInput.focus();
}

function leaveEmptyState() {
  if (!app.classList.contains('is-empty')) return;
  app.classList.remove('is-empty');
  chatArea.innerHTML = '';
}

const ICONS = {
  copy: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="12" height="12" rx="2"></rect><path d="M5 15V5a2 2 0 0 1 2-2h10"></path></svg>',
  check: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 5 5 9-10"></path></svg>',
  up: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 10v11H4a1 1 0 0 1-1-1v-9a1 1 0 0 1 1-1h3Z"></path><path d="M7 10l4.2-7.2a1.9 1.9 0 0 1 3.5 1.3L14 9h5.3a2 2 0 0 1 2 2.4l-1.5 7.5a2.5 2.5 0 0 1-2.4 2.1H7"></path></svg>',
  down: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 14V3h3a1 1 0 0 1 1 1v9a1 1 0 0 1-1 1h-3Z"></path><path d="M17 14l-4.2 7.2a1.9 1.9 0 0 1-3.5-1.3L10 15H4.7a2 2 0 0 1-2-2.4l1.5-7.5A2.5 2.5 0 0 1 6.6 3H17"></path></svg>',
  share: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 15V3"></path><path d="m7 8 5-5 5 5"></path><path d="M20 14v5a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-5"></path></svg>',
  regen: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-2.6-6.4L21 8"></path><path d="M21 3v5h-5"></path></svg>',
  more: '<svg viewBox="0 0 24 24" fill="currentColor"><circle cx="5" cy="12" r="1.8"></circle><circle cx="12" cy="12" r="1.8"></circle><circle cx="19" cy="12" r="1.8"></circle></svg>',
  speak: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 5 6 9H3v6h3l5 4V5Z"></path><path d="M15.5 8.5a5 5 0 0 1 0 7"></path><path d="M18.5 5.5a9 9 0 0 1 0 13"></path></svg>',
  stop: '<svg viewBox="0 0 24 24" fill="currentColor"><rect x="6" y="6" width="12" height="12" rx="2.5"></rect></svg>',
};

// Read aloud: the browser's own voice on the web; the app uses its native voice when it has one.
// Best: natural Azure voice from our server (same clear voice on web and in the app).
// Fallbacks: the app's own voice, then the browser's voice.
const SERVER_TTS = @json(\App\Http\Controllers\SpeechController::enabled());
const APP_TTS = (() => { try { return !!(window.RKAppVoice && RKAppVoice.canSpeak && RKAppVoice.canSpeak()); } catch (e) { return false; } })();
const WEB_TTS = !IN_APP && 'speechSynthesis' in window;
const TTS_OK = SERVER_TTS || APP_TTS || WEB_TTS;
const SILENT_CLIP = 'data:audio/wav;base64,UklGRnQAAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YVAAAACAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgA==';
let speakingBtn = null, speakingAudio = null, speakingFetch = null, speakingFor = null;

function guessLang(text) {
  const malay = (text.toLowerCase().match(/\b(yang|dan|anda|untuk|boleh|tidak|ini|itu|dengan|saya|ada|kepada|akan|pelajar|sila)\b/g) || []).length;
  return malay >= 2 ? 'ms-MY' : 'en-US';
}
function stopSpeaking() {
  if (speakingFetch) { speakingFetch.abort(); speakingFetch = null; }
  if (speakingAudio) { speakingAudio.pause(); if (speakingAudio.src.startsWith('blob:')) URL.revokeObjectURL(speakingAudio.src); speakingAudio = null; }
  if (APP_TTS) { try { RKAppVoice.stopSpeaking(); } catch (e) {} }
  if (WEB_TTS) speechSynthesis.cancel();
  if (speakingBtn) { speakingBtn.innerHTML = ICONS.speak + `<span>${t('Read aloud')}</span>`; speakingBtn = null; }
  hideMascot();
}
window.RKSpeakDone = stopSpeaking;   // called by the app when its voice finishes
// Browsers ship different voices: Edge has natural Malay voices (Yasmin / Osman),
// Chrome only has "Google Bahasa Indonesia" (close to Malay, very clear), and an
// English voice reading Malay sounds silly — so pick the best match, never a wrong-language one.
let VOICES = [];
function loadVoices() { try { VOICES = speechSynthesis.getVoices() || []; } catch (e) { VOICES = []; } }
if (WEB_TTS) { loadVoices(); speechSynthesis.addEventListener?.('voiceschanged', loadVoices); }

function pickVoice(lang) {
  const want = lang === 'ms-MY' ? ['ms', 'id'] : ['en'];
  let best = null, bestScore = -1;
  VOICES.forEach(v => {
    const l = (v.lang || '').toLowerCase().replace('_', '-');
    const i = want.findIndex(w => l.startsWith(w));
    if (i < 0) return;
    let score = 100 - i * 40;                                  // Malay first, then Indonesian
    if (/natural|neural|online/i.test(v.name)) score += 30;     // Edge "Online (Natural)" voices
    if (/google/i.test(v.name)) score += 20;                     // Chrome's cloud voices
    if (lang === 'en-US' && /^en-(us|gb)/.test(l)) score += 5;
    if (score > bestScore) { best = v; bestScore = score; }
  });
  return best;
}

// Make the text pleasant to listen to and cut it into sentence-sized pieces
// (Chrome's voices stop by themselves on very long text).
function speechChunks(text) {
  const clean = text
    .replace(/https?:\/\/\S+/g, '')
    .replace(/[\u{1F300}-\u{1FAFF}\u{2600}-\u{27BF}\u{FE0F}]/gu, '')
    .replace(/^\s*(\d+)[.)]\s+/gm, '$1, ')
    .replace(/[*_#|`>~]/g, ' ')
    .replace(/&/g, ' dan ')
    .replace(/\s*\n+\s*/g, '. ')
    .replace(/\s{2,}/g, ' ')
    .replace(/\.\s*\./g, '.')
    .trim();
  const parts = clean.match(/[^.!?]+[.!?]*/g) || [clean];
  const out = [];
  parts.forEach(p => {
    p = p.trim();
    if (!p) return;
    while (p.length > 220) {                                  // split long sentences at a comma/space
      let cut = p.lastIndexOf(',', 220);
      if (cut < 80) cut = p.lastIndexOf(' ', 220);
      if (cut < 80) cut = 220;
      out.push(p.slice(0, cut + 1).trim());
      p = p.slice(cut + 1).trim();
    }
    if (p) out.push(p);
  });
  return out;
}


// ---------- Floating talking mascot while reading aloud ----------
let mascotEl = null, mascotMsg = null, mascotParts = [];
function showMascot(text, msgEl) {
  hideMascot(true);
  const main = document.querySelector('.main');
  mascotEl = document.getElementById('mascotTpl').content.firstElementChild.cloneNode(true);
  mascotEl.querySelector('.m-stop').addEventListener('click', stopSpeaking);
  main.appendChild(mascotEl);
  mascotMsg = msgEl;
  // same pieces the voice reads, minus the '1,' list numbers (shown as badges in the answer)
  mascotParts = speechChunks(text).map(x => x.replace(/^\d+,\s*/, ''));
  mascotLoading();
}
function mascotLoading() {
  if (!mascotEl) return;
  mascotEl.classList.add('loading'); mascotEl.classList.remove('talking');
  mascotEl.querySelector('.m-label').textContent = t('Loading voice…');
  mascotEl.querySelector('.m-text').textContent = mascotParts[0] || '';
}
function mascotSay(i) {
  if (!mascotEl) return;
  mascotEl.classList.remove('loading'); mascotEl.classList.add('talking');
  mascotEl.querySelector('.m-label').textContent = t('Reading aloud');
  const part = mascotParts[Math.max(0, Math.min(i, mascotParts.length - 1))] || '';
  const box = mascotEl.querySelector('.m-text');
  if (box.dataset.i !== String(i)) {
    box.dataset.i = i; box.textContent = part;
    box.style.animation = 'none'; void box.offsetWidth; box.style.animation = '';
    highlightSentence(part);
  }
}
function hideMascot(now) {
  clearHighlight();
  if (!mascotEl) return;
  const el = mascotEl; mascotEl = null; mascotMsg = null;
  if (now) { el.remove(); return; }
  el.classList.add('out');
  setTimeout(() => el.remove(), 320);
}
// Underline the sentence being read inside the answer (CSS Custom Highlight API; skipped where unsupported)
function clearHighlight() { try { CSS.highlights && CSS.highlights.delete('rk-reading'); } catch (e) {} }
function highlightSentence(part) {
  clearHighlight();
  if (!mascotMsg || !window.CSS || !CSS.highlights || typeof Highlight === 'undefined') return;
  const norm = x => x.toLowerCase().replace(/[^\p{L}\p{N}]+/gu, '');
  const target = norm(part);
  if (target.length < 3) return;
  // map every letter/number of the answer back to its text node + offset
  const map = []; const walker = document.createTreeWalker(mascotMsg, NodeFilter.SHOW_TEXT);
  let flat = '';
  for (let n = walker.nextNode(); n; n = walker.nextNode()) {
    const v = n.nodeValue;
    for (let k = 0; k < v.length; k++) {
      const c = v[k].toLowerCase();
      if (/[\p{L}\p{N}]/u.test(c)) { flat += c; map.push([n, k]); }
    }
  }
  const at = flat.indexOf(target.slice(0, Math.min(target.length, 40)));
  if (at < 0) return;
  const end = Math.min(at + target.length, map.length) - 1;
  try {
    const r = new Range();
    r.setStart(map[at][0], map[at][1]);
    r.setEnd(map[end][0], map[end][1] + 1);
    CSS.highlights.set('rk-reading', new Highlight(r));
  } catch (e) {}
}
// Which sentence is playing, estimated from how far through the audio we are
function partAtFraction(f) {
  const total = mascotParts.reduce((n, p) => n + p.length, 0) || 1;
  let acc = 0;
  for (let i = 0; i < mascotParts.length; i++) { acc += mascotParts[i].length; if (f * total < acc) return i; }
  return mascotParts.length - 1;
}

function speak(text, btn, actions) {
  const wasMine = speakingBtn === btn;
  stopSpeaking();
  if (wasMine) return;
  const lang = guessLang(text);
  speakingBtn = btn;
  btn.innerHTML = ICONS.stop + `<span>${t('Stop reading')}</span>`;
  showMascot(text, actions ? actions.parentElement.querySelector('.message') : null);
  if (SERVER_TTS) { speakFromServer(text, lang, btn, actions); return; }
  speakLocally(text, lang, btn, actions);
}

function speakFromServer(text, lang, btn, actions) {
  // Start a silent clip inside the tap so phones/the app allow the real audio to play afterwards
  const audio = new Audio(SILENT_CLIP);
  audio.play().catch(() => {});
  speakingAudio = audio;
  const ctrl = new AbortController();
  speakingFetch = ctrl;
  btn.innerHTML = ICONS.stop + `<span>${t('Loading voice…')}</span>`;
  fetch('/chatbot/speak', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'audio/mpeg' },
    body: JSON.stringify({ text, lang }),
    signal: ctrl.signal,
  })
    .then(async res => {
      if (!res.ok) {
        const info = await res.json().catch(() => ({}));
        console.warn('[RakanKampus] AI voice failed, using the browser voice instead:', info);
        throw new Error('tts ' + res.status);
      }
      return res.blob();
    })
    .then(blob => {
      if (speakingBtn !== btn) return;
      speakingFetch = null;
      btn.innerHTML = ICONS.stop + `<span>${t('Stop reading')}</span>`;
      audio.src = URL.createObjectURL(blob);
      audio.onended = () => { if (speakingBtn === btn) stopSpeaking(); };
      audio.onerror = () => { if (speakingBtn === btn) stopSpeaking(); };
      audio.onplaying = () => { if (speakingBtn === btn) mascotSay(partAtFraction(audio.duration ? audio.currentTime / audio.duration : 0)); };
      audio.ontimeupdate = () => { if (speakingBtn === btn && audio.duration) mascotSay(partAtFraction(audio.currentTime / audio.duration)); };
      return audio.play();
    })
    .catch(err => {
      if (err && err.name === 'AbortError') return;
      if (speakingBtn !== btn) return;
      speakingFetch = null; speakingAudio = null;
      if (APP_TTS || WEB_TTS) { btn.innerHTML = ICONS.stop + `<span>${t('Stop reading')}</span>`; speakLocally(text, lang, btn, actions); }
      else { stopSpeaking(); if (actions) flashNote(actions, t('Voice is not available right now.')); }
    });
}

function speakLocally(text, lang, btn, actions) {
  if (APP_TTS) { mascotSay(0); try { RKAppVoice.speak(text.replace(/https?:\/\/\S+/g, '').trim(), lang); } catch (e) { stopSpeaking(); } return; }

  if (!VOICES.length) loadVoices();
  const voice = pickVoice(lang);
  if (lang === 'ms-MY' && !voice && actions) {
    flashNote(actions, t('No Malay voice in this browser — Microsoft Edge or Chrome sounds clearer'));
  }

  const chunks = speechChunks(text);
  chunks.forEach((chunk, i) => {
    const u = new SpeechSynthesisUtterance(chunk);
    u.lang = voice ? voice.lang : lang;
    if (voice) u.voice = voice;
    u.rate = 0.95;      // a touch slower = clearer
    u.pitch = 1;
    u.onstart = () => { if (speakingBtn === btn) mascotSay(i); };
    if (i === chunks.length - 1) u.onend = () => { if (speakingBtn === btn) stopSpeaking(); };
    u.onerror = e => { if (e.error !== 'interrupted' && e.error !== 'canceled' && speakingBtn === btn) stopSpeaking(); };
    speechSynthesis.speak(u);
  });
}

function flashNote(actions, msg) {
  actions.querySelector('.act-note')?.remove();
  const n = document.createElement('span');
  n.className = 'act-note';
  n.textContent = msg;
  actions.appendChild(n);
  setTimeout(() => n.remove(), 2300);
}
function copyText(text) {
  if (navigator.clipboard && window.isSecureContext) return navigator.clipboard.writeText(text);
  return new Promise((ok, fail) => {
    const ta = document.createElement('textarea');
    ta.value = text; ta.style.position = 'fixed'; ta.style.opacity = '0';
    document.body.appendChild(ta); ta.select();
    try { document.execCommand('copy') ? ok() : fail(); } catch (e) { fail(e); } finally { ta.remove(); }
  });
}
function closeActMenus() { document.querySelectorAll('.act-menu').forEach(m => { m.parentElement.classList.remove('pinned'); m.remove(); }); }
document.addEventListener('click', e => { if (!e.target.closest('.act-menu, [data-more]')) closeActMenus(); });

function buildActions(row, text, meta) {
  const actions = document.createElement('div');
  actions.className = 'msg-actions';
  const btn = (key, icon, tip) => `<button type="button" data-${key} data-tip="${escapeHtml(t(tip))}" aria-label="${escapeHtml(t(tip))}">${icon}</button>`;
  actions.innerHTML =
    btn('copy', ICONS.copy, 'Copy') +
    btn('up', ICONS.up, 'Good response') +
    btn('down', ICONS.down, 'Bad response') +
    btn('share', ICONS.share, 'Share') +
    btn('regen', ICONS.regen, 'Regenerate') +
    (TTS_OK ? btn('more', ICONS.more, 'More') : '');

  const copyBtn = actions.querySelector('[data-copy]');
  copyBtn.addEventListener('click', () => {
    copyText(text).then(() => {
      copyBtn.innerHTML = ICONS.check; copyBtn.dataset.tip = t('Copied');
      setTimeout(() => { copyBtn.innerHTML = ICONS.copy; copyBtn.dataset.tip = t('Copy'); }, 1500);
    }).catch(() => {});
  });

  // Thumbs up / down — saved on the message so the admin side can use it later
  let rating = meta.rating || 0;
  const up = actions.querySelector('[data-up]'), down = actions.querySelector('[data-down]');
  const paint = () => {
    up.classList.toggle('on', rating === 1); down.classList.toggle('on', rating === -1);
    up.classList.toggle('gone', rating === -1); down.classList.toggle('gone', rating === 1);
  };
  paint();
  const rate = (value, b) => {
    rating = rating === value ? 0 : value;
    paint();
    if (rating) { b.classList.remove('pop'); void b.offsetWidth; b.classList.add('pop'); flashNote(actions, t('Thanks for your feedback!')); }
    const id = row.dataset.id;
    if (id) fetch(`/chatbot/message/${id}/rate`, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }, body: JSON.stringify({ rating }) }).catch(() => {});
  };
  up.addEventListener('click', () => rate(1, up));
  down.addEventListener('click', () => rate(-1, down));

  // Share the question + answer (phone share sheet when there is one, otherwise copy)
  const shareBtn = actions.querySelector('[data-share]');
  shareBtn.addEventListener('click', () => {
    let q = row.previousElementSibling;
    while (q && !q.classList.contains('user')) q = q.previousElementSibling;
    const body = (q ? `${t('Question')}: ${q.textContent.trim()}\n\n` : '') + `RakanKampus: ${text}`;
    if (navigator.share && !IN_APP) {
      navigator.share({ title: 'RakanKampus', text: body }).catch(() => {});
    } else {
      copyText(body).then(() => flashNote(actions, t('Copied — paste it anywhere to share'))).catch(() => {});
    }
  });

  actions.querySelector('[data-regen]').addEventListener('click', e => regenerate(row, e.currentTarget));

  const moreBtn = actions.querySelector('[data-more]');
  if (moreBtn) moreBtn.addEventListener('click', () => {
    const open = actions.querySelector('.act-menu');
    closeActMenus();
    if (open) return;
    const menu = document.createElement('div');
    menu.className = 'act-menu';
    const speakBtn = document.createElement('button');
    speakBtn.type = 'button';
    const mine = speakingBtn && speakingFor === text;
    speakBtn.innerHTML = (mine ? ICONS.stop : ICONS.speak) + `<span>${t(mine ? 'Stop reading' : 'Read aloud')}</span>`;
    speakBtn.addEventListener('click', () => {
      if (speakingBtn && speakingFor === text) stopSpeaking(); else { speak(text, speakBtn, actions); speakingFor = text; }
      setTimeout(closeActMenus, 150);
    });
    menu.appendChild(speakBtn);
    actions.classList.add('pinned');
    actions.appendChild(menu);
  });

  return actions;
}

function addMessage(text, sender, meta = {}) {
  leaveEmptyState();
  const col = ensureCol();
  const row = document.createElement('div');
  row.className = `message-row ${sender}`;
  if (meta.id) row.dataset.id = meta.id;

  if (sender === 'user') {
    const msg = document.createElement('div');
    msg.className = 'message';
    msg.textContent = text;
    row.appendChild(msg);
  } else {
    row.appendChild(document.getElementById('botAvatarTpl').content.firstElementChild.cloneNode(true));
    const body = document.createElement('div');
    body.className = 'bot-body';
    const msg = document.createElement('div');
    msg.className = 'message';
    msg.innerHTML = formatBotText(text);
    body.appendChild(msg);
    if (meta.actions !== false) body.appendChild(buildActions(row, text, meta));
    row.appendChild(body);
  }

  col.appendChild(row);
  scrollToBottom();
  return row;
}

// Ask the AI for a different answer to the last question
function regenerate(row, btn) {
  if (busy || !currentConversationId) return;
  stopSpeaking();
  btn.classList.add('spin');
  busy = true;
  fetch('/chatbot', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
    body: JSON.stringify({ message: '-', conversation_id: currentConversationId, regenerate: true }),
  })
    .then(res => res.json())
    .then(data => {
      if (!data.reply) throw new Error('no reply');
      const fresh = addMessage(data.reply, 'bot', { id: data.message_id });
      row.replaceWith(fresh);
      fresh.querySelector('.message').animate([{ opacity: 0, transform: 'translateY(6px)' }, { opacity: 1, transform: 'none' }], { duration: 260, easing: 'ease-out' });
    })
    .catch(() => { btn.classList.remove('spin'); flashNote(btn.parentElement, t('Could not regenerate. Try again.')); })
    .finally(() => { busy = false; });
}

// Bot "thinking" row: robot with a magnifying glass flipping through documents,
// with a status line that changes every couple of seconds.
const SEARCH_STEPS = [
  'Reading your question',
  'Searching the campus knowledge base',
  'Checking the matching information',
  'Writing the answer',
];
function addSearching() {
  leaveEmptyState();
  const col = ensureCol();
  const row = document.createElement('div');
  row.className = 'message-row bot';
  row.id = 'typingIndicator';
  row.appendChild(document.getElementById('searchingTpl').content.cloneNode(true));
  col.appendChild(row);

  const status = row.querySelector('[data-status]');
  let step = 0;
  const show = () => {
    status.textContent = t(SEARCH_STEPS[Math.min(step, SEARCH_STEPS.length - 1)]);
    status.style.animation = 'none'; void status.offsetWidth; status.style.animation = '';
    step++;
  };
  show();
  row._timer = setInterval(show, 1800);
  scrollToBottom();
  return row;
}
function removeSearching() {
  const row = document.getElementById('typingIndicator');
  if (row) { clearInterval(row._timer); row.remove(); }
}

function sendMessage(textArg) {
  const text = (typeof textArg === 'string' ? textArg : messageInput.value).trim();
  if (text === '' || busy) return;
  if (isListening) stopVoice();

  addMessage(text, 'user');
  messageInput.value = '';
  autoResize(messageInput);
  addSearching();

  busy = true;
  currentController = new AbortController();
  sendBtn.classList.add('hidden');
  stopBtn.classList.remove('hidden');

  fetch('/chatbot', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
    body: JSON.stringify({ message: text, conversation_id: currentConversationId }),
    signal: currentController.signal,
  })
    .then(res => res.json())
    .then(data => {
      removeSearching();
      if (data.reply) {
        addMessage(data.reply, 'bot', { id: data.message_id });
        currentConversationId = data.conversation_id;
        loadHistory();
      } else {
        addMessage(t('Sorry, there was a problem getting a response. Please try again.'), 'bot');
      }
    })
    .catch(error => {
      removeSearching();
      if (error.name === 'AbortError') {
        const row = addMessage('', 'bot', { actions: false });
        row.querySelector('.message').innerHTML = `<span class="stopped">${escapeHtml(t('Stopped'))}</span>`;
      } else {
        addMessage(t('Sorry, unable to connect to the server. Please try again.'), 'bot');
      }
    })
    .finally(() => {
      busy = false;
      sendBtn.classList.remove('hidden');
      stopBtn.classList.add('hidden');
      currentController = null;
      if (!isMobile()) messageInput.focus();
    });
}

/* ---------- Bot reply formatting ----------
   Turns the bot's plain text into tidy HTML: "1. ..." lines become numbered
   step badges, "- ..." lines become bullets, links become clickable, and a
   short "Label:" at the start of a step is bolded. Everything is escaped
   first, so no HTML from the reply is ever trusted. */
function linkify(safe) {
  return safe.replace(/(https?:\/\/[^\s<]+?)([.,;:!?)\]]*)(?=\s|$)/g,
    (m, url, tail) => `<a href="${url}" target="_blank" rel="noopener noreferrer">${url}</a>${tail}`);
}
function inline(line) {
  let html = linkify(escapeHtml(line));
  // "Label: rest" → bold label (only short labels, so normal sentences aren't touched)
  html = html.replace(/\*\*(.+?)\*\*/g, '<b>$1</b>').replace(/(^|\s)\*(\S[^*]*?)\*(?=\s|$)/g, '$1<i>$2</i>');
  if (!/^<b>/.test(html)) html = html.replace(/^([^:<]{2,40}?):\s+(?!\/\/)/, '<b>$1:</b> ');
  return html;
}
function formatBotText(text) {
  const lines = String(text || '').replace(/\r\n?/g, '\n').split('\n');
  const out = [];
  let para = [], list = null;   // list = { type: 'ol', items: [{ n, text, subs: [], notes: [] }] } | { type: 'ul', items: [text] }
  let n = 0, paraSinceList = true, afterItem = false;   // afterItem: previous line was a numbered item or its note

  const flushPara = () => { if (para.length) { out.push('<p>' + para.map(l => linkify(escapeHtml(l)).replace(/\*\*(.+?)\*\*/g, '<b>$1</b>')).join('<br>') + '</p>'); para = []; paraSinceList = true; } };
  const flushList = () => {
    if (!list) return;
    if (list.type === 'ol') {
      out.push('<ol class="steps">' + list.items.map(it =>
        `<li data-n="${it.n}"${it.subs.length || it.notes.length ? ' class="has-sub"' : ''}><span class="st">${inline(it.text)}</span>` +
        it.notes.map(x => `<span class="note">${inline(x)}</span>`).join('') +
        (it.subs.length ? '<ul class="sub">' + it.subs.map(x => `<li>${inline(x)}</li>`).join('') + '</ul>' : '') + '</li>').join('') + '</ol>');
    } else {
      out.push('<ul class="bullets">' + list.items.map(i => `<li><span>${inline(i)}</span></li>`).join('') + '</ul>');
    }
    list = null;
  };

  for (const raw of lines) {
    const line = raw.trim();
    const wasAfterItem = afterItem; afterItem = false;
    const indented = /^\s{2,}\S/.test(raw);
    const num = line.match(/^(\d{1,2})[.)]\s+(.+)$/);
    const bul = line.match(/^[-•*]\s+(.+)$/);
    const head = line.match(/^#{1,4}\s+(.+)$/) || line.match(/^\*\*([^*]{2,80})\*\*:?$/);
    if (num) {
      flushPara();
      if (list && list.type !== 'ol') flushList();
      if (!list) list = { type: 'ol', items: [] };
      // keep counting across bullets/blank lines; the model sometimes writes "1." for every item
      const k = parseInt(num[1], 10);
      n = (paraSinceList && k === 1) ? 1 : (k > n ? k : n + 1);
      paraSinceList = false;
      list.items.push({ n, text: num[2].trim(), subs: [], notes: [] });
      afterItem = true;
    } else if (bul) {
      flushPara();
      if (list && list.type === 'ol') list.items[list.items.length - 1].subs.push(bul[1].trim());   // bullet belongs to the numbered item above
      else { if (!list) list = { type: 'ul', items: [] }; list.items.push(bul[1].trim()); }
    } else if (line === '') {
      flushPara();                // a blank line doesn't end a list; the next line decides
    } else if (head) {
      flushPara(); flushList();
      out.push(`<h4>${inline(head[1])}</h4>`); paraSinceList = true;
    } else if (list && list.type === 'ol' && (indented || wasAfterItem)) {
      list.items[list.items.length - 1].notes.push(line); afterItem = true;                         // indented text under a numbered item
    } else {
      flushList();
      para.push(line);
    }
  }
  flushPara();
  flushList();
  return out.join('');
}

/* ---------- Conversation list ---------- */
function escapeHtml(str) { const d = document.createElement('div'); d.textContent = str || ''; return d.innerHTML; }

function groupLabel(iso) {
  if (!iso) return t('Older');
  const d = new Date(iso), now = new Date();
  const startOf = x => new Date(x.getFullYear(), x.getMonth(), x.getDate()).getTime();
  const days = Math.round((startOf(now) - startOf(d)) / 86400000);
  if (days <= 0) return t('Today');
  if (days === 1) return t('Yesterday');
  if (days <= 7) return t('Previous 7 days');
  if (days <= 30) return t('Previous 30 days');
  return t('Older');
}

function loadHistory() {
  fetch('/chatbot/history', { headers: { 'Accept': 'application/json' } })
    .then(res => res.json())
    .then(conversations => {
      conversationsCache = conversations;
      renderRecentList();
      const conv = conversationsCache.find(c => c.id === currentConversationId);
      if (conv) setTitle(conv.title);
    })
    .catch(() => {});
}

function closeMenus() {
  document.querySelectorAll('.item-menu').forEach(m => m.remove());
  document.querySelectorAll('.dots-btn[aria-expanded="true"]').forEach(b => b.setAttribute('aria-expanded', 'false'));
}
document.addEventListener('click', closeMenus);

function renderRecentList() {
  const query = '';
  const list = conversationsCache;

  recentList.innerHTML = '';
  if (list.length === 0) {
    recentList.innerHTML = `<div class="recent-empty">${escapeHtml(query ? t('No matching conversations') : t('No conversations yet'))}</div>`;
    return;
  }

  let lastGroup = null;
  list.forEach(conv => {
    const group = groupLabel(conv.updated_at);
    if (group !== lastGroup) {
      const g = document.createElement('div');
      g.className = 'grp';
      g.textContent = group;
      recentList.appendChild(g);
      lastGroup = group;
    }

    const item = document.createElement('div');
    item.className = 'recent-item' + (conv.id === currentConversationId ? ' active' : '');
    item.title = conv.preview || '';
    item.innerHTML = `
      <span class="recent-title">${escapeHtml(conv.title)}</span>
      <button class="dots-btn" type="button" aria-label="${escapeHtml(t('Options'))}" aria-expanded="false">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><circle cx="5" cy="12" r="1.8"/><circle cx="12" cy="12" r="1.8"/><circle cx="19" cy="12" r="1.8"/></svg>
      </button>`;

    item.addEventListener('click', () => openConversation(conv.id));

    const dots = item.querySelector('.dots-btn');
    dots.addEventListener('click', (e) => {
      e.stopPropagation();
      const wasOpen = dots.getAttribute('aria-expanded') === 'true';
      closeMenus();
      if (wasOpen) return;
      dots.setAttribute('aria-expanded', 'true');
      const menu = document.createElement('div');
      menu.className = 'item-menu';
      menu.innerHTML = `
        <button type="button" data-act="rename"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"></path><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"></path></svg>${escapeHtml(t('Rename'))}</button>
        <button type="button" data-act="delete" class="danger"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"></path><path d="M8 6V4h8v2"></path><path d="M19 6l-1 14H6L5 6"></path></svg>${escapeHtml(t('Delete'))}</button>`;
      menu.addEventListener('click', ev => ev.stopPropagation());
      menu.querySelector('[data-act="rename"]').addEventListener('click', () => { closeMenus(); renameConversation(conv.id, conv.title); });
      menu.querySelector('[data-act="delete"]').addEventListener('click', () => { closeMenus(); deleteConversation(conv.id, conv.title); });
      item.appendChild(menu);
    });

    recentList.appendChild(item);
  });
}

async function renameConversation(id, currentTitle) {
  const newTitle = await RKDialog.prompt({
      scene: 'write',
      title: t('Rename conversation'),
      value: currentTitle,
      placeholder: t('Conversation name'),
      confirmText: t('Save'),
  });
  if (!newTitle || newTitle.trim() === '' || newTitle === currentTitle) return;
  fetch(`/chatbot/${id}/rename`, {
    method: 'PUT',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
    body: JSON.stringify({ title: newTitle.trim() }),
  })
    .then(res => res.json())
    .then(() => { if (id === currentConversationId) setTitle(newTitle.trim()); loadHistory(); RKToast.show({ text: t('Conversation renamed') }); })
    .catch(err => console.error('Rename failed', err));
}

async function deleteConversation(id, title) {
  const ok = await RKDialog.confirm({
    scene: 'chat',
    title: t('Delete this conversation?'),
    message: t('All messages in this chat will be removed.'),
    list: title ? [{ label: title }] : [],
    warn: t('This cannot be undone.'),
    confirmText: t('Delete'),
  });
  if (!ok) return;
  fetch(`/chatbot/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': CSRF } })
    .then(res => res.json())
    .then(() => {
      if (id === currentConversationId) { currentConversationId = null; showEmptyState(); }
      loadHistory();
      RKToast.show({ text: t('Conversation deleted') });
    })
    .catch(err => console.error('Delete failed', err));
}

function setTitle(title) {
  topbarTitle.innerHTML = `${escapeHtml(title)}<span>· RakanKampus AI</span>`;
}

function openConversation(id) {
  stopSpeaking();
  fetch(`/chatbot/${id}`, { headers: { 'Accept': 'application/json' } })
    .then(res => res.json())
    .then(messages => {
      app.classList.remove('is-empty');
      chatArea.innerHTML = '';
      currentConversationId = id;
      messages.forEach(m => addMessage(m.message, m.sender, { id: m.id, rating: m.rating }));
      const conv = conversationsCache.find(c => c.id === id);
      if (conv) setTitle(conv.title);
      renderRecentList();
      if (isMobile()) setSidebarOpen(false);
    });
}

function startNewChat() {
  stopSpeaking();
  if (currentController) currentController.abort();
  currentConversationId = null;
  showEmptyState();
  renderRecentList();
  if (isMobile()) setSidebarOpen(false);
}
document.getElementById('newChatBtn').addEventListener('click', startNewChat);
document.getElementById('topNewChatBtn').addEventListener('click', startNewChat);

/* ---------- Collapsed rail, Chats flyout & Search pop-up ---------- */
const SVG_BUBBLE = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.4 8.4 0 0 1-9 8.4 8.8 8.8 0 0 1-3.8-.9L3 21l1.9-5A8.4 8.4 0 0 1 12 3a8.5 8.5 0 0 1 9 8.5z"></path></svg>`;
const SVG_PEN = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"></path><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"></path></svg>`;
const railChats = document.getElementById('railChats');
const flyout = document.getElementById('chatsFlyout');
const flyList = document.getElementById('flyList');

document.getElementById('railOpen').addEventListener('click', () => { closeFlyout(); setSidebarOpen(true); });
document.getElementById('railNew').addEventListener('click', () => { closeFlyout(); startNewChat(); });
document.getElementById('railSearch').addEventListener('click', () => { closeFlyout(); openSearch(); });
document.getElementById('sideSearchBtn').addEventListener('click', () => openSearch());

function renderFlyout() {
  flyList.innerHTML = '';
  if (!conversationsCache.length) {
    flyList.innerHTML = `<div class="fly-empty">${escapeHtml(t('No conversations yet'))}</div>`;
    return;
  }
  conversationsCache.forEach(conv => {
    const b = document.createElement('button');
    b.type = 'button';
    b.className = 'fly-item' + (conv.id === currentConversationId ? ' active' : '');
    b.textContent = conv.title;
    b.title = conv.title;
    b.addEventListener('click', () => { closeFlyout(); openConversation(conv.id); });
    flyList.appendChild(b);
  });
}
function openFlyout() {
  renderFlyout();
  const r = railChats.getBoundingClientRect();
  flyout.hidden = false;
  const top = Math.min(r.top - 6, window.innerHeight - flyout.offsetHeight - 16);
  flyout.style.top = Math.max(12, top) + 'px';
  railChats.classList.add('open');
  railChats.setAttribute('aria-expanded', 'true');
}
function closeFlyout() {
  flyout.hidden = true;
  railChats.classList.remove('open');
  railChats.setAttribute('aria-expanded', 'false');
}
railChats.addEventListener('click', (e) => {
  e.stopPropagation();
  flyout.hidden ? openFlyout() : closeFlyout();
});
flyout.addEventListener('click', e => e.stopPropagation());
document.addEventListener('click', closeFlyout);

// Search pop-up
const searchModal = document.getElementById('searchModal');
const searchInput = document.getElementById('searchModalInput');
const searchResults = document.getElementById('searchResults');
let searchFocus = -1;

function highlight(text, q) {
  const safe = escapeHtml(text);
  if (!q) return safe;
  const i = text.toLowerCase().indexOf(q);
  if (i < 0) return safe;
  return escapeHtml(text.slice(0, i)) + '<mark>' + escapeHtml(text.slice(i, i + q.length)) + '</mark>' + escapeHtml(text.slice(i + q.length));
}
function renderSearch() {
  const q = searchInput.value.toLowerCase().trim();
  const list = conversationsCache.filter(c =>
    !q || (c.title || '').toLowerCase().includes(q) || (c.preview || '').toLowerCase().includes(q));
  searchFocus = -1;
  let html = '';
  if (!q) {
    html += `<button type="button" class="sm-item sm-new" data-new>${SVG_PEN}<span class="sm-text"><b>${escapeHtml(t('New Chat'))}</b></span></button>`;
  }
  if (list.length) {
    html += `<div class="sm-label">${escapeHtml(q ? t('Results') : t('Recent chats'))}</div>`;
    list.forEach(c => {
      html += `<button type="button" class="sm-item" data-id="${c.id}">${SVG_BUBBLE}<span class="sm-text"><b>${highlight(c.title || '', q)}</b>${q && c.preview ? `<small>${highlight(c.preview, q)}</small>` : ''}</span><span class="sm-when">${escapeHtml(groupLabel(c.updated_at))}</span></button>`;
    });
  } else {
    html += `<div class="sm-empty">${escapeHtml(q ? t('No matching conversations') : t('No conversations yet'))}</div>`;
  }
  searchResults.innerHTML = html;
  searchResults.querySelectorAll('.sm-item').forEach(el => el.addEventListener('click', () => pickSearch(el)));
}
function pickSearch(el) {
  searchModal.close();
  if (el.hasAttribute('data-new')) startNewChat();
  else openConversation(parseInt(el.dataset.id, 10));
}
function openSearch() {
  searchInput.value = '';
  renderSearch();
  searchModal.showModal();
  searchInput.focus();
  if (isMobile()) setSidebarOpen(false);
}
searchInput.addEventListener('input', renderSearch);
searchInput.addEventListener('keydown', (e) => {
  const items = [...searchResults.querySelectorAll('.sm-item')];
  if (!items.length) return;
  if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
    e.preventDefault();
    searchFocus = (searchFocus + (e.key === 'ArrowDown' ? 1 : -1) + items.length) % items.length;
    items.forEach((el, i) => el.classList.toggle('focus', i === searchFocus));
    items[searchFocus].scrollIntoView({ block: 'nearest' });
  } else if (e.key === 'Enter') {
    e.preventDefault();
    pickSearch(items[searchFocus >= 0 ? searchFocus : (items.length > 1 && items[0].hasAttribute('data-new') && searchInput.value ? 1 : 0)]);
  }
});
document.getElementById('searchModalClose').addEventListener('click', () => searchModal.close());
searchModal.addEventListener('click', (e) => { if (e.target === searchModal) searchModal.close(); });
document.addEventListener('keydown', (e) => {
  if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') { e.preventDefault(); openSearch(); }
  if (e.key === 'Escape') closeFlyout();
});

/* ---------- Start ---------- */
const urlParams = new URLSearchParams(window.location.search);
const prefilledQuestion = urlParams.get('q');
const conversationFromUrl = urlParams.get('conversation');

loadHistory();
if (prefilledQuestion) {
  window.history.replaceState({}, document.title, window.location.pathname);
  sendMessage(prefilledQuestion);
} else if (conversationFromUrl) {
  openConversation(parseInt(conversationFromUrl, 10));
} else {
  showEmptyState();
}
</script>

</body>
</html>
