@php
  $fmtHour = function ($h) {
      $suffix = $h < 12 ? 'AM' : 'PM';
      $h12 = $h % 12 === 0 ? 12 : $h % 12;
      return $h12.' '.$suffix;
  };
  $peakLabel = $peakHour === null ? '—' : $fmtHour($peakHour).' – '.$fmtHour(($peakHour + 1) % 24);
  $days = [1 => 'Mon', 2 => 'Tue', 3 => 'Wed', 4 => 'Thu', 5 => 'Fri', 6 => 'Sat', 7 => 'Sun'];
  $fmtDate = fn ($v) => $v ? \Illuminate\Support\Carbon::parse($v)->format('Y-m-d H:i') : '—';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.js"></script>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; background: #F3FAF1; color: #1f2937; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; }
        .page-header { padding: 26px 32px; background: #fff; border-bottom: 1px solid #e3ece2; display: flex; justify-content: space-between; align-items: center; gap: 16px; flex-wrap: wrap; }
        .page-header h1 { margin: 0; font-size: 22px; font-weight: 800; }
        .page-header p { margin: 4px 0 0; color: #64748b; font-size: 14px; }
        .filters { display: flex; gap: 8px; flex-wrap: wrap; }
        .filters select, .filters a { border: 1px solid #cfdccf; border-radius: 10px; padding: 8px 12px; background: #fff; font-size: 13px; color: #334155; text-decoration: none; font-family: inherit; cursor: pointer; }
        .page { padding: 0 28px 32px; max-width: 1400px; }
        .adm-hero .filters { display: flex; gap: 8px; align-items: center; }
        .adm-hero .kpis-x { display: none; }
        .adm-kpi b { font-size: 24px; }
        .kpis { display: grid; grid-template-columns: repeat(5, minmax(0, 1fr)); gap: 14px; margin-bottom: 16px; }
        .card { background: #fff; border: 1px solid #e1eadf; border-radius: 14px; padding: 18px; margin-bottom: 16px; }
        .kpis .card { margin-bottom: 0; }
        .kpi-label { font-size: 11.5px; letter-spacing: .06em; text-transform: uppercase; color: #64748b; }
        .kpi-value { font-size: 30px; font-weight: 800; color: #2f6b4a; margin-top: 6px; display: flex; align-items: center; gap: 8px; }
        .kpi-sub { font-size: 12px; color: #2a9d6f; margin-top: 4px; }
        .dot { width: 10px; height: 10px; background: #22c55e; border-radius: 50%; box-shadow: 0 0 0 4px rgba(34,197,94,.18); }
        .card h3 { margin: 0; font-size: 16px; }
        .card .sub { font-size: 12.5px; color: #6b7a86; margin: 3px 0 14px; }
        .chart-box { position: relative; height: 260px; }
        .empty { color: #8a98a3; font-size: 13px; padding: 30px 0; text-align: center; }
        .heat { display: grid; grid-template-columns: 40px repeat(24, minmax(0, 1fr)); gap: 3px; align-items: center; }
        .heat .d { font-size: 11.5px; color: #7a8a95; }
        .heat .c { height: 24px; border-radius: 4px; }
        .heat .h { font-size: 10px; color: #7a8a95; text-align: left; }
        .heat-legend { display: flex; align-items: center; gap: 6px; font-size: 11px; color: #7a8a95; margin-top: 10px; justify-content: flex-end; }
        .heat-legend span { width: 14px; height: 14px; border-radius: 3px; display: inline-block; }
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        th, td { text-align: left; padding: 10px 8px; border-bottom: 1px solid #eef3ee; white-space: nowrap; }
        th { color: #64748b; font-weight: 600; font-size: 12px; }
        td.num { font-variant-numeric: tabular-nums; }
        .topics { columns: 2; column-gap: 28px; }
        .topics .topic-btn { break-inside: avoid; }
        .topic-btn { display: block; padding: 8px 4px; color: inherit; }
        .topic-row { display: flex; justify-content: space-between; gap: 10px; font-size: 13px; margin-bottom: 5px; }
        .topic-row b { font-weight: 600; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .topic-row span { color: #64748b; font-variant-numeric: tabular-nums; flex-shrink: 0; }
        .bar { height: 8px; background: #edf3ec; border-radius: 99px; overflow: hidden; }
        .bar i { display: block; height: 100%; background: #5c9f78; border-radius: 99px; }
        .topic-btn.muted .bar i { background: #e0a100; }
        a.topic-btn.muted:hover b { text-decoration: underline; }
        @media (max-width: 860px) { .topics { columns: 1; } }
        .card.clickable { cursor: pointer; text-align: left; font: inherit; color: inherit; width: 100%; position: relative; overflow: hidden;
            transition: transform .25s cubic-bezier(.2,.8,.2,1), box-shadow .25s ease, border-color .25s ease; }
        .card.clickable::before { content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 4px; background: #2f6b4a; transform: scaleY(0); transform-origin: bottom; transition: transform .25s ease; }
        .card.clickable:hover { transform: translateY(-3px); border-color: #b9d8c1; box-shadow: 0 10px 24px -8px rgba(47,107,74,.28); }
        .card.clickable:hover::before { transform: scaleY(1); }
        .card.clickable:active { transform: translateY(-1px); box-shadow: 0 4px 12px -6px rgba(47,107,74,.3); }
        .card.clickable:focus-visible { outline: 2px solid #2f6b4a; outline-offset: 2px; }
        .card.clickable .view { display: inline-flex; align-items: center; gap: 4px; font-size: 12px; font-weight: 600; color: #64748b; margin-top: 10px; transition: color .2s ease; }
        .card.clickable .view .arrow { display: inline-block; transition: transform .25s ease; }
        .card.clickable:hover .view { color: #2f6b4a; }
        .card.clickable:hover .view .arrow { transform: translateX(4px); }
        @media (prefers-reduced-motion: reduce) { .card.clickable, .card.clickable::before, .card.clickable .view .arrow { transition: none; } .card.clickable:hover { transform: none; } }
        dialog.list-modal { border: none; border-radius: 16px; padding: 0; width: min(760px, calc(100vw - 32px)); max-height: 80vh; box-shadow: 0 24px 60px rgba(15,40,25,.25); }
        dialog.list-modal::backdrop { background: rgba(15,30,20,.45); }
        .modal-head { display: flex; justify-content: space-between; align-items: center; gap: 12px; padding: 18px 20px; border-bottom: 1px solid #e8efe7; position: sticky; top: 0; background: #fff; }
        .modal-head h3 { margin: 0; font-size: 17px; }
        .modal-head p { margin: 2px 0 0; font-size: 12.5px; color: #64748b; }
        .modal-close { border: none; background: #f1f5f1; width: 34px; height: 34px; border-radius: 50%; font-size: 18px; cursor: pointer; color: #334155; flex-shrink: 0; }
        .modal-body { padding: 8px 20px 20px; overflow: auto; max-height: calc(80vh - 76px); }
        .modal-search { width: 100%; border: 1px solid #d6e2d5; border-radius: 10px; padding: 9px 12px; font: inherit; font-size: 13px; margin: 10px 0 6px; }
        .ulist { list-style: none; margin: 0; padding: 0; }
        .ulist li { display: flex; align-items: center; gap: 12px; padding: 11px 0; border-bottom: 1px solid #eef3ee; }
        .ulist li:last-child { border-bottom: 0; }
        .avatar { width: 36px; height: 36px; border-radius: 50%; background: #e3f0e6; color: #2f6b4a; font-weight: 700; font-size: 14px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; position: relative; }
        .avatar.on::after { content: ''; position: absolute; right: -1px; bottom: -1px; width: 11px; height: 11px; border-radius: 50%; background: #22c55e; border: 2px solid #fff; }
        .uinfo { flex: 1; min-width: 0; }
        .uinfo b { display: block; font-size: 14px; }
        .uinfo span { display: block; font-size: 12.5px; color: #64748b; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .umeta { text-align: right; font-size: 12px; color: #64748b; flex-shrink: 0; }
        .umeta .tag { display: inline-block; font-size: 10.5px; font-weight: 700; padding: 2px 8px; border-radius: 99px; background: #eef6ec; color: #2f6b4a; margin-bottom: 3px; }
        .umeta .tag.admin { background: #fff4d6; color: #8a6300; }
        @media (max-width: 1100px) { .kpis { grid-template-columns: repeat(3, minmax(0, 1fr)); } }
        @media (max-width: 640px) {
            .kpis { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .page, .page-header { padding: 16px; }
            .heat { grid-template-columns: 30px repeat(24, minmax(0, 1fr)); gap: 2px; } .heat .c { height: 14px; border-radius: 2px; } .heat .h { font-size: 8px; }
        }
    </style>
</head>
<body>

@include('partials.admin-nav', ['active' => 'analytics', 'unansweredCount' => $unansweredCount, 'unreadFeedbackCount' => $unreadFeedbackCount])

<div class="adm-wrap" style="padding-bottom:0">
    <x-admin-hero title="📈 Analytics" sub="Users, online activity & data entries across the app"
      :kpis="[
        ['icon' => 'users', 'value' => number_format($totalUsers), 'label' => 'Total users · view all', 'tag' => $newThisWeek ? '+' . $newThisWeek . ' this week' : null, 'onclick' => 'document.getElementById(\'usersModal\').showModal()'],
        ['icon' => 'online', 'value' => $onlineNow, 'label' => 'Online now · see who', 'tag' => $onlineNow ? 'live' : null, 'onclick' => 'document.getElementById(\'onlineModal\').showModal()'],
        ['icon' => 'peak', 'value' => $peakLabel, 'label' => 'Peak hour'],
        ['icon' => 'db', 'value' => number_format($totalRecords), 'label' => 'Records in ' . $tableCount . ' tables'],
        ['icon' => 'history', 'value' => number_format($updatedToday), 'label' => 'Records updated today'],
      ]">
      <x-slot:actions>
        <form class="filters" method="GET" action="{{ route('admin.analytics') }}">
        <select name="range" onchange="this.form.submit()" aria-label="Date range">
            @foreach($ranges as $r)
                <option value="{{ $r }}" @selected($r === $range)>Last {{ $r }} days</option>
            @endforeach
        </select>
        <select name="table" onchange="this.form.submit()" aria-label="Table">
            <option value="all" @selected($tableFilter === 'all')>Table: All</option>
            @foreach($tables as $t)
                <option value="{{ $t }}" @selected($tableFilter === $t)>{{ $t }}</option>
            @endforeach
        </select>
        <a href="{{ request()->fullUrl() }}" class="adm-btn-glass">⟳ Refresh</a>
    </form>
      </x-slot:actions>
    </x-admin-hero>
</div>

<div class="page">


    <div class="card">
        <h3>Users online by hour of day</h3>
        <div class="sub">How many users were active at each hour — last {{ $range }} days</div>
        @if(array_sum($byHour) === 0)
            <div class="empty">No activity recorded yet for this range.</div>
        @else
            <div class="chart-box"><canvas id="hourChart"></canvas></div>
        @endif
    </div>

    <div class="card">
        <h3>Most asked topics</h3>
        <div class="sub">What students ask the chatbot about — last {{ $range }} days</div>
        @if(count($topics) === 0)
            <div class="empty">No chatbot questions matched to a topic yet for this range.</div>
        @else
            @php $topicMax = max(array_column($topics, 'count')); @endphp
            <div class="topics">
                    @foreach($topics as $i => $t)
                        <div class="topic-btn">
                            <div class="topic-row"><b>{{ $t['name'] }}</b><span>{{ $t['count'] }} {{ $t['count'] === 1 ? 'question' : 'questions' }}</span></div>
                            <div class="bar"><i style="width: {{ round($t['count'] / $topicMax * 100) }}%"></i></div>
                        </div>
                    @endforeach
                    @if($unansweredAsked > 0)
                        <a class="topic-btn muted" href="{{ route('admin.unanswered.index') }}" style="text-decoration:none">
                            <div class="topic-row"><b>Not answered by chatbot</b><span>{{ $unansweredAsked }} &rarr;</span></div>
                            <div class="bar"><i style="width: {{ min(100, round($unansweredAsked / $topicMax * 100)) }}%"></i></div>
                        </a>
                    @endif
            </div>
        @endif
    </div>

    <div class="card">
        <h3>Activity heatmap</h3>
        <div class="sub">Day of week × hour — darker = more users online</div>
        <div class="heat">
            @foreach($days as $d => $label)
                <div class="d">{{ $label }}</div>
                @for($h = 0; $h < 24; $h++)
                    @php $v = $heatmap[$d][$h]; $a = $heatMax > 0 ? 0.07 + 0.93 * ($v / $heatMax) : 0.07; @endphp
                    <div class="c" style="background: rgba(47,107,74,{{ number_format($a, 2) }})"
                         title="{{ $label }} {{ sprintf('%02d:00', $h) }} — {{ $v }} {{ $v === 1 ? 'user' : 'users' }}"></div>
                @endfor
            @endforeach
            <div></div>
            @for($h = 0; $h < 24; $h++)
                <div class="h">{{ $h % 6 === 0 ? sprintf('%02d:00', $h) : '' }}</div>
            @endfor
        </div>
        <div class="heat-legend">Less
            <span style="background:rgba(47,107,74,.07)"></span>
            <span style="background:rgba(47,107,74,.35)"></span>
            <span style="background:rgba(47,107,74,.65)"></span>
            <span style="background:rgba(47,107,74,1)"></span> More
        </div>
    </div>

    <div class="card">
        <h3>Table summary</h3>
        <div class="sub">Rows, first entry, last created &amp; last updated for every table</div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Table</th>
                        <th>Rows</th>
                        <th>First entry</th>
                        <th>Last created</th>
                        <th>Last updated</th>
                        <th>Added ({{ $range }} days)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($summary as $row)
                        <tr>
                            <td>{{ $row['table'] }}</td>
                            <td class="num">{{ number_format($row['rows']) }}</td>
                            <td class="num">{{ $row['first'] ? \Illuminate\Support\Carbon::parse($row['first'])->format('Y-m-d') : '—' }}</td>
                            <td class="num">{{ $fmtDate($row['last_created']) }}</td>
                            <td class="num">{{ $fmtDate($row['last_updated']) }}</td>
                            <td class="num">{{ $row['added'] === null ? '—' : number_format($row['added']) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>

@if(array_sum($byHour) > 0)
<script>
(function () {
    const data = @json(array_values($byHour));
    const peak = @json($peakHour);
    const labels = data.map((_, h) => {
        const s = h < 12 ? 'AM' : 'PM';
        const h12 = h % 12 === 0 ? 12 : h % 12;
        return h12 + ' ' + s;
    });
    new Chart(document.getElementById('hourChart'), {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                data,
                backgroundColor: data.map((_, h) => h === peak ? '#2f6b4a' : '#5c9f78'),
                borderRadius: 4,
                maxBarThickness: 36,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { callbacks: { label: (c) => c.parsed.y + (c.parsed.y === 1 ? ' user' : ' users') } }
            },
            scales: {
                x: { grid: { display: false }, ticks: { color: '#7a8a95', maxRotation: 0, autoSkip: true, maxTicksLimit: 12 } },
                y: { beginAtZero: true, ticks: { precision: 0, color: '#7a8a95' }, grid: { color: '#e8efe8' } }
            }
        }
    });
})();
</script>
@endif



{{-- User list pop-ups (opened from the Total users / Online now cards) --}}
@php
  $initials = fn ($name) => mb_strtoupper(mb_substr(trim((string) $name), 0, 1)) ?: '?';
@endphp
<dialog class="list-modal" id="usersModal">
    <div class="modal-head">
        <div>
            <h3>All accounts</h3>
            <p>{{ $userList->count() }} registered {{ $userList->count() === 1 ? 'account' : 'accounts' }} · newest first</p>
        </div>
        <button type="button" class="modal-close" aria-label="Close" onclick="this.closest('dialog').close()">&times;</button>
    </div>
    <div class="modal-body">
        <input type="search" class="modal-search" placeholder="Search name, email or matric no..." oninput="filterUsers(this)">
        <ul class="ulist" id="usersList">
            @foreach($userList as $u)
                <li data-search="{{ mb_strtolower($u->name.' '.$u->email.' '.$u->student_id) }}">
                    <div class="avatar {{ $u->online ? 'on' : '' }}">{{ $initials($u->name) }}</div>
                    <div class="uinfo">
                        <b>{{ $u->name }}</b>
                        <span>{{ $u->email }}@if($u->student_id) · {{ $u->student_id }}@endif</span>
                    </div>
                    <div class="umeta">
                        <span class="tag {{ $u->role === 'admin' ? 'admin' : '' }}">{{ $u->role === 'admin' ? 'Admin' : 'Student' }}</span><br>
                        Joined {{ \Illuminate\Support\Carbon::parse($u->created_at)->format('d M Y') }}<br>
                        {{ $u->online ? 'Online now' : ($u->last_active ? 'Last active '.$u->last_active->diffForHumans() : 'No recent activity') }}
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
</dialog>

<dialog class="list-modal" id="onlineModal">
    <div class="modal-head">
        <div>
            <h3>Online now</h3>
            <p>Users active in the last 5 minutes</p>
        </div>
        <button type="button" class="modal-close" aria-label="Close" onclick="this.closest('dialog').close()">&times;</button>
    </div>
    <div class="modal-body">
        @if($onlineUsers->isEmpty())
            <div class="empty">Nobody is online right now.</div>
        @else
            <ul class="ulist">
                @foreach($onlineUsers as $u)
                    <li>
                        <div class="avatar on">{{ $initials($u->name) }}</div>
                        <div class="uinfo">
                            <b>{{ $u->name }}</b>
                            <span>{{ $u->email }}@if($u->student_id) · {{ $u->student_id }}@endif</span>
                        </div>
                        <div class="umeta">
                            <span class="tag {{ $u->role === 'admin' ? 'admin' : '' }}">{{ $u->role === 'admin' ? 'Admin' : 'Student' }}</span><br>
                            Active {{ $u->last_active->diffForHumans() }}
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</dialog>

<script>
function filterUsers(input) {
    const q = input.value.trim().toLowerCase();
    document.querySelectorAll('#usersList li').forEach(function (li) {
        li.hidden = q !== '' && !li.dataset.search.includes(q);
    });
}
// Close a pop-up when clicking the dark area around it.
document.querySelectorAll('dialog.list-modal').forEach(function (d) {
    d.addEventListener('click', function (e) { if (e.target === d) d.close(); });
});
</script>

</body>
</html>
