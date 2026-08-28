<!DOCTYPE html>
<html lang="ms">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>RakanKampus - Home</title>
<style>
  :root {
    --blue-primary: #a78bfa;
    --blue-dark: #4c1d95;
    --bg-page: #eef0ff;
    --bg-card: #ffffff;
    --icon-bg: #ede4ff;
    --border-light: #ece6fb;
    --text-muted: #8b7fae;
    --chip-text: #a78bfa;
  }

  * { box-sizing: border-box; }

  html, body {
    margin: 0;
    height: 100%;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    overflow-x: hidden;
  }

  body {
    background: linear-gradient(120deg, #a78bfa, #f472b6, #60a5fa, #a78bfa);
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

  @keyframes waveHand {
    0%, 100% { transform: rotate(0deg); }
    20% { transform: rotate(16deg); }
    40% { transform: rotate(-10deg); }
    60% { transform: rotate(14deg); }
    80% { transform: rotate(-6deg); }
  }

  @keyframes iconWiggle {
    0%, 100% { transform: rotate(0deg); }
    25% { transform: rotate(-10deg); }
    75% { transform: rotate(10deg); }
  }

  @keyframes softPulse {
    0%, 100% { box-shadow: 0 10px 24px rgba(124, 58, 237, 0.30); }
    50% { box-shadow: 0 16px 36px rgba(124, 58, 237, 0.50); }
  }

  .wave-emoji {
    display: inline-block;
    animation: waveHand 2.2s ease-in-out infinite;
    transform-origin: 70% 70%;
  }

  /* Top bar */
  .topbar {
    background: rgba(255, 255, 255, 0.92);
    backdrop-filter: blur(10px);
    border-bottom: 1px solid #e7efff;
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
    border: 1px solid #e5edff;
    box-shadow: 0 4px 12px rgba(59, 99, 238, 0.12);
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
    box-shadow: 0 8px 20px rgba(59, 99, 238, 0.18);
}

.avatar-btn svg {
    width: 20px;
    height: 20px;
    stroke: #3b63ee !important;
}

.avatar-btn img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.profile-link:hover {
    background: #f5f8ff;
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

  /* Greeting card */
  .greeting-card {
    position: relative;
    overflow: hidden;
    background: linear-gradient(120deg, #c084fc 0%, #a78bfa 55%, #f472b6 100%);
    border-radius: 28px;
    padding: 28px 32px;
    margin-bottom: 28px;
    color: #fff;
    box-shadow: 0 16px 40px rgba(41, 84, 229, 0.28);
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
    color: #c9d5fb;
    margin: 0;
  }

  .greeting-hello {
    font-size: 14.5px;
    color: #dbe4fd;
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
    box-shadow: 0 6px 16px rgba(124, 58, 237, 0.16);
    transform: translateY(-2px) scale(1.02);
  }

  .start-chat-btn {
    width: 100%;
    background: linear-gradient(135deg, #a78bfa, #f472b6);
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
    box-shadow: 0 10px 24px rgba(124, 58, 237, 0.30);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    text-decoration: none;
    animation: fadeInUp 0.5s ease 0.35s both, softPulse 2.8s ease-in-out 1.2s infinite;
}

.start-chat-btn:hover {
    transform: translateY(-2px) scale(1.02);
    box-shadow: 0 16px 32px rgba(124, 58, 237, 0.42);
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

  .conv-card {
    background: var(--bg-card);
    border: 1px solid var(--border-light);
    border-radius: 14px;
    padding: 16px 18px;
    display: flex;
    align-items: center;
    gap: 14px;
    cursor: pointer;
    box-shadow: 0 1px 2px rgba(20, 40, 100, 0.04);
    transition: box-shadow 0.15s ease, transform 0.15s ease, border-color 0.15s ease;
    animation: fadeInUp 0.5s ease both;
  }

  .conversation-list .conv-card:nth-child(1) { animation-delay: 0.45s; }
  .conversation-list .conv-card:nth-child(2) { animation-delay: 0.52s; }
  .conversation-list .conv-card:nth-child(3) { animation-delay: 0.59s; }
  .conversation-list .conv-card:nth-child(4) { animation-delay: 0.66s; }

  .conv-card:hover {
    box-shadow: 0 10px 24px rgba(124, 58, 237, 0.14);
    transform: translateY(-2px);
    border-color: #d9cef5;
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
    stroke: #b8c3e0;
  }

  /* Help FAB */
  .help-fab {
    position: fixed;
    bottom: 24px;
    right: 24px;
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: linear-gradient(135deg, #a78bfa, #f472b6);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 18px;
    font-weight: 700;
    border: none;
    cursor: pointer;
    box-shadow: 0 6px 16px rgba(124, 58, 237, 0.35);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    animation: fadeInUp 0.5s ease 0.8s both;
  }

  .help-fab:hover {
    transform: scale(1.1) rotate(-8deg);
    box-shadow: 0 10px 22px rgba(124, 58, 237, 0.45);
  }

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

        @if($user->photo)
            <img src="{{ Storage::url($user->photo) }}" alt="Profile photo">
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
            @if($user->photo)
                <img src="{{ Storage::url($user->photo) }}" alt="Profile photo">
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
          $hour = now()->hour;
          if ($hour < 12) {
              $greeting = 'Good morning';
          } elseif ($hour < 18) {
              $greeting = 'Good afternoon';
          } else {
              $greeting = 'Good evening';
          }
      @endphp
      <p class="greeting-hello">{{ $greeting }}! <span class="wave-emoji">👋</span></p>
      <p class="greeting-question">How can I help you today?</p>
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

        <a href="{{ route('student.chat') }}?conversation={{ $conv['id'] }}" class="conv-card">
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
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
            </div>
        </a>

    @empty

        <p style="text-align:center; color: var(--text-muted); padding: 24px 0; grid-column: 1/-1;">
            Belum ada perbualan lagi. Mula chat pertama anda! 💬
        </p>

    @endforelse

</div>

  </div>

  <button class="help-fab" aria-label="Help">?</button>

</body>
</html>