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

        <div class="top-row">
            <div class="stat-card">
                <p class="label">Total Pending</p>
                <p class="value">{{ $questions->count() }}</p>
            </div>

            <div class="stat-card">
                <p class="label">Jumlah Kali Ditanya</p>
                <p class="value">{{ $questions->sum('asked_count') }}</p>
            </div>
        </div>

        <div class="list">

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

            <form action="{{ route('admin.unanswered.answer', $q->id) }}" method="POST"
                  style="margin-top:10px; display:flex; flex-direction:column; gap:8px;">
                @csrf

                <select name="information_id" required
                    style="background:var(--admin-bg); color:white; border:1px solid var(--admin-border); border-radius:8px; padding:8px; font-size:13px;">
                    <option value="">-- Pilih Topic --</option>
                    @foreach($topics as $topic)
                        <option value="{{ $topic->id }}">{{ $topic->main_topic }}</option>
                    @endforeach
                </select>

                <textarea name="answer" required rows="2" placeholder="Taip jawapan di sini..."
                    style="background:var(--admin-bg); color:white; border:1px solid var(--admin-border); border-radius:8px; padding:8px; font-size:13px;"></textarea>

                <div style="display:flex; gap:8px;">
                    <button type="submit" class="resolve-btn" style="background:#8b5cf6; border-color:#8b5cf6; color:white;">
                        Simpan &amp; Selesai
                    </button>
                </div>
            </form>

            <form action="{{ route('admin.unanswered.resolve', $q->id) }}" method="POST" style="margin-top:6px;">
                @csrf
                @method('PUT')
                <button type="submit" class="resolve-btn" style="font-size:11px; opacity:.7;">Abaikan (bukan relevan)</button>
            </form>
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

    </div>

</body>
</html>