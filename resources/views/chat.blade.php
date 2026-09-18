<!DOCTYPE html>
<html lang="ms">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>RakanKampus - New Conversation</title>
<style>

  :root {
    --blue-primary: #60a5fa;
    --blue-dark: #3355a6;
    --sidebar-bg: #4c3d7a;
    --sidebar-bg-2: #4c1d95;
    --bg-page: #eef0ff;
    --border-light: #ece6fb;
    --text-muted: #8b7fae;
    --input-bg: #ece4fb;
    --input-placeholder: #a998cf;
  }

  * { box-sizing: border-box; }

  .sidebar-user {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 20px;
    border-top: 1px solid rgba(255,255,255,0.08);
    border-bottom: 1px solid rgba(255,255,255,0.08);
    transition: background 0.15s ease;
  }

  .sidebar-user:hover {
    background: rgba(255,255,255,0.06);
  }

  html, body {
    margin: 0;
    height: 100%;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
  }

  body {
    background: linear-gradient(120deg, #60a5fa, #22d3ee, #a78bfa, #60a5fa);
    background-size: 300% 300%;
    animation: gradientShift 15s ease infinite;
  }

  .app {
    display: flex;
    height: 100vh;
    height: 100dvh;
    overflow: hidden;
  }

  .sidebar {
    width: 300px;
    min-width: 300px;
    height: 100vh;
    height: 100dvh;
    overflow: hidden;
    background: linear-gradient(160deg, #24476b, #60a5fa, #24476b);
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

  .sidebar-close-btn {
    margin-left: auto;
    background: none;
    border: none;
    cursor: pointer;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 4px;
    border-radius: 8px;
    flex-shrink: 0;
  }

  .sidebar-close-btn:hover {
    background: rgba(255,255,255,0.1);
  }

  .sidebar-close-btn svg { width: 20px; height: 20px; }

  .sidebar-logo {
    width: 42px;
    height: 42px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    filter: drop-shadow(0 4px 10px rgba(0,0,0,0.25));
  }

  .sidebar-logo svg,
  .sidebar-logo img { width: 100%; height: 100%; }

  .sidebar-brand { font-size: 18px; font-weight: 800; }

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
    overflow: hidden;
  }

  .user-name { font-size: 14px; font-weight: 700; }
  .user-matric { font-size: 12px; color: #a998cf; }

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
    color: rgba(255,255,255,0.85);
    text-shadow: 0 1px 3px rgba(0,0,0,0.35);
    margin-bottom: 8px;
}

  .recent-list {
    flex: 1;
    min-height: 0;
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
    text-shadow: 0 1px 3px rgba(0,0,0,0.35);
    margin: 0 0 2px;
}

  .recent-preview {
    font-size: 12px;
    color: rgba(255,255,255,0.85);
    text-shadow: 0 1px 3px rgba(0,0,0,0.35);
    margin: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

  .recent-item.active .recent-preview { color: #e8e0fa; }

  .sidebar-footer {
    padding: 16px 20px;
    border-top: 1px solid rgba(255,255,255,0.08);
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    font-weight: 600;
    color: #d9cef5;
    cursor: pointer;
    text-decoration: none;
    transition: color 0.15s ease, background 0.15s ease;
  }

  .sidebar-footer:hover {
    color: #ffffff;
    background: rgba(255,255,255,0.06);
  }

  .sidebar-footer svg { width: 16px; height: 16px; stroke: currentColor; }

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

  .hidden { display: none !important; }

.stop-btn {
  background: #ef4444;
}

  .menu-btn svg { width: 22px; height: 22px; stroke: var(--blue-primary); }

  .topbar-title { font-size: 16px; font-weight: 800; color: var(--blue-dark); margin: 0; }
  .topbar-subtitle { font-size: 12.5px; color: #ad9cdb; margin: 0; }

  .empty-state {
    text-align: center;
    max-width: 420px;
    background: rgba(255,255,255,0.88);
    backdrop-filter: blur(8px);
    border-radius: 24px;
    padding: 32px 28px;
    box-shadow: 0 16px 40px rgba(37, 99, 235, 0.18);
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
    color: #ad9cdb;
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

  .chat-area{
    flex:1;
    padding:24px;
    overflow-y:auto;
    display:flex;
    flex-direction:column;
    gap:14px;
  }

  .chat-area:has(.empty-state){
    align-items:center;
    justify-content:center;
  }

 .message{
    max-width:75%;
    padding:14px 18px;
    border-radius:18px;
    line-height:1.5;
    font-size:14px;
    white-space: pre-line;
}

  .message.user{
    align-self:flex-end;
    background:var(--blue-primary);
    color:white;
  }

  .message.bot{
    align-self:flex-start;
    background:white;
    color:var(--blue-dark);
    border:1px solid var(--border-light);
  }

  @media (max-width: 768px) {
    .sidebar {
      position: fixed;
      z-index: 20;
      height: 100vh;
      height: 100dvh;
    }
    .app.sidebar-collapsed .sidebar {
      margin-left: -300px;
    }
    .app:not(.sidebar-collapsed) .sidebar {
      margin-left: 0;
    }
  }
  @media (max-width: 860px) {
    .app {
      height: calc(100vh - 64px);
      height: calc(100dvh - 64px);
    }
  }
  .recent-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
}

.recent-info {
    flex: 1;
    min-width: 0; /* penting supaya text truncate still work */
}

.recent-actions {
    display: flex;
    align-items: center;
    gap: 4px;
    flex-shrink: 0;
}

.recent-action-btn {
    background: transparent;
    border: none;
    color: rgba(255,255,255,0.65);
    cursor: pointer;
    padding: 3px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.recent-action-btn:hover {
    color: #ffffff;
    background: rgba(255,255,255,0.14);
}

.recent-action-btn svg { width: 13px; height: 13px; }

#recentSearchInput::placeholder {
  color: rgba(255,255,255,0.7);
  }

  @keyframes gradientShift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
  }
</style>
</head>
<body>

@include('partials.app-nav', ['active' => 'chat', 'user' => $user])

  <div class="app" id="app">

    <!-- Sidebar -->
    <aside class="sidebar">
      <div class="sidebar-header">
        <div class="sidebar-logo">
          <x-brand-logo size="42" />
        </div>
        <span class="sidebar-brand">RakanKampus</span>
        <button class="sidebar-close-btn" id="sidebarCloseBtn" aria-label="Close sidebar">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
      </div>

      <a href="{{ route('student.profile') }}" class="sidebar-user" style="text-decoration:none; color:inherit; cursor:pointer;">
        <div class="user-avatar">
          @if($user->photo)
            <img src="{{ Storage::url($user->photo) }}" alt="Profile photo" style="width:100%; height:100%; border-radius:50%; object-fit:cover;">
          @else
            {{ strtoupper(substr($user->first_name ?? 'A', 0, 1) . substr($user->last_name ?? '', 0, 1)) }}
          @endif
        </div>
        <div>
          <div class="user-name">{{ $user->first_name }} {{ $user->last_name }}</div>
          <div class="user-matric">{{ $user->student_id }}</div>
        </div>
      </a>

      <button class="new-chat-btn" id="newChatBtn">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
        New Chat
      </button>

      <p class="recent-label">RECENT</p>

      <div style="padding: 0 20px 10px;">
   <input type="text" id="recentSearchInput" placeholder="Search conversation..."
    style="width:100%; padding:8px 12px; border-radius:10px; border:none; background:rgba(0,0,0,0.15); color:#fff; font-size:13px; outline:none;">
</div>

      <div class="recent-list" id="recentList">
        <!-- diisi secara dinamik oleh JavaScript -->
      </div>

      <a href="{{ route('student.home') }}" class="sidebar-footer">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12l9-9 9 9"></path><path d="M5 10v10h14V10"></path></svg>
        Back to home
      </a>
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
      </div>

      <div class="chat-area" id="chatArea">
        <div class="empty-state" id="emptyState">
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
          <input type="text" id="messageInput" placeholder="Ask me anything about university...">
          <button class="send-btn" id="sendBtn" aria-label="Send">
            <svg viewBox="0 0 24 24">
              <path d="M2 21l21-9L2 3v7l15 2-15 2z"/>
            </svg>
          </button>
          <button class="send-btn stop-btn hidden" id="stopBtn" aria-label="Stop">
            <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round">
              <rect x="7" y="7" width="10" height="10" rx="2"></rect>
            </svg>
          </button>
        </div>
      </div>
    </div>
  </div>

  <script>
const app = document.getElementById('app');
const menuBtn = document.getElementById('menuBtn');
const sendBtn = document.getElementById('sendBtn');
const messageInput = document.getElementById('messageInput');
const chatArea = document.getElementById('chatArea');
const newChatBtn = document.getElementById('newChatBtn');
const recentList = document.getElementById('recentList');
let emptyState = document.getElementById('emptyState');
let currentConversationId = null;
const stopBtn = document.getElementById('stopBtn');
let currentController = null;

menuBtn.addEventListener('click', () => {
  app.classList.toggle('sidebar-collapsed');
});

const sidebarCloseBtn = document.getElementById('sidebarCloseBtn');
sidebarCloseBtn.addEventListener('click', () => {
  app.classList.add('sidebar-collapsed');
});

function showEmptyState(){
  chatArea.innerHTML = `
    <div class="empty-state" id="emptyState">
      <div class="empty-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
        </svg>
      </div>
      <h2>How can I help you today?</h2>
      <p>Ask me anything about courses, campus services, fees, library resources, and more.</p>
    </div>`;
  emptyState = document.getElementById('emptyState');
}

function addMessage(text, sender){
  if(emptyState){
    emptyState.remove();
    emptyState = null;
  }
  const msg = document.createElement('div');
  msg.className = `message ${sender}`;
  msg.textContent = text;
  chatArea.appendChild(msg);
  chatArea.scrollTop = chatArea.scrollHeight;
  return msg;
}

function sendMessage(){
  const text = messageInput.value.trim();
  if(text === '') return;

  addMessage(text, 'user');
  messageInput.value = '';

  const typingMsg = addMessage('Typing...', 'bot');
  typingMsg.id = 'typingIndicator';

  currentController = new AbortController();
  sendBtn.classList.add('hidden');
  stopBtn.classList.remove('hidden');

  fetch('/chatbot', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
    },
    body: JSON.stringify({ message: text, conversation_id: currentConversationId }),
    signal: currentController.signal,
  })
    .then(res => res.json())
    .then(data => {
      document.getElementById('typingIndicator')?.remove();

      if (data.reply) {
        addMessage(data.reply, 'bot');
        currentConversationId = data.conversation_id;
        loadHistory();
      } else {
        addMessage('Sorry, there was a problem getting a response. Please try again.', 'bot');
      }
    })
    .catch((error) => {
      document.getElementById('typingIndicator')?.remove();
      if (error.name === 'AbortError') {
        addMessage('(Stopped)', 'bot');
      } else {
        addMessage('Sorry, unable to connect to the server. Please try again.', 'bot');
      }
    })
    .finally(() => {
      sendBtn.classList.remove('hidden');
      stopBtn.classList.add('hidden');
      currentController = null;
    });
}

function loadHistory(){
  fetch('/chatbot/history')
    .then(res => res.json())
    .then(conversations => renderRecentList(conversations))
    .catch(() => {});
}

function escapeHtml(str){
  const div = document.createElement('div');
  div.textContent = str || '';
  return div.innerHTML;
}

function renderRecentList(conversations) {
    recentList.innerHTML = '';

    conversations.forEach(conv => {
        const item = document.createElement('div');
        item.className = 'recent-item' + (conv.id === currentConversationId ? ' active' : '');
        item.innerHTML = `
            <div class="recent-info">
                <p class="recent-title">${escapeHtml(conv.title)}</p>
                <p class="recent-preview">${escapeHtml(conv.preview)}</p>
            </div>
            <div class="recent-actions">
                <button class="recent-action-btn" type="button" data-action="edit" aria-label="Edit">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.12 2.12 0 0 1 3 3L12 15l-4 1 1-4Z"></path></svg>
                </button>
                <button class="recent-action-btn" type="button" data-action="delete" aria-label="Delete">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
        `;

        item.addEventListener('click', () => openConversation(conv.id));

        item.querySelector('[data-action="edit"]').addEventListener('click', (e) => {
            e.stopPropagation();
            renameConversation(conv.id, conv.title);
        });

        item.querySelector('[data-action="delete"]').addEventListener('click', (e) => {
            e.stopPropagation();
            deleteConversation(conv.id);
        });

        recentList.appendChild(item);
    });
}

function renameConversation(id, currentTitle) {
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
    .then(() => loadHistory())
    .catch(err => console.error('Rename failed', err));
}

function deleteConversation(id) {
    if (!confirm('Delete this conversation?')) return;

    fetch(`/chatbot/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
    })
    .then(res => res.json())
    .then(() => {
        if (id === currentConversationId) {
            currentConversationId = null;
            showEmptyState();
        }
        loadHistory();
    })
    .catch(err => console.error('Delete failed', err));
}
function openConversation(id){
  fetch(`/chatbot/${id}`)
    .then(res => res.json())
    .then(messages => {
      chatArea.innerHTML = '';
      emptyState = null;
      currentConversationId = id;

      messages.forEach(m => addMessage(m.message, m.sender));
      loadHistory();
    });
}

newChatBtn.addEventListener('click', () => {
  currentConversationId = null;
  showEmptyState();
  document.querySelectorAll('.recent-item').forEach(el => el.classList.remove('active'));
});

sendBtn.addEventListener('click', sendMessage);
stopBtn.addEventListener('click', () => {
  if (currentController) currentController.abort();
});

messageInput.addEventListener('keypress', function(e){
  if(e.key === 'Enter'){
    sendMessage();
  }
});

const urlParams = new URLSearchParams(window.location.search);
const prefilledQuestion = urlParams.get('q');
const conversationFromUrl = urlParams.get('conversation');

if (prefilledQuestion) {
  messageInput.value = prefilledQuestion;
  sendMessage();
  window.history.replaceState({}, document.title, window.location.pathname);
} else if (conversationFromUrl) {
  openConversation(parseInt(conversationFromUrl));
} else {
  loadHistory();
}

const recentSearchInput = document.getElementById('recentSearchInput');

recentSearchInput.addEventListener('input', () => {
    const query = recentSearchInput.value.toLowerCase().trim();

    document.querySelectorAll('.recent-item').forEach(item => {
        const title = item.querySelector('.recent-title')?.textContent.toLowerCase() || '';
        const preview = item.querySelector('.recent-preview')?.textContent.toLowerCase() || '';

        if (title.includes(query) || preview.includes(query)) {
            item.style.display = 'flex';
        } else {
            item.style.display = 'none';
        }
    });
});
</script>

</body>
</html>