<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Soalan Belum Terjawab</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tabler-icons/3.44.0/tabler-icons.min.css">

    <style>
        :root{
            --admin-bg:#020b24;
            --admin-card:#0f1738;
            --admin-border:#1c2746;
            --admin-muted:#93a4d1;
            --green:#c084fc;
        }

        *{box-sizing:border-box}

        body{
            margin:0;
            background: linear-gradient(120deg, #2a5f59, #2dd4bf, #3355a6, #2a5f59);
            background-size: 300% 300%;
            animation: gradientShift 15s ease infinite;
            color:white;
            font-family:Arial,Helvetica,sans-serif;
        }

        .topbar{
            display:flex;
            align-items:center;
            gap:16px;
            padding:24px 32px;
            border-bottom:1px solid var(--admin-border);
        }

        .back-btn{
            width:52px;
            height:52px;
            border-radius:16px;
            background:var(--admin-card);
            border:1px solid var(--admin-border);
            display:flex;
            align-items:center;
            justify-content:center;
            color:white;
            text-decoration:none;
            transition:.2s;
            font-size:20px;
        }

        .back-btn:hover{
            border-color:var(--green);
        }

        .topbar h1{
            margin:0;
            font-size:24px;
            font-weight:800;
        }

        .topbar p{
            margin:4px 0 0;
            color:var(--admin-muted);
            font-size:14px;
        }

        .page{
            max-width:980px;
            margin:0 auto;
            padding:36px 32px 120px;
        }

        .status-msg{
            background:rgba(192,132,252,.1); border:1px solid rgba(192,132,252,.35);
            color:#c084fc; padding:12px 18px; border-radius:12px; margin-bottom:20px; font-size:14px;
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
            background:white;
            color:var(--admin-bg);
            border-color:white;
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
            background:#131f45;
        }

        .item.hot{
            border-left-color:#f472b6;
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
            background:rgba(244,114,182,.15);
            color:#f472b6;
        }

        .type-icon.done{
            background:rgba(31,216,143,.12);
            color:#1fd88f;
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
            color:white;
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
            background:transparent;
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
            color:white;
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
            background:rgba(31,216,143,.12);
            color:#1fd88f;
        }

        .resolution-tag.ignored{
            background:rgba(147,164,209,.12);
            color:var(--admin-muted);
        }

        /* ===== Modal ===== */
        .modal{
            display:none; position:fixed; inset:0; background:rgba(0,0,0,.6);
            z-index:999; align-items:center; justify-content:center;
        }

        .modal-content{
            width:90%; max-width:600px; background:#07133a;
            border:1px solid rgba(255,255,255,.08); border-radius:24px; padding:24px;
            max-height:85vh; overflow-y:auto;
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
        .form-group textarea,
        .form-group select{
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

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
    </style>
</head>
<body>

    <div class="topbar">

        <a href="{{ route('admin.dashboard') }}" class="back-btn" aria-label="Back to dashboard">
            <i class="ti ti-arrow-left" aria-hidden="true"></i>
        </a>

        <div>
            <h1>Soalan Belum Terjawab</h1>
            <p>Soalan yang bot tak jumpa jawapan dalam knowledge base</p>
        </div>

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
                <p class="label">Jumlah Kali Ditanya</p>
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
                Belum Terjawab
            </button>
            <button class="tab" onclick="setTab('history', this)">
                <i class="ti ti-history" aria-hidden="true"></i>
                History
            </button>
        </div>

        <!-- PENDING LIST -->
        <div class="list" id="pendingListWrap">

            @forelse($questions as $q)

                <div class="item {{ $q->asked_count >= 3 ? 'hot' : '' }}">

                    <div class="type-icon">
                        <i class="ti ti-help-circle" aria-hidden="true"></i>
                    </div>

                    <div class="body">
                        <div class="row-top">
                            <div class="title">{{ $q->question }} <span>· ditanya {{ $q->asked_count }}x</span></div>
                            <div class="time">{{ $q->created_at->diffForHumans() }}</div>
                        </div>
                        <p class="preview">Belum ada jawapan dalam knowledge base untuk soalan ni.</p>

                        <div style="display:flex; gap:8px; margin-top:10px;">
                            <button type="button" class="resolve-btn" style="background:#8b5cf6; border-color:#8b5cf6; color:white;"
                                onclick="openAnswerModal({{ $q->id }}, {{ json_encode($q->question) }})">
                                + Tambah ke Knowledge Base
                            </button>

                            <form action="{{ route('admin.unanswered.resolve', $q->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="resolve-btn" style="opacity:.7;">Abaikan</button>
                            </form>
                        </div>
                    </div>

                </div>

            @empty

                <div class="item">
                    <div class="body">
                        <div class="title">Takde soalan belum terjawab 🎉</div>
                        <p class="preview">Semua soalan pelajar dah ada dalam knowledge base.</p>
                    </div>
                </div>

            @endforelse

        </div>

        <!-- HISTORY LIST -->
        <div class="list" id="historyListWrap" style="display:none;">

            @forelse($history as $h)

                <div class="item">

                    <div class="type-icon done">
                        <i class="ti ti-check" aria-hidden="true"></i>
                    </div>

                    <div class="body">
                        <div class="row-top">
                            <div class="title">
                                {{ $h->question }} <span>· ditanya {{ $h->asked_count }}x</span>
                                <span class="resolution-tag {{ $h->resolution }}">
                                    {{ $h->resolution === 'answered' ? 'Dijawab' : 'Diabaikan' }}
                                </span>
                            </div>
                            <div class="time">diselesaikan {{ $h->updated_at->diffForHumans() }}</div>
                        </div>
                    </div>

                </div>

            @empty

                <div class="item">
                    <div class="body">
                        <div class="title">Takde history lagi</div>
                        <p class="preview">Soalan yang dah diselesaikan akan muncul di sini.</p>
                    </div>
                </div>

            @endforelse

        </div>

    </div>

    <!-- Answer Modal -->
    <div id="answerModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Tambah ke Knowledge Base</h2>
                <button type="button" class="close-btn" onclick="closeAnswerModal()">&times;</button>
            </div>

            <form id="answerForm" method="POST">
                @csrf

                <div class="form-group">
                    <label>Main Topic</label>
                    <select name="information_id" id="answer_topic" required>
                        <option value="">-- Pilih Topic --</option>
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
                    <textarea name="answer" id="answer_answer" rows="4" placeholder="Tulis jawapan di sini..." required></textarea>
                </div>

                <div class="form-group">
                    <label>Key Words</label>
                    <input type="text" name="keywords" id="answer_keywords" placeholder="e.g. yuran, bayaran, tarikh akhir">
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