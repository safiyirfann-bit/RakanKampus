<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Inbox</title>
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

        .stat-card p.value.accent{
            color:#8b5cf6;
        }

        .tabs{
            display:flex;
            gap:8px;
            overflow-x:auto;
            scrollbar-width:none;
            margin-bottom:20px;
            flex-wrap:wrap;
        }

        .tabs::-webkit-scrollbar{display:none}

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

        .tab i{
            font-size:15px;
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

        .item.unread{
            border-left-color:#8b5cf6;
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
        }

        .type-icon.feedback{
            background:rgba(79,124,255,.15);
            color:#a78bfa;
        }

        .type-icon.feature{
            background:rgba(168,85,247,.15);
            color:#c99bf7;
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
            overflow:hidden;
            text-overflow:ellipsis;
            white-space:nowrap;
        }

        .fab{
            position:fixed;
            right:28px;
            bottom:28px;
            width:64px;
            height:64px;
            border-radius:9999px;
            border:none;
            background:#f472b6;
            color:white;
            font-size:32px;
            font-weight:300;
            cursor:pointer;
            box-shadow:0 14px 28px rgba(244,63,142,.45);
            transition:.2s;
        }

        .fab:hover{
            transform:translateY(-2px) scale(1.03);
        }

        @media (max-width: 640px){
            .top-row{
                flex-direction:column;
            }

            .stat-card{
                min-width:100%;
            }

            .tabs{
                flex-wrap:nowrap;
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

    <div class="topbar">

        <a href="{{ route('admin.dashboard') }}" class="back-btn" aria-label="Back to dashboard">
            <i class="ti ti-arrow-left" aria-hidden="true"></i>
        </a>

        <div>
            <h1>Feedback Inbox</h1>
            <p>Student feedback, feature requests & reported issues</p>
        </div>

    </div>

    <div class="page">

        <div class="top-row">
            <div class="stat-card">
                <p class="label">Total</p>
                <p class="value">{{ $feedbacks->count() }}</p>
            </div>

            <div class="stat-card">
                <p class="label">Unread</p>
                <p class="value accent">{{ $feedbacks->where('is_read', false)->count() }}</p>
            </div>
        </div>

        <div class="tabs">
            <button class="tab active" onclick="setInboxTab('all', this)">
                <i class="ti ti-list" aria-hidden="true"></i>
                All
            </button>
            <button class="tab" onclick="setInboxTab('feedback', this)">
                <i class="ti ti-message-2" aria-hidden="true"></i>
                Feedback
            </button>
            <button class="tab" onclick="setInboxTab('feature', this)">
                <i class="ti ti-bulb" aria-hidden="true"></i>
                Feature requests
            </button>
            <button class="tab" onclick="setInboxTab('issue', this)">
                <i class="ti ti-alert-triangle" aria-hidden="true"></i>
                Report issue
            </button>
        </div>

        <div class="list" id="inboxList">

            @forelse($feedbacks as $item)

                @if($item->feedback)
                    <div class="item {{ !$item->is_read ? 'unread' : '' }}" data-type="feedback">

                        <div class="type-icon feedback">
                            <i class="ti ti-message-2" aria-hidden="true"></i>
                        </div>

                        <div class="body">

                            <div class="row-top">
                                <div class="title">Feedback <span>· {{ $item->user_name }}</span></div>
                                <div class="time">{{ $item->created_at->diffForHumans() }}</div>
                            </div>

                            <p class="preview">
                                {{ $item->feedback }}
                            </p>

                        </div>

                    </div>
                @endif

                @if($item->feature_request)
                    <div class="item {{ !$item->is_read ? 'unread' : '' }}" data-type="feature">

                        <div class="type-icon feature">
                            <i class="ti ti-bulb" aria-hidden="true"></i>
                        </div>

                        <div class="body">

                            <div class="row-top">
                                <div class="title">Feature request <span>· {{ $item->user_name }}</span></div>
                                <div class="time">{{ $item->created_at->diffForHumans() }}</div>
                            </div>

                            <p class="preview">
                                {{ $item->feature_request }}
                            </p>

                        </div>

                    </div>
                @endif

            @empty

                <div class="item">
                    <div class="body">
                        <div class="title">No feedback yet</div>
                        <p class="preview">No student feedback or feature requests received.</p>
                    </div>
                </div>

            @endforelse

        </div>

    </div>

    <button class="fab">+</button>

<script>
function setInboxTab(type, btn) {

    // active tab
    document.querySelectorAll('.tab').forEach(tab => {
        tab.classList.remove('active');
    });

    btn.classList.add('active');

    // filter items
    document.querySelectorAll('.item').forEach(item => {

        const itemType = item.dataset.type;

        if (type === 'all' || itemType === type) {
            item.style.display = 'flex';
        } else {
            item.style.display = 'none';
        }

    });

}
</script>
</body>
</html>