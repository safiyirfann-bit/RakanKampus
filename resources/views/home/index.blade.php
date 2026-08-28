@extends('layouts.app')


@section('content')


<style>
<style>
  :root {
    --blue-primary: #2954e5;
    --blue-dark: #1a2e6e;
    --bg-page: #eaf0fc;
    --bg-card: #ffffff;
    --icon-bg: #dbe6fd;
    --border-light: #e7edfb;
    --text-muted: #7c8bb5;
    --chip-text: #2954e5;
  }

  * { box-sizing: border-box; }

  html, body {
    margin: 0;
    height: 100%;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    background: var(--bg-page);
  }

  /* Top bar */
  .topbar {
    background: #ffffff;
    border-bottom: 1px solid var(--border-light);
    padding: 16px 32px;
    display: flex;
    align-items: center;
    justify-content: space-between;
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
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--icon-bg);
    display: flex;
    align-items: center;
    justify-content: center;
    border: none;
    cursor: pointer;
  }

  .avatar-btn svg {
    width: 20px;
    height: 20px;
    stroke: var(--blue-primary);
  }

  /* Main content */
  .container {
    max-width: 600px;
    margin: 0 auto;
    padding: 32px 16px 64px;
  }

  /* Greeting card */
  .greeting-card {
    position: relative;
    overflow: hidden;
    background: linear-gradient(120deg, #3b63ee 0%, #2954e5 55%, #1d3fd6 100%);
    border-radius: 20px;
    padding: 24px 28px;
    margin-bottom: 24px;
    color: #fff;
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
    transition: box-shadow 0.15s ease, transform 0.1s ease;
  }

  .quick-chip:hover {
    box-shadow: 0 4px 12px rgba(20, 40, 100, 0.08);
    transform: translateY(-1px);
  }

  .start-chat-btn {
    width: 100%;
    background: var(--blue-primary);
    color: #fff;
    border: none;
    border-radius: 14px;
    padding: 18px;
    font-size: 16px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    cursor: pointer;
    margin-bottom: 32px;
    box-shadow: 0 6px 16px rgba(41, 84, 229, 0.35);
    transition: filter 0.15s ease;
  }

  .start-chat-btn:hover {
    filter: brightness(1.05);
  }

  .start-chat-btn svg {
    width: 18px;
    height: 18px;
  }

  .section-label {
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.08em;
    color: var(--blue-primary);
    margin: 0 0 16px 4px;
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
    transition: box-shadow 0.15s ease, transform 0.1s ease;
  }

  .conv-card:hover {
    box-shadow: 0 4px 14px rgba(20, 40, 100, 0.08);
    transform: translateY(-1px);
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
    background: #1a2033;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 18px;
    font-weight: 700;
    border: none;
    cursor: pointer;
    box-shadow: 0 6px 16px rgba(0,0,0,0.25);
  }

  @media (max-width: 480px) {
    .quick-grid {
      grid-template-columns: 1fr;
    }
    .topbar {
      padding: 14px 20px;
    }
  }
</style>

</head>
<body>

  <div class="topbar">
    <div class="topbar-left">
      <div class="logo-icon">
        <x-brand-logo size="36" />
      </div>
      <span class="brand-name">RakanKampus</span>
    </div>
    <button class="avatar-btn" aria-label="Profile">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="12" cy="8" r="4"></circle>
        <path d="M4 20c0-4 4-6 8-6s8 2 8 6"></path>
      </svg>
    </button>
  </div>

  <div class="container">

    <!-- Greeting card -->
    <div class="greeting-card">
      <div class="greeting-top">
        <div class="greeting-avatar">AR</div>
        <div>
          <p class="greeting-name">Ahmad Razif</p>
          <p class="greeting-meta">D157EKP1085 · Computer Science</p>
        </div>
      </div>
      <p class="greeting-hello">Good morning! 👋</p>
      <p class="greeting-question">How can I help you today?</p>
    </div>

    <div class="quick-grid">
      <button class="quick-chip">How do I register for courses?</button>
      <button class="quick-chip">When is the fee payment deadline?</button>
      <button class="quick-chip">How do I access library resources?</button>
      <button class="quick-chip">What's the exam timetable?</button>
    </div>

    <a href="{{ route('chat') }}" class="start-chat-btn">

  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
      <line x1="12" y1="5" x2="12" y2="19"></line>
      <line x1="5" y1="12" x2="19" y2="12"></line>
  </svg>

  Start New Chat

</a>

    <p class="section-label">RECENT CONVERSATIONS</p>

    <div class="conversation-list">

      <div class="conv-card">
        <div class="conv-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
          </svg>
        </div>
        <div class="conv-body">
          <p class="conv-title">Course Registration Help</p>
          <p class="conv-preview">When does registration open for next semester?</p>
        </div>
        <div class="conv-meta">
          <span class="conv-time">10:24 AM</span>
          <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
        </div>
      </div>

      <div class="conv-card">
        <div class="conv-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
          </svg>
        </div>
        <div class="conv-body">
          <p class="conv-title">Library Resources</p>
          <p class="conv-preview">How do I access the library's e-journals?</p>
        </div>
        <div class="conv-meta">
          <span class="conv-time">Yesterday</span>
          <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
        </div>
      </div>

      <div class="conv-card">
        <div class="conv-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
          </svg>
        </div>
        <div class="conv-body">
          <p class="conv-title">Exam Schedule Query</p>
          <p class="conv-preview">Where can I find the final exam timetable?</p>
        </div>
        <div class="conv-meta">
          <span class="conv-time">Mon</span>
          <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
        </div>
      </div>

      <div class="conv-card">
        <div class="conv-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
          </svg>
        </div>
        <div class="conv-body">
          <p class="conv-title">Scholarship Application</p>
          <p class="conv-preview">What scholarships are available for CS students?</p>
        </div>
        <div class="conv-meta">
          <span class="conv-time">Sun</span>
          <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
        </div>
      </div>

    </div>

  </div>

  <button class="help-fab" aria-label="Help">?</button>

</body>
</html>

<div class="topbar">

    <div class="topbar-left">

        <div class="logo-icon">
            Logo
        </div>

        <span class="brand-name">
            RakanKampus
        </span>

    </div>

</div>



<div class="container">


    <div class="greeting-card">

        <h1>
        Welcome {{ auth()->user()->name }}
        </h1>


    </div>


</div>
@endsection