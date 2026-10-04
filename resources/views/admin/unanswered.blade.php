<!DOCTYPE html>
<html lang="en">
<head>
@include('partials.font')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Unanswered Questions - RakanKampus Admin</title>
    <style>
        body { margin: 0; }
        .un-sort { height: 42px; border-radius: 99px; padding-left: 16px !important; border: 1px solid var(--a-line); background: #fff; color: var(--a-ink2); font-weight: 700; font-size: 13px; padding: 0 12px; font-family: inherit; outline: 0; cursor: pointer; }
        .un-sort:focus { border-color: #b9d4c1; }
        .un-q { display: block; color: var(--a-ink); font-weight: 600; line-height: 1.45; }
        .un-sub { display: block; font-size: 12px; color: var(--a-mute); margin-top: 3px; }
        .un-when { white-space: nowrap; font-size: 13px; }
        .un-when small { display: block; font-size: 11.5px; color: #9aa8a0; margin-top: 2px; }
        .un-bulk { display: none; align-items: center; gap: 12px; padding: 10px 20px; background: var(--a-g50); border-bottom: 1px solid #eef3ef; font-size: 13px; font-weight: 700; color: var(--a-g800); }
        .un-bulk.on { display: flex; }
        .un-bulk .adm-btn { margin-left: auto; }
        tr.sel td { background: #f6faf7; }
        .un-panel { display: none; }
        .un-panel.on { display: block; }
        .adm-table td.cb, .adm-table th.cb { width: 44px; padding-right: 0; }
        .adm-table td.cb { padding-top: 15px; }
        .un-acts { display: flex; gap: 8px; justify-content: flex-end; align-items: center; }
        .un-acts form { margin: 0; }
    </style>
</head>
<body>

@php
    $answered = $history->where('resolution', 'answered');
    $dismissed = $history->where('resolution', '!=', 'answered');
    $avgLabel = $avgAnswerHours === null ? '—' : ($avgAnswerHours < 1 ? round($avgAnswerHours * 60) . ' min' : ($avgAnswerHours < 48 ? round($avgAnswerHours, 1) . ' h' : round($avgAnswerHours / 24, 1) . ' d'));
    $svgBin = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6"/></svg>';
@endphp

@include('partials.admin-nav', ['active' => 'unanswered', 'unansweredCount' => $questions->count(), 'unreadFeedbackCount' => $unreadFeedbackCount])

<div class="adm-wrap">

    <div class="adm-ph">
        <div>
            <h1>Unanswered questions</h1>
            <p>Teach the bot: answer once and it will reply to every student who asks.</p>
        </div>
        <div class="r">
            <select class="un-sort" id="sortSelect" onchange="location.href = @js(route('admin.unanswered.index')) + '?sort=' + this.value" aria-label="Sort">
                <option value="newest" @selected($sort === 'newest')>Sort: Newest first</option>
                <option value="oldest" @selected($sort === 'oldest')>Sort: Oldest first</option>
                <option value="most_asked" @selected($sort === 'most_asked')>Sort: Most asked</option>
            </select>
        </div>
    </div>

    @if(session('status'))
        <div class="adm-flash">{{ session('status') }}</div>
    @endif
    @if($errors->any())
        <div class="adm-flash" style="background:#fdecec;border-color:#f3d0d0;color:#b42318">{{ $errors->first() }}</div>
    @endif

    <div class="adm-kpis">
        <div class="adm-card adm-k">
            <div class="adm-ci t-amber"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="9"/><path d="M12 8v5M12 16h.01"/></svg></div>
            <div><b id="kWaiting">{{ $questions->count() }}</b><span>Waiting for an answer</span></div>
            @if($questions->count())<em class="t-red">needs you</em>@endif
        </div>
        <div class="adm-card adm-k">
            <div class="adm-ci t-green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 2l4 4-4 4M3 11v-1a4 4 0 0 1 4-4h14M7 22l-4-4 4-4M21 13v1a4 4 0 0 1-4 4H3"/></svg></div>
            <div><b id="kAsked">{{ $questions->sum('asked_count') }}</b><span>Times asked by students</span></div>
        </div>
        <div class="adm-card adm-k">
            <div class="adm-ci t-blue"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg></div>
            <div><b>{{ $answeredTotal }}</b><span>Answered so far</span></div>
        </div>
        <div class="adm-card adm-k">
            <div class="adm-ci t-violet"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg></div>
            <div><b>{{ $avgLabel }}</b><span>Avg. time to answer</span></div>
        </div>
    </div>

    <section class="adm-card flush">
        <div class="adm-tabs">
            <button type="button" class="tab on" data-tab="pending" onclick="setTab('pending', this)">Waiting <i id="cntPending">{{ $questions->count() }}</i></button>
            <button type="button" class="tab" data-tab="answered" onclick="setTab('answered', this)">Answered <i id="cntAnswered">{{ $answered->count() }}</i></button>
            <button type="button" class="tab" data-tab="dismissed" onclick="setTab('dismissed', this)">Dismissed <i id="cntDismissed">{{ $dismissed->count() }}</i></button>
        </div>

        <!-- WAITING -->
        <div class="un-panel on" id="panel-pending">
            <div id="pendingToolbar" class="un-bulk">
                <span><span id="selectedCount">0</span> selected</span>
                <button type="button" id="bulkDeleteBtn" class="adm-btn danger sm" onclick="bulkDeleteSelected()">{!! $svgBin !!} Delete selected</button>
            </div>
            <table class="adm-table">
                <thead>
                    <tr>
                        <th class="cb"><input type="checkbox" class="adm-check" id="selectAllCheckbox" onchange="toggleSelectAll(this)" aria-label="Select all" @disabled($questions->isEmpty())></th>
                        <th>Question</th>
                        <th style="width:120px">Asked</th>
                        <th style="width:150px">Last asked</th>
                        <th style="width:310px"></th>
                    </tr>
                </thead>
                <tbody id="pendingListWrap">
                    @forelse($questions as $q)
                        <tr data-question-id="{{ $q->id }}" data-asked-count="{{ $q->asked_count }}">
                            <td class="cb"><input type="checkbox" class="adm-check q-select-checkbox" value="{{ $q->id }}" onchange="updateBulkToolbar()" aria-label="Select this question"></td>
                            <td>
                                <span class="un-q">{{ $q->question }}</span>
                                <span class="un-sub">No matching answer yet · first asked {{ $q->created_at->format('j M') }}</span>
                            </td>
                            <td>
                                <span class="adm-pill {{ $q->asked_count >= 3 ? 't-red' : 't-amber' }}">{{ $q->asked_count }}× asked</span>
                            </td>
                            <td class="un-when">{{ $q->updated_at->diffForHumans() }}<small>{{ $q->updated_at->format('j M, g:i A') }}</small></td>
                            <td>
                                <div class="un-acts">
                                    <button type="button" class="adm-btn g sm" onclick="openAnswerModal({{ $q->id }}, @js($q->question))">＋ Add to knowledge base</button>
                                    <form action="{{ route('admin.unanswered.resolve', $q->id) }}" method="POST"
                                          onsubmit="return RKDialog.confirmForm(event, { scene: 'chat', title: 'Dismiss this question?', message: @js($q->question), warn: 'It moves to the Dismissed tab without adding an answer.', confirmText: 'Dismiss' })">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="adm-btn o sm">Dismiss</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr class="empty-row"><td colspan="5">
                            <div class="adm-empty">
                                <div class="ic t-green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg></div>
                                <b>No unanswered questions 🎉</b>
                                Every student question already has an answer in the knowledge base.
                            </div>
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- ANSWERED + DISMISSED -->
        @foreach(['answered' => $answered, 'dismissed' => $dismissed] as $key => $list)
            <div class="un-panel" id="panel-{{ $key }}">
                <table class="adm-table">
                    <thead>
                        <tr>
                            <th>Question</th>
                            <th style="width:120px">Asked</th>
                            <th style="width:140px">Status</th>
                            <th style="width:170px">{{ $key === 'answered' ? 'Answered' : 'Dismissed' }}</th>
                            <th style="width:60px"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($list as $h)
                            <tr data-history-id="{{ $h->id }}">
                                <td><span class="un-q">{{ $h->question }}</span></td>
                                <td><span class="adm-pill" style="background:#eef2ef;color:var(--a-ink2)">{{ $h->asked_count }}× asked</span></td>
                                <td>
                                    @if($key === 'answered')
                                        <span class="adm-pill t-green">● Added to KB</span>
                                    @else
                                        <span class="adm-pill" style="background:#f1f5f2;color:var(--a-mute)">● Dismissed</span>
                                    @endif
                                </td>
                                <td class="un-when">{{ $h->updated_at->diffForHumans() }}<small>{{ $h->updated_at->format('j M, g:i A') }}</small></td>
                                <td><div class="acts"><button type="button" class="adm-ab del" title="Delete record" aria-label="Delete this record" onclick="deleteHistoryItem({{ $h->id }}, this)">{!! $svgBin !!}</button></div></td>
                            </tr>
                        @empty
                            <tr class="empty-row"><td colspan="5">
                                <div class="adm-empty">
                                    <b>Nothing here yet</b>
                                    {{ $key === 'answered' ? 'Questions you add to the knowledge base will show up here.' : 'Questions you dismiss will show up here.' }}
                                </div>
                            </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endforeach
    </section>
</div>

<!-- Answer Modal -->
<div id="answerModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Add to knowledge base</h2>
            <button type="button" class="close-btn" onclick="closeAnswerModal()">&times;</button>
        </div>
        <form id="answerForm" method="POST">
            @csrf
            <div class="form-group">
                <label>Topic</label>
                <select name="information_id" id="answer_topic" required>
                    <option value="">— Select topic —</option>
                    @foreach($topics as $topic)
                        <option value="{{ $topic->id }}">{{ $topic->main_topic }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group"><label>Intent</label><input type="text" name="intent" id="answer_intent" placeholder="e.g. yuran_pembayaran"></div>
            <div class="form-group"><label>Question</label><input type="text" name="question" id="answer_question" required></div>
            <div class="form-group"><label>Answer</label><textarea name="answer" id="answer_answer" rows="5" placeholder="Write the answer here..." required></textarea></div>
            <div class="form-group"><label>Key words</label><input type="text" name="keywords" id="answer_keywords" placeholder="e.g. fees, payment, deadline"></div>
            <div class="modal-actions">
                <button type="button" class="cancel-btn" onclick="closeAnswerModal()">Cancel</button>
                <button type="submit" class="submit-btn">Add &amp; resolve</button>
            </div>
        </form>
    </div>
</div>

<script>
    const EMPTY_PENDING = '<tr class="empty-row"><td colspan="5"><div class="adm-empty"><b>No unanswered questions \u{1F389}</b>Every student question already has an answer in the knowledge base.</div></td></tr>';

    function setTab(tab, btn) {
        document.querySelectorAll('.adm-tabs .tab').forEach(t => t.classList.toggle('on', t === btn));
        document.querySelectorAll('.un-panel').forEach(p => p.classList.toggle('on', p.id === 'panel-' + tab));
        document.getElementById('sortSelect').style.visibility = tab === 'pending' ? 'visible' : 'hidden';
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
        document.getElementById('pendingToolbar').classList.toggle('on', checked.length > 0);
        all.forEach(el => el.closest('tr').classList.toggle('sel', el.checked));
        const selectAll = document.getElementById('selectAllCheckbox');
        selectAll.checked = all.length > 0 && checked.length === all.length;
        selectAll.indeterminate = checked.length > 0 && checked.length < all.length;
    }

    function updatePendingStats() {
        const rows = document.querySelectorAll('#pendingListWrap tr[data-question-id]');
        let asked = 0;
        rows.forEach(el => { asked += parseInt(el.dataset.askedCount || '0', 10); });
        document.getElementById('kWaiting').textContent = rows.length;
        document.getElementById('kAsked').textContent = asked;
        document.getElementById('cntPending').textContent = rows.length;
        if (!rows.length) {
            document.getElementById('pendingListWrap').innerHTML = EMPTY_PENDING;
            document.getElementById('selectAllCheckbox').disabled = true;
        }
    }

    async function bulkDeleteSelected() {
        const boxes = Array.from(document.querySelectorAll('.q-select-checkbox:checked'));
        const ids = boxes.map(el => el.value);
        if (!ids.length) return;
        const ok = await RKDialog.confirm({
            scene: 'trash', tone: 'danger', confirmText: 'Delete',
            title: 'Delete ' + ids.length + (ids.length === 1 ? ' question?' : ' questions?'),
            list: boxes.slice(0, 4).map(b => ({ label: b.closest('tr').querySelector('.un-q').textContent })),
            warn: 'They will be removed without an answer. This cannot be undone.',
        });
        if (!ok) return;

        safeJsonFetch(@js(route('admin.unanswered.bulkDestroy')), {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken() },
            body: JSON.stringify({ ids }),
        }).then(({ ok, data }) => {
            if (!ok || !data || !data.success) {
                RKDialog.alert('Could not delete the selected question(s) right now. Please try again.');
                return;
            }
            ids.forEach(id => document.querySelector(`tr[data-question-id="${id}"]`)?.remove());
            document.getElementById('selectAllCheckbox').checked = false;
            updateBulkToolbar();
            updatePendingStats();
            RKToast.show({ text: ids.length + (ids.length === 1 ? ' question deleted' : ' questions deleted') });
        }).catch(() => RKDialog.alert('Connection problem. Please try again.'));
    }

    async function deleteHistoryItem(id, btn) {
        const row = btn.closest('tr');
        const ok = await RKDialog.confirm({
            scene: 'trash', tone: 'danger', confirmText: 'Delete',
            title: 'Delete this record?', message: row.querySelector('.un-q').textContent,
            warn: 'This only removes it from the list. This cannot be undone.',
        });
        if (!ok) return;

        safeJsonFetch(`/admin/unanswered/${id}`, {
            method: 'DELETE',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken() },
        }).then(({ ok, data }) => {
            if (!ok || !data || !data.success) {
                RKDialog.alert('Could not delete this record right now. Please try again.');
                return;
            }
            const panel = row.closest('.un-panel');
            const key = panel.id.replace('panel-', '');
            row.remove();
            const left = panel.querySelectorAll('tr[data-history-id]').length;
            document.getElementById(key === 'answered' ? 'cntAnswered' : 'cntDismissed').textContent = left;
            if (!left) panel.querySelector('tbody').innerHTML = '<tr class="empty-row"><td colspan="5"><div class="adm-empty"><b>Nothing here yet</b></div></td></tr>';
            RKToast.show({ text: 'Record deleted' });
        }).catch(() => RKDialog.alert('Connection problem. Please try again.'));
    }

    function openAnswerModal(id, questionText) {
        document.getElementById('answer_topic').value = '';
        document.getElementById('answer_intent').value = '';
        document.getElementById('answer_question').value = questionText;
        document.getElementById('answer_answer').value = '';
        document.getElementById('answer_keywords').value = '';
        document.getElementById('answerForm').action = @js(route('admin.unanswered.answer', '__ID__')).replace('__ID__', id);
        document.getElementById('answerModal').style.display = 'flex';
        setTimeout(() => document.getElementById('answer_topic').focus(), 50);
    }

    function closeAnswerModal() {
        document.getElementById('answerModal').style.display = 'none';
    }

    window.addEventListener('click', function (event) {
        if (event.target === document.getElementById('answerModal')) closeAnswerModal();
    });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeAnswerModal(); });
</script>

</body>
</html>
