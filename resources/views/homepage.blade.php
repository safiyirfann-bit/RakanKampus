<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
@include('partials.pwa-head')
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>RakanKampus - Home</title>
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
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
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

 .avatar-btn {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: #ffffff !important;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    border: 1px solid #dbeeee;
    box-shadow: 0 4px 12px rgba(20, 33, 61, 0.12);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    overflow: hidden;
}

.topbar-right a.avatar-btn,
.topbar-right a.avatar-btn:hover {
    background: #ffffff !important;
}

.avatar-btn:hover,
.avatar-btn:focus,
.avatar-btn:active {
    background: #ffffff !important;
    transform: scale(1.03);
    box-shadow: 0 8px 20px rgba(20, 33, 61, 0.18);
}

.avatar-btn svg {
    width: 20px;
    height: 20px;
    stroke: #14213d !important;
}

.avatar-btn img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.profile-link:hover {
    background: #eafbfa;
}

@media (max-width: 640px) {
    .profile-text {
        display: none;
    }
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

  .quick-grid a:nth-child(1) { animation-delay: 0.10s; }
  .quick-grid a:nth-child(2) { animation-delay: 0.17s; }
  .quick-grid a:nth-child(3) { animation-delay: 0.24s; }
  .quick-grid a:nth-child(4) { animation-delay: 0.31s; }

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
  }

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
    animation: fadeInUp 0.5s ease both;
    touch-action: pan-y;
  }

  .conversation-list .conv-card:nth-child(1) { animation-delay: 0.45s; }
  .conversation-list .conv-card:nth-child(2) { animation-delay: 0.52s; }
  .conversation-list .conv-card:nth-child(3) { animation-delay: 0.59s; }
  .conversation-list .conv-card:nth-child(4) { animation-delay: 0.66s; }

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
</head>
<body>

@include('partials.app-nav', ['active' => 'home', 'user' => $user])

<div class="bg-blob b1"></div>
<div class="bg-blob b2"></div>
<div class="bg-blob b3"></div>
<div class="bg-blob b4"></div>

<div class="topbar">

    <div class="topbar-left">
        <div class="logo-icon">
            <x-brand-logo size="36" />
        </div>

        <span class="brand-name">RakanKampus</span>
    </div>

    <div class="topbar-right">

    <a href="{{ route('student.profile') }}" class="avatar-btn" aria-label="Profile">

        @if($user->photo_data)
            <img src="{{ $user->photo_data }}" alt="Profile photo">
        @else
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="8" r="4"></circle>
                <path d="M4 20c0-4 4-6 8-6s8 2 8 6"></path>
            </svg>
        @endif

    </a>

</div>

    </div>

</div>

  <div class="container">

    <!-- Greeting card -->
    <div class="greeting-card">
      <div class="greeting-top">
        <div class="greeting-avatar">
            @if($user->photo_data)
                <img src="{{ $user->photo_data }}" alt="Profile photo">
            @else
                {{ strtoupper(substr($user->first_name ?? 'A', 0, 1) . substr($user->last_name ?? '', 0, 1)) }}
            @endif
        </div>
        <div>
          <p class="greeting-name">{{ $user->first_name }} {{ $user->last_name }}</p>
          <p class="greeting-meta">
              @if($user->student_id){{ $user->student_id }}@endif
              @if($user->student_id && $user->faculty) &middot; @endif
              {{ $user->faculty }}
          </p>
        </div>
      </div>
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
      <p class="greeting-hello"><span id="greetingHello">{{ $greeting }}</span>!</p>
      <p class="greeting-question">How can I help you today?</p>
    </div>

    <a href="{{ route('student.reminders') }}" class="reminders-banner">
      <div class="reminders-banner-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.7 21a2 2 0 0 1-3.4 0"></path></svg>
      </div>
      <div class="reminders-banner-body">
        <p class="reminders-banner-title">Reminders</p>
        <p class="reminders-banner-sub">For exams, assignments &amp; deadlines</p>
      </div>
      <svg viewBox="0 0 24 24" fill="none" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"></path></svg>
    </a>

    <div class="today-classes-card">
      <div class="today-classes-head">
        <div class="today-classes-title-row">
          <svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="17" rx="2"></rect><path d="M3 9h18"></path><path d="M8 3v4"></path><path d="M16 3v4"></path></svg>
          <p class="today-classes-title" id="todayClassesTitle">Today's Classes</p>
        </div>
        <a href="{{ route('student.timetable') }}" class="today-classes-link">View all &rarr;</a>
      </div>

      <div class="today-classes-body" id="todayClassesBody"
           onpointerdown="startClassesDrag(event)" onpointermove="moveClassesDrag(event)"
           onpointerup="endClassesDrag(event)" onpointerleave="endClassesDrag(event)"></div>
    </div>

    <div class="quick-grid">
  <a href="{{ route('student.chat') }}?q={{ urlencode('How do I register for courses?') }}" class="quick-chip">How do I register for courses?</a>
  <a href="{{ route('student.chat') }}?q={{ urlencode('When is the fee payment deadline?') }}" class="quick-chip">When is the fee payment deadline?</a>
  <a href="{{ route('student.chat') }}?q={{ urlencode('How do I access library resources?') }}" class="quick-chip">How do I access library resources?</a>
  <a href="{{ route('student.chat') }}?q={{ urlencode("What's the exam timetable?") }}" class="quick-chip">What's the exam timetable?</a>
</div>

    <a href="{{ route('student.chat') }}" class="start-chat-btn">

    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <line x1="12" y1="5" x2="12" y2="19"></line>
        <line x1="5" y1="12" x2="19" y2="12"></line>
    </svg>

    Start New Chat

</a>

    <p class="section-label">RECENT CONVERSATIONS</p>

    <div class="conversation-list">

    @forelse($conversations as $conv)

        <div class="conv-swipe-wrap">
            <button type="button" class="conv-swipe-delete" aria-label="Delete" onclick="deleteHomeConversationDirect({{ $conv['id'] }})">
                <svg viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                <span>Delete</span>
            </button>
            <div class="conv-card" data-conv-id="{{ $conv['id'] }}" data-conv-title="{{ $conv['title'] }}" data-chat-url="{{ route('student.chat') }}?conversation={{ $conv['id'] }}"
                 onpointerdown="startConvDrag(event, {{ $conv['id'] }})" onpointermove="moveConvDrag(event)" onpointerup="endConvDrag(event, {{ $conv['id'] }})" onpointerleave="endConvDrag(event, {{ $conv['id'] }})">
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
                    <button type="button" class="conv-action-btn" aria-label="Edit" onclick="event.stopPropagation(); renameHomeConversation({{ $conv['id'] }}, {{ Js::from($conv['title']) }})">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.12 2.12 0 0 1 3 3L12 15l-4 1 1-4Z"></path></svg>
                    </button>
                    <button type="button" class="conv-action-btn" aria-label="Delete" onclick="event.stopPropagation(); deleteHomeConversation({{ $conv['id'] }})">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
            </div>
        </div>

    @empty

        <p style="text-align:center; color: var(--text-muted); padding: 24px 0; grid-column: 1/-1;">
            No conversations yet. Start your first chat! 💬
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

    if (dayOffset === 0) titleEl.textContent = "Today's Classes";
    else if (dayOffset === 1) titleEl.textContent = "Tomorrow's Classes";
    else if (dayOffset === -1) titleEl.textContent = "Yesterday's Classes";
    else titleEl.textContent = dayName + "'s Classes";

    const isActualToday = dayOffset === 0;
    const now = new Date();

    const items = classSchedules
        .filter(s => s.day_of_week === dayName)
        .sort((a, b) => a.start_time.localeCompare(b.start_time));

    if (items.length === 0) {
        bodyEl.innerHTML = `<p class="today-classes-empty">${isActualToday ? 'No classes today 🎉' : 'No classes'}</p>`;
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
            || (status === 'ongoing' ? 'Ongoing now' : '');

        return `<div class="today-class-row ${status === 'past' ? 'is-past' : ''} ${status === 'ongoing' ? 'is-ongoing' : ''}">
            <div class="today-class-time">${formatClassTime(s.start_time)}</div>
            <div class="today-class-body">
              <p class="today-class-subject">${escapeHtmlHome(s.subject)}</p>
              <p class="today-class-meta">${meta}</p>
            </div>
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
    if (hour < 5 || hour >= 22) greeting = 'Good night';
    else if (hour < 12) greeting = 'Good morning';
    else if (hour < 18) greeting = 'Good afternoon';
    else greeting = 'Good evening';

    if (el.textContent !== greeting) el.textContent = greeting;
}
updateGreeting();
setInterval(updateGreeting, 60000);

let convDragId = null;
let convDragStartX = null;
let convDragOffset = 0;
let convDragMoved = false;

function startConvDrag(e, id) {
    convDragId = id;
    convDragStartX = e.clientX;
    convDragOffset = 0;
    convDragMoved = false;
}

function moveConvDrag(e) {
    if (convDragId === null) return;
    let delta = e.clientX - convDragStartX;
    if (Math.abs(delta) > 5) convDragMoved = true;
    if (delta < 0) delta = 0;
    if (delta > 90) delta = 90;
    convDragOffset = delta;
    const card = document.querySelector(`.conv-card[data-conv-id="${convDragId}"]`);
    if (card) card.style.transform = `translateX(${convDragOffset}px)`;
}

function endConvDrag(e, id) {
    if (convDragId === null) return;
    const shouldDelete = convDragOffset > 45;
    const moved = convDragMoved;
    convDragId = null;
    convDragStartX = null;
    convDragOffset = 0;
    convDragMoved = false;

    if (shouldDelete) {
        deleteHomeConversationDirect(id);
        return;
    }

    const card = document.querySelector(`.conv-card[data-conv-id="${id}"]`);
    if (card) card.style.transform = 'translateX(0px)';
    if (!moved && card) {
        window.location = card.dataset.chatUrl;
    }
}

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
    })
    .catch(err => console.error('Delete failed', err));
}

function renameHomeConversation(id, currentTitle) {
    const newTitle = prompt('Rename conversation:', currentTitle);
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
    })
    .catch(err => console.error('Rename failed', err));
}

function deleteHomeConversation(id) {
    if (!confirm('Delete this conversation?')) return;

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
    })
    .catch(err => console.error('Delete failed', err));
}
</script>

</body>
</html>