@extends('layouts.app')


@section('content')

<style>
  :root {
    --blue-primary: #2954e5;
    --blue-dark: #1a2e6e;
    --sidebar-bg: #0f1f4d;
    --sidebar-bg-2: #14265c;
    --bg-page: #eef2fb;
    --border-light: #e7edfb;
    --text-muted: #8493c2;
    --input-bg: #dfe8fb;
    --input-placeholder: #9fb0dc;
  }

  * { box-sizing: border-box; }

  html, body {
    margin: 0;
    height: 100%;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    background: var(--bg-page);
  }

  .app {
    display: flex;
    height: 100vh;
    overflow: hidden;
  }

  /* ---------- Sidebar ---------- */
  .sidebar {
    width: 300px;
    min-width: 300px;
    background: linear-gradient(180deg, var(--sidebar-bg), var(--sidebar-bg-2));
    color: #fff;
    display: flex;
    flex-direction: column;
    transform: translateX(0);
    transition: transform 0.25s ease, margin-left 0.25s ease;
  }

  .app.sidebar-collapsed .sidebar {
    margin-left: -300px;
  }

  .sidebar-header {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 20px 20px 16px;
  }

  .sidebar-logo {
    width: 36px;
    height: 36px;
    border-radius: 9px;
    background: var(--blue-primary);
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .sidebar-logo svg { width: 18px; height: 18px; fill: #fff; }

  .sidebar-brand { font-size: 18px; font-weight: 800; }

  .sidebar-user {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 20px;
    border-top: 1px solid rgba(255,255,255,0.08);
    border-bottom: 1px solid rgba(255,255,255,0.08);
  }

  .user-avatar {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: var(--blue-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    font-weight: 700;
    flex-shrink: 0;
  }

  .user-name { font-size: 14px; font-weight: 700; }
  .user-matric { font-size: 12px; color: #9fb0dc; }

  .new-chat-btn {
    margin: 16px 20px;
    background: var(--blue-primary);
    border: none;
    border-radius: 10px;
    color: #fff;
    font-size: 14px;
    font-weight: 700;
    padding: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    cursor: pointer;
  }

  .new-chat-btn svg { width: 15px; height: 15px; }

  .recent-label {
    padding: 0 20px;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.08em;
    color: #7d8fc4;
    margin-bottom: 8px;
  }

  .recent-list {
    flex: 1;
    overflow-y: auto;
    padding: 0 12px;
    display: flex;
    flex-direction: column;
    gap: 4px;
  }

  .recent-item {
    padding: 10px 8px;
    border-radius: 10px;
    cursor: pointer;
  }

  .recent-item.active {
    background: var(--blue-primary);
  }

  .recent-item:not(.active):hover {
    background: rgba(255,255,255,0.06);
  }

  .recent-title {
    font-size: 13.5px;
    font-weight: 700;
    color: #fff;
    margin: 0 0 2px;
  }

  .recent-preview {
    font-size: 12px;
    color: #a7b6e0;
    margin: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .recent-item.active .recent-preview { color: #dbe4fb; }

  .sidebar-footer {
    padding: 16px 20px;
    border-top: 1px solid rgba(255,255,255,0.08);
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    font-weight: 600;
    color: #c4cff0;
    cursor: pointer;
  }

  .sidebar-footer svg { width: 16px; height: 16px; }

  /* ---------- Main ---------- */
  .main {
    flex: 1;
    display: flex;
    flex-direction: column;
    min-width: 0;
  }

  .topbar {
    background: #fff;
    border-bottom: 1px solid var(--border-light);
    padding: 14px 24px;
    display: flex;
    align-items: center;
    gap: 14px;
  }

  .menu-btn {
    background: none;
    border: none;
    cursor: pointer;
    padding: 4px;
    display: flex;
  }

  .menu-btn svg { width: 22px; height: 22px; stroke: var(--blue-primary); }

  .topbar-title { font-size: 16px; font-weight: 800; color: var(--blue-dark); margin: 0; }
  .topbar-subtitle { font-size: 12.5px; color: #8ea1d6; margin: 0; }

  .info-btn {
    margin-left: auto;
    background: none;
    border: none;
    cursor: pointer;
  }

  .info-btn svg { width: 20px; height: 20px; stroke: var(--blue-primary); }

  .chat-area {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 24px;
    overflow-y: auto;
  }

  .empty-state {
    text-align: center;
    max-width: 420px;
  }

  .empty-icon {
    width: 64px;
    height: 64px;
    border-radius: 16px;
    background: var(--input-bg);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
  }

  .empty-icon svg { width: 30px; height: 30px; stroke: var(--blue-primary); }

  .empty-state h2 {
    font-size: 22px;
    font-weight: 800;
    color: var(--blue-dark);
    margin: 0 0 10px;
  }

  .empty-state p {
    font-size: 14.5px;
    color: #8ea1d6;
    line-height: 1.5;
    margin: 0;
  }

  .input-bar {
    padding: 16px 24px 10px;
    border-top: 1px solid var(--border-light);
    background: #fff;
  }

  .input-row {
    display: flex;
    align-items: center;
    background: var(--input-bg);
    border-radius: 999px;
    padding: 6px 8px 6px 20px;
    gap: 10px;
  }

  .input-row input {
    flex: 1;
    border: none;
    outline: none;
    background: transparent;
    font-size: 14.5px;
    color: var(--blue-dark);
    padding: 10px 0;
  }

  .input-row input::placeholder { color: var(--input-placeholder); }

  .mic-btn {
    background: none;
    border: none;
    cursor: pointer;
    display: flex;
    padding: 6px;
  }

  .mic-btn svg { width: 19px; height: 19px; stroke: var(--blue-primary); }

  .send-btn {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: var(--blue-primary);
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    flex-shrink: 0;
  }

  .send-btn svg { width: 16px; height: 16px; fill: #fff; }

  .disclaimer {
    text-align: center;
    font-size: 11.5px;
    color: #9fb0dc;
    padding: 8px 0 12px;
  }

  @media (max-width: 768px) {
    .sidebar {
      position: fixed;
      z-index: 20;
      height: 100vh;
    }
    .app.sidebar-collapsed .sidebar {
      margin-left: -300px;
    }
    .app:not(.sidebar-collapsed) .sidebar {
      margin-left: 0;
    }
  }
</style>


  <div class="app sidebar-collapsed" id="app">

    <!-- Sidebar -->
    <aside class="sidebar">
      <div class="sidebar-header">
        <div class="sidebar-logo">
          <svg viewBox="0 0 24 24"><polygon points="12,2 20,7 20,17 12,22 4,17 4,7"/></svg>
        </div>
        <span class="sidebar-brand">RakanKampus</span>
      </div>

      <div class="sidebar-user">
        <div class="user-avatar">AR</div>
        <div>
          <div class="user-name">Ahmad Razif</div>
          <div class="user-matric">D157EKP1085</div>
        </div>
      </div>

      <button class="new-chat-btn">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
        New Chat
      </button>

      <p class="recent-label">RECENT</p>

      <div class="recent-list">
        <div class="recent-item active">
          <p class="recent-title">New Conversation</p>
          <p class="recent-preview">Start chatting with RakanKampus...</p>
        </div>
        <div class="recent-item">
          <p class="recent-title">Course Registration Help</p>
          <p class="recent-preview">When does registration open for next semester?</p>
        </div>
        <div class="recent-item">
          <p class="recent-title">Library Resources</p>
          <p class="recent-preview">How do I access the library's e-journals?</p>
        </div>
        <div class="recent-item">
          <p class="recent-title">Exam Schedule Query</p>
          <p class="recent-preview">Where can I find the final exam timetable?</p>
        </div>
        <div class="recent-item">
          <p class="recent-title">Scholarship Application</p>
          <p class="recent-preview">What scholarships are available for CS students?</p>
        </div>
      </div>

      <div class="sidebar-footer">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12l9-9 9 9"></path><path d="M5 10v10h14V10"></path></svg><a href="homepage.html">Back to home</a>
      </div>
    </aside>

    <!-- Main -->
    <div class="main">
      <div class="topbar">
        <button class="menu-btn" id="menuBtn" aria-label="Toggle sidebar">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="4" y1="7" x2="20" y2="7"></line><line x1="4" y1="12" x2="20" y2="12"></line><line x1="4" y1="17" x2="20" y2="17"></line></svg>
        </button>
        <div>
          <p class="topbar-title">New Conversation</p>
          <p class="topbar-subtitle">RakanKampus AI · Politeknik Assistant</p>
        </div>
        <button class="info-btn" aria-label="Info">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
        </button>
      </div>

      <div class="chat-area">
        <div class="empty-state">
          <div class="empty-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
            </svg>
          </div>
          <h2>How can I help you today?</h2>
          <p>Ask me anything about courses, campus services, fees, library resources, and more.</p>
        </div>
      </div>

      <div class="input-bar">
        <div class="input-row">
          <input type="text" placeholder="Ask me anything about university...">
          <button class="mic-btn" aria-label="Voice input">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"></path>
              <path d="M19 10v2a7 7 0 0 1-14 0v-2"></path>
              <line x1="12" y1="19" x2="12" y2="23"></line>
            </svg>
          </button>
          <button class="send-btn" aria-label="Send">
            <svg viewBox="0 0 24 24"><path d="M2 21l21-9L2 3v7l15 2-15 2z"/></svg>
          </button>
        </div>
        <p class="disclaimer">RakanKampus AI may make mistakes. Verify important information with university staff.</p>
      </div>
    </div>

  </div>

  <script>
    const app = document.getElementById('app');
    const menuBtn = document.getElementById('menuBtn');
    menuBtn.addEventListener('click', () => {
      app.classList.toggle('sidebar-collapsed');
    });
  </script>
  @endsection