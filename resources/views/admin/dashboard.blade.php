<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Admin Dashboard - RakanKampus</title>
<style>
body { margin: 0; }
.dash-row { display: grid; gap: 20px; margin-bottom: 20px; }
.dash-row.two { grid-template-columns: minmax(0, 1.5fr) minmax(0, 1fr); }
.dash-row.three { grid-template-columns: repeat(3, minmax(0, 1fr)); }
.dash-row .adm-card { display: flex; flex-direction: column; }
.lnk { font-size: 13px; font-weight: 700; color: var(--a-g700); text-decoration: none; white-space: nowrap; }
.lnk:hover { text-decoration: underline; }

/* questions per day */
.qchart { padding: 6px 20px 18px; flex: 1; display: flex; flex-direction: column; }
.qbars { flex: 1; min-height: 170px; display: flex; align-items: flex-end; gap: var(--gap, 10px); }
.qbars span { flex: 1; min-height: 4px; border-radius: 7px 7px 3px 3px; background: #cfe3d4; position: relative; transition: background .15s; }
.qbars span.today { background: var(--a-g700); }
.qbars span:hover { background: var(--a-g500); }
.qbars span:hover::after { content: attr(data-tip); position: absolute; bottom: calc(100% + 6px); left: 50%; transform: translateX(-50%); background: var(--a-ink); color: #fff; font-size: 11px; font-weight: 700; padding: 4px 8px; border-radius: 7px; white-space: nowrap; z-index: 2; }
.qaxis { display: flex; justify-content: space-between; font-size: 11px; color: #9aa8a0; margin-top: 8px; font-weight: 600; }
.qempty { font-size: 12.5px; color: var(--a-mute); margin-top: 6px; }

/* most asked topics */
.tt { display: grid; grid-template-columns: 24px minmax(0, 1fr) 44px; gap: 10px; align-items: center; padding: 9px 20px; text-decoration: none; color: inherit; }
.tt:hover { background: #fafcfb; }
.tt > span { font-weight: 800; color: #9aa8a0; font-size: 13px; }
.tt b { font-size: 13px; display: block; margin-bottom: 6px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: var(--a-ink); font-weight: 700; }
.tt em { font-style: normal; font-weight: 800; font-size: 13px; text-align: right; color: var(--a-ink); }
.bar { height: 6px; border-radius: 99px; background: #eef3ef; overflow: hidden; }
.bar i { display: block; height: 100%; border-radius: 99px; background: linear-gradient(90deg, var(--a-g500), var(--a-g700)); }

/* list rows */
.li { display: flex; gap: 12px; align-items: flex-start; padding: 12px 20px; border-top: 1px solid #eef3ef; text-decoration: none; color: inherit; }
a.li:hover { background: #fafcfb; }
.li .q { width: 34px; height: 34px; border-radius: 10px; display: grid; place-items: center; font-weight: 800; font-size: 13px; flex-shrink: 0; }
.li b { font-size: 13.5px; font-weight: 600; line-height: 1.4; color: var(--a-ink); display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden; }
.li small { font-size: 12px; color: var(--a-mute); margin-top: 2px; display: block; }
.li .dot { width: 8px; height: 8px; border-radius: 50%; background: #22c55e; margin: 6px 0 0 auto; flex-shrink: 0; }
.card-empty { padding: 8px 20px 20px; font-size: 13px; color: var(--a-mute); }
.card-empty b { display: block; color: var(--a-ink); margin-bottom: 2px; }

/* online today */
.obars { display: flex; align-items: flex-end; gap: 5px; height: 110px; padding: 4px 20px 0; }
.obars span { flex: 1; border-radius: 6px 6px 2px 2px; background: #cfe3d4; min-height: 4px; position: relative; }
.obars span.now { background: var(--a-g700); }
.obars span:hover::after { content: attr(data-tip); position: absolute; bottom: calc(100% + 6px); left: 50%; transform: translateX(-50%); background: var(--a-ink); color: #fff; font-size: 11px; font-weight: 700; padding: 4px 7px; border-radius: 7px; white-space: nowrap; }
.ohours { display: flex; justify-content: space-between; font-size: 10.5px; color: #9aa8a0; margin: 6px 20px 18px; font-weight: 600; }
</style>
</head>
<body>

@include('partials.admin-nav', ['active' => 'dashboard', 'unansweredCount' => $unansweredCount, 'unreadFeedbackCount' => $unreadFeedbackCount])
@php
  $hour = (int) now()->format('G');
  $greet = $hour < 12 ? 'Selamat pagi' : ($hour < 15 ? 'Selamat tengah hari' : ($hour < 19 ? 'Selamat petang' : 'Selamat malam'));
  $firstName = explode(' ', trim(auth()->user()->name ?? 'Admin'))[0];
  $askTrend = $askedLastWeek > 0 ? (int) round(($askedThisWeek - $askedLastWeek) * 100 / $askedLastWeek) : null;
  $sub = $answeredRate === null
      ? 'No student questions yet. The numbers fill in as students start chatting.'
      : 'RakanKampus answered ' . $answeredRate . '% of student questions.' . ($unansweredCount ? ' ' . $unansweredCount . ' ' . \Illuminate\Support\Str::plural('question', $unansweredCount) . ' still ' . ($unansweredCount === 1 ? 'needs' : 'need') . ' your help.' : ' Nothing is waiting for you.');
  $feedbackKind = function ($f) {
      if (filled($f->issue_report)) return ['!', 't-red', $f->issue_type ?: 'Report issue', $f->issue_report];
      if (filled($f->feature_request)) return ['★', 't-blue', 'Feature request', $f->feature_request];
      return ['✉', 't-green', 'Feedback', $f->feedback];
  };
  $maxOnline = max(1, max($onlineToday ?: [0]));
  $peakHour = array_sum($onlineToday) ? array_search(max($onlineToday), $onlineToday) : null;
  $maxTop = max(1, (int) ($topTopics->max('n') ?? 1));
  $icon = fn ($p) => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' . $p . '</svg>';
@endphp

<div class="adm-wrap">

  <x-admin-hero :title="$greet . ', ' . e($firstName) . ' 👋'" :label="now()->translatedFormat('l, j F Y')" :sub="$sub"
    :kpis="[
      ['value' => number_format($studentCount), 'label' => 'Students', 'tag' => $newStudents ? '▲ ' . $newStudents . ' this week' : 'no new this week', 'href' => route('admin.analytics')],
      ['value' => number_format($questionsAsked), 'label' => 'Questions asked', 'tag' => $askTrend === null ? number_format($askedThisWeek) . ' this week' : ($askTrend >= 0 ? '▲ ' : '▼ ') . abs($askTrend) . '% vs last week', 'tone' => ($askTrend ?? 0) < 0 ? 'red' : null, 'href' => route('admin.analytics')],
      ['value' => $answeredRate === null ? '—' : $answeredRate . '%', 'label' => 'Answered by bot', 'tag' => 'all time'],
      ['value' => $unansweredCount, 'label' => 'Unanswered', 'tag' => $unansweredCount ? 'needs you' : 'all clear', 'tone' => $unansweredCount ? 'red' : null, 'href' => route('admin.unanswered.index')],
    ]">
    <x-slot:actions>
      <a href="{{ route('admin.unanswered.index') }}" class="adm-btn w">Answer questions</a>
      <a href="{{ route('admin.knowledge') }}" class="adm-btn gl">Open knowledge base</a>
    </x-slot:actions>
  </x-admin-hero>

  @if(session('status'))
    <div class="adm-flash">{{ session('status') }}</div>
  @endif

  <div class="dash-row two">
    <section class="adm-card flush">
      <div class="adm-ch">
        <div class="adm-ci t-green">{!! $icon('<path d="M3 3v18h18"/><path d="M7 16v-4M12 16V8M17 16v-7"/>') !!}</div>
        <div><h3>Questions asked</h3><p id="qSub"></p></div>
        <div class="r">
          <div class="adm-seg" id="qSeg">
            <button type="button" data-days="7" onclick="drawQ(7)">7d</button>
            <button type="button" data-days="14" class="on" onclick="drawQ(14)">14d</button>
            <button type="button" data-days="30" onclick="drawQ(30)">30d</button>
          </div>
        </div>
      </div>
      <div class="qchart">
        <div class="qbars" id="qBars"></div>
        <div class="qaxis" id="qAxis"></div>
      </div>
    </section>

    <section class="adm-card flush">
      <div class="adm-ch">
        <div class="adm-ci t-amber">{!! $icon('<path d="m12 3 2.7 5.6 6.1.9-4.4 4.3 1 6.1L12 17l-5.4 2.9 1-6.1L3.2 9.5l6.1-.9z"/>') !!}</div>
        <div><h3>Most asked topics</h3><p>Answers the bot gave this week</p></div>
        <div class="r"><a class="lnk" href="{{ route('admin.knowledge') }}">Knowledge base →</a></div>
      </div>
      @forelse($topTopics as $i => $t)
        <a class="tt" href="{{ route('admin.information.show', $t['id']) }}">
          <span>{{ $i + 1 }}</span>
          <div><b title="{{ $t['name'] }}">{{ $t['name'] }}</b><div class="bar"><i style="width: {{ round($t['n'] * 100 / $maxTop) }}%"></i></div></div>
          <em>{{ $t['n'] }}</em>
        </a>
      @empty
        <div class="card-empty"><b>No answers this week yet</b>Topics show up here once the bot starts answering from them.</div>
      @endforelse
      <div style="height:10px"></div>
    </section>
  </div>

  <div class="dash-row three">
    <section class="adm-card flush">
      <div class="adm-ch">
        <div class="adm-ci t-red">{!! $icon('<circle cx="12" cy="12" r="9"/><path d="M12 8v5M12 16h.01"/>') !!}</div>
        <div><h3>Needs an answer</h3><p>{{ $unansweredCount }} waiting</p></div>
        <div class="r"><a class="lnk" href="{{ route('admin.unanswered.index') }}">See all →</a></div>
      </div>
      @forelse($pendingQuestions as $q)
        <a class="li" href="{{ route('admin.unanswered.index') }}">
          <div class="q t-amber">{{ $q->asked_count }}×</div>
          <div><b>{{ $q->question }}</b><small>Last asked {{ $q->updated_at?->diffForHumans() }}</small></div>
        </a>
      @empty
        <div class="card-empty"><b>All caught up 🎉</b>The bot has an answer for everything students asked.</div>
      @endforelse
    </section>

    <section class="adm-card flush">
      <div class="adm-ch">
        <div class="adm-ci t-blue">{!! $icon('<rect x="3" y="5" width="18" height="14" rx="3"/><path d="m3 8 9 6 9-6"/>') !!}</div>
        <div><h3>Latest feedback</h3><p>{{ $unreadFeedbackCount ? $unreadFeedbackCount . ' new' : 'No new messages' }}</p></div>
        <div class="r"><a class="lnk" href="{{ route('admin.inbox') }}">Inbox →</a></div>
      </div>
      @forelse($latestFeedback as $f)
        @php [$sym, $tone, $kind, $text] = $feedbackKind($f); @endphp
        <a class="li" href="{{ route('admin.inbox') }}">
          <div class="q {{ $tone }}">{{ $sym }}</div>
          <div><b>{{ \Illuminate\Support\Str::limit($text, 80) }}</b><small>{{ $kind }} · {{ $f->user_name ?: 'Student' }} · {{ $f->created_at?->diffForHumans(null, true, true) }}</small></div>
          @unless($f->is_read)<span class="dot" title="New"></span>@endunless
        </a>
      @empty
        <div class="card-empty"><b>No feedback yet</b>Student feedback and feature requests show up here.</div>
      @endforelse
    </section>

    <section class="adm-card flush">
      <div class="adm-ch">
        <div class="adm-ci t-green">{!! $icon('<circle cx="12" cy="12" r="4"/><path d="M4.9 4.9a10 10 0 0 0 0 14.2M19.1 4.9a10 10 0 0 1 0 14.2"/>') !!}</div>
        <div><h3>Online today</h3><p>{{ $peakHour !== null ? 'Peak ' . \Illuminate\Support\Carbon::createFromTime($peakHour)->format('g A') . ' · ' . $onlineToday[$peakHour] . ' ' . \Illuminate\Support\Str::plural('student', $onlineToday[$peakHour]) : 'No student activity yet today' }}</p></div>
        <div class="r"><a class="lnk" href="{{ route('admin.analytics') }}">Analytics →</a></div>
      </div>
      <div style="flex:1"></div>
      <div class="obars">
        @foreach($onlineToday as $h => $n)
          <span class="{{ $h === $hour ? 'now' : '' }}" style="height: {{ max(4, round($n / $maxOnline * 100)) }}%"
                data-tip="{{ \Illuminate\Support\Carbon::createFromTime($h)->format('g A') }} · {{ $n }} {{ \Illuminate\Support\Str::plural('student', $n) }}"></span>
        @endforeach
      </div>
      <div class="ohours"><span>8am</span><span>12pm</span><span>4pm</span><span>8pm</span><span>10pm</span></div>
    </section>
  </div>
</div>

<script>
  const DAILY = @js($dailyQuestions);
  function drawQ(days) {
    document.querySelectorAll('#qSeg button').forEach(b => b.classList.toggle('on', +b.dataset.days === days));
    const data = DAILY.slice(-days);
    const max = Math.max(1, ...data.map(d => d.n));
    const total = data.reduce((s, d) => s + d.n, 0);
    const bars = document.getElementById('qBars');
    bars.style.setProperty('--gap', days > 14 ? '4px' : days > 7 ? '10px' : '18px');
    bars.innerHTML = data.map((d, i) =>
      `<span class="${i === data.length - 1 ? 'today' : ''}" style="height:${Math.max(3, d.n / max * 100)}%" data-tip="${d.d} · ${d.n} ${d.n === 1 ? 'question' : 'questions'}"></span>`
    ).join('');
    const mid = data[Math.floor((data.length - 1) / 2)];
    document.getElementById('qAxis').innerHTML = `<span>${data[0].d}</span><span>${mid.d}</span><span>Today</span>`;
    document.getElementById('qSub').textContent = 'Last ' + days + ' days · ' + total.toLocaleString() + (total === 1 ? ' question' : ' questions');
  }
  drawQ(14);
</script>
</body>
</html>
