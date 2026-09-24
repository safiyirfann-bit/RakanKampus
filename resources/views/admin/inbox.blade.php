<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Feedback Inbox</title>
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
            background: #F3FAF1;
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
            font-weight:800;
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

        .stat-card p.value.accent{
            color:#d97706;
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
            background:#4a7856;
            color:white;
            border-color:#4a7856;
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

        .item.unread{
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
        }

        .type-icon.feedback{
            background:rgba(59,130,246,.12);
            color:#3b82f6;
        }

        .type-icon.feature{
            background:rgba(168,85,247,.12);
            color:#9333ea;
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
            overflow:hidden;
            text-overflow:ellipsis;
            white-space:nowrap;
        }

        .item-delete-btn{
            flex-shrink:0;
            width:34px;
            height:34px;
            border-radius:10px;
            border:1px solid var(--admin-border);
            background:transparent;
            color:var(--admin-muted);
            display:flex;
            align-items:center;
            justify-content:center;
            cursor:pointer;
            font-size:15px;
            transition:.2s;
        }

        .item-delete-btn:hover{
            background:rgba(239,68,68,.15);
            border-color:#ef4444;
            color:#ef4444;
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
</style>
</head>
<body>

@include('partials.admin-nav', ['active' => 'inbox', 'unansweredCount' => $unansweredCount])

<div class="page-header">
    <h1>Feedback Inbox</h1>
    <p>Student feedback, feature requests & reported issues</p>
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
                    <div class="item {{ !$item->is_read ? 'unread' : '' }}" data-type="feedback" data-feedback-id="{{ $item->id }}">

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

                        <button type="button" class="item-delete-btn" aria-label="Delete" onclick="deleteFeedbackItem({{ $item->id }})">
                            <i class="ti ti-trash" aria-hidden="true"></i>
                        </button>

                    </div>
                @endif

                @if($item->feature_request)
                    <div class="item {{ !$item->is_read ? 'unread' : '' }}" data-type="feature" data-feedback-id="{{ $item->id }}">

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

                        <button type="button" class="item-delete-btn" aria-label="Delete" onclick="deleteFeedbackItem({{ $item->id }})">
                            <i class="ti ti-trash" aria-hidden="true"></i>
                        </button>

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

function deleteFeedbackItem(id) {
    if (!confirm('Delete this feedback? This cannot be undone.')) return;

    fetch(`/admin/inbox/${id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
    })
        .then(res => res.json())
        .then(data => {
            if (!data || !data.success) {
                alert('Could not delete this feedback right now. Please try again.');
                return;
            }

            document.querySelectorAll(`.item[data-feedback-id="${id}"]`).forEach(el => el.remove());
            updateInboxStats();

            const list = document.getElementById('inboxList');
            if (!list.querySelector('.item')) {
                list.innerHTML = `
                    <div class="item">
                        <div class="body">
                            <div class="title">No feedback yet</div>
                            <p class="preview">No student feedback or feature requests received.</p>
                        </div>
                    </div>`;
            }
        })
        .catch(() => alert('Connection problem. Please try again.'));
}

function updateInboxStats() {
    const items = document.querySelectorAll('#inboxList .item[data-feedback-id]');
    // A single feedback record can render as two rows (feedback + feature request),
    // so count distinct record ids rather than rows for the "Total" stat.
    const ids = new Set();
    let unread = 0;
    items.forEach(el => {
        const id = el.dataset.feedbackId;
        if (!ids.has(id)) {
            ids.add(id);
            if (el.classList.contains('unread')) unread++;
        }
    });
    const totalEl = document.querySelector('.stat-card .value');
    const unreadEl = document.querySelector('.stat-card .value.accent');
    if (totalEl) totalEl.textContent = ids.size;
    if (unreadEl) unreadEl.textContent = unread;
}
</script>
</body>
</html>