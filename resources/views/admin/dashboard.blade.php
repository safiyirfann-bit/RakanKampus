<!DOCTYPE html>
<html lang="ms">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard - RakanKampus</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tabler-icons/3.44.0/tabler-icons.min.css">

<style>
body{
    margin:0;
    background: linear-gradient(120deg, #2a5f59, #2dd4bf, #3355a6, #2a5f59);
    background-size: 300% 300%;
    animation: gradientShift 15s ease infinite;
    color:white;
    font-family:Arial,Helvetica,sans-serif;
}

.header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:20px 32px;
    border-bottom:1px solid #1c2746;
    flex-wrap:wrap;
    gap:16px;
}

.logo{
    display:flex;
    align-items:center;
    gap:14px;
}

.logo-box{
    width:48px;
    height:48px;
    border-radius:14px;
    background:#c084fc;
    color:#081124;
    display:flex;
    align-items:center;
    justify-content:center;
    font-weight:900;
    flex-shrink:0;
}

.logo h1{
    margin:0;
    font-size:22px;
}

.logo h1 span{
    color:#c084fc;
}

.logo p{
    margin:2px 0 0;
    color:#8b96b8;
    font-size:13px;
}

.header-actions{
    display:flex;
    align-items:center;
    gap:12px;
}

.inbox-btn{
    width:44px;
    height:44px;
    border-radius:12px;
    background:#121a33;
    border:1px solid #273150;
    display:flex;
    align-items:center;
    justify-content:center;
    position:relative;
    cursor:pointer;
    transition:.2s;
    text-decoration:none;
}

.inbox-btn:hover{
    border-color:#c084fc;
}

.inbox-btn .icon{
    width:20px;
    height:20px;
    color:#cbd5e1;
}

.badge-count{
    position:absolute;
    top:-6px;
    right:-6px;
    width:20px;
    height:20px;
    border-radius:9999px;
    background:#ef4444;
    color:white;
    font-size:11px;
    font-weight:700;
    display:flex;
    align-items:center;
    justify-content:center;
}

.logout{
    background:#121a33;
    border:1px solid #273150;
    color:white;
    padding:11px 18px;
    border-radius:12px;
    font-weight:700;
    cursor:pointer;
    display:flex;
    align-items:center;
    gap:6px;
    font-size:14px;
}

.logout:hover{
    border-color:rgba(240,117,122,.4);
    color:#f0757a;
}

.container{
    max-width:1200px;
    margin:auto;
    padding:32px;
}

.top-row{
    display:flex;
    gap:14px;
    margin-bottom:20px;
    flex-wrap:wrap;
}

.stat-card{
    background:#121a33;
    border:1px solid #273150;
    border-radius:16px;
    padding:16px 20px;
    flex:1;
    min-width:140px;
}

.stat-card p.label{
    font-size:12px;
    color:#8b96b8;
    margin:0 0 4px;
    text-transform:uppercase;
    letter-spacing:.05em;
}

.stat-card p.value{
    font-size:24px;
    font-weight:800;
    margin:0;
    color:#c084fc;
}

.search-card{
    background:#121a33;
    border:1px solid #273150;
    border-radius:16px;
    padding:16px 20px;
    flex:2.5;
    min-width:240px;
    display:flex;
    align-items:center;
    gap:10px;
}

.search-card i{
    font-size:18px;
    color:#8b96b8;
}

.search-card input{
    border:none;
    background:transparent;
    color:white;
    font-size:14px;
    width:100%;
    outline:none;
}

.search-card input::placeholder{
    color:#5f6a8a;
}

.add-btn{
    background:#c084fc;
    color:#081124;
    border:none;
    padding:14px 22px;
    border-radius:14px;
    font-weight:800;
    cursor:pointer;
    margin-bottom:20px;
    display:flex;
    align-items:center;
    gap:8px;
    font-size:15px;
}

.grid{
    display:flex;
    flex-direction:column;
    gap:12px;
}

.card{
    background:#121a33;
    border:1px solid #273150;
    border-radius:16px;
    padding:16px 20px;
    display:flex;
    align-items:center;
    gap:16px;
    flex-wrap:wrap;
    transition:.2s;
}

.card:hover{
    border-color:#c084fc;
}

.card-icon{
    width:44px;
    height:44px;
    border-radius:12px;
    background:rgba(31,216,143,.12);
    display:flex;
    align-items:center;
    justify-content:center;
    flex-shrink:0;
    color:#c084fc;
    font-size:20px;
}

.card-info{
    flex:1;
    min-width:200px;
}

.card-info h2{
    margin:0 0 2px;
    font-size:17px;
    font-weight:700;
}

.card-info p{
    margin:0;
    color:#8b96b8;
    font-size:13px;
    overflow:hidden;
    text-overflow:ellipsis;
    white-space:nowrap;
}

.card-actions{
    display:flex;
    gap:6px;
    flex-shrink:0;
}

.icon-btn{
    width:36px;
    height:36px;
    display:flex;
    align-items:center;
    justify-content:center;
    border:none;
    border-radius:9px;
    cursor:pointer;
    font-size:16px;
    background:#1a2550;
    color:#cbd5e1;
    text-decoration:none;
}

.icon-btn:hover{ background:#22305f; }
.icon-btn.view-btn{ color:#a78bfa; }
.icon-btn.edit-btn{ color:#f5c563; }
.icon-btn.delete-btn{ color:#f0757a; }
.icon-btn.delete-btn:hover{ background:rgba(240,117,122,.15); }

.modal{
    display:none;
    position:fixed;
    inset:0;
    background:rgba(0,0,0,.6);
    z-index:999;
    align-items:center;
    justify-content:center;
}

.modal-content{
    width:90%;
    max-width:600px;
    background:#07133a;
    border:1px solid rgba(255,255,255,.08);
    border-radius:24px;
    padding:24px;
}

.modal-header{
    display:flex;
    align-items:center;
    justify-content:space-between;
    margin-bottom:20px;
}

.modal-header h2{
    color:#fff;
    margin:0;
}

.close-btn{
    background:none;
    border:none;
    color:#c4b5fd;
    font-size:30px;
    cursor:pointer;
}

.form-group{
    margin-bottom:18px;
}

.form-group label{
    display:block;
    color:#c4b5fd;
    margin-bottom:8px;
    font-size:14px;
    font-weight:700;
    letter-spacing:.08em;
    text-transform:uppercase;
}

.form-group input,
.form-group textarea{
    width:100%;
    padding:16px 18px;
    border-radius:16px;
    border:1px solid rgba(255,255,255,.08);
    background:#13214d;
    color:#fff;
    font-size:16px;
    outline:none;
    box-sizing:border-box;
}

.modal-actions{
    display:flex;
    gap:14px;
    margin-top:24px;
}

.confirm-icon{
    width:64px; height:64px; margin:0 auto 16px;
    border-radius:50%;
    background:rgba(240,117,122,.15);
    display:flex; align-items:center; justify-content:center;
    color:#f0757a;
}
.confirm-icon svg{ width:28px; height:28px; }
.confirm-title{ color:#fff; font-size:19px; font-weight:800; margin:0 0 8px; text-align:center; }
.confirm-message{ color:#8b96b8; font-size:14px; margin:0; text-align:center; line-height:1.5; }
.delete-confirm-btn{
    flex:1; padding:16px; border:none; border-radius:16px;
    font-size:16px; font-weight:700; cursor:pointer;
    background:#ef4444; color:#fff;
}
.delete-confirm-btn:hover{ background:#dc2626; }

.cancel-btn,
.submit-btn{
    flex:1;
    padding:16px;
    border:none;
    border-radius:16px;
    font-size:16px;
    font-weight:700;
    cursor:pointer;
}

.cancel-btn{
    background:#0d183f;
    color:#fff;
    border:1px solid rgba(255,255,255,.08);
}

.submit-btn{
    background:#c084fc;
    color:#4c3d7a;
}

.no-results{
    color:#8b96b8;
    text-align:center;
    padding:24px;
    display:none;
}

.status-msg{
    background:rgba(31,216,143,.1);
    border:1px solid rgba(31,216,143,.35);
    color:#c084fc;
    padding:12px 18px;
    border-radius:12px;
    margin-bottom:20px;
    font-size:14px;
}

@media (max-width: 768px){
    .header{
        padding:16px 20px;
    }

    .container{ padding:16px; }

    .top-row{
        flex-direction:column;
    }

    .stat-card,
    .search-card{
        min-width:100%;
        flex:none;
    }

    .add-btn{ width:100%; justify-content:center; }

    .card{
        flex-direction:column;
        align-items:flex-start;
    }

    .card-actions{
        width:100%;
        justify-content:flex-end;
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

<div class="header">

    <div class="logo">
        <x-brand-logo size="48" />
        <div>
            <h1>Administrator <span>RakanKampus</span></h1>
            <p>Content Management System</p>
        </div>
    </div>

    <!-- Right Actions -->
    <div class="header-actions">

        <!-- Feedback Inbox -->
        <a href="{{ route('admin.inbox') }}" class="inbox-btn" aria-label="Feedback inbox">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8m-2 10H5a2 2 0 01-2-2V8a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2z"/>
            </svg>
            <span class="badge-count" id="feedbackBadge">3</span>
        </a>

        <!-- Logout -->
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="logout" type="submit">
                <i class="ti ti-logout" aria-hidden="true"></i>
                Logout
            </button>
        </form>

    </div>

</div>


<div class="container">

    @if(session('status'))
        <div class="status-msg">{{ session('status') }}</div>
    @endif

    <div class="top-row">
        <div class="stat-card">
            <p class="label">Total Topics</p>
            <p class="value">{{ $informations->count() }}</p>
        </div>

        <div class="search-card">
            <i class="ti ti-search" aria-hidden="true"></i>
            <input type="text" placeholder="Search topics..." id="cardSearchInput" onkeyup="filterCards()">
        </div>
    </div>

    <button class="add-btn" onclick="openModal()">
        <i class="ti ti-plus" aria-hidden="true"></i>
        Add more information
    </button>

    <!-- Add Information Modal -->
    <div id="addModal" class="modal">
        <div class="modal-content">

            <div class="modal-header">
                <h2>Add New Information</h2>
                <button type="button" class="close-btn" onclick="closeModal()">&times;</button>
            </div>

            <form action="{{ route('admin.information.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label>Main Topic</label>
                    <input type="text" name="main_topic"
                           placeholder="e.g. Course Registration" required>
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="6"
                              placeholder="Write the description here..." required></textarea>
                </div>

                <div class="modal-actions">
                    <button type="button" class="cancel-btn" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="submit-btn">Submit</button>
                </div>

            </form>

        </div>
    </div>

    <!-- Edit Information Modal -->
    <div id="editInfoModal" class="modal">
        <div class="modal-content">

            <div class="modal-header">
                <h2>Edit Information</h2>
                <button type="button" class="close-btn" onclick="closeEditInfoModal()">&times;</button>
            </div>

            <form id="editInfoForm" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label>Main Topic</label>
                    <input type="text" name="main_topic" id="edit_info_main_topic" required>
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" id="edit_info_description" rows="6" required></textarea>
                </div>

                <div class="modal-actions">
                    <button type="button" class="cancel-btn" onclick="closeEditInfoModal()">Cancel</button>
                    <button type="submit" class="submit-btn">Save</button>
                </div>

            </form>

        </div>
    </div>

    <div class="grid" id="cardsGrid">

        @foreach($informations as $info)
            <div class="card">
                <div class="card-icon">
                    <i class="ti ti-file-text" aria-hidden="true"></i>
                </div>

                <div class="card-info">
                    <h2>{{ $info->main_topic }}</h2>
                    <p>{{ $info->description }}</p>
                </div>

                <div class="card-actions">
                    <a href="{{ route('admin.information.show', $info->id) }}" class="icon-btn view-btn" aria-label="View">
                        <i class="ti ti-eye" aria-hidden="true"></i>
                    </a>

                    <button type="button" class="icon-btn edit-btn" aria-label="Edit"
                        onclick="openEditInfoModal({{ $info->id }}, {{ json_encode($info->main_topic) }}, {{ json_encode($info->description) }})">
                        <i class="ti ti-edit" aria-hidden="true"></i>
                    </button>

                    <form method="POST" action="{{ route('admin.information.destroy', $info->id) }}" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="icon-btn delete-btn" aria-label="Delete"
                            onclick="openDeleteModal(this.closest('form'), {{ json_encode('Delete '.$info->main_topic.'?') }}, 'This will also delete all its knowledge base entries. This cannot be undone.')">
                            <i class="ti ti-trash" aria-hidden="true"></i>
                        </button>
                    </form>
                </div>
            </div>
        @endforeach

    </div>

    <p class="no-results" id="noResultsMsg">No matching results found.</p>

</div>

<!-- Delete Confirm Modal -->
<div id="deleteConfirmModal" class="modal">
    <div class="modal-content" style="max-width:380px; text-align:center;">
        <div class="confirm-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="3 6 5 6 21 6"></polyline>
                <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6m5 0V4a2 2 0 0 1 2-2h0a2 2 0 0 1 2 2v2"></path>
                <line x1="10" y1="11" x2="10" y2="17"></line>
                <line x1="14" y1="11" x2="14" y2="17"></line>
            </svg>
        </div>
        <h2 class="confirm-title" id="deleteConfirmTitle">Delete this item?</h2>
        <p class="confirm-message" id="deleteConfirmMessage">This action cannot be undone.</p>
        <div class="modal-actions">
            <button type="button" class="cancel-btn" onclick="closeDeleteModal()">Cancel</button>
            <button type="button" class="delete-confirm-btn" onclick="confirmDeleteAction()">Delete</button>
        </div>
    </div>
</div>

<script>
  let __deleteFormTarget = null;

  function openDeleteModal(formEl, title, message) {
      __deleteFormTarget = formEl;
      document.getElementById('deleteConfirmTitle').textContent = title || 'Delete this item?';
      document.getElementById('deleteConfirmMessage').textContent = message || 'This action cannot be undone.';
      document.getElementById('deleteConfirmModal').style.display = 'flex';
  }

  function closeDeleteModal() {
      document.getElementById('deleteConfirmModal').style.display = 'none';
      __deleteFormTarget = null;
  }

  function confirmDeleteAction() {
      if (__deleteFormTarget) __deleteFormTarget.submit();
      closeDeleteModal();
  }

  window.addEventListener('click', function(e) {
      const m = document.getElementById('deleteConfirmModal');
      if (e.target === m) closeDeleteModal();
  });
</script>

<script>
  // Same data as feedback-inbox.html, kept here only to compute the unread badge.
  const feedbackItems = [
    { unread: true },
    { unread: true },
    { unread: true },
    { unread: false },
    { unread: false },
    { unread: true },
    { unread: false },
  ];

  function updateBadgeCount() {
    const unreadCount = feedbackItems.filter(item => item.unread).length;
    const badge = document.getElementById("feedbackBadge");
    if (unreadCount > 0) {
      badge.textContent = unreadCount;
      badge.style.display = "flex";
    } else {
      badge.style.display = "none";
    }
  }

  updateBadgeCount();

  function openModal() {
      document.getElementById('addModal').style.display = 'flex';
  }

  function closeModal() {
      document.getElementById('addModal').style.display = 'none';
  }

  function openEditInfoModal(id, mainTopic, description) {
      document.getElementById('edit_info_main_topic').value = mainTopic;
      document.getElementById('edit_info_description').value = description;

      document.getElementById('editInfoForm').action =
          "{{ route('admin.information.update', '__ID__') }}".replace('__ID__', id);

      document.getElementById('editInfoModal').style.display = 'flex';
  }

  function closeEditInfoModal() {
      document.getElementById('editInfoModal').style.display = 'none';
  }

  window.onclick = function(event) {
      const addModal = document.getElementById('addModal');
      const editInfoModal = document.getElementById('editInfoModal');
      if (event.target === addModal) closeModal();
      if (event.target === editInfoModal) closeEditInfoModal();
  }

  function filterCards() {
      const query = document.getElementById('cardSearchInput').value.toLowerCase();
      const cards = document.querySelectorAll('#cardsGrid > .card');
      const noResultsMsg = document.getElementById('noResultsMsg');

      let visibleCount = 0;

      cards.forEach(card => {
          const matches = card.textContent.toLowerCase().includes(query);
          card.style.display = matches ? 'flex' : 'none';
          if (matches) visibleCount++;
      });

      noResultsMsg.style.display = visibleCount === 0 ? 'block' : 'none';
  }
</script>

</body>
</html>