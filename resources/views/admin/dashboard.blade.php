<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Admin Dashboard - RakanKampus</title>
<style>
body { margin: 0; }
.bento { display: grid; grid-template-columns: repeat(12, minmax(0, 1fr)); gap: 18px; }
.bento > * { min-width: 0; }
.s3 { grid-column: span 3; } .s4 { grid-column: span 4; } .s5 { grid-column: span 5; } .s7 { grid-column: span 7; } .s8 { grid-column: span 8; }
.lnk { font-size: 13px; font-weight: 800; color: var(--a-ink); text-decoration: none; white-space: nowrap; display: inline-flex; align-items: center; gap: 6px; }
.lnk svg { width: 15px; height: 15px; transition: transform .15s; }
.lnk:hover svg { transform: translateX(3px); }

/* hero tile */
.hero { position: relative; overflow: hidden; border-radius: 28px; padding: 34px 36px; min-height: 300px; color: #fff; display: flex; align-items: center;
  background: linear-gradient(135deg, #0f2a1c 0%, #1d4d33 60%, #2f7a4f 100%); }
.hero::before { content: ''; position: absolute; width: 460px; height: 460px; border-radius: 50%; right: -80px; top: -150px; background: radial-gradient(circle, rgba(126,224,176,.35), transparent 65%); }
.hero::after { content: ''; position: absolute; inset: 0; background-image: radial-gradient(rgba(255,255,255,.07) 1px, transparent 1px); background-size: 20px 20px;
  -webkit-mask-image: linear-gradient(100deg, transparent 40%, #000); mask-image: linear-gradient(100deg, transparent 40%, #000); pointer-events: none; }
.hero .txt { position: relative; z-index: 1; max-width: 62%; }
.hero small { font-size: 12px; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; color: #9fd6b6; }
.hero h2 { margin: 10px 0 0; font-size: 32px; font-weight: 800; letter-spacing: -.03em; line-height: 1.15; }
.hero p { margin: 12px 0 22px; font-size: 14.5px; line-height: 1.55; color: #bfe0cb; max-width: 420px; }
.hero .acts { display: flex; gap: 10px; flex-wrap: wrap; }
.hero .bot { position: absolute; right: 40px; bottom: -8px; z-index: 1; filter: drop-shadow(0 22px 34px rgba(0,0,0,.35)); animation: bob 4s ease-in-out infinite; }
@keyframes bob { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-8px); } }
.hero .bot .arm { transform-box: view-box; transform-origin: 163px 100px; animation: wave 2.6s ease-in-out infinite; }
@keyframes wave { 0%, 60%, 100% { transform: rotate(0); } 70% { transform: rotate(-16deg); } 80% { transform: rotate(8deg); } 90% { transform: rotate(-10deg); } }

/* 2×2 metric tiles */
.metrics { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
.metrics .adm-k { min-height: 141px; text-decoration: none; transition: transform .15s, box-shadow .15s; }
.metrics a.adm-k:hover { transform: translateY(-3px); box-shadow: 0 16px 30px rgba(17,28,21,.08); }

/* questions chart */
.chart { position: relative; padding: 0 24px 20px; }
.chart svg { display: block; width: 100%; height: 230px; overflow: visible; cursor: crosshair; }
.chart .axis { display: flex; justify-content: space-between; font-size: 11px; color: #9aa39c; font-weight: 700; margin-top: 8px; }
.chart .tip { position: absolute; pointer-events: none; transform: translate(-50%, -125%); background: var(--a-dark); color: #fff; font-size: 12px; font-weight: 700; padding: 6px 10px; border-radius: 10px; white-space: nowrap; opacity: 0; transition: opacity .1s; }
.chart .tip small { display: block; font-size: 10.5px; color: #9fb3a6; font-weight: 600; }

/* most asked */
.tt { display: flex; align-items: center; gap: 14px; padding: 12px 24px; border-top: 1px solid #f1f2ef; text-decoration: none; color: inherit; }
.tt:first-of-type { border-top: 0; }
.tt:hover { background: #fafbf9; }
.tt .n { font-size: 24px; font-weight: 800; color: #d5dcd7; width: 26px; letter-spacing: -.04em; flex-shrink: 0; }
.tt b { flex: 1; min-width: 0; font-size: 13.5px; font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: var(--a-ink); }
.tt em { font-style: normal; font-size: 12px; font-weight: 800; padding: 4px 11px; border-radius: 99px; background: var(--a-g100); color: var(--a-g800); }

/* lists */
.li { display: flex; gap: 12px; align-items: center; padding: 12px 24px; border-top: 1px solid #f1f2ef; text-decoration: none; color: inherit; }
.li:first-of-type { border-top: 0; }
a.li:hover { background: #fafbf9; }
.li .q { width: 42px; height: 42px; border-radius: 14px; display: grid; place-items: center; font-weight: 800; font-size: 13px; flex-shrink: 0; }
.li .q.round { border-radius: 50%; color: #fff; }
.li .body { min-width: 0; flex: 1; }
.li b { font-size: 13.5px; font-weight: 700; color: var(--a-ink); display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.li small { font-size: 12px; color: var(--a-mute); display: block; margin-top: 2px; }
.li .go { flex-shrink: 0; }
.li .dot { width: 8px; height: 8px; border-radius: 50%; background: #22c55e; flex-shrink: 0; }
.card-empty { padding: 4px 24px 24px; font-size: 13px; color: var(--a-mute); }
.card-empty b { display: block; color: var(--a-ink); margin-bottom: 2px; font-size: 14px; }

/* online today */
.online { background: var(--a-g100); border-color: transparent; display: flex; flex-direction: column; }
.online .adm-ch p { color: var(--a-g700); }
.obars { flex: 1; display: flex; align-items: flex-end; gap: 5px; min-height: 130px; padding: 0 24px; }
.obars span { flex: 1; border-radius: 99px; background: rgba(31,107,67,.2); min-height: 8px; position: relative; }
.obars span.now { background: var(--a-g700); }
.obars span:hover { background: var(--a-g500); }
.obars span:hover::after { content: attr(data-tip); position: absolute; bottom: calc(100% + 6px); left: 50%; transform: translateX(-50%); background: var(--a-dark); color: #fff; font-size: 11px; font-weight: 700; padding: 5px 8px; border-radius: 8px; white-space: nowrap; z-index: 2; }
.ohours { display: flex; justify-content: space-between; font-size: 11px; color: var(--a-g700); font-weight: 700; padding: 10px 24px 22px; }
</style>
</head>
<body>

@include('partials.admin-nav', ['active' => 'dashboard', 'unansweredCount' => $unansweredCount, 'unreadFeedbackCount' => $unreadFeedbackCount])
@php
  $hour = (int) now()->format('G');
  $greet = $hour < 12 ? 'Selamat pagi' : ($hour < 15 ? 'Selamat tengah hari' : ($hour < 19 ? 'Selamat petang' : 'Selamat malam'));
  $firstName = explode(' ', trim(auth()->user()->name ?? 'Admin'))[0];
  $askTrend = $askedLastWeek > 0 ? (int) round(($askedThisWeek - $askedLastWeek) * 100 / $askedLastWeek) : null;
  $feedbackKind = function ($f) {
      if (filled($f->issue_report)) return ['#d64545', $f->issue_type ?: 'Report issue', $f->issue_report];
      if (filled($f->feature_request)) return ['#3c6fb0', 'Feature request', $f->feature_request];
      return ['#1f6b43', 'Feedback', $f->feedback];
  };
  $maxOnline = max(1, max($onlineToday ?: [0]));
  $peakHour = array_sum($onlineToday) ? array_search(max($onlineToday), $onlineToday) : null;
  $icon = fn ($p) => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' . $p . '</svg>';
  $arrow = $icon('<path d="M5 12h14M13 6l6 6-6 6"/>');
@endphp

<div class="adm-wrap">

  @if(session('status'))
    <div class="adm-flash">{{ session('status') }}</div>
  @endif

  <div class="bento">

    {{-- hero --}}
    <section class="hero s7">
      <div class="txt">
        <small>RakanKampus · Admin</small>
        <h2>{{ $greet }}, {{ $firstName }} 👋<br>
          @if($answeredRate === null) RakanKampus is ready. @else RakanKampus answered {{ $answeredRate }}%. @endif
        </h2>
        <p>
          @if($unansweredCount)
            {{ $unansweredCount }} student {{ \Illuminate\Support\Str::plural('question', $unansweredCount) }} still {{ $unansweredCount === 1 ? 'needs' : 'need' }} your help. Answer once and the bot learns for everyone.
          @elseif($answeredRate === null)
            No student questions yet. Numbers fill in as students start chatting.
          @else
            Every question has an answer right now. Nice work!
          @endif
        </p>
        <div class="acts">
          @if($unansweredCount)
            <a href="{{ route('admin.unanswered.index') }}" class="adm-btn w">Answer questions {!! $arrow !!}</a>
          @endif
          <a href="{{ route('admin.knowledge') }}" class="adm-btn gl">Knowledge base</a>
        </div>
      </div>
      <svg class="bot" width="178" height="196" viewBox="0 0 200 220" aria-hidden="true">
        <line x1="100" y1="14" x2="100" y2="30" stroke="#f4faf6" stroke-width="8"/><circle cx="100" cy="12" r="9" fill="#7ee0b0"/>
        <rect x="56" y="30" width="88" height="64" rx="24" fill="#f4faf6"/><circle cx="54" cy="58" r="14" fill="#7ee0b0"/><circle cx="146" cy="58" r="14" fill="#7ee0b0"/>
        <rect x="72" y="44" width="56" height="38" rx="14" fill="#1f3a2b"/><circle cx="90" cy="62" r="6" fill="#7ee0b0"/><circle cx="110" cy="62" r="6" fill="#7ee0b0"/>
        <path d="M90 72 Q100 78 110 72" stroke="#7ee0b0" stroke-width="3" fill="none" stroke-linecap="round"/>
        <rect x="52" y="100" width="96" height="82" rx="26" fill="#f4faf6"/><circle cx="100" cy="132" r="9" fill="#3fb070"/>
        <rect x="68" y="178" width="20" height="36" rx="9" fill="#f4faf6"/><rect x="112" y="178" width="20" height="36" rx="9" fill="#f4faf6"/>
        <rect x="26" y="104" width="22" height="50" rx="11" fill="#f4faf6" transform="rotate(20 37 104)"/>
        <g class="arm"><rect x="152" y="96" width="22" height="50" rx="11" fill="#f4faf6" transform="rotate(-150 163 100)"/></g>
      </svg>
    </section>

    {{-- metrics --}}
    <div class="metrics s5">
      <a class="adm-card adm-k" href="{{ route('admin.analytics') }}">
        <div class="adm-ci t-green">{!! $icon('<circle cx="9" cy="8" r="4"/><path d="M2 21a7 7 0 0 1 14 0M17 11a3 3 0 1 0 0-6M22 21a6 6 0 0 0-5-6"/>') !!}</div>
        @if($newStudents)<em class="t-green">+{{ $newStudents }} this week</em>@endif
        <div><b>{{ number_format($studentCount) }}</b><span>Students</span></div>
      </a>
      <a class="adm-card adm-k dark" href="{{ route('admin.analytics') }}">
        <div class="adm-ci">{!! $icon('<path d="M4 5h16v11H8l-4 4z"/>') !!}</div>
        <em>{{ $askTrend === null ? number_format($askedThisWeek) . ' this week' : ($askTrend >= 0 ? '▲ ' : '▼ ') . abs($askTrend) . '%' }}</em>
        <div><b>{{ number_format($questionsAsked) }}</b><span>Questions asked</span></div>
      </a>
      <div class="adm-card adm-k">
        <div class="adm-ci t-amber">{!! $icon('<path d="M20 6 9 17l-5-5"/>') !!}</div>
        <div><b>{{ $answeredRate === null ? '—' : $answeredRate . '%' }}</b><span>Answered by bot</span></div>
      </div>
      <a class="adm-card adm-k" href="{{ route('admin.unanswered.index') }}">
        <div class="adm-ci t-red">{!! $icon('<circle cx="12" cy="12" r="9"/><path d="M12 8v5M12 16h.01"/>') !!}</div>
        @if($unansweredCount)<em class="t-red">needs you</em>@endif
        <div><b>{{ $unansweredCount }}</b><span>Unanswered</span></div>
      </a>
    </div>

    {{-- questions per day --}}
    <section class="adm-card flush s8">
      <div class="adm-ch">
        <div><h3>Questions asked</h3><p id="qSub"></p></div>
        <div class="r">
          <div class="adm-seg" id="qSeg">
            <button type="button" data-days="7" onclick="drawQ(7)">7d</button>
            <button type="button" data-days="14" class="on" onclick="drawQ(14)">14d</button>
            <button type="button" data-days="30" onclick="drawQ(30)">30d</button>
          </div>
        </div>
      </div>
      <div class="chart" id="qChart">
        <svg id="qSvg" viewBox="0 0 760 230" preserveAspectRatio="none" aria-label="Questions asked per day"></svg>
        <div class="axis" id="qAxis"></div>
        <div class="tip" id="qTip"></div>
      </div>
    </section>

    {{-- most asked topics --}}
    <section class="adm-card flush s4">
      <div class="adm-ch">
        <div><h3>Most asked</h3><p>Topics the bot answered this week</p></div>
      </div>
      @forelse($topTopics as $i => $t)
        <a class="tt" href="{{ route('admin.information.show', $t['id']) }}">
          <span class="n">{{ $i + 1 }}</span>
          <b title="{{ $t['name'] }}">{{ $t['name'] }}</b>
          <em>{{ $t['n'] }}</em>
        </a>
      @empty
        <div class="card-empty"><b>Nothing yet this week</b>Topics show up here once the bot starts answering from them.</div>
      @endforelse
      <div style="padding:10px 24px 22px"><a class="lnk" href="{{ route('admin.knowledge') }}">All topics {!! $arrow !!}</a></div>
    </section>

    {{-- needs an answer --}}
    <section class="adm-card flush s5">
      <div class="adm-ch">
        <div><h3>Needs an answer</h3><p>{{ $unansweredCount }} waiting</p></div>
        <div class="r"><a class="lnk" href="{{ route('admin.unanswered.index') }}">See all {!! $arrow !!}</a></div>
      </div>
      @forelse($pendingQuestions as $q)
        <div class="li">
          <div class="q t-amber">{{ $q->asked_count }}×</div>
          <div class="body"><b title="{{ $q->question }}">{{ $q->question }}</b><small>Last asked {{ $q->updated_at?->diffForHumans() }}</small></div>
          <a class="adm-btn g sm go" href="{{ route('admin.unanswered.index') }}">Answer</a>
        </div>
      @empty
        <div class="card-empty"><b>All caught up 🎉</b>The bot has an answer for everything students asked.</div>
      @endforelse
      <div style="height:10px"></div>
    </section>

    {{-- latest feedback --}}
    <section class="adm-card flush s4">
      <div class="adm-ch">
        <div><h3>Latest feedback</h3><p>{{ $unreadFeedbackCount ? $unreadFeedbackCount . ' new' : 'No new messages' }}</p></div>
        <div class="r"><a class="lnk" href="{{ route('admin.inbox') }}">Inbox {!! $arrow !!}</a></div>
      </div>
      @forelse($latestFeedback as $f)
        @php [$col, $kind, $text] = $feedbackKind($f); @endphp
        <a class="li" href="{{ route('admin.inbox') }}">
          <div class="q round" style="background: {{ $col }}">{{ strtoupper(mb_substr($f->user_name ?: 'S', 0, 1)) }}</div>
          <div class="body"><b title="{{ $text }}">{{ \Illuminate\Support\Str::limit($text, 80) }}</b><small>{{ $f->user_name ?: 'Student' }} · {{ $kind }} · {{ $f->created_at?->diffForHumans(null, true, true) }}</small></div>
          @unless($f->is_read)<span class="dot" title="New"></span>@endunless
        </a>
      @empty
        <div class="card-empty"><b>No feedback yet</b>Student feedback and feature requests show up here.</div>
      @endforelse
      <div style="height:10px"></div>
    </section>

    {{-- online today --}}
    <section class="adm-card flush online s3">
      <div class="adm-ch">
        <div><h3>Online today</h3><p>{{ $peakHour !== null ? 'Peak ' . \Illuminate\Support\Carbon::createFromTime($peakHour)->format('g A') . ' · ' . $onlineToday[$peakHour] . ' ' . \Illuminate\Support\Str::plural('student', $onlineToday[$peakHour]) : 'No activity yet today' }}</p></div>
      </div>
      <div class="obars">
        @foreach($onlineToday as $h => $n)
          <span class="{{ $h === $hour ? 'now' : '' }}" style="height: {{ max(6, round($n / $maxOnline * 100)) }}%"
                data-tip="{{ \Illuminate\Support\Carbon::createFromTime($h)->format('g A') }} · {{ $n }}"></span>
        @endforeach
      </div>
      <div class="ohours"><span>8am</span><span>12pm</span><span>4pm</span><span>10pm</span></div>
    </section>
  </div>
</div>

<script>
  const DAILY = @js($dailyQuestions);
  const qSvg = document.getElementById('qSvg'), qTip = document.getElementById('qTip');
  let qPts = [], qData = [];
  const QW = 760, QH = 210;

  function drawQ(days) {
    document.querySelectorAll('#qSeg button').forEach(b => b.classList.toggle('on', +b.dataset.days === days));
    qData = DAILY.slice(-days);
    const max = Math.max(4, ...qData.map(d => d.n)) * 1.15;
    const total = qData.reduce((s, d) => s + d.n, 0);
    qPts = qData.map((d, i) => [i * QW / (qData.length - 1), QH - 6 - d.n / max * (QH - 24)]);
    let path = `M${qPts[0][0]} ${qPts[0][1]}`;
    for (let i = 1; i < qPts.length; i++) {
      const [x0, y0] = qPts[i - 1], [x1, y1] = qPts[i], cx = (x0 + x1) / 2;
      path += ` C${cx} ${y0} ${cx} ${y1} ${x1} ${y1}`;
    }
    const grid = [50, 105, 160].map(y => `<line x1="0" x2="${QW}" y1="${y}" y2="${y}" stroke="#e3e7e2" stroke-dasharray="4 6" vector-effect="non-scaling-stroke"/>`).join('');
    const last = qPts[qPts.length - 1];
    qSvg.innerHTML = `<defs><linearGradient id="qFill" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#3fb070" stop-opacity=".32"/><stop offset="1" stop-color="#3fb070" stop-opacity="0"/></linearGradient></defs>
      ${grid}<path d="${path} L${QW} ${QH} L0 ${QH}Z" fill="url(#qFill)"/>
      <path d="${path}" fill="none" stroke="#1f6b43" stroke-width="3" stroke-linecap="round" vector-effect="non-scaling-stroke"/>
      <line id="qLine" x1="0" x2="0" y1="0" y2="${QH}" stroke="#111c15" stroke-opacity=".18" vector-effect="non-scaling-stroke" style="opacity:0"/>`;
    placeDot(qPts.length - 1, false);
    const mid = qData[Math.floor((qData.length - 1) / 2)];
    document.getElementById('qAxis').innerHTML = `<span>${qData[0].d}</span><span>${mid.d}</span><span>Today</span>`;
    document.getElementById('qSub').textContent = `Last ${days} days · ${total.toLocaleString()} ${total === 1 ? 'question' : 'questions'}`;
  }

  // the dot is an HTML element so it stays round when the chart stretches
  const qDot = document.createElement('i');
  qDot.style.cssText = 'position:absolute;width:14px;height:14px;border-radius:50%;background:#fff;border:3px solid #1f6b43;box-sizing:border-box;transform:translate(-50%,-50%);pointer-events:none';
  document.getElementById('qChart').appendChild(qDot);
  function placeDot(i, showTip) {
    const r = qSvg.getBoundingClientRect();
    const [px, py] = qPts[i];
    const left = 24 + px / QW * r.width, top = py / 230 * r.height;
    qDot.style.left = left + 'px'; qDot.style.top = top + 'px';
    if (showTip) {
      qTip.innerHTML = `${qData[i].n} ${qData[i].n === 1 ? 'question' : 'questions'}<small>${qData[i].d}</small>`;
      qTip.style.left = left + 'px'; qTip.style.top = top + 'px'; qTip.style.opacity = 1;
      const line = document.getElementById('qLine'); line.setAttribute('x1', px); line.setAttribute('x2', px); line.style.opacity = 1;
    }
  }
  qSvg.addEventListener('mousemove', (e) => {
    const r = qSvg.getBoundingClientRect();
    const i = Math.max(0, Math.min(qPts.length - 1, Math.round((e.clientX - r.left) / r.width * (qPts.length - 1))));
    placeDot(i, true);
  });
  qSvg.addEventListener('mouseleave', () => { qTip.style.opacity = 0; document.getElementById('qLine').style.opacity = 0; placeDot(qPts.length - 1, false); });
  addEventListener('resize', () => placeDot(qPts.length - 1, false));

  drawQ(14);
</script>
</body>
</html>
