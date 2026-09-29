<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Knowledge base - RakanKampus Admin</title>
<style>
  body { margin: 0; }
  .kb-ti { width: 40px; height: 40px; border-radius: 12px; display: grid; place-items: center; }
  .kb-ti svg { width: 19px; height: 19px; }
  .kb-name { display: block; font-size: 14px; font-weight: 700; color: var(--a-ink); text-decoration: none; line-height: 1.4; }
  .kb-name:hover { color: var(--a-g700); text-decoration: underline; }
  .kb-tags { display: flex; gap: 6px; margin-top: 6px; flex-wrap: nowrap; overflow: hidden; max-width: 460px; }
  .kb-tags .adm-chip { max-width: 220px; overflow: hidden; text-overflow: ellipsis; }
  .kb-desc { display: block; font-size: 12.5px; color: var(--a-mute); margin-top: 4px; max-width: 520px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .kb-cnt b { font-size: 14px; } .kb-cnt small { font-size: 12px; color: var(--a-mute); font-weight: 600; }
  .kb-cnt .low { display: block; font-size: 11px; font-weight: 700; color: var(--a-amber); margin-top: 3px; }
  .kb-use { display: flex; gap: 10px; align-items: center; }
  .kb-bar { flex: 1; height: 6px; border-radius: 99px; background: #eef3ef; overflow: hidden; }
  .kb-bar i { display: block; height: 100%; border-radius: 99px; background: linear-gradient(90deg, var(--a-g500), var(--a-g700)); }
  .kb-use b { width: 30px; text-align: right; font-size: 13.5px; }
  .kb-upd { font-size: 13px; white-space: nowrap; }
  .kb-upd small { display: block; font-size: 11.5px; color: #9aa8a0; margin-top: 2px; }
  #topicTable td { vertical-align: middle; }
  #topicTable tbody tr { cursor: pointer; }
  #topicTable td.acts-cell { cursor: default; }
  .kb-none td { text-align: center; color: var(--a-mute); padding: 30px 16px; }
</style>
</head>
<body>

@php
  $maxUse = max(1, (int) $usage->max());
  $trend = $usedLastWeek > 0 ? (int) round(($usedThisWeek - $usedLastWeek) * 100 / $usedLastWeek) : null;
  $thin = $informations->filter(fn ($i) => $i->knowledge_entries_count < 10)->count();
  $svgOpen = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5V5a2 2 0 0 1 2-2h13v16H6.5A2.5 2.5 0 0 0 4 21.5"/><path d="M8 7h7M8 11h5"/></svg>';
  $svgPen = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>';
  $svgBin = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6"/></svg>';
@endphp

@include('partials.admin-nav', ['active' => 'knowledge', 'unansweredCount' => $unansweredCount, 'unreadFeedbackCount' => $unreadFeedbackCount])

<div class="adm-wrap">

  <div class="adm-ph">
    <div>
      <h1>Knowledge base</h1>
      <p>Every topic and answer the bot knows. Click a topic to manage its Q&amp;A.</p>
    </div>
    <div class="r">
      <button type="button" class="adm-btn g" onclick="openModal()">＋ New topic</button>
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
      <div class="adm-ci t-green">{!! $svgOpen !!}</div>
      <div><b>{{ $informations->count() }}</b><span>Topics</span></div>
    </div>
    <div class="adm-card adm-k">
      <div class="adm-ci t-blue"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 5h16v11H8l-4 4z"/></svg></div>
      <div><b>{{ number_format($entryCount) }}</b><span>Q&amp;A entries</span></div>
    </div>
    <div class="adm-card adm-k">
      <div class="adm-ci t-amber"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="m7 14 4-4 4 3 5-6"/></svg></div>
      <div><b>{{ number_format($usedThisWeek) }}</b><span>Answers used this week</span></div>
      @if($trend !== null)<em class="{{ $trend >= 0 ? 't-green' : 't-red' }}">{{ $trend >= 0 ? '▲' : '▼' }} {{ abs($trend) }}%</em>@endif
    </div>
    <div class="adm-card adm-k">
      <div class="adm-ci t-red"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3 2 20h20L12 3z"/><path d="M12 10v4M12 17h.01"/></svg></div>
      <div><b>{{ $thin }}</b><span>Topics under 10 Q&amp;A</span></div>
    </div>
  </div>

  <section class="adm-card flush">
    <div class="adm-ch">
      <div>
        <h3>All topics</h3>
        <p id="sortNote">Sorted by most used this week</p>
      </div>
      <div class="r">
        <label class="adm-search">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
          <input type="text" placeholder="Search topics…" id="topicSearch" oninput="filterTopics()">
        </label>
        <div class="adm-seg" id="sortSeg">
          <button type="button" class="on" data-sort="used" onclick="sortTopics(this)">Most used</button>
          <button type="button" data-sort="recent" onclick="sortTopics(this)">Recent</button>
          <button type="button" data-sort="az" onclick="sortTopics(this)">A–Z</button>
        </div>
      </div>
    </div>

    <table class="adm-table" id="topicTable">
      <thead>
        <tr>
          <th style="width:72px"></th>
          <th>Topic</th>
          <th style="width:120px">Entries</th>
          <th style="width:210px">Used this week</th>
          <th style="width:140px">Updated</th>
          <th style="width:134px"></th>
        </tr>
      </thead>
      <tbody id="topicBody">
        @foreach($informations as $info)
          @php
            $look = \App\Support\TopicLook::for($info->main_topic);
            $tags = \App\Support\TopicLook::tags($info->description, 2);
            $used = (int) ($usage[$info->id] ?? 0);
            $url = route('admin.information.show', $info->id);
          @endphp
          <tr data-used="{{ $used }}" data-updated="{{ $info->updated_at?->timestamp ?? 0 }}" data-name="{{ mb_strtolower($info->main_topic) }}"
              onclick="if (!event.target.closest('.acts-cell, a')) location.href = @js($url)">
            <td><div class="kb-ti" style="background:{{ $look['bg'] }};color:{{ $look['fg'] }}">{!! $look['svg'] !!}</div></td>
            <td>
              <a class="kb-name" href="{{ $url }}">{{ $info->main_topic }}</a>
              @if(count($tags))
                <div class="kb-tags">@foreach($tags as $tg)<span class="adm-chip">{{ $tg }}</span>@endforeach</div>
              @elseif($info->description)
                <span class="kb-desc" title="{{ $info->description }}">{{ $info->description }}</span>
              @endif
            </td>
            <td class="kb-cnt">
              <b>{{ $info->knowledge_entries_count }}</b> <small>Q&amp;A</small>
              @if($info->knowledge_entries_count < 10)<span class="low">needs more</span>@endif
            </td>
            <td>
              <div class="kb-use">
                <div class="kb-bar"><i style="width: {{ round($used * 100 / $maxUse) }}%"></i></div>
                <b>{{ $used }}</b>
              </div>
            </td>
            <td class="kb-upd">{{ $info->updated_at?->diffForHumans() ?? '—' }}<small>{{ $info->updated_at?->format('j M Y') }}</small></td>
            <td class="acts-cell">
              <div class="acts">
                <a href="{{ $url }}" class="adm-ab" title="Open Q&amp;A" aria-label="Open">{!! $svgOpen !!}</a>
                <button type="button" class="adm-ab" title="Edit topic" aria-label="Edit"
                  onclick="openEditInfoModal({{ $info->id }}, @js($info->main_topic), @js($info->description))">{!! $svgPen !!}</button>
                <form method="POST" action="{{ route('admin.information.destroy', $info->id) }}" style="display:inline"
                      onsubmit="return RKDialog.confirmForm(event, { scene: 'trash', tone: 'danger', confirmText: 'Delete topic', title: @js('Delete ' . $info->main_topic . '?'), warn: @js('Its ' . $info->knowledge_entries_count . ' Q&A ' . \Illuminate\Support\Str::plural('entry', $info->knowledge_entries_count) . ' will be deleted too. This cannot be undone.') })">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="adm-ab del" title="Delete topic" aria-label="Delete">{!! $svgBin !!}</button>
                </form>
              </div>
            </td>
          </tr>
        @endforeach
        <tr class="kb-none" id="noMatch" style="display:none"><td colspan="6">No topics match your search.</td></tr>
        @if($informations->isEmpty())
          <tr><td colspan="6">
            <div class="adm-empty">
              <div class="ic t-green">{!! $svgOpen !!}</div>
              <b>No topics yet</b>
              Add the first topic, then fill it with questions and answers.
              <div style="margin-top:14px"><button type="button" class="adm-btn g" onclick="openModal()">＋ New topic</button></div>
            </div>
          </td></tr>
        @endif
      </tbody>
    </table>
    <div class="adm-foot" id="topicFoot">{{ $informations->count() }} {{ \Illuminate\Support\Str::plural('topic', $informations->count()) }} · {{ number_format($entryCount) }} Q&amp;A entries</div>
  </section>
</div>

<!-- Add topic -->
<div id="addModal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h2>New topic</h2>
      <button type="button" class="close-btn" onclick="closeModal()">&times;</button>
    </div>
    <form action="{{ route('admin.information.store') }}" method="POST">
      @csrf
      <div class="form-group"><label>Topic name</label><input type="text" name="main_topic" placeholder="e.g. Pendaftaran Kursus" required></div>
      <div class="form-group"><label>Description</label><textarea name="description" rows="4" placeholder="e.g. Pendaftaran Kursus — Tarikh, Cara daftar" required></textarea></div>
      <p style="margin:-4px 0 0;font-size:12px;color:var(--a-mute)">Tip: write tags after a dash, e.g. “Topic — tag one, tag two”, and they show as chips.</p>
      <div class="modal-actions">
        <button type="button" class="cancel-btn" onclick="closeModal()">Cancel</button>
        <button type="submit" class="submit-btn">Add topic</button>
      </div>
    </form>
  </div>
</div>

<!-- Edit topic -->
<div id="editInfoModal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h2>Edit topic</h2>
      <button type="button" class="close-btn" onclick="closeEditInfoModal()">&times;</button>
    </div>
    <form id="editInfoForm" method="POST">
      @csrf
      @method('PUT')
      <div class="form-group"><label>Topic name</label><input type="text" name="main_topic" id="edit_info_main_topic" required></div>
      <div class="form-group"><label>Description</label><textarea name="description" id="edit_info_description" rows="4" required></textarea></div>
      <div class="modal-actions">
        <button type="button" class="cancel-btn" onclick="closeEditInfoModal()">Cancel</button>
        <button type="submit" class="submit-btn">Save topic</button>
      </div>
    </form>
  </div>
</div>

<script>
  function openModal() { document.getElementById('addModal').style.display = 'flex'; }
  function closeModal() { document.getElementById('addModal').style.display = 'none'; }
  function openEditInfoModal(id, mainTopic, description) {
    document.getElementById('edit_info_main_topic').value = mainTopic;
    document.getElementById('edit_info_description').value = description || '';
    document.getElementById('editInfoForm').action = @js(route('admin.information.update', '__ID__')).replace('__ID__', id);
    document.getElementById('editInfoModal').style.display = 'flex';
  }
  function closeEditInfoModal() { document.getElementById('editInfoModal').style.display = 'none'; }
  window.addEventListener('click', function (e) {
    if (e.target === document.getElementById('addModal')) closeModal();
    if (e.target === document.getElementById('editInfoModal')) closeEditInfoModal();
  });
  document.addEventListener('keydown', function (e) { if (e.key === 'Escape') { closeModal(); closeEditInfoModal(); } });

  const NOTES = { used: 'Sorted by most used this week', recent: 'Sorted by last updated', az: 'Sorted A–Z' };
  function sortTopics(btn) {
    const by = btn.dataset.sort;
    document.querySelectorAll('#sortSeg button').forEach(b => b.classList.toggle('on', b === btn));
    document.getElementById('sortNote').textContent = NOTES[by];
    const body = document.getElementById('topicBody');
    const rows = Array.from(body.querySelectorAll('tr[data-name]'));
    rows.sort((a, b) => {
      if (by === 'az') return a.dataset.name.localeCompare(b.dataset.name);
      if (by === 'recent') return b.dataset.updated - a.dataset.updated;
      return (b.dataset.used - a.dataset.used) || (b.dataset.updated - a.dataset.updated);
    });
    rows.forEach(r => body.insertBefore(r, document.getElementById('noMatch')));
  }

  function filterTopics() {
    const q = document.getElementById('topicSearch').value.toLowerCase().trim();
    const rows = document.querySelectorAll('#topicBody tr[data-name]');
    let shown = 0;
    rows.forEach(r => { const ok = !q || r.textContent.toLowerCase().includes(q); r.style.display = ok ? '' : 'none'; if (ok) shown++; });
    document.getElementById('noMatch').style.display = rows.length && !shown ? '' : 'none';
  }

  sortTopics(document.querySelector('#sortSeg button.on'));
</script>
</body>
</html>
