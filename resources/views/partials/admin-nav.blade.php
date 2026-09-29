{{--
  Admin sidebar (PC only) — white sidebar, grouped menu with count badges,
  "teach the bot" card, admin row + logout, and a top bar (breadcrumb, quick search, inbox bell). Also pulls in the shared admin
  theme (partials/admin-theme: page background, green hero banner, glass stat tiles, cards).

  @include('partials.admin-nav', ['active' => 'dashboard'])
  Counts are looked up here when a page doesn't pass them.
--}}
@php
  $adminNavActive = $active ?? 'dashboard';
  $adminUnanswered = $unansweredCount ?? \App\Models\UnansweredQuestion::where('status', 'pending')->count();
  $adminUnreadFeedback = $unreadFeedbackCount ?? \App\Models\Feedback::where('is_read', false)->count();
  $adminTopics = \App\Models\Information::count();
  $adminUser = auth()->user();
  $adminCrumb = ['dashboard' => 'Dashboard', 'analytics' => 'Analytics', 'knowledge' => 'Knowledge base', 'unanswered' => 'Unanswered', 'inbox' => 'Inbox', 'database' => 'Database'][$adminNavActive] ?? 'Dashboard';
  $adminCrumbSub = $crumb ?? null;
  $adminJump = \App\Models\Information::orderBy('main_topic')->get(['id', 'main_topic']);
  $adminNavGroups = [
    'Overview' => [
      ['key' => 'dashboard', 'route' => 'admin.dashboard', 'label' => 'Dashboard', 'icon' => '<rect x="3" y="3" width="7" height="9" rx="1.5"></rect><rect x="14" y="3" width="7" height="5" rx="1.5"></rect><rect x="14" y="12" width="7" height="9" rx="1.5"></rect><rect x="3" y="16" width="7" height="5" rx="1.5"></rect>', 'badge' => 0],
      ['key' => 'analytics', 'route' => 'admin.analytics', 'label' => 'Analytics', 'icon' => '<path d="M3 3v18h18"></path><path d="M7 16v-4"></path><path d="M12 16V8"></path><path d="M17 16v-7"></path>', 'badge' => 0],
    ],
    'Chatbot' => [
      ['key' => 'knowledge', 'route' => 'admin.knowledge', 'label' => 'Knowledge base', 'icon' => '<path d="M4 19.5V5a2 2 0 0 1 2-2h13v16H6.5A2.5 2.5 0 0 0 4 21.5"></path><path d="M8 7h7M8 11h5"></path>', 'badge' => $adminTopics, 'tone' => 'soft'],
      ['key' => 'unanswered', 'route' => 'admin.unanswered.index', 'label' => 'Unanswered', 'icon' => '<path d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"></path>', 'badge' => $adminUnanswered, 'tone' => 'red'],
      ['key' => 'inbox', 'route' => 'admin.inbox', 'label' => 'Inbox', 'icon' => '<path d="M3 8l7.89 4.26a2 2 0 0 0 2.22 0L21 8m-2 10H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2z"></path>', 'badge' => $adminUnreadFeedback, 'tone' => 'soft'],
    ],
    'System' => [
      ['key' => 'database', 'route' => 'admin.database', 'label' => 'Database', 'icon' => '<ellipse cx="12" cy="5" rx="8" ry="3"></ellipse><path d="M4 5v6c0 1.66 3.58 3 8 3s8-1.34 8-3V5M4 11v6c0 1.66 3.58 3 8 3s8-1.34 8-3v-6"></path>', 'badge' => 0],
    ],
  ];
@endphp
@include('partials.admin-theme')
<style>
  body { margin-left: 256px !important; }
  .rk-admin-sidebar { position: fixed; left: 0; top: 0; bottom: 0; width: 256px; box-sizing: border-box; z-index: 38; display: flex; flex-direction: column; padding: 20px 16px;
    background: #fff; border-right: 1px solid var(--a-line); font-family: var(--a-font); }
  .rk-admin-brand { display: flex; align-items: center; gap: 12px; padding: 2px 8px 18px; text-decoration: none; }
  .rk-admin-brand .logo { width: 40px; height: 40px; border-radius: 12px; background: linear-gradient(135deg, var(--a-g800), var(--a-g500)); display: grid; place-items: center; box-shadow: 0 6px 16px rgba(47,79,58,.25); }
  .rk-admin-brand b { display: block; font-size: 16px; font-weight: 800; letter-spacing: -.01em; color: var(--a-ink); }
  .rk-admin-brand small { display: block; font-size: 11.5px; font-weight: 600; color: var(--a-mute); }
  .rk-admin-nav { flex: 1; min-height: 0; overflow-y: auto; margin: 0 -16px; padding: 0 16px; }
  .rk-admin-grp { font-size: 11px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; color: #9aa8a0; padding: 14px 12px 8px; }
  .rk-admin-link { position: relative; display: flex; align-items: center; gap: 12px; padding: 10px 12px; border-radius: 12px; margin-bottom: 2px;
    color: var(--a-ink2); font-size: 14px; font-weight: 600; text-decoration: none; transition: background .15s, color .15s; }
  .rk-admin-link svg { width: 19px; height: 19px; stroke: #8a9a90; flex-shrink: 0; }
  .rk-admin-link:hover { background: var(--a-g50); color: var(--a-g800); }
  .rk-admin-link.active { background: var(--a-g100); color: var(--a-g800); }
  .rk-admin-link.active svg { stroke: var(--a-g700); }
  .rk-admin-link.active::before { content: ''; position: absolute; left: -16px; top: 9px; bottom: 9px; width: 4px; border-radius: 0 4px 4px 0; background: var(--a-g600); }
  .rk-admin-badge { margin-left: auto; min-width: 24px; height: 22px; padding: 0 7px; border-radius: 8px; font-size: 11.5px; font-weight: 700; display: grid; place-items: center; background: #eef2ef; color: var(--a-ink2); }
  .rk-admin-badge.red { background: #fdecec; color: var(--a-red); }
  .rk-admin-cta { display: block; margin-top: 12px; border-radius: 18px; padding: 16px; background: linear-gradient(145deg, var(--a-g800), var(--a-g600)); color: #fff; position: relative; overflow: hidden; text-decoration: none; }
  .rk-admin-cta::after { content: ''; position: absolute; width: 140px; height: 140px; border-radius: 50%; background: rgba(255,255,255,.12); right: -40px; top: -50px; }
  .rk-admin-cta b { font-size: 14px; display: block; }
  .rk-admin-cta p { font-size: 12px; color: #d8e8dc; margin: 4px 0 12px; line-height: 1.45; }
  .rk-admin-cta span { display: inline-block; background: #fff; color: var(--a-g800); font-weight: 700; font-size: 12px; padding: 8px 12px; border-radius: 10px; }
  .rk-admin-cta:hover span { background: var(--a-g50); }
  .rk-admin-me { display: flex; align-items: center; gap: 10px; padding: 14px 8px 0; margin-top: 14px; border-top: 1px solid var(--a-line); }
  .rk-admin-me .av { width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg, #f5c563, #e49b3b); color: #fff; display: grid; place-items: center; font-weight: 800; flex-shrink: 0; }
  .rk-admin-me .who { min-width: 0; }
  .rk-admin-me b { display: block; font-size: 13.5px; color: var(--a-ink); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .rk-admin-me small { display: block; font-size: 12px; color: var(--a-mute); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .rk-admin-me form { margin-left: auto; }
  .rk-admin-me button { width: 34px; height: 34px; border-radius: 10px; border: 1px solid var(--a-line); background: #fff; color: var(--a-mute); cursor: pointer; display: grid; place-items: center; }
  .rk-admin-me button:hover { background: #fdecec; color: var(--a-red); border-color: #f3d0d0; }
  .rk-admin-me button svg { width: 17px; height: 17px; }

  .rk-admin-top { position: sticky; top: 0; z-index: 30; height: 68px; box-sizing: border-box; display: flex; align-items: center; gap: 14px; padding: 0 32px;
    border-bottom: 1px solid var(--a-line); background: rgba(255,255,255,.72); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); font-family: var(--a-font); }
  .rk-admin-crumb { font-size: 13px; color: var(--a-mute); font-weight: 600; }
  .rk-admin-crumb b { color: var(--a-ink); }
  .rk-admin-jump { margin-left: auto; width: 340px; height: 40px; border-radius: 12px; border: 1px solid var(--a-line); background: #fff; display: flex; align-items: center; gap: 10px; padding: 0 12px; color: #9aa8a0; position: relative; }
  .rk-admin-jump:focus-within { border-color: #b9d4c1; box-shadow: 0 0 0 3px rgba(95,147,112,.12); }
  .rk-admin-jump svg { width: 16px; height: 16px; flex-shrink: 0; }
  .rk-admin-jump input { border: 0; outline: 0; background: transparent; font-size: 13px; color: var(--a-ink); width: 100%; font-family: inherit; }
  .rk-admin-jump kbd { white-space: nowrap; font-family: inherit; font-size: 11px; font-weight: 700; border: 1px solid var(--a-line); border-radius: 6px; padding: 1px 6px; color: var(--a-mute); }
  .rk-admin-jump ul { display: none; position: absolute; left: 0; right: 0; top: 46px; list-style: none; margin: 0; padding: 6px; background: #fff; border: 1px solid var(--a-line); border-radius: 14px; box-shadow: 0 18px 40px rgba(22,36,28,.14); max-height: 320px; overflow-y: auto; }
  .rk-admin-jump ul.open { display: block; }
  .rk-admin-jump li a { display: flex; align-items: center; gap: 10px; padding: 9px 10px; border-radius: 10px; color: var(--a-ink); font-size: 13.5px; font-weight: 600; text-decoration: none; }
  .rk-admin-jump li a small { margin-left: auto; font-size: 11px; color: var(--a-mute); font-weight: 700; text-transform: uppercase; letter-spacing: .04em; }
  .rk-admin-jump li a.sel, .rk-admin-jump li a:hover { background: var(--a-g50); color: var(--a-g800); }
  .rk-admin-jump li.none { padding: 10px; font-size: 13px; color: var(--a-mute); }
  .rk-admin-bell { width: 40px; height: 40px; border-radius: 12px; border: 1px solid var(--a-line); background: #fff; display: grid; place-items: center; color: var(--a-ink2); position: relative; text-decoration: none; }
  .rk-admin-bell:hover { background: var(--a-g50); }
  .rk-admin-bell svg { width: 18px; height: 18px; }
  .rk-admin-bell i { position: absolute; top: 9px; right: 10px; width: 8px; height: 8px; border-radius: 50%; background: var(--a-red); border: 2px solid #fff; }
</style>

<nav class="rk-admin-sidebar" aria-label="Admin">
  <a href="{{ route('admin.dashboard') }}" class="rk-admin-brand">
    <span class="logo"><svg width="24" height="26" viewBox="0 0 200 220" aria-hidden="true"><line x1="100" y1="14" x2="100" y2="30" stroke="#fff" stroke-width="8"/><circle cx="100" cy="12" r="9" fill="#bff0cd"/><rect x="56" y="30" width="88" height="64" rx="24" fill="#fff"/><circle cx="54" cy="58" r="14" fill="#bff0cd"/><circle cx="146" cy="58" r="14" fill="#bff0cd"/><rect x="72" y="44" width="56" height="38" rx="14" fill="#2f4f3a"/><circle cx="90" cy="62" r="6" fill="#bff0cd"/><circle cx="110" cy="62" r="6" fill="#bff0cd"/><rect x="52" y="100" width="96" height="82" rx="26" fill="#fff"/><circle cx="100" cy="132" r="9" fill="#5f9370"/><rect x="68" y="178" width="20" height="36" rx="9" fill="#fff"/><rect x="112" y="178" width="20" height="36" rx="9" fill="#fff"/></svg></span>
    <span><b>RakanKampus</b><small>Admin Console</small></span>
  </a>
  <div class="rk-admin-nav">
    @foreach($adminNavGroups as $group => $items)
      <div class="rk-admin-grp">{{ $group }}</div>
      @foreach($items as $item)
        <a href="{{ route($item['route']) }}{{ $item['hash'] ?? '' }}" class="rk-admin-link {{ $adminNavActive === $item['key'] ? 'active' : '' }}">
          <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{!! $item['icon'] !!}</svg>
          <span>{{ $item['label'] }}</span>
          @if($item['badge'] > 0)
            <span class="rk-admin-badge {{ ($item['tone'] ?? '') === 'red' ? 'red' : '' }}">{{ $item['badge'] }}</span>
          @endif
        </a>
      @endforeach
    @endforeach
  </div>
  @if($adminUnanswered > 0 && $adminNavActive !== 'unanswered')
    <a href="{{ route('admin.unanswered.index') }}" class="rk-admin-cta">
      <b>Teach the bot faster</b>
      <p>{{ $adminUnanswered }} {{ \Illuminate\Support\Str::plural('question', $adminUnanswered) }} waiting for an answer.</p>
      <span>Answer now →</span>
    </a>
  @endif
  <div class="rk-admin-me">
    <span class="av">{{ strtoupper(mb_substr($adminUser->name ?? 'A', 0, 1)) }}</span>
    <span class="who"><b>{{ $adminUser->name ?? 'Admin' }}</b><small>{{ $adminUser->email ?? '' }}</small></span>
    <form method="POST" action="{{ route('logout') }}"
          onsubmit="return RKDialog.confirmForm(event, { scene: 'signout', title: 'Log out?', message: 'You will be signed out of the admin panel.', confirmText: 'Log Out' })">
      @csrf
      <button type="submit" aria-label="Logout" title="Logout">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><path d="M16 17l5-5-5-5"></path><path d="M21 12H9"></path></svg>
      </button>
    </form>
  </div>
</nav>

<header class="rk-admin-top">
  <div class="rk-admin-crumb">Admin &nbsp;/&nbsp; @if($adminCrumbSub){{ $adminCrumb }} &nbsp;/&nbsp; <b>{{ $adminCrumbSub }}</b>@else<b>{{ $adminCrumb }}</b>@endif</div>
  <div class="rk-admin-jump" id="rkJump">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
    <input type="text" id="rkJumpInput" placeholder="Jump to a page or topic…" autocomplete="off" aria-label="Jump to a page or topic">
    <kbd>Ctrl K</kbd>
    <ul id="rkJumpList" role="listbox"></ul>
  </div>
  <a href="{{ route('admin.inbox') }}" class="rk-admin-bell" title="{{ $adminUnreadFeedback ? $adminUnreadFeedback . ' unread in inbox' : 'Inbox' }}" aria-label="Inbox">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
    @if($adminUnreadFeedback)<i></i>@endif
  </a>
</header>

<script>
(function () {
  var items = [
    @foreach($adminNavGroups as $items)
      @foreach($items as $item)
        { t: @js($item['label']), k: 'Page', u: @js(route($item['route']) . ($item['hash'] ?? '')) },
      @endforeach
    @endforeach
    @foreach($adminJump as $topic)
      { t: @js($topic->main_topic), k: 'Topic', u: @js(route('admin.information.show', $topic->id)) },
    @endforeach
  ];
  var box = document.getElementById('rkJump'), inp = document.getElementById('rkJumpInput'), list = document.getElementById('rkJumpList'), sel = 0, shown = [];
  function esc(s) { return String(s).replace(/[&<>"]/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]; }); }
  function render() {
    var q = inp.value.trim().toLowerCase();
    shown = items.filter(function (i) { return !q || i.t.toLowerCase().indexOf(q) !== -1; }).slice(0, 8);
    if (sel >= shown.length) sel = 0;
    list.innerHTML = shown.length ? shown.map(function (i, n) {
      return '<li><a href="' + esc(i.u) + '" class="' + (n === sel ? 'sel' : '') + '">' + esc(i.t) + '<small>' + i.k + '</small></a></li>';
    }).join('') : '<li class="none">Nothing found</li>';
    list.classList.add('open');
  }
  inp.addEventListener('focus', render);
  inp.addEventListener('input', function () { sel = 0; render(); });
  inp.addEventListener('keydown', function (e) {
    if (e.key === 'ArrowDown') { sel = Math.min(sel + 1, shown.length - 1); render(); e.preventDefault(); }
    else if (e.key === 'ArrowUp') { sel = Math.max(sel - 1, 0); render(); e.preventDefault(); }
    else if (e.key === 'Enter' && shown[sel]) { location.href = shown[sel].u; }
    else if (e.key === 'Escape') { inp.blur(); list.classList.remove('open'); }
  });
  document.addEventListener('click', function (e) { if (!box.contains(e.target)) list.classList.remove('open'); });
  document.addEventListener('keydown', function (e) {
    if ((e.ctrlKey || e.metaKey) && (e.key === 'k' || e.key === 'K')) { e.preventDefault(); inp.focus(); inp.select(); }
  });
})();
</script>
