<!DOCTYPE html>
<html lang="ms">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $information->main_topic }} - RakanKampus</title>

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
    padding:24px 32px;
    border-bottom:1px solid #1c2746;
}

.logo{ display:flex; align-items:center; gap:14px; }

.logo-box{
    width:52px; height:52px; border-radius:14px;
    background:#c084fc; color:#081124;
    display:flex; align-items:center; justify-content:center;
    font-weight:900;
}

.logo h1{ margin:0; font-size:28px; }
.logo h1 span{ color:#c084fc; }
.logo p{ margin:4px 0 0; color:#8b96b8; }

.logout{
    background:#121a33; border:1px solid #273150; color:white;
    padding:12px 18px; border-radius:12px; font-weight:700; cursor:pointer;
}

.stats{ display:flex; gap:14px; align-items:center; }

.stat-box{
    background:#121a33; border:1px solid #273150;
    padding:10px 18px; border-radius:14px; text-align:center;
}

.stat-box strong{ display:block; font-size:20px; color:#c084fc; }
.stat-box span{ font-size:12px; color:#8b96b8; }

.container{ max-width:1200px; margin:auto; padding:32px; }

.page-title{ display:flex; align-items:center; gap:16px; margin-bottom:6px; }
.page-title a{ color:#c4b5fd; text-decoration:none; font-size:22px; }
.page-title h2{ margin:0; font-size:26px; }
.container > p.subtitle{ color:#8b96b8; margin:0 0 24px 38px; }

.topbar{
    display:flex; justify-content:space-between; align-items:center;
    gap:16px; margin-bottom:24px; flex-wrap:wrap;
}

.add-btn{
    background:#c084fc; color:#081124; border:none;
    padding:14px 22px; border-radius:14px; font-weight:800; cursor:pointer;
}

.search{
    flex:1; min-width:260px; max-width:380px;
    background:#121a33; border:1px solid #273150; color:white;
    padding:14px 16px; border-radius:14px;
}

table{
    width:100%; border-collapse:collapse;
    background:#121a33; border:1px solid #273150; border-radius:18px;
    overflow:hidden;
}

thead th{
    text-align:left; padding:16px 18px; font-size:12px;
    letter-spacing:.06em; color:#8b96b8; border-bottom:1px solid #273150;
}

tbody td{
    padding:16px 18px; border-bottom:1px solid #1c2746;
    vertical-align:top; font-size:14px;
}

tbody tr:last-child td{ border-bottom:none; }

.intent-tag{
    display:inline-block; background:rgba(31,216,143,.1);
    border:1px solid rgba(31,216,143,.3); color:#c084fc;
    padding:4px 10px; border-radius:8px; font-size:12px; font-family:monospace;
}

.category-tag{
    display:inline-block; background:#1a2550; border:1px solid #273150;
    color:white; padding:6px 12px; border-radius:8px; font-size:13px; font-weight:700;
}

.keywords{ color:#8b96b8; font-size:13px; }

.actions{ display:flex; gap:8px; }

.edit-btn,.delete-btn{
    border:none; padding:8px 14px; border-radius:8px;
    font-weight:700; font-size:13px; cursor:pointer;
}

.edit-btn{ background:#f5a623; color:#1a1200; }
.delete-btn{ background:#ef4444; color:white; }

.modal{
    display:none; position:fixed; inset:0; background:rgba(0,0,0,.6);
    z-index:999; align-items:center; justify-content:center;
}

.modal-content{
    width:90%; max-width:600px; background:#07133a;
    border:1px solid rgba(255,255,255,.08); border-radius:24px; padding:24px;
}

.modal-header{
    display:flex; align-items:center; justify-content:space-between; margin-bottom:20px;
}

.modal-header h2{ color:#fff; margin:0; }

.close-btn{ background:none; border:none; color:#c4b5fd; font-size:30px; cursor:pointer; }

.form-group{ margin-bottom:18px; }

.form-group label{
    display:block; color:#c4b5fd; margin-bottom:8px; font-size:14px;
    font-weight:700; letter-spacing:.08em; text-transform:uppercase;
}

.form-group input,
.form-group textarea{
    width:100%; padding:16px 18px; border-radius:16px;
    border:1px solid rgba(255,255,255,.08); background:#13214d; color:#fff;
    font-size:16px; outline:none; box-sizing:border-box;
}

.modal-actions{ display:flex; gap:14px; margin-top:24px; }

.cancel-btn,.submit-btn{
    flex:1; padding:16px; border:none; border-radius:16px;
    font-size:16px; font-weight:700; cursor:pointer;
}

.cancel-btn{ background:#0d183f; color:#fff; border:1px solid rgba(255,255,255,.08); }
.submit-btn{ background:#c084fc; color:#4c3d7a; }

.confirm-icon{
    width:64px; height:64px; margin:0 auto 16px;
    border-radius:50%;
    background:rgba(240,117,122,.15);
    display:flex; align-items:center; justify-content:center;
    color:#ef4444;
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

.status-msg{
    background:rgba(31,216,143,.1); border:1px solid rgba(31,216,143,.35);
    color:#c084fc; padding:12px 18px; border-radius:12px; margin-bottom:20px; font-size:14px;
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
            <p>Knowledge Base Management</p>
        </div>
    </div>

    <div class="stats">
        <div class="stat-box">
            <strong>{{ $entries->count() }}</strong>
            <span>Entries</span>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="logout" type="submit">Logout</button>
        </form>
    </div>
</div>

<div class="container">

    <div class="page-title">
        <a href="{{ route('admin.dashboard') }}">←</a>
        <h2>{{ $information->main_topic }}</h2>
    </div>
    <p class="subtitle">Manage your chatbot knowledge base entries below.</p>

    @if(session('status'))
        <div class="status-msg">{{ session('status') }}</div>
    @endif

    <div class="topbar">
        <button class="add-btn" onclick="openModal()">+ Add New Data</button>
        <input class="search" type="text" placeholder="Search..." id="searchInput" onkeyup="filterEntries()">
    </div>

    <table>
        <thead>
            <tr>
                <th>INTENT</th>
                <th>QUESTION</th>
                <th>ANSWER</th>
                <th>CATEGORY</th>
                <th>KEY WORDS</th>
                <th>ACTION</th>
            </tr>
        </thead>
        <tbody id="entriesBody">
            @forelse($entries as $entry)
                <tr>
                    <td><span class="intent-tag">{{ $entry->intent }}</span></td>
                    <td>{{ $entry->question }}</td>
                    <td>{{ $entry->answer }}</td>
                    <td><span class="category-tag">{{ $entry->category }}</span></td>
                    <td class="keywords">{{ $entry->keywords }}</td>
                    <td>
                        <div class="actions">
                            <button type="button" class="edit-btn"
                                onclick="openEditModal({{ $entry->id }}, {{ json_encode($entry->intent) }}, {{ json_encode($entry->question) }}, {{ json_encode($entry->answer) }}, {{ json_encode($entry->category) }}, {{ json_encode($entry->keywords) }})">
                                Edit
                            </button>

                            <form method="POST"
                                  action="{{ route('admin.information.entries.destroy', [$information->id, $entry->id]) }}">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="delete-btn"
                                    onclick="openDeleteModal(this.closest('form'), 'Padam entry ini?', 'Tindakan ini tidak boleh dibuat asal.')">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align:center; color:#8b96b8;">
                        Belum ada data untuk topik ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</div>

<!-- Add Data Modal -->
<div id="addModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Add New Data</h2>
            <button type="button" class="close-btn" onclick="closeModal()">&times;</button>
        </div>

        <form action="{{ route('admin.information.entries.store', $information->id) }}" method="POST">
            @csrf

            <div class="form-group">
                <label>Intent</label>
                <input type="text" name="intent" placeholder="e.g. course_registration" required>
            </div>

            <div class="form-group">
                <label>Question</label>
                <input type="text" name="question" placeholder="e.g. When does registration open?" required>
            </div>

            <div class="form-group">
                <label>Answer</label>
                <textarea name="answer" rows="4" placeholder="Write the answer here..." required></textarea>
            </div>

            <div class="form-group">
                <label>Category</label>
                <input type="text" name="category" placeholder="e.g. Akademik">
            </div>

            <div class="form-group">
                <label>Key Words</label>
                <input type="text" name="keywords" placeholder="e.g. registration, semester, course">
            </div>

            <div class="modal-actions">
                <button type="button" class="cancel-btn" onclick="closeModal()">Cancel</button>
                <button type="submit" class="submit-btn">Submit</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Data Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Edit Data</h2>
            <button type="button" class="close-btn" onclick="closeEditModal()">&times;</button>
        </div>

        <form id="editForm" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label>Intent</label>
                <input type="text" name="intent" id="edit_intent" required>
            </div>

            <div class="form-group">
                <label>Question</label>
                <input type="text" name="question" id="edit_question" required>
            </div>

            <div class="form-group">
                <label>Answer</label>
                <textarea name="answer" id="edit_answer" rows="4" required></textarea>
            </div>

            <div class="form-group">
                <label>Category</label>
                <input type="text" name="category" id="edit_category">
            </div>

            <div class="form-group">
                <label>Key Words</label>
                <input type="text" name="keywords" id="edit_keywords">
            </div>

            <div class="modal-actions">
                <button type="button" class="cancel-btn" onclick="closeEditModal()">Cancel</button>
                <button type="submit" class="submit-btn">Save</button>
            </div>
        </form>
    </div>
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
        <h2 class="confirm-title" id="deleteConfirmTitle">Padam entry ini?</h2>
        <p class="confirm-message" id="deleteConfirmMessage">Tindakan ini tidak boleh dibuat asal.</p>
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
      document.getElementById('deleteConfirmTitle').textContent = title || 'Padam entry ini?';
      document.getElementById('deleteConfirmMessage').textContent = message || 'Tindakan ini tidak boleh dibuat asal.';
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
</script>

<script>
function openModal() {
    document.getElementById('addModal').style.display = 'flex';
}

function closeModal() {
    document.getElementById('addModal').style.display = 'none';
}

function openEditModal(id, intent, question, answer, category, keywords) {
    document.getElementById('edit_intent').value = intent;
    document.getElementById('edit_question').value = question;
    document.getElementById('edit_answer').value = answer;
    document.getElementById('edit_category').value = category;
    document.getElementById('edit_keywords').value = keywords;

    document.getElementById('editForm').action =
        "{{ route('admin.information.entries.update', [$information->id, '__ID__']) }}".replace('__ID__', id);

    document.getElementById('editModal').style.display = 'flex';
}

function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}

window.onclick = function(event) {
    const addModal = document.getElementById('addModal');
    const editModal = document.getElementById('editModal');
    const deleteModal = document.getElementById('deleteConfirmModal');
    if (event.target === addModal) closeModal();
    if (event.target === editModal) closeEditModal();
    if (event.target === deleteModal) closeDeleteModal();
}

function filterEntries() {
    const query = document.getElementById('searchInput').value.toLowerCase();
    document.querySelectorAll('#entriesBody tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(query) ? '' : 'none';
    });
}
</script>

</body>
</html>