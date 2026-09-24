<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Unanswered Questions</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tabler-icons/3.44.0/tabler-icons.min.css">

    <style>
        :root{
            --admin-bg:#f5faf6;
            --admin-card:#fff;
            --admin-border:#e0e7ff;
            --admin-muted:#64748b;
            --green:#3f7a52;
        }

        *{box-sizing:border-box}

        body{
            margin:0;
            background: linear-gradient(160deg, #ffffff, #E5F5E0);
            color:#1f2937;
            font-family:Arial,Helvetica,sans-serif;
        }

        .page-header{
            padding:28px 32px;
            background: #fff;
            border-bottom:1px solid #e0e7ff;
            color:#1f2937;
        }

        .page-header h1{
            margin:0;
            font-size:22px;
            color:#1f2937;
        }

        .page-header p{
            margin:4px 0 0;
            color:#64748b;
            font-size:13px;
        }

        .page{
            max-width:980px;
            margin:0 auto;
            padding:36px 32px 120px;
        }

        .status-msg{
            background:#ecfdf5; border:1px solid #a7f3d0;
            color:#065f46; padding:12px 18px; border-radius:12px; margin-bottom:20px; font-size:14px;
        }

        .top-row{
            display:flex;
            gap:14px;
            margin-bottom:24px;
            flex-wrap:wrap;
        }

        .stat-card{
            background:var(--admin-card);
            border:1px solid var(--admin-border);
            border-radius:16px;
            padding:16px 20px;
            flex:1;
            min-width:120px;
            box-shadow: 0 1px 2px rgba(20, 40, 100, 0.04);
        }

        .stat-card p.label{
            font-size:12px;
            color:var(--admin-muted);
            margin:0 0 4px;
            text-transform:uppercase;
            letter-spacing:.05em;
        }

        .stat-card p.value{
            font-size:24px;
            font-weight:800;
            margin:0;
            color:var(--green);
        }

        .tabs{
            display:flex;
            gap:8px;
            margin-bottom:20px;
            flex-wrap:wrap;
        }

        .tab{
            background:var(--admin-card);
            border:1px solid var(--admin-border);
            color:var(--admin-muted);
            font-size:13px;
            font-weight:700;
            padding:10px 16px;
            border-radius:10px;
            cursor:pointer;
            white-space:nowrap;
            display:flex;
            align-items:center;
            gap:6px;
            transition:.2s;
        }

        .tab.active{
            background:#4a7856;
            color:white;
            border-color:#4a7856;
        }

        .sort-select{
            margin-left:auto;
            background:var(--admin-card);
            border:1px solid var(--admin-border);
            color:#1f2937;
            font-size:13px;
            font-weight:700;
            padding:10px 14px;
            border-radius:10px;
            cursor:pointer;
        }

        .sort-select:focus{
            outline:none;
            border-color:var(--green);
        }

        .list{
            display:flex;
            flex-direction:column;
            border:1px solid var(--admin-border);
            border-radius:16px;
            overflow:hidden;
        }

        .item{
            position:relative;
            display:flex;
            align-items:center;
            gap:16px;
            background:var(--admin-card);
            padding:16px 20px;
            border-left:3px solid transparent;
            border-bottom:1px solid var(--admin-border);
            transition:.2s;
        }

        .item:last-child{
            border-bottom:none;
        }

        .item:hover{
            background:#f5faf6;
        }

        .item.hot{
            border-left-color:#d97706;
        }

        .type-icon{
            width:40px;
            height:40px;
            border-radius:10px;
            display:flex;
            align-items:center;
            justify-content:center;
            flex-shrink:0;
            font-size:18px;
            background:rgba(217,119,6,.12);
            color:#d97706;
        }

        .type-icon.done{
            background:rgba(22,163,74,.12);
            color:#16a34a;
        }

        .body{
            flex:1;
            min-width:0;
        }

        .row-top{
            display:flex;
            justify-content:space-between;
            gap:12px;
            margin-bottom:2px;
        }

        .title{
            font-size:14px;
            font-weight:700;
            color:#1f2937;
        }

        .title span{
            color:var(--admin-muted);
            font-weight:400;
        }

        .time{
            font-size:12px;
            color:var(--admin-muted);
            white-space:nowrap;
            flex-shrink:0;
        }

        .preview{
            margin:0;
            font-size:13px;
            color:var(--admin-muted);
        }

        .resolve-btn{
            background:#f1f5f9;
            border:1px solid var(--admin-border);
            color:var(--admin-muted);
            font-size:12px;
            font-weight:700;
            padding:8px 14px;
            border-radius:8px;
            cursor:pointer;
            white-space:nowrap;
            transition:.2s;
        }

        .resolve-btn:hover{
            border-color:var(--green);
            color:#1f2937;
        }

        .resolution-tag{
            display:inline-block;
            padding:3px 10px;
            border-radius:6px;
            font-size:11px;
            font-weight:700;
            margin-left:8px;
        }

        .resolution-tag.answered{
            background:rgba(22,163,74,.12);
            color:#16a34a;
        }

        .resolution-tag.ignored{
            background:rgba(100,116,139,.12);
            color:var(--admin-muted);
        }

        .bulk-bar{
            display:flex;
            align-items:center;
            gap:14px;
            margin-bottom:12px;
        }

        .select-all-label{
            display:flex;
            align-items:center;
            gap:8px;
            font-size:13px;
            color:var(--admin-muted);
            cursor:pointer;
        }

        .select-all-label input,
        .q-select-checkbox{
            width:16px;
            height:16px;
            accent-color:var(--green);
            cursor:pointer;
        }

        .bulk-delete-btn{
            display:none;
            align-items:center;
            gap:6px;
            background:#ef4444;
            border:1px solid #ef4444;
            color:white;
            font-size:12px;
            font-weight:700;
            padding:8px 14px;
            border-radius:8px;
            cursor:pointer;
        }

        .history-delete-btn{
            background:transparent;
            border:1px solid var(--admin-border);
            color:var(--admin-muted);
            width:34px;
            height:34px;
            border-radius:8px;
            display:flex;
            align-items:center;
            justify-content:center;
            cursor:pointer;
            flex-shrink:0;
            transition:.2s;
        }

        .history-delete-btn:hover{
            border-color:#ef4444;
            color:#ef4444;
        }

        /* ===== Modal ===== */
        .modal{
            display:none; position:fixed; inset:0; background:rgba(0,0,0,.6);
            z-index:999; align-items:center; justify-content:center;
        }

        .modal-content{
            width:90%; max-width:600px; background:#fff;
            border:1px solid #e0e7ff; border-radius:24px; padding:24px;
            max-height:85vh; overflow-y:auto;
            box-shadow: 0 20px 44px rgba(20, 40, 100, 0.18);
        }

        .modal-header{
            display:flex; align-items:center; justify-content:space-between; margin-bottom:20px;
        }

        .modal-header h2{ color:#1f2937; margin:0; }

        .close-btn{ background:none; border:none; color:#64748b; font-size:30px; cursor:pointer; }

        .form-group{ margin-bottom:18px; }

        .form-group label{
            display:block; color:#3f7a52; margin-bottom:8px; font-size:14px;
            font-weight:700; letter-spacing:.08em; text-transform:uppercase;
        }

        .form-group input,
        .form-group textarea,
        .form-group select{
            width:100%; padding:16px 18px; border-radius:16px;
            border:1px solid #e0e7ff; background:#f8fafc; color:#1f2937;
            font-size:16px; outline:none; box-sizing:border-box;
        }

        .modal-actions{ display:flex; gap:14px; margin-top:24px; }

        .cancel-btn,.submit-btn{
            flex:1; padding:16px; border:none; border-radius:16px;
            font-size:16px; font-weight:700; cursor:pointer;
        }

        .cancel-btn{ background:#f1f5f9; color:#374151; border:1px solid #e0e7ff; }
        .submit-btn{ background:#4a7856; color:#fff; }
    </style>
</head>
<body>

@include('partials.admin-nav', ['active' => 'unanswered', 'unansweredCount' => $questions->count(), 'unreadFeedbackCount' => $unreadFeedbackCount])

<div class="page-header">
    <h1>Unanswered Questions</h1>
    <p>Questions the bot couldn't find an answer for in the knowledge base</p>
</div>

    <div class="page">

        @if(session('status'))
            <div class="status-msg">{{ session('status') }}</div>
        @endif

        <div class="top-row">
            <div class="stat-card">
                <p class="label">Total Pending</p>
                <p class="value">{{ $questions->count() }}</p>
            </div>

            <div class="stat-card">
                <p class="label">Total Times Asked</p>
                <p class="value">{{ $questions->sum('asked_count') }}</p>
            </div>

            <div class="stat-card">
                <p class="label">Total History</p>
                <p class="value">{{ $history->count() }}</p>
            </div>
        </div>

        <div class="tabs">
            <button class="tab active" onclick="setTab('pending', this)">
                <i class="ti ti-help-circle" aria-hidden="true"></i>
                Unanswered
            </button>
            <button class="tab" onclick="setTab('history', this)">
                <i class="ti ti-history" aria-hidden="true"></i>
                History
            </button>

            <select class="sort-select" id="sortSelect" onchange="location.href = '{{ route('admin.unanswered.index') }}?sort=' + this.value">
                <option value="newest" {{ $sort === 'newest' ? 'selected' : '' }}>Sort: Newest first</option>
                <option value="oldest" {{ $sort === 'oldest' ? 'selected' : '' }}>Sort: Oldest first</option>
                <option value="most_asked" {{ $sort === 'most_asked' ? 'selected' : '' }}>Sort: Most asked</option>
            </select>
        </div>

        <!-- PENDING LIST -->
        <div id="pendingToolbar" class="bulk-bar">
            <label class="select-all-label">
                <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll(this)">
                Select all
            </label>
            <button type="button" id="bulkDeleteBtn" class="bulk-delete-btn" onclick="bulkDeleteSelected()">
                <i class="ti ti-trash" aria-hidden="true"></i>
                Delete Selected (<span id="selectedCount">0</span>)
            </button>
        </div>

        <div class="list" id="pendingListWrap">

            @forelse($questions as $q)

                <div class="item {{ $q->asked_count >= 3 ? 'hot' : '' }}" data-question-id="{{ $q->id }}" data-asked-count="{{ $q->asked_count }}">

                    <input type="checkbox" class="q-select-checkbox" value="{{ $q->id }}" onchange="updateBulkToolbar()" aria-label="Select this question">

                    <div class="type-icon">
                        <i class="ti ti-help-circle" aria-hidden="true"></i>
                    </div>

                    <div class="body">
                        <div class="row-top">
                            <div class="title">{{ $q->question }} <span>· asked {{ $q->asked_count }}x</span></div>
                            <div class="time">{{ $q->created_at->diffForHumans() }}</div>
                        </div>
                        <p class="preview">No answer in the knowledge base for this question yet.</p>

                        <div style="display:flex; gap:8px; margin-top:10px;">
                            <button type="button" class="resolve-btn" style="background:#4a7856; border-color:#4a7856; color:white;"
                                onclick="openAnswerModal({{ $q->id }}, {{ json_encode($q->question) }})">
                                + Add to Knowledge Base
                            </button>

                            <form action="{{ route('admin.unanswered.resolve', $q->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="resolve-btn" style="opacity:.7;">Dismiss</button>
                            </form>
                        </div>
                    </div>

                </div>

            @empty

                <div class="item">
                    <div class="body">
                        <div class="title">No unanswered questions 🎉</div>
                        <p class="preview">All student questions are already in the knowledge base.</p>
                    </div>
                </div>

            @endforelse

        </div>

        <!-- HISTORY LIST -->
        <div class="list" id="historyListWrap" style="display:none;">

            @forelse($history as $h)

                <div class="item" data-history-id="{{ $h->id }}">

                    <div class="type-icon done">
                        <i class="ti ti-check" aria-hidden="true"></i>
                    </div>

                    <div class="body">
                        <div class="row-top">
                            <div class="title">
                                {{ $h->question }} <span>· asked {{ $h->asked_count }}x</span>
                                <span class="resolution-tag {{ $h->resolution }}">
                                    {{ $h->resolution === 'answered' ? 'Answered' : 'Dismissed' }}
                                </span>
                            </div>
                            <div class="time">resolved {{ $h->updated_at->diffForHumans() }}</div>
                        </div>
                    </div>

                    <button type="button" class="history-delete-btn" onclick="deleteHistoryItem({{ $h->id }})" aria-label="Delete this record">
                        <i class="ti ti-trash" aria-hidden="true"></i>
                    </button>

                </div>

            @empty

                <div class="item">
                    <div class="body">
                        <div class="title">No history yet</div>
                        <p class="preview">Resolved questions will appear here.</p>
                    </div>
                </div>

            @endforelse

        </div>

    </div>

    <!-- Answer Modal -->
    <div id="answerModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Add to Knowledge Base</h2>
                <button type="button" class="close-btn" onclick="closeAnswerModal()">&times;</button>
            </div>

            <form id="answerForm" method="POST">
                @csrf

                <div class="form-group">
                    <label>Main Topic</label>
                    <select name="information_id" id="answer_topic" required>
                        <option value="">-- Select Topic --</option>
                        @foreach($topics as $topic)
                            <option value="{{ $topic->id }}">{{ $topic->main_topic }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Intent</label>
                    <input type="text" name="intent" id="answer_intent" placeholder="e.g. yuran_pembayaran">
                </div>

                <div class="form-group">
                    <label>Question</label>
                    <input type="text" name="question" id="answer_question" required>
                </div>

                <div class="form-group">
                    <label>Answer</label>
                    <textarea name="answer" id="answer_answer" rows="4" placeholder="Write the answer here..." required></textarea>
                </div>

                <div class="form-group">
                    <label>Key Words</label>
                    <input type="text" name="keywords" id="answer_keywords" placeholder="e.g. fees, payment, deadline">
                </div>

                <div class="modal-actions">
                    <button type="button" class="cancel-btn" onclick="closeAnswerModal()">Cancel</button>
                    <button type="submit" class="submit-btn">Submit</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function setTab(tab, btn) {
            document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
            btn.classList.add('active');

            document.getElementById('pendingListWrap').style.display = tab === 'pending' ? 'flex' : 'none';
            document.getElementById('historyListWrap').style.display = tab === 'history' ? 'flex' : 'none';
            document.getElementById('pendingToolbar').style.display = tab === 'pending' ? 'flex' : 'none';

            // The sort control only affects the Unanswered (pending) list, so hide it
            // on the History tab where it wouldn't do anything.
            document.getElementById('sortSelect').style.display = tab === 'pending' ? 'block' : 'none';
        }

        function csrfToken() {
            return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        }

        function safeJsonFetch(url, options) {
            return fetch(url, options).then(res => res.text().then((text) => {
                let data = null;
                try { data = JSON.parse(text); } catch (e) { /* not JSON */ }
                return { ok: res.ok, data };
            }));
        }

        function toggleSelectAll(checkbox) {
            document.querySelectorAll('.q-select-checkbox').forEach(el => { el.checked = checkbox.checked; });
            updateBulkToolbar();
        }

        function updateBulkToolbar() {
            const all = document.querySelectorAll('.q-select-checkbox');
            const checked = document.querySelectorAll('.q-select-checkbox:checked');

            document.getElementById('selectedCount').textContent = checked.length;
            document.getElementById('bulkDeleteBtn').style.display = checked.length > 0 ? 'inline-flex' : 'none';

            const selectAllCb = document.getElementById('selectAllCheckbox');
            selectAllCb.checked = all.length > 0 && checked.length === all.length;
        }

        function updatePendingStats() {
            const items = document.querySelectorAll('#pendingListWrap .item[data-question-id]');
            const values = document.querySelectorAll('.stat-card .value');
            let askedSum = 0;
            items.forEach(el => { askedSum += parseInt(el.getAttribute('data-asked-count') || '0', 10); });
            if (values[0]) values[0].textContent = items.length;
            if (values[1]) values[1].textContent = askedSum;
        }

        function updateHistoryStats() {
            const items = document.querySelectorAll('#historyListWrap .item[data-history-id]');
            const values = document.querySelectorAll('.stat-card .value');
            if (values[2]) values[2].textContent = items.length;
        }

        function bulkDeleteSelected() {
            const ids = Array.from(document.querySelectorAll('.q-select-checkbox:checked')).map(el => el.value);
            if (ids.length === 0) return;
            if (!confirm(`Delete ${ids.length} selected question(s)? This action cannot be undone.`)) return;

            safeJsonFetch("{{ route('admin.unanswered.bulkDestroy') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken(),
                },
                body: JSON.stringify({ ids }),
            }).then(({ ok, data }) => {
                if (!ok || !data || !data.success) {
                    alert('Could not delete the selected question(s) right now. Please try again.');
                    return;
                }

                ids.forEach(id => {
                    const el = document.querySelector(`.item[data-question-id="${id}"]`);
                    if (el) el.remove();
                });

                document.getElementById('selectAllCheckbox').checked = false;
                updateBulkToolbar();
                updatePendingStats();

                if (!document.querySelector('#pendingListWrap .item[data-question-id]')) {
                    document.getElementById('pendingListWrap').innerHTML =
                        '<div class="item"><div class="body"><div class="title">No unanswered questions \u{1F389}</div><p class="preview">All student questions are already in the knowledge base.</p></div></div>';
                }
            }).catch(() => alert('Connection problem. Please try again.'));
        }

        function deleteHistoryItem(id) {
            if (!confirm('Delete this record from history? This action cannot be undone.')) return;

            safeJsonFetch(`/admin/unanswered/${id}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken(),
                },
            }).then(({ ok, data }) => {
                if (!ok || !data || !data.success) {
                    alert('Could not delete this record right now. Please try again.');
                    return;
                }

                const el = document.querySelector(`.item[data-history-id="${id}"]`);
                if (el) el.remove();
                updateHistoryStats();

                if (!document.querySelector('#historyListWrap .item[data-history-id]')) {
                    document.getElementById('historyListWrap').innerHTML =
                        '<div class="item"><div class="body"><div class="title">No history yet</div><p class="preview">Resolved questions will appear here.</p></div></div>';
                }
            }).catch(() => alert('Connection problem. Please try again.'));
        }

        function openAnswerModal(id, questionText) {
            document.getElementById('answer_topic').value = '';
            document.getElementById('answer_intent').value = '';
            document.getElementById('answer_question').value = questionText;
            document.getElementById('answer_answer').value = '';
            document.getElementById('answer_keywords').value = '';

            document.getElementById('answerForm').action =
                "{{ route('admin.unanswered.answer', '__ID__') }}".replace('__ID__', id);

            document.getElementById('answerModal').style.display = 'flex';
        }

        function closeAnswerModal() {
            document.getElementById('answerModal').style.display = 'none';
        }

        window.onclick = function(event) {
            const answerModal = document.getElementById('answerModal');
            if (event.target === answerModal) closeAnswerModal();
        }
    </script>

</body>
</html>