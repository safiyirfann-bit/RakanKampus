<!DOCTYPE html>
<html lang="en">
<head>
@include('partials.font')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Inbox - RakanKampus Admin</title>
    <style>
        body { margin: 0; }
        .ib-grid { display: grid; grid-template-columns: 400px minmax(0, 1fr); gap: 20px; align-items: start; }
        .ib-list { max-height: calc(100vh - 250px); min-height: 320px; overflow-y: auto; }
        .ib-tabs { gap: 2px; padding: 0 14px; }
        .ib-tabs button { padding: 16px 8px 14px !important; }
        .ib-tabs button { font-size: 13px; }
        .item { display: flex; gap: 12px; align-items: flex-start; padding: 14px 20px; border-top: 1px solid #eef3ef; cursor: pointer; transition: background .12s; position: relative; }
        .item:first-child { border-top: 0; }
        .item:hover { background: #fafcfb; }
        .item.on { background: var(--a-g100); box-shadow: inset 3px 0 0 var(--a-dark); }
        .ib-av { width: 36px; height: 36px; border-radius: 50%; color: #fff; font-weight: 800; font-size: 13px; display: grid; place-items: center; flex-shrink: 0; }
        .ib-av.feedback { background: linear-gradient(135deg, #5f9370, #3f6e4f); }
        .ib-av.feature { background: linear-gradient(135deg, #5b8fd0, #3c6fb0); }
        .ib-av.issue { background: linear-gradient(135deg, #e36a6a, #d64545); }
        .item .body { min-width: 0; flex: 1; }
        .item .top { display: flex; gap: 8px; align-items: center; }
        .item .top b { font-size: 13.5px; font-weight: 700; color: var(--a-ink); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .item .top .dot { width: 7px; height: 7px; border-radius: 50%; background: #22c55e; flex-shrink: 0; }
        .item .top small { margin-left: auto; font-size: 12px; color: var(--a-mute); white-space: nowrap; }
        .item .pv { font-size: 13px; color: var(--a-ink2); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-top: 2px; }
        .item .adm-pill { margin-top: 7px; }
        .p-feedback { background: #e8f2eb; color: #3f6e4f; }
        .p-feature { background: #e7f0fb; color: #3c6fb0; }
        .p-issue { background: #fdecec; color: #d64545; }

        .ib-detail { padding: 30px 34px; position: sticky; top: 90px; min-height: 360px; }
        .ib-head { display: flex; gap: 14px; align-items: center; }
        .ib-head .ib-av { width: 46px; height: 46px; font-size: 17px; }
        .ib-head b { font-size: 15.5px; color: var(--a-ink); display: block; }
        .ib-head .meta { font-size: 12.5px; color: var(--a-mute); margin-top: 2px; }
        .ib-head .adm-pill { margin-left: auto; font-size: 12px; padding: 4px 11px; }
        .ib-detail h2 { font-size: 19px; font-weight: 800; margin: 24px 0 10px; letter-spacing: -.01em; color: var(--a-ink); }
        .ib-msg { font-size: 14.5px; line-height: 1.75; color: var(--a-ink2); white-space: pre-wrap; word-break: break-word; margin: 0; }
        .ib-chips { display: flex; gap: 8px; margin-top: 18px; flex-wrap: wrap; }
        .ib-foot { display: flex; gap: 10px; margin-top: 26px; padding-top: 18px; border-top: 1px solid var(--a-line); align-items: center; }
        .ib-foot .when { font-size: 12.5px; color: var(--a-mute); }
        .ib-foot .adm-btn.danger { margin-left: auto; }
        .ib-none { padding: 38px 16px; }
    </style>
</head>
<body>

@php
    $unreadNow = $feedbacks->where('is_read', false)->count();
    $rows = collect();
    foreach ($feedbacks as $f) {
        $prog = \App\Support\MatricNumber::programme($f->student_id);
        $base = ['id' => $f->id, 'name' => $f->user_name ?: 'Student', 'sid' => $f->student_id, 'prog' => $prog, 'at' => $f->created_at, 'unread' => ! $f->is_read];
        if (filled($f->feedback)) $rows->push($base + ['type' => 'feedback', 'label' => 'Feedback', 'title' => 'Feedback', 'text' => $f->feedback]);
        if (filled($f->feature_request)) $rows->push($base + ['type' => 'feature', 'label' => 'Feature request', 'title' => 'Feature request', 'text' => $f->feature_request]);
        if (filled($f->issue_report)) $rows->push($base + ['type' => 'issue', 'label' => 'Report issue', 'title' => $f->issue_type ?: 'Issue report', 'text' => $f->issue_report]);
    }
    $count = fn ($t) => $rows->where('type', $t)->count();
    $svgBin = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6"/></svg>';
@endphp

@include('partials.admin-nav', ['active' => 'inbox', 'unansweredCount' => $unansweredCount, 'unreadFeedbackCount' => 0])

<div class="adm-wrap">

    <div class="adm-ph">
        <div>
            <h1>Inbox</h1>
            <p>Feedback, feature requests and problems reported by students.</p>
        </div>
        <div class="r">
            @if($unreadNow)
                <span class="adm-pill t-green" style="font-size:12.5px;padding:6px 12px">● {{ $unreadNow }} new since your last visit</span>
            @endif
        </div>
    </div>

    <div class="ib-grid">
        <section class="adm-card flush">
            <div class="adm-tabs ib-tabs">
                <button type="button" class="tab on" data-type="all" onclick="setInboxTab('all', this)">All <i>{{ $rows->count() }}</i></button>
                <button type="button" class="tab" data-type="feedback" onclick="setInboxTab('feedback', this)">Feedback <i>{{ $count('feedback') }}</i></button>
                <button type="button" class="tab" data-type="feature" onclick="setInboxTab('feature', this)">Requests <i>{{ $count('feature') }}</i></button>
                <button type="button" class="tab" data-type="issue" onclick="setInboxTab('issue', this)">Issues <i>{{ $count('issue') }}</i></button>
            </div>

            <div class="ib-list" id="inboxList">
                @foreach($rows as $i => $r)
                    <div class="item" tabindex="0" role="button"
                         data-type="{{ $r['type'] }}" data-feedback-id="{{ $r['id'] }}"
                         data-name="{{ $r['name'] }}" data-sid="{{ $r['sid'] }}" data-prog="{{ $r['prog'] }}"
                         data-label="{{ $r['label'] }}" data-title="{{ $r['title'] }}"
                         data-ago="{{ $r['at']->diffForHumans() }}" data-when="{{ $r['at']->format('l, j M Y · g:i A') }}"
                         onclick="openItem(this)" onkeydown="if (event.key === 'Enter') openItem(this)">
                        <div class="ib-av {{ $r['type'] }}">{{ strtoupper(mb_substr($r['name'], 0, 1)) }}</div>
                        <div class="body">
                            <div class="top">
                                <b>{{ $r['name'] }}</b>
                                @if($r['unread'])<span class="dot" title="New"></span>@endif
                                <small>{{ $r['at']->diffForHumans(null, true, true) }}</small>
                            </div>
                            <div class="pv">{{ $r['text'] }}</div>
                            <span class="adm-pill p-{{ $r['type'] }}">{{ $r['type'] === 'issue' ? $r['title'] : $r['label'] }}</span>
                        </div>
                    </div>
                @endforeach
                <div class="adm-empty ib-none" id="listEmpty" style="{{ $rows->isEmpty() ? '' : 'display:none' }}">
                    <div class="ic t-green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="5" width="18" height="14" rx="3"/><path d="m3 8 9 6 9-6"/></svg></div>
                    <b>Nothing here</b>
                    No messages from students in this tab.
                </div>
            </div>
        </section>

        <section class="adm-card ib-detail" id="detail">
            <div class="adm-empty" id="detailEmpty">
                <div class="ic t-green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="5" width="18" height="14" rx="3"/><path d="m3 8 9 6 9-6"/></svg></div>
                <b>{{ $rows->isEmpty() ? 'Inbox is empty' : 'Select a message' }}</b>
                {{ $rows->isEmpty() ? 'Student feedback, feature requests and issue reports will appear here.' : 'Pick a message on the left to read it.' }}
            </div>
            <div id="detailBody" style="display:none">
                <div class="ib-head">
                    <div class="ib-av" id="dAv"></div>
                    <div>
                        <b id="dName"></b>
                        <div class="meta" id="dMeta"></div>
                    </div>
                    <span class="adm-pill" id="dPill"></span>
                </div>
                <h2 id="dTitle"></h2>
                <p class="ib-msg" id="dText"></p>
                <div class="ib-foot">
                    <span class="when" id="dWhen"></span>
                    <button type="button" class="adm-btn o sm" id="dCopy" onclick="copyMessage()">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="9" y="9" width="12" height="12" rx="2"/><path d="M5 15V5a2 2 0 0 1 2-2h10"/></svg> Copy text
                    </button>
                    <button type="button" class="adm-btn danger sm" onclick="deleteFeedbackItem(current && current.dataset.feedbackId)">{!! $svgBin !!} Delete</button>
                </div>
            </div>
        </section>
    </div>
</div>

<script>
let current = null;
let currentTab = 'all';

function setInboxTab(type, btn) {
    currentTab = type;
    document.querySelectorAll('.ib-tabs .tab').forEach(t => t.classList.toggle('on', t === btn));
    let first = null, keep = false;
    document.querySelectorAll('#inboxList .item').forEach(item => {
        const show = type === 'all' || item.dataset.type === type;
        item.style.display = show ? '' : 'none';
        if (show && !first) first = item;
        if (show && item === current) keep = true;
    });
    document.getElementById('listEmpty').style.display = first ? 'none' : '';
    if (!keep) openItem(first);
}

function openItem(item) {
    current = item;
    document.querySelectorAll('#inboxList .item').forEach(i => i.classList.toggle('on', i === item));
    document.getElementById('detailEmpty').style.display = item ? 'none' : '';
    document.getElementById('detailBody').style.display = item ? '' : 'none';
    if (!item) return;
    const d = item.dataset;
    const av = document.getElementById('dAv');
    av.className = 'ib-av ' + d.type;
    av.textContent = (d.name || 'S').charAt(0).toUpperCase();
    document.getElementById('dName').textContent = d.name;
    document.getElementById('dMeta').textContent = [d.sid, d.prog, d.ago].filter(Boolean).join(' · ');
    const pill = document.getElementById('dPill');
    pill.className = 'adm-pill p-' + d.type;
    pill.textContent = '● ' + d.label;
    document.getElementById('dTitle').textContent = d.title;
    document.getElementById('dText').textContent = item.querySelector('.pv').textContent.trim();
    document.getElementById('dWhen').textContent = d.when;
}

function copyMessage() {
    if (!current) return;
    const text = current.querySelector('.pv').textContent.trim();
    (navigator.clipboard ? navigator.clipboard.writeText(text) : Promise.reject())
        .then(() => RKToast.show({ text: 'Message copied' }))
        .catch(() => RKDialog.alert('Could not copy the text on this browser.'));
}

async function deleteFeedbackItem(id) {
    if (!id) return;
    const parts = document.querySelectorAll(`.item[data-feedback-id="${id}"]`);
    const ok = await RKDialog.confirm({
        scene: 'trash', tone: 'danger', confirmText: 'Delete',
        title: 'Delete this message?',
        message: current ? current.dataset.name + ' · ' + current.dataset.label : '',
        warn: parts.length > 1 ? 'This student sent ' + parts.length + ' messages in one form; all of them will be deleted. This cannot be undone.' : 'This cannot be undone.',
    });
    if (!ok) return;

    fetch(`/admin/inbox/${id}`, {
        method: 'DELETE',
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
    })
        .then(res => res.json())
        .then(data => {
            if (!data || !data.success) {
                RKDialog.alert('Could not delete this message right now. Please try again.');
                return;
            }
            parts.forEach(el => el.remove());
            updateCounts();
            current = null;
            setInboxTab(currentTab, document.querySelector(`.ib-tabs .tab[data-type="${currentTab}"]`));
            RKToast.show({ text: 'Message deleted' });
        })
        .catch(() => RKDialog.alert('Connection problem. Please try again.'));
}

function updateCounts() {
    document.querySelectorAll('.ib-tabs .tab').forEach(t => {
        const type = t.dataset.type;
        const n = document.querySelectorAll(type === 'all' ? '#inboxList .item' : `#inboxList .item[data-type="${type}"]`).length;
        t.querySelector('i').textContent = n;
    });
}

openItem(document.querySelector('#inboxList .item'));
</script>
</body>
</html>
