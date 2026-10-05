<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
@include('partials.font')
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
@include('partials.pwa-head')
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ __('RakanKampus - Home') }}</title>
<style>
  :root {
    --blue-primary: #0d9488;
    --blue-dark: #14213d;
    --bg-page: #f0fafa;
    --bg-card: #ffffff;
    --icon-bg: #e6fbfa;
    --border-light: #dbeeee;
    --text-muted: #64748b;
    --chip-text: #0d9488;
  }

  * { box-sizing: border-box; }

  html, body {
    margin: 0;
    min-height: 100%;
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    overflow-x: hidden;
  }

  body {
    background: linear-gradient(120deg, #14213d, #1b3a5c, #2ec4c6, #14213d);
    background-size: 300% 300%;
    animation: gradientShift 15s ease infinite;
  }

  /* Playful floating background blobs */
  .bg-blob {
    position: fixed;
    border-radius: 50%;
    filter: blur(50px);
    opacity: 0.55;
    z-index: 0;
    pointer-events: none;
    animation: blobFloat 16s ease-in-out infinite;
  }

  .bg-blob.b1 { width: 300px; height: 300px; top: -80px; left: -90px; background: rgba(255,255,255,0.28); animation-duration: 17s; }
  .bg-blob.b2 { width: 260px; height: 260px; top: 32%; right: -100px; background: rgba(255,255,255,0.20); animation-duration: 20s; animation-delay: -4s; }
  .bg-blob.b3 { width: 240px; height: 240px; bottom: -70px; left: 12%; background: rgba(255,255,255,0.24); animation-duration: 15s; animation-delay: -8s; }
  .bg-blob.b4 { width: 200px; height: 200px; top: 60%; left: -60px; background: rgba(255,255,255,0.18); animation-duration: 22s; animation-delay: -11s; }

  @keyframes blobFloat {
    0%, 100% { transform: translate(0, 0) scale(1); }
    50% { transform: translate(20px, -30px) scale(1.08); }
  }

  @keyframes fadeInUp {
    from { opacity: 0; transform: translateY(16px); }
    to { opacity: 1; transform: translateY(0); }
  }

  @keyframes iconWiggle {
    0%, 100% { transform: rotate(0deg); }
    25% { transform: rotate(-10deg); }
    75% { transform: rotate(10deg); }
  }

  @keyframes softPulse {
    0%, 100% { box-shadow: 0 10px 24px rgba(20, 33, 61, 0.30); }
    50% { box-shadow: 0 16px 36px rgba(20, 33, 61, 0.50); }
  }

  /* Top bar */
  .topbar {
    background: rgba(255, 255, 255, 0.92);
    backdrop-filter: blur(10px);
    border-bottom: 1px solid #dbeeee;
    padding: 16px 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: sticky;
    top: 0;
    z-index: 20;
}

  .topbar-left {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .logo-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: var(--blue-primary);
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .logo-icon svg {
    width: 20px;
    height: 20px;
    fill: #fff;
  }

  .brand-name {
    font-size: 20px;
    font-weight: 800;
    color: var(--blue-dark);
  }

  /* Main content */
  .container {
    max-width: 600px;
    margin: 0 auto;
    padding: 32px 16px 64px;
    position: relative;
    z-index: 1;
  }

@media (min-width: 861px) {
    body {
        background: #f0fafa;
    }

    .container {
        max-width: 1080px;
        padding: 36px 44px 90px;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .greeting-card { grid-column: 1 / -1; order: 1; margin-bottom: 0; }
    .today-classes-card { grid-column: 1 / 2; order: 2; margin-bottom: 0; }
    .reminders-banner { grid-column: 2 / 3; order: 3; margin-bottom: 0; }
    .quick-grid { grid-column: 1 / -1; order: 4; margin-bottom: 0; }
    .start-chat-btn { grid-column: 1 / -1; order: 5; margin-bottom: 0; }
    .section-label { grid-column: 1 / -1; order: 6; margin-bottom: 0; }
    .conversation-list { grid-column: 1 / -1; order: 7; }
}

  /* Greeting card */
  .greeting-card {
    position: relative;
    overflow: hidden;
    background: linear-gradient(120deg, #14213d 0%, #1b3a5c 55%, #2ec4c6 100%);
    border-radius: 28px;
    padding: 28px 32px;
    margin-bottom: 28px;
    color: #fff;
    box-shadow: 0 16px 40px rgba(20, 33, 61, 0.28);
    animation: fadeInUp 0.55s ease both;
}

  .greeting-card::before,
  .greeting-card::after {
    content: "";
    position: absolute;
    border-radius: 50%;
    background: rgba(255,255,255,0.08);
  }

  .greeting-card::before {
    width: 160px;
    height: 160px;
    top: -60px;
    right: -30px;
  }

  .greeting-card::after {
    width: 110px;
    height: 110px;
    bottom: -50px;
    right: 60px;
  }

  .greeting-top {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 18px;
    position: relative;
    z-index: 1;
  }

  .greeting-avatar {
    width: 44px;
    height: 44px;
    min-width: 44px;
    border-radius: 50%;
    background: rgba(255,255,255,0.15);
    border: 1.5px solid rgba(255,255,255,0.4);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    font-weight: 700;
    overflow: hidden;
  }

  .greeting-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .greeting-name {
    font-size: 16px;
    font-weight: 800;
    margin: 0;
  }

  .greeting-programme {
    font-size: 13px;
    font-weight: 600;
    color: #e6fbfb;
    margin: 2px 0 1px;
    line-height: 1.35;
  }

  .greeting-meta {
    font-size: 12.5px;
    color: #bfe9ea;
    margin: 0;
  }

  .greeting-hello {
    font-size: 14.5px;
    color: #bfe9ea;
    margin: 0 0 4px;
    position: relative;
    z-index: 1;
  }

  .greeting-question {
    font-size: 19px;
    font-weight: 800;
    margin: 0;
    position: relative;
    z-index: 1;
  }

  .reminders-banner {
    display: flex;
    align-items: center;
    gap: 14px;
    background: linear-gradient(120deg, #14213d, #1b3a5c 55%, #2ec4c6);
    border-radius: 18px;
    padding: 18px 22px;
    margin-bottom: 24px;
    box-shadow: 0 10px 24px rgba(20, 33, 61, 0.25);
    text-decoration: none;
    transition: transform 0.15s ease, box-shadow 0.15s ease;
    animation: fadeInUp 0.5s ease 0.05s both;
  }

  .reminders-banner:hover {
    transform: translateY(-2px);
    box-shadow: 0 14px 32px rgba(20, 33, 61, 0.35);
  }

  .reminders-banner-icon {
    width: 42px;
    height: 42px;
    min-width: 42px;
    border-radius: 50%;
    background: rgba(255,255,255,0.18);
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .reminders-banner-icon svg { width: 20px; height: 20px; stroke: #fff; }

  .reminders-banner-body { flex: 1; min-width: 0; }

  .reminders-banner-title { font-size: 15px; font-weight: 700; color: #fff; margin: 0; }

  .reminders-banner-sub { font-size: 12.5px; color: #bfe9ea; margin: 4px 0 0; }

  .reminders-banner > svg { width: 16px; height: 16px; stroke: #fff; flex-shrink: 0; }

  .today-classes-card {
    background: var(--bg-card);
    border: 1px solid var(--border-light);
    border-radius: 18px;
    padding: 16px 18px;
    margin-bottom: 24px;
    box-shadow: 0 1px 2px rgba(20, 40, 100, 0.04);
    animation: fadeInUp 0.5s ease 0.12s both;
  }

  .today-classes-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 4px; }

  .today-classes-title-row { display: flex; align-items: center; gap: 9px; }

  .today-classes-title-row svg { width: 17px; height: 17px; stroke: var(--blue-primary); }

  .today-classes-title { font-size: 14px; font-weight: 800; color: #14213d; margin: 0; }

  .today-classes-link { font-size: 11.5px; font-weight: 700; color: var(--blue-primary); text-decoration: none; white-space: nowrap; }

  .today-class-row { display: flex; align-items: center; gap: 12px; padding: 10px 0; border-top: 1px solid var(--border-light); }

  .today-class-time { flex-shrink: 0; width: 58px; font-size: 11px; font-weight: 800; color: #14213d; line-height: 1.3; }

  .today-class-body { flex: 1; min-width: 0; }

  .today-class-subject { font-size: 13px; font-weight: 700; color: #14213d; margin: 0 0 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

  .today-class-meta { font-size: 11px; color: var(--text-muted); margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

  .today-class-row.is-past { opacity: 0.45; }

  .today-class-row.is-ongoing .today-class-time { color: #16a34a; }

  .today-class-row.is-ongoing .today-class-subject { color: #16a34a; }

  .today-classes-empty { font-size: 12.5px; color: var(--text-muted); padding: 10px 0 2px; }

  .today-classes-body { touch-action: pan-y; transition: transform 0.2s ease; }
  .today-classes-body.dragging { transition: none; }

  .quick-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
    margin-bottom: 24px;
  }

  .quick-chip {
    background: var(--bg-card);
    border: 1px solid var(--border-light);
    border-radius: 12px;
    padding: 16px;
    color: var(--chip-text);
    font-size: 14px;
    font-weight: 600;
    text-align: left;
    cursor: pointer;
    box-shadow: 0 1px 2px rgba(20, 40, 100, 0.04);
    transition: box-shadow 0.15s ease, transform 0.15s ease;
    text-decoration: none;
    display: block;
    animation: fadeInUp 0.5s ease both;
}

  .quick-grid a:nth-of-type(1) { animation-delay: 0.10s; }
  .quick-grid a:nth-of-type(2) { animation-delay: 0.17s; }
  .quick-grid a:nth-of-type(3) { animation-delay: 0.24s; }
  .quick-grid a:nth-of-type(4) { animation-delay: 0.31s; }

  .quick-chip-tag { display: block; font-size: 10px; font-weight: 800; color: #ea580c; margin-bottom: 3px; letter-spacing: 0.02em; }
  .quick-chip:hover {
    box-shadow: 0 6px 16px rgba(20, 33, 61, 0.16);
    transform: translateY(-2px) scale(1.02);
  }

  .start-chat-btn {
    width: 100%;
    background: linear-gradient(135deg, #14213d, #2ec4c6);
    color: #fff;
    border: none;
    border-radius: 18px;
    padding: 18px;
    font-size: 16px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    cursor: pointer;
    margin-bottom: 32px;
    box-shadow: 0 10px 24px rgba(20, 33, 61, 0.30);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    text-decoration: none;
    animation: fadeInUp 0.5s ease 0.35s both, softPulse 2.8s ease-in-out 1.2s infinite;
}

.start-chat-btn:hover {
    transform: translateY(-2px) scale(1.02);
    box-shadow: 0 16px 32px rgba(20, 33, 61, 0.42);
    animation: fadeInUp 0.5s ease 0.35s both;
}

  .start-chat-btn svg {
    width: 18px;
    height: 18px;
  }

  /* Heading above the quick-question chips — same look as "RECENT CONVERSATIONS". */
  .faq-label {
    grid-column: 1 / -1;
    justify-self: start;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.08em;
    color: var(--blue-primary);
    margin: 0 0 4px 4px;
    animation: fadeInUp 0.5s ease 0.05s both;
    background: rgba(255,255,255,0.85);
    backdrop-filter: blur(6px);
    padding: 6px 14px;
    border-radius: 999px;
  }

  .section-label {
    display: inline-block;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.08em;
    color: var(--blue-primary);
    margin: 0 0 16px 4px;
    animation: fadeInUp 0.5s ease 0.4s both;
    background: rgba(255,255,255,0.85);
    backdrop-filter: blur(6px);
    padding: 6px 14px;
    border-radius: 999px;
  }

  .conversation-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
  }

  .conv-swipe-wrap {
    position: relative;
    border-radius: 14px;
    overflow: hidden;
  }

  .conv-swipe-delete {
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 90px;
    background: #dc2626;
    border: none;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 5px;
    cursor: pointer;
    color: #fff;
    /* only shown while a card is being swiped / is swiped open — never peeks out
       from behind a card that is still fading in */
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.15s ease;
  }
  .conv-swipe-wrap.swiping .conv-swipe-delete,
  .conv-swipe-wrap.open .conv-swipe-delete { opacity: 1; pointer-events: auto; }
  .conv-swipe-wrap.swiping .conv-card { transition: none; }

  .conv-swipe-delete svg { width: 17px; height: 17px; }
  .conv-swipe-delete span { font-size: 11px; font-weight: 700; }

  .conv-card {
    position: relative;
    background: var(--bg-card);
    border: 1px solid var(--border-light);
    border-radius: 14px;
    padding: 16px 18px;
    display: flex;
    align-items: center;
    gap: 14px;
    cursor: pointer;
    box-shadow: 0 1px 2px rgba(20, 40, 100, 0.04);
    transition: box-shadow 0.15s ease, border-color 0.15s ease, transform 0.2s ease;
    /* "backwards", not "both": once it has faded in, the swipe can move the card */
    animation: fadeInUp 0.5s ease backwards;
    touch-action: pan-y;
  }

  .conversation-list .conv-swipe-wrap:nth-child(1) .conv-card { animation-delay: 0.45s; }
  .conversation-list .conv-swipe-wrap:nth-child(2) .conv-card { animation-delay: 0.52s; }
  .conversation-list .conv-swipe-wrap:nth-child(3) .conv-card { animation-delay: 0.59s; }
  .conversation-list .conv-swipe-wrap:nth-child(4) .conv-card { animation-delay: 0.66s; }

  .conv-card:hover {
    box-shadow: 0 10px 24px rgba(20, 33, 61, 0.14);
    transform: translateY(-2px);
    border-color: #b8e6e6;
}

  .conv-card:hover .conv-icon {
    animation: iconWiggle 0.4s ease;
  }

  .conv-icon {
    width: 40px;
    height: 40px;
    min-width: 40px;
    border-radius: 50%;
    background: var(--icon-bg);
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .conv-icon svg {
    width: 18px;
    height: 18px;
    stroke: var(--blue-primary);
  }

  .conv-body {
    flex: 1;
    min-width: 0;
  }

  .conv-title {
    font-size: 15px;
    font-weight: 700;
    color: var(--blue-dark);
    margin: 0 0 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .conv-preview {
    font-size: 13px;
    color: var(--blue-primary);
    opacity: 0.75;
    margin: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .conv-meta {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-shrink: 0;
  }

  .conv-time {
    font-size: 12px;
    color: var(--text-muted);
  }

  .conv-meta svg {
    width: 16px;
    height: 16px;
    stroke: #94a3b8;
  }

  .conv-actions {
    display: flex;
    align-items: center;
    gap: 4px;
    flex-shrink: 0;
  }

  .conv-action-btn {
    background: none;
    border: none;
    color: #94a3b8;
    cursor: pointer;
    padding: 4px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .conv-action-btn:hover {
    color: #0d9488;
    background: #eafbfa;
  }

  .conv-action-btn svg { width: 15px; height: 15px; }

  @media (max-width: 480px) {
    .quick-grid {
      grid-template-columns: 1fr;
    }
    .topbar {
      padding: 14px 20px;
    }
  }

  @keyframes gradientShift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
  }
</style>
<style id="rk-dark-theme">
/* Dark theme — generated by scripts/dark-theme/generate.py, do not edit by hand.
   Hand-made fixes go in resources/views/partials/dark-fixes.blade.php */
html[data-theme="dark"] body { background-image: linear-gradient(120deg, #0c1320, #112031, #1d6869, #0c1320); }
html[data-theme="dark"] .topbar { background: rgba(23, 32, 45, 0.92); border-bottom: 1px solid #284848; }
html[data-theme="dark"] .brand-name { color: #dee1e9; }
@media (min-width: 861px) {
  html[data-theme="dark"] body { background: #10161f; }
}
html[data-theme="dark"] .greeting-card { box-shadow: 0 16px 40px rgba(0, 0, 0, 0.55); }
html[data-theme="dark"] .greeting-avatar { border: 1.5px solid rgba(42, 51, 65, 0.4); }
html[data-theme="dark"] .reminders-banner { box-shadow: 0 10px 24px rgba(0, 0, 0, 0.5); }
html[data-theme="dark"] .reminders-banner:hover { box-shadow: 0 14px 32px rgba(0, 0, 0, 0.68); }
html[data-theme="dark"] .today-classes-card { background: #17202d; border: 1px solid #284848; box-shadow: 0 1px 2px rgba(0, 0, 0, 0.12); }
html[data-theme="dark"] .today-classes-title-row svg { stroke: #41eedf; }
html[data-theme="dark"] .today-classes-title { color: #dee1e9; }
html[data-theme="dark"] .today-classes-link { color: #41eedf; }
html[data-theme="dark"] .today-class-row { border-top: 1px solid #284848; }
html[data-theme="dark"] .today-class-time { color: #dee1e9; }
html[data-theme="dark"] .today-class-subject { color: #dee1e9; }
html[data-theme="dark"] .today-class-meta { color: #b0b6be; }
html[data-theme="dark"] .today-class-row.is-ongoing .today-class-time { color: #5ee992; }
html[data-theme="dark"] .today-class-row.is-ongoing .today-class-subject { color: #5ee992; }
html[data-theme="dark"] .today-classes-empty { color: #b0b6be; }
html[data-theme="dark"] .quick-chip { background: #17202d; border: 1px solid #284848; color: #41eedf; box-shadow: 0 1px 2px rgba(0, 0, 0, 0.12); }
html[data-theme="dark"] .quick-chip-tag { color: #f9b18c; }
html[data-theme="dark"] .quick-chip:hover { box-shadow: 0 6px 16px rgba(0, 0, 0, 0.34); }
html[data-theme="dark"] .start-chat-btn { box-shadow: 0 10px 24px rgba(0, 0, 0, 0.59); }
html[data-theme="dark"] .start-chat-btn:hover { box-shadow: 0 16px 32px rgba(0, 0, 0, 0.81); }
html[data-theme="dark"] .faq-label { color: #41eedf; background: rgba(23, 32, 45, 0.85); }
html[data-theme="dark"] .section-label { color: #41eedf; background: rgba(23, 32, 45, 0.85); }
html[data-theme="dark"] .conv-card { background: #17202d; border: 1px solid #284848; box-shadow: 0 1px 2px rgba(0, 0, 0, 0.12); }
html[data-theme="dark"] .conv-card:hover { box-shadow: 0 10px 24px rgba(0, 0, 0, 0.3); border-color: #284848; }
html[data-theme="dark"] .conv-icon { background: #1d3d3b; }
html[data-theme="dark"] .conv-icon svg { stroke: #41eedf; }
html[data-theme="dark"] .conv-title { color: #dee1e9; }
html[data-theme="dark"] .conv-preview { color: #41eedf; }
html[data-theme="dark"] .conv-time { color: #b0b6be; }
html[data-theme="dark"] .conv-meta svg { stroke: #ced3d9; }
html[data-theme="dark"] .conv-action-btn { color: #ced3d9; }
html[data-theme="dark"] .conv-action-btn:hover { color: #41eedf; background: #1c3b39; }
</style>
<style id="sky-greeting">
/* ---------- Greeting card: sky that follows the time of day ---------- */
.greeting-card.sky-card { padding: 26px 30px 70px; transition: background .8s ease, color .4s; min-height: 200px; }
.greeting-card.sky-card::before, .greeting-card.sky-card::after { display: none; }
.sky { position: absolute; inset: 0; pointer-events: none; overflow: hidden; border-radius: inherit; }
.sky-orb { position: absolute; right: 230px; top: 26px; width: 66px; height: 66px; border-radius: 50%; transition: all .8s ease; animation: skyBob 6s ease-in-out infinite; }
@keyframes skyBob { 50% { transform: translateY(-6px); } }
.sky-cloud { position: absolute; height: 24px; border-radius: 20px; background: #fff; opacity: .9; animation: skyDrift 32s linear infinite; }
.sky-cloud::before { content: ""; position: absolute; left: 20%; top: -13px; width: 44%; height: 28px; border-radius: 50%; background: inherit; }
.sky-cloud.k1 { top: 30px; width: 90px; animation-delay: -6s; }
.sky-cloud.k2 { top: 78px; width: 64px; animation-duration: 42s; animation-delay: -24s; }
.sky-cloud.k3 { top: 46px; width: 74px; animation-duration: 38s; animation-delay: -15s; }
@keyframes skyDrift { from { left: 38%; opacity: 0; } 12% { opacity: var(--co, .9); } 90% { opacity: var(--co, .9); } to { left: 100%; opacity: 0; } }
.sky-stars { position: absolute; inset: 0 0 40% 0; opacity: 0; transition: opacity .8s;
  background-image: radial-gradient(1.5px 1.5px at 8% 30%, #fff 50%, transparent 51%), radial-gradient(1px 1px at 18% 70%, #fff 50%, transparent 51%), radial-gradient(1.5px 1.5px at 30% 20%, #fff 50%, transparent 51%),
    radial-gradient(1px 1px at 42% 55%, #fff 50%, transparent 51%), radial-gradient(1.5px 1.5px at 55% 25%, #fff 50%, transparent 51%), radial-gradient(1px 1px at 63% 65%, #fff 50%, transparent 51%),
    radial-gradient(1.5px 1.5px at 72% 15%, #fff 50%, transparent 51%), radial-gradient(1px 1px at 84% 45%, #fff 50%, transparent 51%), radial-gradient(1.5px 1.5px at 93% 22%, #fff 50%, transparent 51%);
  animation: skyTwinkle 3s ease-in-out infinite alternate; }
@keyframes skyTwinkle { from { filter: brightness(.6); } to { filter: brightness(1.3); } }
.sky-city { position: absolute; left: 0; right: 0; bottom: 0; width: 100%; height: 52px; fill: #0f2747; opacity: .88; transition: fill .8s; }
.sky-win rect { fill: transparent; transition: fill .8s; }
.sky-bot { position: absolute; right: 44px; bottom: 6px; width: 116px; height: auto; z-index: 2; filter: drop-shadow(0 12px 16px rgba(15,39,71,.3)); animation: skyBob 3.4s ease-in-out infinite; }
.sky-card .greeting-top, .sky-card .greeting-hello, .sky-card .greeting-question { position: relative; z-index: 2; }
.sky-badge { display: inline-flex; align-items: center; gap: 6px; padding: 5px 12px; border-radius: 999px; font-size: 12.5px; font-weight: 800; }
.sky-dot { opacity: .6; }
.sky-card .greeting-question { font-size: 24px; letter-spacing: -.4px; }

/* morning */
.sky-morning { background: linear-gradient(180deg, #7dd3fc 0%, #bae6fd 55%, #fef9c3 100%) !important; }
.sky-morning .sky-orb { right: 320px; top: 46px; background: radial-gradient(#fffbea, #fde047); box-shadow: 0 0 50px 16px rgba(253,224,71,.55); }
/* afternoon */
.sky-afternoon { background: linear-gradient(180deg, #38bdf8 0%, #7dd3fc 45%, #fde68a 100%) !important; }
.sky-afternoon .sky-orb { background: radial-gradient(#fff7cc, #fbbf24); box-shadow: 0 0 60px 20px rgba(253,224,71,.55); }
/* evening */
.sky-evening { background: linear-gradient(180deg, #6d28d9 0%, #db2777 55%, #fb923c 100%) !important; }
.sky-evening .sky-orb { right: 300px; top: 70px; background: radial-gradient(#fed7aa, #f97316); box-shadow: 0 0 60px 18px rgba(249,115,22,.55); }
.sky-evening .sky-cloud { background: #fbcfe8; --co: .55; }
.sky-evening .sky-city { fill: #2e1065; }
/* night */
.sky-night { background: linear-gradient(180deg, #0b1026 0%, #1e1b4b 60%, #312e81 100%) !important; }
.sky-night .sky-stars { opacity: 1; }
.sky-night .sky-orb { width: 54px; height: 54px; background: #f8fafc; box-shadow: inset -14px -6px 0 0 #cbd5e1, 0 0 36px 8px rgba(226,232,240,.35); }
.sky-night .sky-cloud { background: #475569; --co: .35; }
.sky-night .sky-city { fill: #050816; opacity: 1; }
.sky-night .sky-win rect, .sky-evening .sky-win rect { fill: #fde68a; }

/* text colours: dark on the day sky, white on evening / night */
.sky-morning, .sky-afternoon { color: #0f2747 !important; }
.sky-morning .greeting-programme, .sky-afternoon .greeting-programme { color: #1e3a5f; }
.sky-morning .greeting-meta, .sky-afternoon .greeting-meta { color: #334155; }
.sky-morning .greeting-avatar, .sky-afternoon .greeting-avatar { background: #0f2747; color: #fff; border-color: rgba(255,255,255,.7); }
.sky-morning .sky-badge, .sky-afternoon .sky-badge { background: rgba(255,255,255,.78); color: #0f2747; }
.sky-evening .sky-badge, .sky-night .sky-badge { background: rgba(255,255,255,.16); color: #fff; border: 1px solid rgba(255,255,255,.25); }
.sky-evening .greeting-programme, .sky-night .greeting-programme { color: #f5f3ff; }
.sky-evening .greeting-meta, .sky-night .greeting-meta { color: #ddd6fe; }
.sky-card .greeting-hello { color: inherit; margin-bottom: 8px; }

@media (max-width: 600px) {
  .greeting-card.sky-card { padding: 20px 20px 62px; }
  .sky-bot { width: 74px; right: 14px; top: 16px; bottom: auto; }
  .sky-orb, .sky-morning .sky-orb, .sky-evening .sky-orb { right: 26px; top: 104px; width: 38px; height: 38px; }
  .sky-night .sky-orb { width: 34px; height: 34px; box-shadow: inset -9px -4px 0 0 #cbd5e1, 0 0 24px 6px rgba(226,232,240,.35); }
  .sky-cloud.k3 { display: none; }
  @keyframes skyDrift { from { left: 58%; opacity: 0; } 15% { opacity: var(--co, .9); } 85% { opacity: var(--co, .9); } to { left: 100%; opacity: 0; } }
  .sky-card .greeting-top { padding-right: 70px; }
  .sky-card .greeting-question { font-size: 19px; }
  .sky-city { height: 40px; }
}
@media (prefers-reduced-motion: reduce) { .sky-cloud, .sky-orb, .sky-bot, .sky-stars { animation: none; } }
html[data-theme="dark"] .greeting-card.sky-card { box-shadow: 0 16px 40px rgba(0,0,0,.55); }
</style>
<style id="home-no-topbar">
/* PC: the sidebar already shows the logo + name, so the top bar is hidden there (kept on phones) */
@media (min-width: 861px) { .topbar { display: none; } .container { padding-top: 24px !important; } }
</style>
<style id="sky-campus">
/* Campus skyline on the greeting card (colours follow the sky phase) */
.sky-campus { position: absolute; left: 0; bottom: 0; width: 100%; height: 112px; z-index: 1; overflow: visible; }
.sky-city .c-far { fill: currentColor; }
.sky-city { color: #0f2747; opacity: .28 !important; }
.sky-campus * { transition: fill .8s ease; }
.sky-card { --wall: #f8fafc; --trim: #cbd5e1; --frame: #334155; --gate: #fdf2e0; --canopy: #334155; --yel: #facc15; --red: #b91c1c; --win: #93c5fd; --lit: #93c5fd; --ground: #65a30d; --gtext: #475569; --sign: #fff; }
.sky-campus .c-wall { fill: var(--wall); } .sky-campus .c-trim { fill: var(--trim); } .sky-campus .c-frame { fill: var(--frame); }
.sky-campus .c-gate { fill: var(--gate); } .sky-campus .c-canopy { fill: var(--canopy); } .sky-campus .c-yel rect { fill: var(--yel); }
.sky-campus .c-red { fill: var(--red); } .sky-campus .c-win rect { fill: var(--win); } .sky-campus .c-win rect.lit { fill: var(--lit); }
.sky-campus .c-ground { fill: var(--ground); } .sky-campus .c-accent2 { fill: var(--red); }
.sky-campus .c-gtext { fill: var(--gtext); font: 800 7.5px 'Plus Jakarta Sans', sans-serif; letter-spacing: .5px; }
.sky-campus .c-sign { fill: var(--sign); font: 800 12px 'Plus Jakarta Sans', sans-serif; letter-spacing: .5px; }
.sky-campus .dept .c-accent { fill: var(--dept, #0d9488); }
.dept-jtmk { --dept: #0d9488; } .dept-jka { --dept: #ea580c; } .dept-jke { --dept: #ca8a04; } .dept-jkm { --dept: #dc2626; } .dept-jp { --dept: #7c3aed; } .dept-puo { --dept: #0f2747; }
.sky-campus .gate, .sky-campus .bercham, .sky-campus .dept { filter: drop-shadow(0 -2px 6px rgba(15,39,71,.12)); }
.sky-evening { --wall: #8a3f7a; --trim: #6b2c63; --frame: #3b0d3c; --gate: #a3527f; --canopy: #3b0d3c; --yel: #c2410c; --red: #7f1d1d; --win: #5b2152; --lit: #fde68a; --ground: #3b0764; --gtext: #fbcfe8; }
.sky-night { --wall: #1f1d52; --trim: #17153f; --frame: #0b0a24; --gate: #2a2766; --canopy: #0b0a24; --yel: #3730a3; --red: #7f1d1d; --win: #15133a; --lit: #fde68a; --ground: #050816; --gtext: #a5b4fc; }
.sky-night .sky-campus .dept .c-accent { filter: drop-shadow(0 0 6px var(--dept)); }
.sky-night .sky-city, .sky-evening .sky-city { opacity: .55 !important; }
.sky-evening .sky-city { color: #2e1065; } .sky-night .sky-city { color: #050816; }
.sky-card .sky-bot { z-index: 3; }
.greeting-card.sky-card { padding-bottom: 122px; }
@media (max-width: 600px) {
  .sky-campus { left: -50px; width: 640px; height: 88px; }
  .greeting-card.sky-card { padding-bottom: 100px; }
}
</style>
<style id="tb-mobile">
.topbar .topbar-left { gap: 10px; }
.tb-bot { display: inline-flex; animation: tbBob 3s ease-in-out infinite; }
@keyframes tbBob { 50% { transform: translateY(-3px); } }
.tb-hello { line-height: 1.15; min-width: 0; }
.tb-hello small { display: block; font-size: 12px; font-weight: 600; color: #64748b; }
.tb-hello b { display: block; font-size: 18px; font-weight: 800; color: #14213d; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 56vw; }
.tb-avatar { width: 40px; height: 40px; flex: none; border-radius: 50%; overflow: hidden; display: grid; place-items: center; text-decoration: none;
  background: #0f2747; color: #fff; font-size: 13px; font-weight: 800; box-shadow: 0 0 0 3px #ccfbf1; }
.tb-avatar img { width: 100%; height: 100%; object-fit: cover; }
html[data-theme="dark"] .tb-hello b { color: #dee1e9; } html[data-theme="dark"] .tb-hello small { color: #b0b6be; }
html[data-theme="dark"] .tb-avatar { box-shadow: 0 0 0 3px #1d3d3b; }
</style>
<style id="home-cards-a">
/* Today's classes as a dotted timeline */
#todayClassesBody { position: relative; padding-left: 18px; margin-top: 6px; }
#todayClassesBody::before { content: ""; position: absolute; left: 5px; top: 14px; bottom: 14px; width: 2px; background: #ccfbf1; border-radius: 2px; }
#todayClassesBody .today-class-row { position: relative; border-top: 0; padding: 9px 12px; border-radius: 14px; margin-bottom: 4px; }
#todayClassesBody .today-class-row::before { content: ""; position: absolute; left: -18px; top: 50%; margin-top: -6px; width: 12px; height: 12px; border-radius: 50%; background: #fff; border: 3px solid #99f6e4; box-sizing: border-box; }
#todayClassesBody .today-class-row.is-ongoing { background: #f0fdfa; }
#todayClassesBody .today-class-row.is-ongoing::before { border-color: #14b8a6; box-shadow: 0 0 0 4px rgba(20,184,166,.2); }
#todayClassesBody .today-class-row.is-ongoing .today-class-time, #todayClassesBody .today-class-row.is-ongoing .today-class-subject { color: #0f766e; }
#todayClassesBody .today-classes-empty { margin-left: -18px; }
#todayClassesBody:has(.today-classes-empty)::before { display: none; }
.tc-pill { margin-left: auto; flex: none; font-size: 10.5px; font-weight: 800; padding: 3px 9px; border-radius: 999px; white-space: nowrap; }
.tc-done { background: #f1f5f9; color: #64748b; } .tc-now { background: #ccfbf1; color: #0f766e; } .tc-soon { background: #e0f2fe; color: #0369a1; }

/* Reminders card (replaces the dark banner) */
.reminders-banner.rem-card { display: block; background: var(--bg-card); border: 1px solid var(--border-light); box-shadow: 0 1px 2px rgba(20,40,100,.04); color: inherit; padding: 18px 20px; border-radius: 20px; cursor: default; }
.reminders-banner.rem-card:hover { transform: none; box-shadow: 0 1px 2px rgba(20,40,100,.04); }
.rem-row { display: flex; align-items: center; gap: 11px; padding: 9px 0; border-top: 1px dashed var(--border-light); text-decoration: none; color: inherit; }
.rem-card .today-classes-head + .rem-row { border-top: 0; }
.rem-ic { width: 34px; height: 34px; flex: none; border-radius: 11px; display: grid; place-items: center; font-size: 16px; background: #f1f5f9; }
.rem-exam { background: #eef2ff; } .rem-quiz { background: #f5f3ff; } .rem-assignment { background: #f0fdfa; }
.rem-body { flex: 1; min-width: 0; }
.rem-body b { display: block; font-size: 13px; color: #14213d; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.rem-body small { font-size: 11px; color: var(--text-muted); }
.rem-cd { flex: none; font-size: 11px; font-weight: 800; padding: 4px 9px; border-radius: 999px; }
.rem-cd-hot { background: #fee2e2; color: #b91c1c; } .rem-cd-warm { background: #fef3c7; color: #b45309; } .rem-cd-cool { background: #e0f2fe; color: #0369a1; }
.rem-empty { font-size: 12.5px; color: var(--text-muted); padding: 12px 0 4px; }
.rem-add { display: block; text-align: center; margin-top: 10px; font-size: 12px; font-weight: 800; color: var(--blue-primary); background: #f0fdfa; border-radius: 12px; padding: 9px; text-decoration: none; transition: background .15s; }
.rem-add:hover { background: #ccfbf1; }

/* FAQ: coloured icon + arrow */
.quick-chip { display: flex !important; align-items: center; gap: 12px; padding: 12px 14px !important; border-radius: 16px !important; color: #14213d !important; }
.qc-ic { width: 38px; height: 38px; flex: none; border-radius: 12px; display: grid; place-items: center; font-size: 17px; }
.qc-teal { background: #ccfbf1; } .qc-amber { background: #fef3c7; } .qc-sky { background: #e0f2fe; } .qc-violet { background: #ede9fe; } .qc-pink { background: #fce7f3; }
.qc-text { flex: 1; min-width: 0; font-size: 13.5px; line-height: 1.35; }
.qc-text .quick-chip-tag { margin-bottom: 1px; }
.qc-go { width: 16px; height: 16px; flex: none; color: #94a3b8; transition: transform .15s, color .15s; }
.quick-chip:hover .qc-go { transform: translateX(3px); color: var(--blue-primary); }

html[data-theme="dark"] .reminders-banner.rem-card { background: #17202d; border-color: #284848; }
html[data-theme="dark"] .rem-body b, html[data-theme="dark"] .quick-chip { color: #dee1e9 !important; }
html[data-theme="dark"] .rem-row { border-color: #284848; }
html[data-theme="dark"] .rem-ic { background: #1f2a39; }
html[data-theme="dark"] .rem-add { background: #1d3d3b; color: #41eedf; }
html[data-theme="dark"] #todayClassesBody::before { background: #1d3d3b; }
html[data-theme="dark"] #todayClassesBody .today-class-row::before { background: #17202d; border-color: #2c6b66; }
html[data-theme="dark"] #todayClassesBody .today-class-row.is-ongoing { background: #1d3d3b; }
html[data-theme="dark"] #todayClassesBody .today-class-row.is-ongoing .today-class-time, html[data-theme="dark"] #todayClassesBody .today-class-row.is-ongoing .today-class-subject { color: #5eead4; }
html[data-theme="dark"] .reminders-banner.rem-card { box-shadow: none; }
html[data-theme="dark"] .qc-ic { filter: saturate(.8) brightness(.9); }
</style>
<style id="start-chat-btn">
.start-chat-btn.scb { position: relative; overflow: hidden; display: flex; align-items: center; justify-content: flex-start; gap: 14px; padding: 14px 16px 14px 14px; border-radius: 22px; text-align: left;
  background: linear-gradient(120deg, #0f2747, #155e75 55%, #14b8a6); animation: fadeInUp .5s ease both; }
.start-chat-btn.scb::after { content: ""; position: absolute; top: 0; left: -40%; width: 30%; height: 100%; background: linear-gradient(100deg, transparent, rgba(255,255,255,.28), transparent); transform: skewX(-20deg); animation: scbShine 4.5s ease-in-out infinite; }
@keyframes scbShine { 0%, 65% { left: -40%; } 100% { left: 130%; } }
.scb-bot { width: 54px; height: 54px; flex: none; border-radius: 18px; background: rgba(255,255,255,.95); display: grid; place-items: center; box-shadow: 0 6px 14px rgba(0,0,0,.18); }
.scb-bot svg { width: 40px !important; height: 40px !important; animation: scbWave 2.8s ease-in-out infinite; }
@keyframes scbWave { 0%, 70%, 100% { transform: rotate(0); } 78% { transform: rotate(-10deg); } 86% { transform: rotate(8deg); } }
.scb-text { flex: 1; min-width: 0; }
.scb-text b { display: block; font-size: 17px; font-weight: 800; color: #fff; }
.scb-text small { display: block; font-size: 12.5px; color: rgba(255,255,255,.82); font-weight: 500; margin-top: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.scb-go { width: 42px; height: 42px; flex: none; border-radius: 50%; background: rgba(255,255,255,.16); border: 1px solid rgba(255,255,255,.3); display: grid; place-items: center; color: #fff; transition: transform .2s, background .2s; }
.scb-go svg { width: 18px !important; height: 18px !important; }
.start-chat-btn.scb:hover .scb-go { transform: translateX(4px); background: rgba(255,255,255,.28); }
.start-chat-btn.scb:active { transform: scale(.985); }

/* tap animation */
.scb-fx { position: fixed; inset: 0; z-index: 9999; pointer-events: none; visibility: hidden; }
.scb-fx.on { visibility: visible; pointer-events: auto; }
.scb-fill { position: absolute; width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, #0f2747, #155e75 55%, #14b8a6); transform: translate(-50%, -50%) scale(0); }
.scb-fx.on .scb-fill { animation: scbFill .75s cubic-bezier(.6,0,.4,1) .25s forwards; }
@keyframes scbFill { to { transform: translate(-50%, -50%) scale(var(--scb-s, 60)); } }
.scb-fx.on .scb-fill { animation-duration: .5s; animation-delay: 0s; }
.scb-cv { position: absolute; inset: 0; width: 100%; height: 100%; }
.scb-fly { position: absolute; left: 50vw; top: 46vh; display: flex; flex-direction: column; align-items: center; gap: 14px; transform: translate(-50%, -50%); opacity: 0; }
.scb-bod { position: relative; display: block; }
.scb-bod svg { position: relative; z-index: 2; filter: drop-shadow(0 14px 22px rgba(0,0,0,.3)); }
.scb-aura { position: absolute; left: 50%; top: 50%; width: 190px; height: 190px; margin: -95px; border-radius: 50%; z-index: 1;
  background: radial-gradient(circle, rgba(94,234,212,.75), rgba(56,189,248,.35) 45%, transparent 70%); opacity: 0; }
.scb-big { position: absolute; left: 50%; top: 50%; width: 200px; height: 200px; margin: -100px; border-radius: 50%; z-index: 3; opacity: 0; transform: scale(.3);
  background: radial-gradient(circle at 32% 28%, rgba(255,255,255,.85) 0 7%, rgba(255,255,255,.12) 18%, transparent 46%), radial-gradient(circle at 50% 50%, transparent 58%, rgba(167,243,208,.35) 68%, rgba(125,211,252,.55) 78%, rgba(244,114,182,.4) 88%, rgba(255,255,255,.7) 97%);
  box-shadow: inset 0 0 22px rgba(255,255,255,.45), 0 0 24px rgba(125,211,252,.35); }
.scb-ring { position: absolute; left: 50vw; top: 46vh; width: 140px; height: 140px; margin: -70px; border-radius: 50%; border: 2px solid rgba(255,255,255,.7); opacity: 0; }
.scb-flash { position: absolute; inset: 0; background: #fff; opacity: 0; }
.scb-say { position: relative; height: 38px; min-width: 170px; }
.scb-say span { position: absolute; left: 50%; top: 0; transform: translateX(-50%) scale(.8); white-space: nowrap; background: #fff; color: #0f2747; font-weight: 800; font-size: 15px; padding: 9px 18px; border-radius: 999px; box-shadow: 0 0 0 3px rgba(94,234,212,.5), 0 10px 22px rgba(0,0,0,.25); opacity: 0; }

.scb-fx.on .scb-fly { animation: scbIn .45s cubic-bezier(.3,1.5,.5,1) .15s forwards, scbLaunch .6s cubic-bezier(.5,0,.75,.3) 1.5s forwards; }
.scb-fx.on .scb-bod { animation: scbHop .42s ease-in-out .55s 2; }
.scb-fx.on .scb-aura { animation: scbAura .5s ease-in-out .5s 3 alternate forwards; }
.scb-fx.on .scb-big { animation: scbBig .45s cubic-bezier(.3,1.6,.5,1) 1.2s forwards, scbPop .2s ease-out 2s forwards; }
.scb-fx.on .scb-ring.r1 { animation: scbRing .7s ease-out .55s; }
.scb-fx.on .scb-ring.r2 { animation: scbRing .7s ease-out .85s; }
.scb-fx.on .scb-ring.r3 { animation: scbRing .7s ease-out 1.15s; }
.scb-fx.on .scb-say .s2 { animation: scbPill2 .3s cubic-bezier(.3,1.6,.5,1) .6s forwards, scbOut .25s ease 1.45s forwards; } @keyframes scbOut { to { opacity: 0; transform: translateX(-50%) scale(.5); } }
.scb-fx.on .scb-flash { animation: scbFlash .3s ease-in 2s forwards; }
@keyframes scbIn { from { opacity: 0; transform: translate(-50%, -50%) scale(.2) rotate(-25deg); } to { opacity: 1; transform: translate(-50%, -50%) scale(1); } }
@keyframes scbLaunch { 0% { opacity: 1; transform: translate(-50%, -50%); } 30% { transform: translate(-46%, -70%); } 60% { transform: translate(-54%, -95%); } 100% { opacity: 1; transform: translate(-50%, -75vh); } }
@keyframes scbHop { 0%, 100% { transform: none; } 30% { transform: translateY(-26px) scale(.96, 1.06); } 55% { transform: translateY(0) scale(1.08, .9); } 75% { transform: translateY(-6px); } }
@keyframes scbAura { from { opacity: .35; transform: scale(.8); } to { opacity: 1; transform: scale(1.25); } }
@keyframes scbBig { to { opacity: 1; transform: scale(1); } } @keyframes scbPop { to { opacity: 0; transform: scale(1.5); } }
@keyframes scbRing { from { opacity: .9; transform: scale(.6); } to { opacity: 0; transform: scale(2.6); } }
@keyframes scbPill { 0% { opacity: 0; transform: translateX(-50%) scale(.7); } 20%, 85% { opacity: 1; transform: translateX(-50%) scale(1); } 100% { opacity: 0; transform: translateX(-50%) scale(.9); } }
@keyframes scbPill2 { from { opacity: 0; transform: translateX(-50%) scale(.6); } to { opacity: 1; transform: translateX(-50%) scale(1.05); } }
@keyframes scbFlash { 0% { opacity: 0; } 60% { opacity: .9; } 100% { opacity: 1; background: #f0fafa; } }
@media (max-width: 600px) { .scb-text b { font-size: 15.5px; } .scb-text small { font-size: 11.5px; } .scb-bot { width: 48px; height: 48px; border-radius: 16px; } }
@media (prefers-reduced-motion: reduce) { .start-chat-btn.scb::after, .scb-bot svg { animation: none; } }
</style>
</head>
<body>

@include('partials.app-nav', ['active' => 'home', 'user' => $user])

<div class="bg-blob b1"></div>
<div class="bg-blob b2"></div>
<div class="bg-blob b3"></div>
<div class="bg-blob b4"></div>

@php
          // Server-rendered fallback for first paint (before the client-side
          // clock in the script below takes over and keeps this live).
          $hour = now()->hour;
          if ($hour < 5 || $hour >= 22) {
              $greeting = 'Good night';
          } elseif ($hour < 12) {
              $greeting = 'Good morning';
          } elseif ($hour < 18) {
              $greeting = 'Good afternoon';
          } else {
              $greeting = 'Good evening';
          }
      @endphp
@php
        $h = now()->hour;
        $skyPhase = ($h < 5 || $h >= 20) ? 'night' : ($h < 12 ? 'morning' : ($h < 18 ? 'afternoon' : 'evening'));
    @endphp
<div class="topbar">
    {{-- Phone top bar: small robot, greeting + first name, avatar → Profile (hidden on PC) --}}
    <div class="topbar-left">
        <span class="tb-bot"><x-brand-logo size="34" /></span>
        <div class="tb-hello">
            <small><span id="tbGreet">{{ __($greeting) }}</span> <span id="tbIcon">{{ ["morning" => "☀️", "afternoon" => "🌤️", "evening" => "🌇", "night" => "🌙"][$skyPhase] }}</span></small>
            <b>{{ $user->first_name ?: $user->name }}</b>
        </div>
    </div>
    <a href="{{ route('student.profile') }}" class="tb-avatar" aria-label="{{ __('Profile') }}">
        @if($user->photo_data)
            <img src="{{ $user->photo_data }}" alt="">
        @else
            {{ strtoupper(substr($user->first_name ?? $user->name ?? 'U', 0, 1) . substr($user->last_name ?? '', 0, 1)) }}
        @endif
    </a>
</div>

  <div class="container">

    <!-- Greeting card -->

    <div class="greeting-card sky-card sky-{{ $skyPhase }}" id="skyCard">
      {{-- Sky that follows the time of day: morning / afternoon / evening / night --}}
      <div class="sky" aria-hidden="true">
        <div class="sky-stars"></div>
        <div class="sky-orb"></div>
        <span class="sky-cloud k1"></span><span class="sky-cloud k2"></span><span class="sky-cloud k3"></span>
        @php $dept = \App\Support\MatricNumber::department($user->student_id); @endphp
        {{-- Campus skyline: the student's department block, the PUO main gate and Kampus Bercham --}}
        <svg class="sky-city" viewBox="0 0 1200 60" preserveAspectRatio="none"><path class="c-far" d="M0 60V36h50v-12h36v12h28V18h46v18h36V28h64V12h18v16h56v8h46V20h74v16h38V28h56V10h28v18h64v8h56V22h46v14h38v-8h84v8h46V16h36v20h56v-6h40v-14h22v14h60v12h44V24h40v12h52v24z"/></svg>
        <svg class="sky-campus" viewBox="0 0 1000 130" preserveAspectRatio="xMaxYMax meet">
          {{-- department block --}}
          <g transform="translate(-40 0)" class="dept dept-{{ strtolower($dept ?? 'puo') }}">
            <rect class="c-wall" x="150" y="44" width="150" height="86"/>
            <rect class="c-trim" x="146" y="38" width="158" height="8" rx="2"/>
            <rect class="c-accent" x="196" y="16" width="58" height="22" rx="4"/>
            <text x="225" y="31.5" text-anchor="middle" class="c-sign">{{ $dept ?? 'PUO' }}</text>
            <g class="c-win">@for($r = 0; $r < 3; $r++)@for($c = 0; $c < 6; $c++)<rect x="{{ 160 + $c * 23 }}" y="{{ 54 + $r * 22 }}" width="13" height="12" rx="1.5" class="{{ ($r * 6 + $c) % 3 === 1 ? 'lit' : '' }}"/>@endfor @endfor</g>
            <rect class="c-trim" x="208" y="112" width="34" height="18"/>
          </g>
          {{-- PUO main gate ("Selamat Datang" arches) --}}
          <g class="gate" transform="translate(-50 0)">
            <path class="c-gate" d="M372 130V70q0-24 45-24t45 24v60h-14V74q0-14-31-14t-31 14v56z"/>
            <path class="c-gate" d="M538 130V70q0-24 45-24t45 24v60h-14V74q0-14-31-14t-31 14v56z"/>
            <path class="c-gate" d="M462 130V40q0-16 38-16t38 16v90z"/>
            <rect class="c-trim" x="478" y="96" width="44" height="22" rx="2"/>
            <rect class="c-accent2" x="486" y="44" width="28" height="12" rx="2"/>
            <rect class="c-canopy" x="430" y="80" width="40" height="5" rx="2"/><rect class="c-canopy" x="530" y="80" width="40" height="5" rx="2"/>
            {{-- the words run along the arch band, following its curve --}}
            <path id="gateArc" d="M381 72Q417 38 453 72" fill="none"/>
            <text class="c-gtext" style="font-size:6.6px;letter-spacing:.4px"><textPath href="#gateArc" startOffset="50%" text-anchor="middle">SELAMAT DATANG</textPath></text>
          </g>
          {{-- Kampus Bercham: tower + yellow-panelled block --}}
          <g class="bercham" transform="translate(-60 0)">
            <rect class="c-wall" x="672" y="22" width="46" height="108"/>
            <rect class="c-red" x="676" y="28" width="38" height="9" rx="1.5"/>
            <rect class="c-frame" x="668" y="18" width="54" height="5"/>
            <rect class="c-wall" x="718" y="52" width="170" height="78"/>
            <g class="c-yel"><rect x="736" y="52" width="16" height="78"/><rect x="800" y="52" width="16" height="78"/><rect x="852" y="52" width="16" height="78"/><rect x="684" y="40" width="10" height="90"/></g>
            <g class="c-win">@for($r = 0; $r < 4; $r++)@foreach([722, 758, 778, 822, 870] as $i => $x)<rect x="{{ $x }}" y="{{ 58 + $r * 17 }}" width="12" height="9" rx="1" class="{{ ($r + $i) % 3 === 0 ? 'lit' : '' }}"/>@endforeach @endfor
              @for($r = 0; $r < 5; $r++)<rect x="700" y="{{ 46 + $r * 16 }}" width="12" height="9" rx="1" class="{{ $r % 2 ? 'lit' : '' }}"/>@endfor</g>
          </g>
          <rect class="c-ground" x="-2000" y="124" width="5000" height="6"/>
        </svg>
      </div>
      <x-brand-logo size="120" class="sky-bot" />
      <div class="greeting-top">
        <div class="greeting-avatar">
            @if($user->photo_data)
                <img src="{{ $user->photo_data }}" alt="{{ __('Profile photo') }}">
            @else
                {{ strtoupper(substr($user->first_name ?? 'A', 0, 1) . substr($user->last_name ?? '', 0, 1)) }}
            @endif
        </div>
        <div>
          <p class="greeting-name">{{ $user->first_name }} {{ $user->last_name }}</p>
          @if($user->programme)
            <p class="greeting-programme">{{ $user->programme }}</p>
          @endif
          @if($user->student_id)
            <p class="greeting-meta">{{ $user->student_id }}</p>
          @endif
        </div>
      </div>

      <p class="greeting-hello"><span class="sky-badge"><span id="skyIcon">{{ ["morning" => "☀️", "afternoon" => "🌤️", "evening" => "🌇", "night" => "🌙"][$skyPhase] }}</span> <span id="greetingHello">{{ __($greeting) }}</span><span class="sky-dot">·</span><span id="skyTime">{{ now()->format('g:i A') }}</span></span></p>
      <p class="greeting-question">{{ __('How can I help you today?') }}</p>
    </div>

    {{-- Next 3 reminders with a day countdown (same grid slot as the old banner) --}}
    <section class="reminders-banner rem-card">
      <div class="today-classes-head">
        <div class="today-classes-title-row">
          <svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.7 21a2 2 0 0 1-3.4 0"></path></svg>
          <p class="today-classes-title">{{ __('Reminders') }}</p>
        </div>
        <a href="{{ route('student.reminders') }}" class="today-classes-link">{{ __('All →') }}</a>
      </div>
      @forelse($upcomingReminders as $r)
        <a href="{{ route('student.reminders') }}" class="rem-row">
          <span class="rem-ic rem-{{ strtolower($r['type']) }}">{{ $r['emoji'] }}</span>
          <span class="rem-body"><b>{{ $r['subject'] }}</b><small>{{ $r['when'] }}</small></span>
          <span class="rem-cd rem-cd-{{ $r['level'] }}">{{ $r['left'] }}</span>
        </a>
      @empty
        <p class="rem-empty">{{ __('No upcoming reminders') }} 🎉</p>
      @endforelse
      <a href="{{ route('student.reminders') }}?add=1" class="rem-add">+ {{ __('Add reminder') }}</a>
    </section>

    <div class="today-classes-card">
      <div class="today-classes-head">
        <div class="today-classes-title-row">
          <svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="17" rx="2"></rect><path d="M3 9h18"></path><path d="M8 3v4"></path><path d="M16 3v4"></path></svg>
          <p class="today-classes-title" id="todayClassesTitle">{{ __('Today\'s Classes') }}</p>
        </div>
        <a href="{{ route('student.timetable') }}" class="today-classes-link">{{ __('View all →') }}</a>
      </div>

      <div class="today-classes-body" id="todayClassesBody"
           onpointerdown="startClassesDrag(event)" onpointermove="moveClassesDrag(event)"
           onpointerup="endClassesDrag(event)" onpointerleave="endClassesDrag(event)"></div>
    </div>

    {{-- Quick questions: the most asked questions from students (see App\Services\PopularQuestions),
         topped up with default ones. Popular chips show a small "Popular" tag. --}}
    <div class="quick-grid">
      <p class="faq-label">🔥 {{ __('FREQUENTLY ASKED QUESTIONS') }}</p>
      @foreach($quickQuestions as $q)
        @php
          $label = $q['popular'] ? $q['text'] : __($q['text']);
          $lc = mb_strtolower($q['text'] . ' ' . $label);
          [$qe, $qc] = match (true) {
              str_contains($lc, 'regist') || str_contains($lc, 'daftar') => ['📝', 'teal'],
              str_contains($lc, 'fee') || str_contains($lc, 'yuran') || str_contains($lc, 'pay') || str_contains($lc, 'bayar') => ['💳', 'amber'],
              str_contains($lc, 'librar') || str_contains($lc, 'perpustakaan') => ['📚', 'sky'],
              str_contains($lc, 'exam') || str_contains($lc, 'peperiksaan') || str_contains($lc, 'timetable') || str_contains($lc, 'jadual') => ['🗓️', 'violet'],
              str_contains($lc, 'surau') || str_contains($lc, 'where') || str_contains($lc, 'mana') => ['📍', 'pink'],
              default => ['💬', ['teal', 'amber', 'sky', 'violet'][$loop->index % 4]],
          };
        @endphp
        <a href="{{ route('student.chat') }}?q={{ urlencode($label) }}" class="quick-chip">
          <span class="qc-ic qc-{{ $qc }}">{{ $qe }}</span>
          <span class="qc-text">@if($q['popular'])<span class="quick-chip-tag">🔥 {{ __('Popular') }}</span>@endif{{ $label }}</span>
          <svg class="qc-go" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
        </a>
      @endforeach
    </div>

    {{-- Start chat: robot + text + arrow; on tap the robot flies up and the screen fills before opening the chat --}}
    <a href="{{ route('student.chat') }}" class="start-chat-btn scb" id="startChatBtn">
      <span class="scb-bot"><x-brand-logo size="40" /></span>
      <span class="scb-text"><b>{{ __('Start New Chat') }}</b><small>{{ __('Ask anything about PUO — classes, fees, places…') }}</small></span>
      <span class="scb-go"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span>
    </a>
    <div class="scb-fx" id="scbFx" aria-hidden="true">
      <div class="scb-fill" id="scbFill"></div>
      <canvas class="scb-cv" id="scbCv"></canvas>
      <div class="scb-ring r1"></div><div class="scb-ring r2"></div><div class="scb-ring r3"></div>
      <div class="scb-fly" id="scbFly"><span class="scb-bod"><span class="scb-aura"></span><x-brand-logo size="120" /><span class="scb-big"></span></span>
        <span class="scb-say"><span class="s2">{{ __("Let's chat!") }} 💬</span></span></div>
      <div class="scb-flash"></div>
    </div>

    <p class="section-label">{{ __('RECENT CONVERSATIONS') }}</p>

    <div class="conversation-list">

    @forelse($conversations as $conv)

        <div class="conv-swipe-wrap">
            <button type="button" class="conv-swipe-delete" aria-label="{{ __('Delete') }}" onclick="deleteHomeConversationDirect({{ $conv['id'] }})">
                <svg viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                <span>{{ __('Delete') }}</span>
            </button>
            <div class="conv-card" data-conv-id="{{ $conv['id'] }}" data-conv-title="{{ $conv['title'] }}" data-chat-url="{{ route('student.chat') }}?conversation={{ $conv['id'] }}"
                 onpointerdown="startConvDrag(event, {{ $conv['id'] }})" onpointermove="moveConvDrag(event)" onpointerup="endConvDrag(event, {{ $conv['id'] }})" onpointerleave="endConvDrag(event, {{ $conv['id'] }})" onpointercancel="endConvDrag(event, {{ $conv['id'] }})">
                <div class="conv-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
                    </svg>
                </div>
                <div class="conv-body">
                    <p class="conv-title">{{ $conv['title'] }}</p>
                    <p class="conv-preview">{{ $conv['preview'] }}</p>
                </div>
                <div class="conv-meta">
                    <span class="conv-time">{{ $conv['time'] }}</span>
                </div>
                <div class="conv-actions">
                    <button type="button" class="conv-action-btn" aria-label="{{ __('Edit') }}" onclick="event.stopPropagation(); renameHomeConversation({{ $conv['id'] }}, {{ Js::from($conv['title']) }})">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.12 2.12 0 0 1 3 3L12 15l-4 1 1-4Z"></path></svg>
                    </button>
                    <button type="button" class="conv-action-btn" aria-label="{{ __('Delete') }}" onclick="event.stopPropagation(); deleteHomeConversation({{ $conv['id'] }})">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
            </div>
        </div>

    @empty

        <p style="text-align:center; color: var(--text-muted); padding: 24px 0; grid-column: 1/-1;">
            {{ __('No conversations yet. Start your first chat! 💬') }}
        </p>

    @endforelse

</div>

  </div>

<script>
const classSchedules = @json($classSchedules);
const CLASS_DAYS = @json($classDays);
let dayOffset = 0; // 0 = today, +1 = tomorrow, -1 = yesterday, etc.

function getViewedDate() {
    const d = new Date();
    d.setHours(0, 0, 0, 0);
    d.setDate(d.getDate() + dayOffset);
    return d;
}

function formatClassTime(hhmm) {
    const [h, m] = hhmm.split(':').map(Number);
    const period = h >= 12 ? 'PM' : 'AM';
    const h12 = h % 12 === 0 ? 12 : h % 12;
    return h12 + ':' + (m < 10 ? '0' + m : m) + ' ' + period;
}

function escapeHtmlHome(s) {
    return String(s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
}

function renderTodayClasses() {
    const titleEl = document.getElementById('todayClassesTitle');
    const bodyEl = document.getElementById('todayClassesBody');
    if (!titleEl || !bodyEl) return;

    const viewedDate = getViewedDate();
    const dayName = CLASS_DAYS[(viewedDate.getDay() + 6) % 7]; // JS Sunday-first -> Monday-first index

    if (dayOffset === 0) titleEl.textContent = t("Today's Classes");
    else if (dayOffset === 1) titleEl.textContent = t("Tomorrow's Classes");
    else if (dayOffset === -1) titleEl.textContent = t("Yesterday's Classes");
    else titleEl.textContent = t(':day\'s Classes', {day: t(dayName)});

    const isActualToday = dayOffset === 0;
    const now = new Date();

    const items = classSchedules
        .filter(s => s.day_of_week === dayName)
        .sort((a, b) => a.start_time.localeCompare(b.start_time));

    if (items.length === 0) {
        bodyEl.innerHTML = `<p class="today-classes-empty">${isActualToday ? t('No classes today') + ' 🎉' : t('No classes')}</p>`;
        return;
    }

    bodyEl.innerHTML = items.map(s => {
        let status = 'upcoming';
        if (isActualToday) {
            const [sh, sm] = s.start_time.split(':').map(Number);
            const [eh, em] = s.end_time.split(':').map(Number);
            const start = new Date(); start.setHours(sh, sm, 0, 0);
            const end = new Date(); end.setHours(eh, em, 0, 0);
            status = now < start ? 'upcoming' : (now < end ? 'ongoing' : 'past');
        }
        const meta = [s.room, s.lecturer].filter(Boolean).map(escapeHtmlHome).join(' · ')
            || (status === 'ongoing' ? t('Ongoing now') : '');

        let pill = '';
        if (status === 'past') pill = `<span class="tc-pill tc-done">${t('Done')}</span>`;
        else if (status === 'ongoing') pill = `<span class="tc-pill tc-now">● ${t('Now')}</span>`;
        else if (isActualToday) {
            const [sh, sm] = s.start_time.split(':').map(Number);
            const mins = Math.round(((sh * 60 + sm) - (now.getHours() * 60 + now.getMinutes())));
            pill = `<span class="tc-pill tc-soon">${mins < 60 ? t('in :n min', {n: mins}) : t('in :nh', {n: Math.round(mins / 60)})}</span>`;
        }

        return `<div class="today-class-row ${status === 'past' ? 'is-past' : ''} ${status === 'ongoing' ? 'is-ongoing' : ''}">
            <div class="today-class-time">${formatClassTime(s.start_time)}</div>
            <div class="today-class-body">
              <p class="today-class-subject">${escapeHtmlHome(s.subject)}</p>
              <p class="today-class-meta">${meta}</p>
            </div>
            ${pill}
          </div>`;
    }).join('');
}

// Swipe the "Today's Classes" card left/right to browse days (left = tomorrow,
// right = previous day) — mirrors the pointer-drag pattern already used for the
// conversation swipe-to-delete cards below, scoped to just this card.
const CLASSES_SWIPE_THRESHOLD = 50;
let classesDragStartX = null;
let classesDragStartY = null;
let classesDragDeltaX = 0;
let classesDragging = false;

function startClassesDrag(e) {
    classesDragStartX = e.clientX;
    classesDragStartY = e.clientY;
    classesDragDeltaX = 0;
    classesDragging = false;
}

function moveClassesDrag(e) {
    if (classesDragStartX === null) return;
    const dx = e.clientX - classesDragStartX;
    const dy = e.clientY - classesDragStartY;

    if (!classesDragging) {
        // Only claim the gesture once it's clearly more horizontal than vertical,
        // so an ordinary vertical page scroll that starts over the card isn't hijacked.
        if (Math.abs(dx) < 8 || Math.abs(dx) <= Math.abs(dy)) return;
        classesDragging = true;
    }

    classesDragDeltaX = dx;
    const bodyEl = document.getElementById('todayClassesBody');
    if (bodyEl) {
        bodyEl.classList.add('dragging');
        bodyEl.style.transform = `translateX(${Math.max(-90, Math.min(90, dx))}px)`;
    }
}

function endClassesDrag(e) {
    if (classesDragStartX === null) return;
    const dx = classesDragDeltaX;
    const wasDragging = classesDragging;
    classesDragStartX = null;
    classesDragStartY = null;
    classesDragDeltaX = 0;
    classesDragging = false;

    const bodyEl = document.getElementById('todayClassesBody');
    if (bodyEl) {
        bodyEl.classList.remove('dragging');
        bodyEl.style.transform = 'translateX(0px)';
    }

    if (!wasDragging) return;

    if (dx <= -CLASSES_SWIPE_THRESHOLD) {
        dayOffset += 1;
        renderTodayClasses();
    } else if (dx >= CLASSES_SWIPE_THRESHOLD) {
        dayOffset -= 1;
        renderTodayClasses();
    }
}

renderTodayClasses();

// Greeting text (morning/afternoon/evening/night) based on the student's own
// device clock — updates immediately on load and keeps itself live while the
// page stays open, so it stays correct across an hour boundary without a
// refresh, and matches the student's local time even if the server isn't in
// the same timezone.
function updateGreeting() {
    const el = document.getElementById('greetingHello');
    if (!el) return;

    const hour = new Date().getHours();
    let greeting;
    if (hour < 5 || hour >= 22) greeting = t('Good night');
    else if (hour < 12) greeting = t('Good morning');
    else if (hour < 18) greeting = t('Good afternoon');
    else greeting = t('Good evening');

    if (el.textContent !== greeting) el.textContent = greeting;
    const tb = document.getElementById('tbGreet');
    if (tb && tb.textContent !== greeting) tb.textContent = greeting;
}
updateGreeting();
setInterval(updateGreeting, 60000);

// Greeting card sky: colours, sun/moon and the little clock follow the device time
function updateSky() {
    const card = document.getElementById('skyCard');
    if (!card) return;
    const d = new Date(), h = d.getHours();
    const phase = (h < 5 || h >= 20) ? 'night' : (h < 12 ? 'morning' : (h < 18 ? 'afternoon' : 'evening'));
    ['morning', 'afternoon', 'evening', 'night'].forEach(p => card.classList.toggle('sky-' + p, p === phase));
    const icon = document.getElementById('skyIcon');
    if (icon) icon.textContent = { morning: '☀️', afternoon: '🌤️', evening: '🌇', night: '🌙' }[phase];
    const tbi = document.getElementById('tbIcon');
    if (tbi && icon) tbi.textContent = icon.textContent;
    const tm = document.getElementById('skyTime');
    if (tm) tm.textContent = d.toLocaleTimeString(window.APP_LOCALE === 'en' ? 'en-US' : (window.APP_LOCALE || 'en-US'), { hour: 'numeric', minute: '2-digit' });
}
updateSky();
setInterval(updateSky, 30000);

// Conversation cards: swipe right to reveal "Delete", tap Delete to remove it,
// tap the card (or swipe back) to close. A mostly-vertical drag is left to the
// page scroll, so scrolling past the list never opens or deletes anything.
const CONV_OPEN_X = 90;
let convDrag = null;

function closeConvSwipes(except) {
    document.querySelectorAll('.conv-swipe-wrap.open').forEach(w => {
        if (w === except) return;
        w.classList.remove('open');
        const c = w.querySelector('.conv-card');
        if (c) c.style.transform = '';
    });
}

function startConvDrag(e, id) {
    if (e.button > 0) return;
    const card = e.currentTarget;
    const wrap = card.closest('.conv-swipe-wrap');
    closeConvSwipes(wrap);
    convDrag = { id, card, wrap, x: e.clientX, y: e.clientY, dx: 0, horiz: false, moved: false,
                 base: wrap.classList.contains('open') ? CONV_OPEN_X : 0 };
}

function moveConvDrag(e) {
    const d = convDrag;
    if (!d) return;
    const dx = e.clientX - d.x, dy = e.clientY - d.y;
    if (!d.horiz) {
        if (Math.abs(dx) < 8 && Math.abs(dy) < 8) return;
        if (Math.abs(dx) <= Math.abs(dy)) { convDrag = null; return; } // it's a scroll
        d.horiz = true;
        d.wrap.classList.add('swiping');
        try { d.card.setPointerCapture(e.pointerId); } catch (err) {}
    }
    d.moved = true;
    d.dx = Math.max(0, Math.min(CONV_OPEN_X, d.base + dx));
    d.card.style.transform = `translateX(${d.dx}px)`;
}

function endConvDrag(e, id) {
    const d = convDrag;
    convDrag = null;
    if (!d) return;
    d.wrap.classList.remove('swiping');
    if (d.moved) {
        const open = d.dx > CONV_OPEN_X / 2;
        d.wrap.classList.toggle('open', open);
        d.card.style.transform = open ? `translateX(${CONV_OPEN_X}px)` : '';
        return;
    }
    if (e.type !== 'pointerup') return;
    if (d.wrap.classList.contains('open')) { closeConvSwipes(); return; }
    if (e.target.closest('.conv-actions')) return; // rename / delete buttons handle themselves
    window.location = d.card.dataset.chatUrl;
}

// tapping anywhere else closes an open card
document.addEventListener('pointerdown', (e) => { if (!e.target.closest('.conv-swipe-wrap')) closeConvSwipes(); });

function deleteHomeConversationDirect(id) {
    fetch(`/chatbot/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
    })
    .then(res => res.json())
    .then(() => {
        const wrap = document.querySelector(`.conv-card[data-conv-id="${id}"]`)?.closest('.conv-swipe-wrap');
        if (wrap) wrap.remove();
        RKToast.show({ text: t('Conversation deleted') });
    })
    .catch(err => console.error('Delete failed', err));
}

async function renameHomeConversation(id, currentTitle) {
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
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({ title: newTitle.trim() }),
    })
    .then(res => res.json())
    .then(() => {
        const card = document.querySelector(`.conv-card[data-conv-id="${id}"]`);
        if (card) card.querySelector('.conv-title').textContent = newTitle.trim();
        RKToast.show({ text: t('Conversation renamed') });
    })
    .catch(err => console.error('Rename failed', err));
}

async function deleteHomeConversation(id) {
    const title = document.querySelector(`.conv-card[data-conv-id="${id}"] .conv-title`)?.textContent.trim();
    const ok = await RKDialog.confirm({
        scene: 'chat',
        title: t('Delete this conversation?'),
        message: t('All messages in this chat will be removed.'),
        list: title ? [{ label: title }] : [],
        warn: t('This cannot be undone.'),
        confirmText: t('Delete'),
    });
    if (!ok) return;

    fetch(`/chatbot/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
    })
    .then(res => res.json())
    .then(() => {
        const wrap = document.querySelector(`.conv-card[data-conv-id="${id}"]`)?.closest('.conv-swipe-wrap');
        if (wrap) wrap.remove();
        RKToast.show({ text: t('Conversation deleted') });
    })
    .catch(err => console.error('Delete failed', err));
}

// "Start New Chat": robot flies from the button to the middle, the gradient fills the screen, then the chat opens
(function () {
    const btn = document.getElementById('startChatBtn'), fx = document.getElementById('scbFx');
    if (!btn || !fx) return;
    document.body.appendChild(fx); // out of the container's stacking context so it covers the bottom bar too
    btn.addEventListener('click', function (e) {
        if (e.metaKey || e.ctrlKey || e.shiftKey || e.button > 0) return;
        if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
        e.preventDefault();
        const r = btn.querySelector('.scb-bot').getBoundingClientRect();
        const cx = r.left + r.width / 2, cy = r.top + r.height / 2;
        const fill = document.getElementById('scbFill'), fly = document.getElementById('scbFly');
        fill.style.left = cx + 'px'; fill.style.top = cy + 'px';

        const far = Math.hypot(Math.max(cx, innerWidth - cx), Math.max(cy, innerHeight - cy));
        fill.style.setProperty('--scb-s', Math.ceil(far * 2 / 60) + 1);
        fx.classList.add('on');
        sparkFx();
        setTimeout(function () { window.location.href = btn.href; }, 2250);
    });
    // Bubbles: glassy soap bubbles rise from the bottom, and chat bubbles pop out of the robot and float up
    function sparkFx() {
        const cv = document.getElementById('scbCv'), ctx = cv.getContext('2d');
        const dpr = Math.min(window.devicePixelRatio || 1, 2), W = innerWidth, H = innerHeight;
        cv.width = W * dpr; cv.height = H * dpr; ctx.scale(dpr, dpr);
        const cx = W / 2, cy = H * .46, t0 = performance.now();
        const words = ['Hi! 👋', '?', '💬', '📅', '💳', '🕌', '📚', '🔔', 'Hello!', '✨'];
        const soap = [], chats = [], pops = [];
        for (let i = 0; i < 26; i++) soap.push({ x: Math.random() * W, y: H + Math.random() * H * .6, r: 6 + Math.random() * 26, v: 1.4 + Math.random() * 2.6, ph: Math.random() * 6, life: 1 });
        function addChat(i) {
            const a = -Math.PI / 2 + (Math.random() - .5) * 2.6;
            chats.push({ x: cx, y: cy, vx: Math.cos(a) * (2.5 + Math.random() * 2.5), vy: Math.sin(a) * (2.5 + Math.random() * 2.5) - 1, s: 0, txt: words[i % words.length], born: performance.now(), dark: i % 3 === 0 });
        }
        for (let i = 0; i < 9; i++) setTimeout(() => addChat(i), 520 + i * 90);
        function soapDraw(b) {
            const g = ctx.createRadialGradient(b.x - b.r * .35, b.y - b.r * .4, b.r * .1, b.x, b.y, b.r);
            g.addColorStop(0, 'rgba(255,255,255,.55)'); g.addColorStop(.55, 'rgba(255,255,255,.06)');
            g.addColorStop(.85, 'rgba(125,211,252,.28)'); g.addColorStop(1, 'rgba(244,114,182,.38)');
            ctx.fillStyle = g; ctx.beginPath(); ctx.arc(b.x, b.y, b.r, 0, Math.PI * 2); ctx.fill();
            ctx.strokeStyle = 'rgba(255,255,255,.55)'; ctx.lineWidth = 1.2; ctx.stroke();
            ctx.fillStyle = 'rgba(255,255,255,.9)'; ctx.beginPath(); ctx.ellipse(b.x - b.r * .4, b.y - b.r * .45, b.r * .18, b.r * .1, -.6, 0, Math.PI * 2); ctx.fill();
        }
        function chatDraw(c) {
            ctx.save(); ctx.translate(c.x, c.y); ctx.scale(c.s, c.s);
            ctx.font = '800 15px "Plus Jakarta Sans", sans-serif';
            const w = Math.max(40, ctx.measureText(c.txt).width + 24), h = 34;
            ctx.fillStyle = c.dark ? '#0f2747' : '#fff'; ctx.shadowColor = 'rgba(0,0,0,.2)'; ctx.shadowBlur = 12; ctx.shadowOffsetY = 4;
            ctx.beginPath(); ctx.roundRect(-w / 2, -h / 2, w, h, 17); ctx.fill();
            ctx.beginPath(); ctx.moveTo(-6, h / 2 - 2); ctx.lineTo(-12, h / 2 + 8); ctx.lineTo(4, h / 2 - 2); ctx.fill();
            ctx.shadowColor = 'transparent'; ctx.fillStyle = c.dark ? '#fff' : '#0f766e'; ctx.textAlign = 'center'; ctx.textBaseline = 'middle';
            ctx.fillText(c.txt, 0, 1); ctx.restore();
        }
        function frame(now) {
            const t = now - t0;
            ctx.clearRect(0, 0, W, H);
            soap.forEach(b => {
                if (t < 250) return;
                b.y -= b.v * (t > 1500 ? 2.2 : 1); b.ph += .05; b.x += Math.sin(b.ph) * .6;
                if (b.life > 0) soapDraw(b);
                if (b.y < H * .12 && b.life > 0 && Math.random() < .04) { b.life = 0; pops.push({ x: b.x, y: b.y, r: b.r, a: 1 }); }
            });
            chats.forEach(c => {
                const age = now - c.born;
                c.s = Math.min(1, age / 180) * (age > 1300 ? Math.max(0, 1 - (age - 1300) / 200) : 1);
                c.x += c.vx; c.y += c.vy; c.vx *= .985; c.vy = c.vy * .985 - .02;
                if (c.s > 0) chatDraw(c);
                if (age > 1300 && !c.popped) { c.popped = true; pops.push({ x: c.x, y: c.y, r: 18, a: 1 }); }
            });
            pops.forEach(p => {
                p.a -= .06; p.r += 2.2; if (p.a <= 0) return;
                ctx.strokeStyle = `rgba(255,255,255,${p.a})`; ctx.lineWidth = 2;
                for (let k = 0; k < 8; k++) { const a = k * Math.PI / 4; ctx.beginPath(); ctx.moveTo(p.x + Math.cos(a) * p.r * .7, p.y + Math.sin(a) * p.r * .7); ctx.lineTo(p.x + Math.cos(a) * p.r, p.y + Math.sin(a) * p.r); ctx.stroke(); }
            });
            if (t < 2400) requestAnimationFrame(frame);
        }
        requestAnimationFrame(frame);
    }
    // coming back with the browser Back button: hide the overlay again
    window.addEventListener('pageshow', function (ev) { if (ev.persisted) fx.classList.remove('on'); });
})();
</script>

</body>
</html>