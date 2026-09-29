<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Database - RakanKampus Admin</title>
<style>
  body { margin: 0; }
  .db-grid { display: grid; grid-template-columns: 272px minmax(0, 1fr); gap: 20px; align-items: start; }

  /* left: table list */
  .db-side { position: sticky; top: 90px; padding: 10px 0 6px; }
  .db-grp { font-size: 11px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; color: #9aa8a0; padding: 12px 20px 6px; }
  .db-t { display: flex; align-items: center; gap: 11px; margin: 1px 10px; padding: 8px 10px; border-radius: 12px; text-decoration: none; color: var(--a-ink2); position: relative; }
  .db-t:hover { background: var(--a-g50); }
  .db-t.on { background: var(--a-g100); color: var(--a-g800); }
  .db-t.on::before { content: ''; position: absolute; left: -10px; top: 8px; bottom: 8px; width: 4px; border-radius: 0 4px 4px 0; background: var(--a-g600); }
  .db-t .ic { width: 30px; height: 30px; border-radius: 9px; display: grid; place-items: center; flex-shrink: 0; }
  .db-t .ic svg { width: 16px; height: 16px; }
  .db-t b { display: block; font-size: 13.5px; font-weight: 700; color: inherit; line-height: 1.25; }
  .db-t small { display: block; font-size: 11px; color: #9aa8a0; font-family: ui-monospace, SFMono-Regular, Menlo, monospace; }
  .db-t .n { margin-left: auto; font-size: 12px; font-weight: 700; color: var(--a-mute); font-variant-numeric: tabular-nums; }
  .db-t.on .n { color: var(--a-g800); }
  .db-foot { margin-top: 8px; padding: 12px 20px 8px; border-top: 1px solid #eef3ef; font-size: 12px; color: var(--a-mute); display: flex; justify-content: space-between; }

  /* right: rows */
  .db-main .adm-ch h3 { display: flex; align-items: center; gap: 10px; }
  .db-scroll { overflow: auto; max-height: calc(100vh - 260px); border-top: 1px solid #eef3ef; }
  .db-table { border-collapse: separate; border-spacing: 0; width: 100%; }
  .db-table th { position: sticky; top: 0; z-index: 1; background: #fafcfb; font-size: 11.5px; font-weight: 700; color: #9aa8a0; text-transform: uppercase; letter-spacing: .05em; text-align: left; padding: 10px 14px; border-bottom: 1px solid #eef3ef; white-space: nowrap; }
  .db-table td { padding: 8px 14px; height: 44px; border-bottom: 1px solid #f1f5f2; font-size: 13px; color: var(--a-ink2); white-space: nowrap; max-width: 280px; overflow: hidden; text-overflow: ellipsis; }
  .db-table tbody tr:hover td { background: #fbfdfb; }
  .db-table td.id { color: #9aa8a0; font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-size: 12px; }
  .db-table td .nil { color: #c3ccc6; }
  .db-table td .dt { color: var(--a-ink2); }
  .db-table td .dt small { color: #9aa8a0; margin-left: 4px; }
  .db-table th.act, .db-table td.act { position: sticky; right: 0; background: #fff; text-align: right; width: 56px; box-shadow: -8px 0 12px -10px rgba(22,36,28,.18); }
  .db-table th.act { background: #fafcfb; }
  .db-table tbody tr:hover td.act { background: #fbfdfb; }
  .yes { color: #166534; background: #dcfce7; } .no { color: #64748b; background: #f1f5f9; }
  .status { display: inline-flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 700; color: var(--a-mute); }
  .status i { width: 8px; height: 8px; border-radius: 50%; background: #cbd5e1; }
  .status.on { color: #166534; } .status.on i { background: #22c55e; box-shadow: 0 0 0 3px rgba(34,197,94,.18); }
  .db-pager { display: flex; align-items: center; gap: 10px; padding: 12px 20px; border-top: 1px solid #eef3ef; font-size: 12.5px; color: var(--a-mute); }
  .db-pager .r { margin-left: auto; display: flex; gap: 8px; }
  .db-pager a.off { opacity: .4; pointer-events: none; }
  .live-chip { display: inline-flex; align-items: center; gap: 8px; height: 36px; padding: 0 14px; border-radius: 99px; background: #fff; border: 1px solid var(--a-line); font-size: 12.5px; font-weight: 700; color: var(--a-ink2); }
  .live-chip i { width: 8px; height: 8px; border-radius: 50%; background: #22c55e; box-shadow: 0 0 0 3px rgba(34,197,94,.18); }
  .live-chip.off i { background: #cbd5e1; box-shadow: none; }
</style>
</head>
<body>

@php
  // Friendly names + grouping for the allow-listed tables (the controller decides which exist)
  $meta = [
    'users' => ['Users', 'People', 'green', '<circle cx="9" cy="8" r="4"/><path d="M2 21a7 7 0 0 1 14 0M17 11a3 3 0 1 0 0-6M22 21a6 6 0 0 0-5-6"/>'],
    'profiles' => ['Profiles', 'People', 'green', '<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>'],
    'sessions' => ['Sessions', 'People', 'green', '<circle cx="12" cy="12" r="4"/><path d="M4.9 4.9a10 10 0 0 0 0 14.2M19.1 4.9a10 10 0 0 1 0 14.2"/>'],
    'information' => ['Topics', 'Chatbot', 'blue', '<path d="M4 19.5V5a2 2 0 0 1 2-2h13v16H6.5A2.5 2.5 0 0 0 4 21.5"/>'],
    'knowledge_bases' => ['Q&A entries', 'Chatbot', 'blue', '<path d="M4 5h16v11H8l-4 4z"/>'],
    'chat_conversations' => ['Conversations', 'Chatbot', 'blue', '<path d="M21 11.5a8.4 8.4 0 0 1-9 8.5 8.4 8.4 0 0 1-3.8-.9L3 21l1.9-5.7A8.5 8.5 0 1 1 21 11.5z"/>'],
    'chat_messages' => ['Messages', 'Chatbot', 'blue', '<path d="M4 6h16M4 12h10M4 18h7"/>'],
    'unanswered_questions' => ['Unanswered', 'Chatbot', 'red', '<circle cx="12" cy="12" r="9"/><path d="M9.5 9a2.5 2.5 0 1 1 3.5 2.3c-.7.3-1 1-1 1.7M12 17h.01"/>'],
    'reminders' => ['Reminders', 'Student tools', 'amber', '<path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.9 1.9 0 0 0 3.4 0"/>'],
    'class_schedules' => ['Timetable', 'Student tools', 'amber', '<rect x="3" y="4" width="18" height="17" rx="3"/><path d="M3 9h18M8 2v4M16 2v4"/>'],
    'push_subscriptions' => ['Push devices', 'Student tools', 'amber', '<rect x="7" y="2" width="10" height="20" rx="2"/><path d="M11 18h2"/>'],
    'feedback' => ['Feedback', 'Inbox', 'violet', '<rect x="3" y="5" width="18" height="14" rx="3"/><path d="m3 8 9 6 9-6"/>'],
  ];
  $m = fn ($t) => $meta[$t] ?? [\Illuminate\Support\Str::headline($t), 'Other', 'green', '<ellipse cx="12" cy="5" rx="8" ry="3"/><path d="M4 5v14c0 1.7 3.6 3 8 3s8-1.3 8-3V5"/>'];
  $groups = collect($counts)->keys()->groupBy(fn ($t) => $m($t)[1]);
  $cur = $m($table);
  $fmt = function ($col, $v) {
      if ($v === null || $v === '') return '<span class="nil">—</span>';
      $s = (string) $v;
      if ($col === 'last_activity' && ctype_digit($s)) {
          $d = \Illuminate\Support\Carbon::createFromTimestamp((int) $s);
          return '<span class="dt">' . e($d->format('j M Y, g:i A')) . '<small>' . e($d->diffForHumans(null, true, true)) . '</small></span>';
      }
      if (preg_match('/^\d{4}-\d{2}-\d{2}[ T]\d{2}:\d{2}/', $s)) {
          try { $d = \Illuminate\Support\Carbon::parse($s); return '<span class="dt">' . e($d->format('j M Y, g:i A')) . '</span>'; } catch (\Throwable $e) {}
      }
      if ((str_starts_with($col, 'is_') || str_starts_with($col, 'has_')) && in_array($s, ['0', '1'], true)) {
          return '<span class="adm-pill ' . ($s === '1' ? 'yes' : 'no') . '">' . ($s === '1' ? 'Yes' : 'No') . '</span>';
      }
      return e(\Illuminate\Support\Str::limit($s, 70));
  };
  $svgBin = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6"/></svg>';
@endphp

@include('partials.admin-nav', ['active' => 'database'])

<div class="adm-wrap">

  <div class="adm-ph">
    <div>
      <h1>Database</h1>
      <p>Browse what the app has stored. Read-only, except deleting a single row.</p>
    </div>
    <div class="r">
      <span class="live-chip {{ $onlineNowCount ? '' : 'off' }}" title="Signed-in users active in the last {{ (int) ($onlineWindowSeconds / 60) }} minutes"><i></i>{{ $onlineNowCount }} online now</span>
    </div>
  </div>

  @if (session('db_viewer_status'))
    <div class="adm-flash">{{ session('db_viewer_status') }}</div>
  @endif
  @if (session('db_viewer_error'))
    <div class="adm-flash" style="background:#fdecec;border-color:#f3d0d0;color:#b42318">{{ session('db_viewer_error') }}</div>
  @endif

  <div class="db-grid">
    {{-- tables --}}
    <nav class="adm-card flush db-side" aria-label="Tables">
      @foreach ($groups as $group => $list)
        <div class="db-grp">{{ $group }}</div>
        @foreach ($list as $t)
          @php [$label, , $tone, $icon] = $m($t); @endphp
          <a href="{{ route('admin.database', ['table' => $t]) }}" class="db-t {{ $t === $table ? 'on' : '' }}">
            <span class="ic t-{{ $tone }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{!! $icon !!}</svg></span>
            <span><b>{{ $label }}</b><small>{{ $t }}</small></span>
            <span class="n">{{ number_format($counts[$t]) }}</span>
          </a>
        @endforeach
      @endforeach
      <div class="db-foot"><span>{{ count($counts) }} tables</span><span>{{ number_format(array_sum($counts)) }} rows</span></div>
    </nav>

    {{-- rows --}}
    <section class="adm-card flush db-main">
      <div class="adm-ch">
        <div class="adm-ci t-{{ $cur[2] }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{!! $cur[3] !!}</svg></div>
        <div>
          <h3>{{ $cur[0] }} <span class="adm-code">{{ $table }}</span></h3>
          <p>{{ number_format($rows->total()) }} {{ \Illuminate\Support\Str::plural('row', $rows->total()) }} · newest first</p>
        </div>
        <div class="r">
          <label class="adm-search" style="width:260px">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
            <input type="text" id="rowSearch" placeholder="Filter this page…" oninput="filterRows(this.value)">
          </label>
        </div>
      </div>

      @if ($rows->isEmpty())
        <div class="adm-empty">
          <div class="ic t-green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><ellipse cx="12" cy="5" rx="8" ry="3"/><path d="M4 5v14c0 1.7 3.6 3 8 3s8-1.3 8-3V5"/></svg></div>
          <b>Nothing here yet</b>
          This table has no rows.
        </div>
      @else
        <div class="db-scroll">
          <table class="db-table">
            <thead>
              <tr>
                @foreach ($columns as $col)
                  <th title="{{ $col }}">{{ $col === 'id' ? '#' : \Illuminate\Support\Str::headline($col) }}</th>
                @endforeach
                @if ($table === 'users')<th>Last online</th>@endif
                @if ($hasId)<th class="act"></th>@endif
              </tr>
            </thead>
            <tbody id="rowsBody">
              @foreach ($rows as $row)
                <tr>
                  @foreach ($columns as $col)
                    <td class="{{ $col === 'id' ? 'id' : '' }}" title="{{ \Illuminate\Support\Str::limit((string) $row->$col, 300) }}">{!! $col === 'id' ? e($row->id) : $fmt($col, $row->$col) !!}</td>
                  @endforeach
                  @if ($table === 'users')
                    @php $lastTs = $lastActiveMap[$row->id] ?? null; $isOnline = $lastTs && (time() - $lastTs) <= $onlineWindowSeconds; @endphp
                    <td>
                      <span class="status {{ $isOnline ? 'on' : '' }}"><i></i>{{ $lastTs ? ($isOnline ? 'Online now' : \Illuminate\Support\Carbon::createFromTimestamp($lastTs)->diffForHumans()) : 'Never' }}</span>
                    </td>
                  @endif
                  @if ($hasId)
                    <td class="act">
                      <form method="POST" action="{{ route('admin.database.destroy', ['table' => $table, 'id' => $row->id]) }}" style="margin:0"
                            onsubmit="return RKDialog.confirmForm(event, { scene: 'trash', tone: 'danger', confirmText: 'Delete row', title: @js('Delete row #' . $row->id . '?'), message: @js($cur[0] . ' · ' . $table), warn: 'This removes it from the database for good.' })">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="adm-ab del" title="Delete row" aria-label="Delete row {{ $row->id }}">{!! $svgBin !!}</button>
                      </form>
                    </td>
                  @endif
                </tr>
              @endforeach
              <tr id="noRowMatch" style="display:none"><td colspan="{{ count($columns) + 2 }}" style="text-align:center;color:var(--a-mute);padding:26px">No rows on this page match.</td></tr>
            </tbody>
          </table>
        </div>
        <div class="db-pager">
          <span>Showing {{ number_format($rows->firstItem()) }}–{{ number_format($rows->lastItem()) }} of {{ number_format($rows->total()) }}</span>
          <div class="r">
            <a class="adm-btn o sm {{ $rows->onFirstPage() ? 'off' : '' }}" href="{{ $rows->appends(['table' => $table])->previousPageUrl() ?? '#' }}">‹ Previous</a>
            <span style="align-self:center">Page {{ $rows->currentPage() }} of {{ $rows->lastPage() }}</span>
            <a class="adm-btn o sm {{ $rows->hasMorePages() ? '' : 'off' }}" href="{{ $rows->appends(['table' => $table])->nextPageUrl() ?? '#' }}">Next ›</a>
          </div>
        </div>
      @endif
    </section>
  </div>
</div>

<script>
  function filterRows(q) {
    q = q.toLowerCase().trim();
    let shown = 0;
    const rows = document.querySelectorAll('#rowsBody tr:not(#noRowMatch)');
    rows.forEach(r => { const ok = !q || r.textContent.toLowerCase().includes(q); r.style.display = ok ? '' : 'none'; if (ok) shown++; });
    const none = document.getElementById('noRowMatch');
    if (none) none.style.display = rows.length && !shown ? '' : 'none';
  }
</script>
</body>
</html>
