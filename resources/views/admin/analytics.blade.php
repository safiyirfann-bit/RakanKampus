<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Analytics - RakanKampus Admin</title>
<style>
body { margin: 0; }
.bento { display: grid; grid-template-columns: repeat(12, minmax(0, 1fr)); gap: 18px; }
.bento > * { min-width: 0; }
.s4 { grid-column: span 4; } .s5 { grid-column: span 5; } .s7 { grid-column: span 7; } .s8 { grid-column: span 8; } .s12 { grid-column: span 12; }
.range { display: flex; background: #fff; border: 1px solid var(--a-line); border-radius: 99px; padding: 4px; }
.range a { font-size: 12.5px; font-weight: 700; padding: 8px 14px; border-radius: 99px; color: var(--a-mute); text-decoration: none; }
.range a.on { background: var(--a-dark); color: #fff; }
button.adm-k { font: inherit; text-align: left; cursor: pointer; transition: transform .15s, box-shadow .15s; }
button.adm-k:hover { transform: translateY(-3px); box-shadow: 0 16px 30px rgba(17,28,21,.08); }
.adm-k small.more { font-size: 11.5px; font-weight: 800; color: var(--a-g700); display: block; margin-top: 6px; }
.adm-k.dark small.more { color: var(--a-mint); }
.live i { display: inline-block; width: 7px; height: 7px; border-radius: 50%; background: #22c55e; margin-right: 5px; vertical-align: 1px; box-shadow: 0 0 0 3px rgba(34,197,94,.2); }

/* activity chart */
.legend { display: flex; gap: 16px; font-size: 12px; font-weight: 700; color: var(--a-mute); }
.legend i { display: inline-block; width: 10px; height: 10px; border-radius: 99px; margin-right: 6px; vertical-align: -1px; }
.chart { position: relative; padding: 0 24px 22px; }
.chart svg { display: block; width: 100%; height: 250px; overflow: visible; cursor: crosshair; }
.chart .axis { display: flex; justify-content: space-between; font-size: 11px; color: #9aa39c; font-weight: 700; margin-top: 8px; }
.chart .tip { position: absolute; pointer-events: none; transform: translate(-50%, -115%); background: var(--a-dark); color: #fff; font-size: 12px; font-weight: 700; padding: 7px 11px; border-radius: 12px; white-space: nowrap; opacity: 0; transition: opacity .1s; line-height: 1.5; }
.chart .tip small { display: block; font-size: 10.5px; color: #9fb3a6; font-weight: 600; }
.chart .tip i { display: inline-block; width: 8px; height: 8px; border-radius: 50%; margin-right: 6px; }

/* busiest hours */
.hours { background: var(--a-g100); border-color: transparent; display: flex; flex-direction: column; }
.hours .adm-ch p { color: var(--a-g700); }
.hbars { flex: 1; display: flex; align-items: flex-end; gap: 4px; min-height: 200px; padding: 0 24px; }
.hbars span { flex: 1; border-radius: 99px; background: rgba(31,107,67,.2); min-height: 8px; position: relative; }
.hbars span.pk { background: var(--a-g700); }
.hbars span:hover { background: var(--a-g500); }
.hbars span:hover::after { content: attr(data-tip); position: absolute; bottom: calc(100% + 6px); left: 50%; transform: translateX(-50%); background: var(--a-dark); color: #fff; font-size: 11px; font-weight: 700; padding: 5px 8px; border-radius: 8px; white-space: nowrap; z-index: 2; }
.haxis { display: flex; justify-content: space-between; font-size: 11px; color: var(--a-g700); font-weight: 700; padding: 10px 24px 22px; }

/* topics */
.tp { display: grid; grid-template-columns: 30px minmax(0, 1fr) 48px; gap: 12px; align-items: center; padding: 11px 24px; text-decoration: none; color: inherit; }
.tp:hover { background: #fafbf9; }
.tp .n { font-size: 22px; font-weight: 800; color: #d5dcd7; letter-spacing: -.04em; }
.tp b { display: block; font-size: 13.5px; font-weight: 700; color: var(--a-ink); margin-bottom: 7px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.tp em { font-style: normal; font-size: 13px; font-weight: 800; text-align: right; color: var(--a-ink); }
.bar { height: 8px; border-radius: 99px; background: #f1f3f0; overflow: hidden; }
.bar i { display: block; height: 100%; border-radius: 99px; background: linear-gradient(90deg, #7ee0b0, #1f6b43); }
.tp.miss b, .tp.miss em { color: var(--a-red); }
.tp.miss .bar i { background: linear-gradient(90deg, #f7b4b4, #d64545); }

/* students */
.st { display: flex; align-items: center; gap: 12px; padding: 11px 24px; border-top: 1px solid #f1f2ef; }
.st:first-of-type { border-top: 0; }
.av { width: 40px; height: 40px; border-radius: 50%; color: #fff; font-weight: 800; font-size: 14px; display: grid; place-items: center; position: relative; flex-shrink: 0; }
.av.on::after { content: ''; position: absolute; right: -1px; bottom: -1px; width: 11px; height: 11px; border-radius: 50%; background: #22c55e; border: 2px solid #fff; }
.st .who { min-width: 0; flex: 1; }
.st b { display: block; font-size: 13.5px; font-weight: 700; color: var(--a-ink); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.st small { font-size: 12px; color: var(--a-mute); }
.st .when { font-size: 12px; font-weight: 700; color: var(--a-mute); white-space: nowrap; }
.st .when.on { color: #16a34a; }
.lnk { font-size: 13px; font-weight: 800; color: var(--a-ink); text-decoration: none; background: none; border: 0; cursor: pointer; font-family: inherit; display: inline-flex; gap: 6px; align-items: center; }
.lnk svg { width: 15px; height: 15px; }
.none { padding: 6px 24px 26px; font-size: 13px; color: var(--a-mute); }
.none b { display: block; color: var(--a-ink); font-size: 14px; margin-bottom: 2px; }

/* user list pop-ups */
dialog.list-modal { border: none; border-radius: 28px; padding: 0; width: min(760px, calc(100vw - 32px)); max-height: 80vh; box-shadow: 0 30px 70px rgba(17,28,21,.3); font-family: var(--a-font); }
dialog.list-modal::backdrop { background: rgba(17,28,21,.45); backdrop-filter: blur(3px); }
.modal-head { display: flex; justify-content: space-between; align-items: center; gap: 12px; padding: 22px 26px 16px; position: sticky; top: 0; background: #fff; z-index: 1; }
.modal-head h3 { margin: 0; font-size: 19px; font-weight: 800; color: var(--a-ink); }
.modal-head p { margin: 2px 0 0; font-size: 12.5px; color: var(--a-mute); }
.modal-close { border: 1px solid var(--a-line); background: #fff; width: 38px; height: 38px; border-radius: 50%; font-size: 20px; cursor: pointer; color: var(--a-ink2); flex-shrink: 0; }
.modal-body { padding: 0 26px 24px; overflow: auto; max-height: calc(80vh - 86px); }
.modal-search { width: 100%; box-sizing: border-box; border: 1px solid var(--a-line); border-radius: 99px; padding: 11px 16px; font: inherit; font-size: 13px; margin: 0 0 8px; outline: 0; }
.modal-search:focus { border-color: #bfe3cc; box-shadow: 0 0 0 4px rgba(63,176,112,.12); }
.ulist { list-style: none; margin: 0; padding: 0; }
.ulist li { display: flex; align-items: center; gap: 12px; padding: 12px 0; border-bottom: 1px solid #f1f2ef; }
.ulist li:last-child { border-bottom: 0; }
.uinfo { flex: 1; min-width: 0; }
.uinfo b { display: block; font-size: 14px; color: var(--a-ink); }
.uinfo span { font-size: 12.5px; color: var(--a-mute); }
.umeta { text-align: right; font-size: 12px; color: var(--a-mute); line-height: 1.6; white-space: nowrap; }
.tag { display: inline-block; font-size: 11px; font-weight: 800; padding: 2px 9px; border-radius: 99px; background: var(--a-g100); color: var(--a-g800); }
.tag.admin { background: var(--a-dark); color: #fff; }
</style>
</head>
<body>

@include('partials.admin-nav', ['active' => 'analytics', 'unansweredCount' => $unansweredCount, 'unreadFeedbackCount' => $unreadFeedbackCount])
@php
  $icon = fn ($p) => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' . $p . '</svg>';
  $arrow = $icon('<path d="M5 12h14M13 6l6 6-6 6"/>');
  $trend = $askedPrev > 0 ? (int) round(($asked - $askedPrev) * 100 / $askedPrev) : null;
  $initials = fn ($name) => mb_strtoupper(mb_substr(trim((string) $name), 0, 1)) ?: '?';
  $palette = ['#1f6b43', '#3c6fb0', '#c7851e', '#7a55c7', '#d64545', '#2f8f8a'];
  $colour = fn ($id) => $palette[$id % count($palette)];
  $hourMax = max(1, max($byHour));
  $topMax = max(1, collect($topics)->max('count') ?? 1, $missed);
  $peakLabel = $peakHour === null ? null : Carbon\Carbon::createFromTime($peakHour)->format('g A');
@endphp

<div class="adm-wrap">

  <div class="adm-ph">
    <div>
      <h1>Analytics</h1>
      <p>How students are using RakanKampus.</p>
    </div>
    <div class="r">
      <nav class="range" aria-label="Date range">
        @foreach($ranges as $r)
          <a href="{{ route('admin.analytics', ['range' => $r]) }}" class="{{ $r === $range ? 'on' : '' }}">{{ $r }} days</a>
        @endforeach
      </nav>
    </div>
  </div>

  <div class="adm-kpis">
    <button type="button" class="adm-card adm-k" onclick="document.getElementById('usersModal').showModal()">
      <div class="adm-ci t-green">{!! $icon('<circle cx="9" cy="8" r="4"/><path d="M2 21a7 7 0 0 1 14 0M17 11a3 3 0 1 0 0-6M22 21a6 6 0 0 0-5-6"/>') !!}</div>
      @if($newStudents)<em class="t-green">+{{ $newStudents }} new</em>@endif
      <div><b>{{ number_format($totalStudents) }}</b><span>Students</span><small class="more">View all →</small></div>
    </button>
    <button type="button" class="adm-card adm-k" onclick="document.getElementById('onlineModal').showModal()">
      <div class="adm-ci" style="background:#dcfce7;color:#16a34a">{!! $icon('<circle cx="12" cy="12" r="4"/><path d="M4.9 4.9a10 10 0 0 0 0 14.2M19.1 4.9a10 10 0 0 1 0 14.2"/>') !!}</div>
      <em class="live" style="background:#dcfce7;color:#16a34a"><i></i>live</em>
      <div><b>{{ $onlineNow }}</b><span>Online now</span><small class="more">See who →</small></div>
    </button>
    <div class="adm-card adm-k dark">
      <div class="adm-ci">{!! $icon('<path d="M4 5h16v11H8l-4 4z"/>') !!}</div>
      @if($trend !== null)<em>{{ $trend >= 0 ? '▲' : '▼' }} {{ abs($trend) }}%</em>@endif
      <div><b>{{ number_format($asked) }}</b><span>Questions · {{ $range }} days</span></div>
    </div>
    <div class="adm-card adm-k">
      <div class="adm-ci t-amber">{!! $icon('<path d="M20 6 9 17l-5-5"/>') !!}</div>
      <div><b>{{ $answeredRate === null ? '—' : $answeredRate . '%' }}</b><span>Answered by bot</span></div>
    </div>
  </div>

  <div class="bento">
    <section class="adm-card flush s8">
      <div class="adm-ch">
        <div><h3>Activity</h3><p>Last {{ $range }} days</p></div>
        <div class="r legend"><span><i style="background:#1f6b43"></i>Questions</span><span><i style="background:#e0a43a"></i>Active students</span></div>
      </div>
      <div class="chart" id="aChart">
        <svg id="aSvg" viewBox="0 0 760 250" preserveAspectRatio="none" aria-label="Questions and active students per day"></svg>
        <div class="axis" id="aAxis"></div>
        <div class="tip" id="aTip"></div>
      </div>
    </section>

    <section class="adm-card flush hours s4">
      <div class="adm-ch">
        <div><h3>Busiest hours</h3><p>{{ $peakLabel ? 'Peak at ' . $peakLabel : 'No activity in this range yet' }}</p></div>
      </div>
      <div class="hbars">
        @foreach($byHour as $h => $n)
          <span class="{{ $h === $peakHour ? 'pk' : '' }}" style="height: {{ max(4, round($n / $hourMax * 100)) }}%" data-tip="{{ Carbon\Carbon::createFromTime($h)->format('g A') }} · {{ $n }}"></span>
        @endforeach
      </div>
      <div class="haxis"><span>12am</span><span>6am</span><span>12pm</span><span>6pm</span><span>11pm</span></div>
    </section>

    <section class="adm-card flush s7">
      <div class="adm-ch">
        <div><h3>Most asked topics</h3><p>What students ask about · last {{ $range }} days</p></div>
        <div class="r"><a class="lnk" href="{{ route('admin.knowledge') }}">Knowledge base {!! $arrow !!}</a></div>
      </div>
      @forelse($topics as $i => $t)
        <a class="tp" href="{{ $t['id'] ? route('admin.information.show', $t['id']) : '#' }}">
          <span class="n">{{ $i + 1 }}</span>
          <div><b title="{{ $t['name'] }}">{{ $t['name'] }}</b><div class="bar"><i style="width: {{ round($t['count'] * 100 / $topMax) }}%"></i></div></div>
          <em>{{ number_format($t['count']) }}</em>
        </a>
      @empty
        <div class="none"><b>No topics yet</b>Topics show up once the bot answers from the knowledge base.</div>
      @endforelse
      @if($missed > 0)
        <a class="tp miss" href="{{ route('admin.unanswered.index') }}">
          <span class="n">!</span>
          <div><b>Couldn't answer</b><div class="bar"><i style="width: {{ round($missed * 100 / $topMax) }}%"></i></div></div>
          <em>{{ number_format($missed) }}</em>
        </a>
      @endif
      <div style="height:12px"></div>
    </section>

    <section class="adm-card flush s5">
      <div class="adm-ch">
        <div><h3>Students</h3><p>Recently active</p></div>
        <div class="r"><button type="button" class="lnk" onclick="document.getElementById('usersModal').showModal()">View all {{ number_format($totalStudents) }} {!! $arrow !!}</button></div>
      </div>
      @forelse($recentUsers as $u)
        <div class="st">
          <div class="av {{ $u->online ? 'on' : '' }}" style="background: {{ $colour($u->id) }}">{{ $initials($u->name) }}</div>
          <div class="who"><b>{{ $u->name }}</b><small>{{ $u->student_id ?: $u->email }}</small></div>
          <span class="when {{ $u->online ? 'on' : '' }}">{{ $u->online ? '● Online' : ($u->last_active ? $u->last_active->diffForHumans(null, true, true) . ' ago' : 'Not yet') }}</span>
        </div>
      @empty
        <div class="none"><b>No students yet</b>New sign-ups will appear here.</div>
      @endforelse
      <div style="height:12px"></div>
    </section>
  </div>
</div>

{{-- User list pop-ups --}}
<dialog class="list-modal" id="usersModal">
  <div class="modal-head">
    <div><h3>All accounts</h3><p>{{ $userList->count() }} registered {{ $userList->count() === 1 ? 'account' : 'accounts' }} · newest first</p></div>
    <button type="button" class="modal-close" aria-label="Close" onclick="this.closest('dialog').close()">&times;</button>
  </div>
  <div class="modal-body">
    <input type="search" class="modal-search" placeholder="Search name, email or matric no..." oninput="filterUsers(this)">
    <ul class="ulist" id="usersList">
      @foreach($userList as $u)
        <li data-search="{{ mb_strtolower($u->name.' '.$u->email.' '.$u->student_id) }}">
          <div class="av {{ $u->online ? 'on' : '' }}" style="background: {{ $colour($u->id) }}">{{ $initials($u->name) }}</div>
          <div class="uinfo"><b>{{ $u->name }}</b><span>{{ $u->email }}@if($u->student_id) · {{ $u->student_id }}@endif</span></div>
          <div class="umeta">
            <span class="tag {{ $u->role === 'admin' ? 'admin' : '' }}">{{ $u->role === 'admin' ? 'Admin' : 'Student' }}</span><br>
            Joined {{ Carbon\Carbon::parse($u->created_at)->format('d M Y') }}<br>
            {{ $u->online ? 'Online now' : ($u->last_active ? 'Last active '.$u->last_active->diffForHumans() : 'No recent activity') }}
          </div>
        </li>
      @endforeach
    </ul>
  </div>
</dialog>

<dialog class="list-modal" id="onlineModal">
  <div class="modal-head">
    <div><h3>Online now</h3><p>Users active in the last 5 minutes</p></div>
    <button type="button" class="modal-close" aria-label="Close" onclick="this.closest('dialog').close()">&times;</button>
  </div>
  <div class="modal-body">
    @if($onlineUsers->isEmpty())
      <div class="none" style="padding-left:0"><b>Nobody is online right now</b>Check back in a bit.</div>
    @else
      <ul class="ulist">
        @foreach($onlineUsers as $u)
          <li>
            <div class="av on" style="background: {{ $colour($u->id) }}">{{ $initials($u->name) }}</div>
            <div class="uinfo"><b>{{ $u->name }}</b><span>{{ $u->email }}@if($u->student_id) · {{ $u->student_id }}@endif</span></div>
            <div class="umeta"><span class="tag {{ $u->role === 'admin' ? 'admin' : '' }}">{{ $u->role === 'admin' ? 'Admin' : 'Student' }}</span><br>Active {{ $u->last_active->diffForHumans() }}</div>
          </li>
        @endforeach
      </ul>
    @endif
  </div>
</dialog>

<script>
function filterUsers(input) {
  const q = input.value.trim().toLowerCase();
  document.querySelectorAll('#usersList li').forEach(li => { li.hidden = q !== '' && !li.dataset.search.includes(q); });
}
document.querySelectorAll('dialog.list-modal').forEach(d => d.addEventListener('click', e => { if (e.target === d) d.close(); }));

// Activity chart: questions (green, filled) + active students (amber line)
(function () {
  const DATA = @js($daily);
  const W = 760, H = 230, svg = document.getElementById('aSvg'), tip = document.getElementById('aTip'), box = document.getElementById('aChart');
  const max = Math.max(4, ...DATA.map(d => Math.max(d.q, d.a))) * 1.15;
  const pts = key => DATA.map((d, i) => [DATA.length > 1 ? i * W / (DATA.length - 1) : W / 2, H - 6 - d[key] / max * (H - 24)]);
  const curve = p => p.reduce((s, [x, y], i) => i ? s + ` C${(p[i - 1][0] + x) / 2} ${p[i - 1][1]} ${(p[i - 1][0] + x) / 2} ${y} ${x} ${y}` : `M${x} ${y}`, '');
  const q = pts('q'), a = pts('a');
  const grid = [55, 115, 175].map(y => `<line x1="0" x2="${W}" y1="${y}" y2="${y}" stroke="#e3e7e2" stroke-dasharray="4 6" vector-effect="non-scaling-stroke"/>`).join('');
  svg.innerHTML = `<defs><linearGradient id="aFill" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#3fb070" stop-opacity=".3"/><stop offset="1" stop-color="#3fb070" stop-opacity="0"/></linearGradient></defs>
    ${grid}<path d="${curve(q)} L${W} ${H} L0 ${H}Z" fill="url(#aFill)"/>
    <path d="${curve(q)}" fill="none" stroke="#1f6b43" stroke-width="3" vector-effect="non-scaling-stroke"/>
    <path d="${curve(a)}" fill="none" stroke="#e0a43a" stroke-width="3" vector-effect="non-scaling-stroke"/>
    <line id="aLine" x1="0" x2="0" y1="0" y2="${H}" stroke="#111c15" stroke-opacity=".18" vector-effect="non-scaling-stroke" style="opacity:0"/>`;
  const step = Math.max(1, Math.round((DATA.length - 1) / 4));
  document.getElementById('aAxis').innerHTML = DATA.filter((_, i) => i % step === 0 || i === DATA.length - 1)
    .map((d, i, arr) => `<span>${i === arr.length - 1 ? 'Today' : d.d}</span>`).join('');
  const dot = (c) => { const e = document.createElement('i'); e.style.cssText = `position:absolute;width:13px;height:13px;border-radius:50%;background:#fff;border:3px solid ${c};box-sizing:border-box;transform:translate(-50%,-50%);pointer-events:none;opacity:0`; box.appendChild(e); return e; };
  const dq = dot('#1f6b43'), da = dot('#e0a43a');
  svg.addEventListener('mousemove', e => {
    const r = svg.getBoundingClientRect();
    const i = Math.max(0, Math.min(DATA.length - 1, Math.round((e.clientX - r.left) / r.width * (DATA.length - 1))));
    const x = 24 + q[i][0] / W * r.width, sy = r.height / 250;
    dq.style.left = da.style.left = x + 'px';
    dq.style.top = q[i][1] * sy + 'px'; da.style.top = a[i][1] * sy + 'px';
    dq.style.opacity = da.style.opacity = 1;
    const line = document.getElementById('aLine'); line.setAttribute('x1', q[i][0]); line.setAttribute('x2', q[i][0]); line.style.opacity = 1;
    tip.innerHTML = `<small>${DATA[i].d}</small><i style="background:#7ee0b0"></i>${DATA[i].q} questions<br><i style="background:#e0a43a"></i>${DATA[i].a} active students`;
    tip.style.left = x + 'px'; tip.style.top = Math.min(q[i][1], a[i][1]) * sy + 'px'; tip.style.opacity = 1;
  });
  svg.addEventListener('mouseleave', () => { tip.style.opacity = 0; dq.style.opacity = da.style.opacity = 0; document.getElementById('aLine').style.opacity = 0; });
})();
</script>
</body>
</html>
