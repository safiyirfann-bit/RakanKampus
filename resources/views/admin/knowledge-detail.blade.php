<!DOCTYPE html>
<html lang="en">
<head>
@include('partials.font')
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $information->main_topic }} - RakanKampus Admin</title>
<style>
  body { margin: 0; }
  .kb-q { display: block; color: var(--a-ink); font-weight: 600; line-height: 1.45; }
  .kb-ask { display: inline-block; margin-top: 5px; font-size: 11.5px; font-weight: 700; color: var(--a-mute); }
  .kb-ans { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.5; max-width: 460px; }
  .kb-kw { display: block; margin-top: 5px; font-size: 12px; color: #9aa8a0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 460px; }
  #entriesTable td:first-child { width: 170px; }
  #entriesTable .adm-code { display: inline-block; max-width: 160px; overflow: hidden; text-overflow: ellipsis; vertical-align: top; }
  #entriesTable tbody tr { cursor: default; }
  .kb-none td { text-align: center; color: var(--a-mute); padding: 30px 16px; }
</style>
</head>
<body>

@php
  $cats = $entries->pluck('category')->filter()->unique()->values();
  $trend = $askedLastWeek > 0 ? round(($askedThisWeek - $askedLastWeek) * 100 / $askedLastWeek) : null;
  $svgEye = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>';
  $svgPen = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>';
  $svgBin = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6"/></svg>';
@endphp

@include('partials.admin-nav', ['active' => 'knowledge', 'crumb' => $information->main_topic, 'unansweredCount' => $unansweredCount, 'unreadFeedbackCount' => $unreadFeedbackCount])

<div class="adm-wrap">
    <x-admin-hero :title="e($information->main_topic)" :sub="$information->description" label="Knowledge base · Topic"
      :back="route('admin.knowledge')" back-label="All topics"
      :kpis="[
        ['value' => $entries->count(), 'label' => 'Q&A entries', 'tag' => $cats->count() . ' ' . \Illuminate\Support\Str::plural('category', $cats->count())],
        ['value' => number_format($askedThisWeek), 'label' => 'Asked this week', 'tag' => $trend === null ? 'last 7 days' : ($trend >= 0 ? '▲ ' . $trend . '%' : '▼ ' . abs($trend) . '%'), 'tone' => ($trend ?? 0) < 0 ? 'red' : null],
        ['value' => $information->updated_at?->diffForHumans() ?? '—', 'small' => true, 'label' => 'Last updated', 'tag' => $information->updated_at?->format('j M Y')],
      ]">
      <x-slot:actions>
        <button type="button" class="adm-btn w" onclick="openModal()">＋ Add Q&amp;A</button>
        <button type="button" class="adm-btn gl" onclick="openTopicModal()">{!! $svgPen !!} Edit topic</button>
      </x-slot:actions>
    </x-admin-hero>

    @if(session('status'))
        <div class="adm-flash">{{ session('status') }}</div>
    @endif
    @if($errors->any())
        <div class="adm-flash" style="background:#fdecec;border-color:#f3d0d0;color:#b42318">{{ $errors->first() }}</div>
    @endif

    <section class="adm-card flush">
        <div class="adm-ch">
            <div>
                <h3>Questions &amp; answers</h3>
                <p>What the bot says when students ask about this topic</p>
            </div>
            <div class="r">
                <label class="adm-search">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
                    <input type="text" placeholder="Search question, answer, keyword…" id="searchInput" oninput="filterEntries()">
                </label>
                @if($cats->count() > 1)
                    <div class="adm-seg" id="catSeg">
                        <button type="button" class="on" data-cat="" onclick="setCat(this)">All</button>
                        @foreach($cats as $c)
                            <button type="button" data-cat="{{ $c }}" onclick="setCat(this)">{{ $c }}</button>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <table class="adm-table" id="entriesTable">
            <thead>
                <tr>
                    <th style="width:170px">Intent</th>
                    <th style="width:28%">Question</th>
                    <th>Answer</th>
                    <th style="width:150px">Category</th>
                    <th style="width:124px"></th>
                </tr>
            </thead>
            <tbody id="entriesBody">
                @forelse($entries as $entry)
                    @php $asked = (int) ($askCounts[$entry->id] ?? 0); @endphp
                    <tr data-cat="{{ $entry->category }}">
                        <td><span class="adm-code" title="{{ $entry->intent }}">{{ $entry->intent }}</span></td>
                        <td>
                            <span class="kb-q">{{ $entry->question }}</span>
                            @if($asked)<span class="kb-ask">Used {{ $asked }}× by the bot</span>@endif
                        </td>
                        <td>
                            <span class="kb-ans" title="{{ $entry->answer }}">{{ $entry->answer }}</span>
                            @if($entry->keywords)<span class="kb-kw" title="{{ $entry->keywords }}">🔑 {{ $entry->keywords }}</span>@endif
                        </td>
                        <td>@if($entry->category)<span class="adm-chip">{{ $entry->category }}</span>@else<span style="color:#b5c1b9">—</span>@endif</td>
                        <td>
                            <div class="acts">
                                <button type="button" class="adm-ab" title="View" aria-label="View"
                                    onclick="openViewModal(@js($entry->intent), @js($entry->question), @js($entry->answer), @js($entry->category), @js($entry->keywords))">{!! $svgEye !!}</button>
                                <button type="button" class="adm-ab" title="Edit" aria-label="Edit"
                                    onclick="openEditModal({{ $entry->id }}, @js($entry->intent), @js($entry->question), @js($entry->answer), @js($entry->category), @js($entry->keywords))">{!! $svgPen !!}</button>
                                <form method="POST" action="{{ route('admin.information.entries.destroy', [$information->id, $entry->id]) }}" style="display:inline"
                                      onsubmit="return RKDialog.confirmForm(event, { scene: 'trash', title: 'Delete this Q&A?', message: @js($entry->question), warn: 'The bot will stop using this answer. This cannot be undone.', confirmText: 'Delete', tone: 'danger' })">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="adm-ab del" title="Delete" aria-label="Delete">{!! $svgBin !!}</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr class="kb-none-static">
                        <td colspan="5">
                            <div class="adm-empty">
                                <div class="ic t-green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 19.5V5a2 2 0 0 1 2-2h13v16H6.5A2.5 2.5 0 0 0 4 21.5"/><path d="M8 7h7M8 11h5"/></svg></div>
                                <b>No Q&amp;A in this topic yet</b>
                                Add the first question and answer so the bot can reply.
                                <div style="margin-top:14px"><button type="button" class="adm-btn g" onclick="openModal()">＋ Add Q&amp;A</button></div>
                            </div>
                        </td>
                    </tr>
                @endforelse
                <tr class="kb-none" id="noMatch" style="display:none"><td colspan="5">No entries match your search.</td></tr>
            </tbody>
        </table>
        <div class="adm-foot" id="entriesFoot">Showing {{ $entries->count() }} {{ Str::plural('entry', $entries->count()) }}</div>
    </section>
</div>

<!-- View Data Modal -->
<div id="viewModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>View Q&amp;A</h2>
            <button type="button" class="close-btn" onclick="closeViewModal()">&times;</button>
        </div>
        <div class="form-group"><label>Intent</label><div class="view-field" id="view_intent"></div></div>
        <div class="form-group"><label>Question</label><div class="view-field" id="view_question"></div></div>
        <div class="form-group"><label>Answer</label><div class="view-field" id="view_answer"></div></div>
        <div class="form-group"><label>Category</label><div class="view-field" id="view_category"></div></div>
        <div class="form-group"><label>Key words</label><div class="view-field" id="view_keywords"></div></div>
        <div class="modal-actions">
            <button type="button" class="cancel-btn" onclick="closeViewModal()">Close</button>
        </div>
    </div>
</div>

<!-- Add Data Modal -->
<div id="addModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Add Q&amp;A</h2>
            <button type="button" class="close-btn" onclick="closeModal()">&times;</button>
        </div>
        <form action="{{ route('admin.information.entries.store', $information->id) }}" method="POST">
            @csrf
            <div class="form-group"><label>Intent</label><input type="text" name="intent" placeholder="e.g. course_registration" required></div>
            <div class="form-group"><label>Question</label><input type="text" name="question" placeholder="e.g. When does registration open?" required></div>
            <div class="form-group"><label>Answer</label><textarea name="answer" rows="5" placeholder="Write the answer here..." required></textarea></div>
            <div class="form-group"><label>Category</label><input type="text" name="category" placeholder="e.g. Akademik" list="catList"></div>
            <div class="form-group"><label>Key words</label><input type="text" name="keywords" placeholder="e.g. registration, semester, course"></div>
            <div class="modal-actions">
                <button type="button" class="cancel-btn" onclick="closeModal()">Cancel</button>
                <button type="submit" class="submit-btn">Add Q&amp;A</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Data Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Edit Q&amp;A</h2>
            <button type="button" class="close-btn" onclick="closeEditModal()">&times;</button>
        </div>
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group"><label>Intent</label><input type="text" name="intent" id="edit_intent" required></div>
            <div class="form-group"><label>Question</label><input type="text" name="question" id="edit_question" required></div>
            <div class="form-group"><label>Answer</label><textarea name="answer" id="edit_answer" rows="5" required></textarea></div>
            <div class="form-group"><label>Category</label><input type="text" name="category" id="edit_category" list="catList"></div>
            <div class="form-group"><label>Key words</label><input type="text" name="keywords" id="edit_keywords"></div>
            <div class="modal-actions">
                <button type="button" class="cancel-btn" onclick="closeEditModal()">Cancel</button>
                <button type="submit" class="submit-btn">Save changes</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Topic Modal -->
<div id="topicModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Edit topic</h2>
            <button type="button" class="close-btn" onclick="closeTopicModal()">&times;</button>
        </div>
        <form action="{{ route('admin.information.update', $information->id) }}" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="back" value="topic">
            <div class="form-group"><label>Topic name</label><input type="text" name="main_topic" value="{{ $information->main_topic }}" required></div>
            <div class="form-group"><label>Description</label><textarea name="description" rows="3" required>{{ $information->description }}</textarea></div>
            <div class="modal-actions">
                <button type="button" class="cancel-btn" onclick="closeTopicModal()">Cancel</button>
                <button type="submit" class="submit-btn">Save topic</button>
            </div>
        </form>
    </div>
</div>

<datalist id="catList">
    @foreach($cats as $c)<option value="{{ $c }}">@endforeach
</datalist>

<script>
function show(id) { document.getElementById(id).style.display = 'flex'; }
function hide(id) { document.getElementById(id).style.display = 'none'; }
function openModal() { show('addModal'); }
function closeModal() { hide('addModal'); }
function openTopicModal() { show('topicModal'); }
function closeTopicModal() { hide('topicModal'); }
function closeViewModal() { hide('viewModal'); }
function closeEditModal() { hide('editModal'); }

function openViewModal(intent, question, answer, category, keywords) {
    document.getElementById('view_intent').textContent = intent;
    document.getElementById('view_question').textContent = question;
    document.getElementById('view_answer').textContent = answer;
    document.getElementById('view_category').textContent = category || '—';
    document.getElementById('view_keywords').textContent = keywords || '—';
    show('viewModal');
}

function openEditModal(id, intent, question, answer, category, keywords) {
    document.getElementById('edit_intent').value = intent;
    document.getElementById('edit_question').value = question;
    document.getElementById('edit_answer').value = answer;
    document.getElementById('edit_category').value = category || '';
    document.getElementById('edit_keywords').value = keywords || '';
    document.getElementById('editForm').action =
        @js(route('admin.information.entries.update', [$information->id, '__ID__'])).replace('__ID__', id);
    show('editModal');
}

window.addEventListener('click', function (event) {
    ['addModal', 'editModal', 'viewModal', 'topicModal'].forEach(function (id) {
        if (event.target === document.getElementById(id)) hide(id);
    });
});
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') ['addModal', 'editModal', 'viewModal', 'topicModal'].forEach(hide);
});

let currentCat = '';
function setCat(btn) {
    currentCat = btn.dataset.cat;
    document.querySelectorAll('#catSeg button').forEach(b => b.classList.toggle('on', b === btn));
    filterEntries();
}

function filterEntries() {
    const query = document.getElementById('searchInput').value.toLowerCase();
    const rows = document.querySelectorAll('#entriesBody tr[data-cat]');
    let shown = 0;
    rows.forEach(row => {
        const ok = row.textContent.toLowerCase().includes(query) && (!currentCat || row.dataset.cat === currentCat);
        row.style.display = ok ? '' : 'none';
        if (ok) shown++;
    });
    document.getElementById('noMatch').style.display = rows.length && !shown ? '' : 'none';
    document.getElementById('entriesFoot').textContent = (query || currentCat)
        ? 'Showing ' + shown + ' of ' + rows.length + ' entries'
        : 'Showing ' + rows.length + (rows.length === 1 ? ' entry' : ' entries');
}
</script>

</body>
</html>
