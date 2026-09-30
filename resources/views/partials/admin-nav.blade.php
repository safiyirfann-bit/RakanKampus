{{--
  Admin navigation (PC only, "Bento Premium"): a slim icon rail (labels pop out on hover,
  red/dark count badges, avatar + log out at the bottom) and a top bar (page name + date,
  quick search with Ctrl K, inbox bell). Also pulls in the shared admin theme (partials/admin-theme).

  @include('partials.admin-nav', ['active' => 'dashboard'])
  Counts are looked up here when a page doesn't pass them.
--}}
@php
  $adminNavActive = $active ?? 'dashboard';
  $adminUnanswered = $unansweredCount ?? \App\Models\UnansweredQuestion::where('status', 'pending')->count();
  $adminUnreadFeedback = $unreadFeedbackCount ?? \App\Models\Feedback::where('is_read', false)->count();
  $adminTopics = \App\Models\Information::count();
  $adminUser = auth()->user();
  $adminCrumb = ['dashboard' => 'Dashboard', 'analytics' => 'Analytics', 'knowledge' => 'Knowledge base', 'unanswered' => 'Unanswered', 'inbox' => 'Inbox', ][$adminNavActive] ?? 'Dashboard';
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
  ];
@endphp
@include('partials.admin-theme')
<style>
  /* Bento Premium: slim icon rail on the left, airy top bar */
  body { margin-left: 92px !important; }
  .rk-admin-sidebar { position: fixed; left: 0; top: 0; bottom: 0; width: 92px; box-sizing: border-box; z-index: 38; display: flex; flex-direction: column; align-items: center; gap: 6px;
    padding: 22px 0 18px; background: #fff; border-right: 1px solid var(--a-line); font-family: var(--a-font); }
  .rk-admin-brand { width: 50px; height: 50px; border-radius: 17px; background: var(--a-dark); display: grid; place-items: center; margin-bottom: 18px; text-decoration: none; transition: transform .15s; }
  .rk-admin-brand:hover { transform: rotate(-6deg) scale(1.04); }
  .rk-admin-nav { display: flex; flex-direction: column; align-items: center; gap: 6px; }
  .rk-admin-sep { width: 28px; height: 1px; background: var(--a-line); margin: 8px 0; }
  .rk-admin-link { position: relative; width: 50px; height: 50px; border-radius: 17px; display: grid; place-items: center; color: #8a948d; text-decoration: none; transition: background .15s, color .15s; }
  .rk-admin-link svg { width: 22px; height: 22px; stroke: currentColor; }
  .rk-admin-link:hover { background: #f3f5f2; color: var(--a-ink); }
  .rk-admin-link.active { background: var(--a-g100); color: var(--a-g700); }
  .rk-admin-badge { position: absolute; top: 3px; left: 31px; min-width: 16px; height: 16px; padding: 0 4px; border-radius: 99px; font-size: 9.5px; font-weight: 800; line-height: 16px; text-align: center;
    background: var(--a-dark); color: #fff; box-shadow: 0 0 0 2px #fff; box-sizing: border-box; }
  .rk-admin-badge.red { background: #e24b4b; }
  /* label bubble on hover */
  .rk-admin-link::after, .rk-admin-out::after { content: attr(data-label); position: absolute; left: 62px; top: 50%; transform: translate(-6px, -50%); opacity: 0; pointer-events: none;
    background: var(--a-dark); color: #fff; font-size: 12.5px; font-weight: 700; padding: 7px 11px; border-radius: 10px; white-space: nowrap; transition: opacity .12s, transform .12s; z-index: 50; }
  .rk-admin-link:hover::after, .rk-admin-out:hover::after { opacity: 1; transform: translate(0, -50%); }
  .rk-admin-me { margin-top: auto; display: flex; flex-direction: column; align-items: center; gap: 10px; }
  .rk-admin-me .av { width: 42px; height: 42px; border-radius: 50%; background: linear-gradient(135deg, #f5c563, #e49b3b); color: #fff; display: grid; place-items: center; font-weight: 800; font-size: 15px; }
  .rk-admin-me form { margin: 0; }
  .rk-admin-out { position: relative; width: 42px; height: 42px; border-radius: 14px; border: 0; background: transparent; color: #8a948d; cursor: pointer; display: grid; place-items: center; }
  .rk-admin-out:hover { background: #fdecec; color: var(--a-red); }
  .rk-admin-out svg { width: 19px; height: 19px; }

  .rk-admin-top { position: sticky; top: 0; z-index: 30; height: 84px; box-sizing: border-box; display: flex; align-items: center; gap: 12px; padding: 0 34px;
    background: rgba(246,247,245,.82); backdrop-filter: blur(14px); -webkit-backdrop-filter: blur(14px); font-family: var(--a-font); }
  .rk-admin-crumb { font-size: 12px; color: #8a948d; font-weight: 800; letter-spacing: .06em; text-transform: uppercase; }
  .rk-admin-crumb b { color: var(--a-ink); font-weight: 800; }
  .rk-admin-crumb span { display: block; font-size: 12px; font-weight: 600; letter-spacing: 0; text-transform: none; color: #9aa39c; margin-top: 3px; }
  .rk-admin-jump { margin-left: auto; width: 340px; height: 46px; border-radius: 99px; border: 1px solid var(--a-line); background: #fff; display: flex; align-items: center; gap: 10px; padding: 0 18px; color: #9aa39c; position: relative; }
  .rk-admin-jump:focus-within { border-color: #bfe3cc; box-shadow: 0 0 0 4px rgba(63,176,112,.12); }
  .rk-admin-jump svg { width: 16px; height: 16px; flex-shrink: 0; }
  .rk-admin-jump input { border: 0; outline: 0; background: transparent; font-size: 13px; color: var(--a-ink); width: 100%; font-family: inherit; }
  .rk-admin-jump kbd { white-space: nowrap; font-family: inherit; font-size: 11px; font-weight: 800; background: #f3f5f2; border-radius: 99px; padding: 3px 9px; color: #8a948d; }
  .rk-admin-jump ul { display: none; position: absolute; left: 0; right: 0; top: 52px; list-style: none; margin: 0; padding: 8px; background: #fff; border: 1px solid var(--a-line); border-radius: 22px; box-shadow: 0 24px 50px rgba(17,28,21,.14); max-height: 340px; overflow-y: auto; }
  .rk-admin-jump ul.open { display: block; }
  .rk-admin-jump li a { display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 14px; color: var(--a-ink); font-size: 13.5px; font-weight: 600; text-decoration: none; }
  .rk-admin-jump li a small { margin-left: auto; font-size: 10.5px; color: #8a948d; font-weight: 800; text-transform: uppercase; letter-spacing: .05em; }
  .rk-admin-jump li a.sel, .rk-admin-jump li a:hover { background: var(--a-g100); color: var(--a-g800); }
  .rk-admin-jump li.none { padding: 10px; font-size: 13px; color: var(--a-mute); }
  .rk-admin-bell { width: 46px; height: 46px; border-radius: 50%; border: 1px solid var(--a-line); background: #fff; display: grid; place-items: center; color: var(--a-ink); position: relative; text-decoration: none; }
  .rk-admin-bell:hover { background: #f3f5f2; }
  .rk-admin-bell svg { width: 19px; height: 19px; }
  .rk-admin-bell i { position: absolute; top: 11px; right: 12px; width: 8px; height: 8px; border-radius: 50%; background: #e24b4b; border: 2px solid #fff; }
</style>

<nav class="rk-admin-sidebar" aria-label="Admin">
  <a href="{{ route('admin.dashboard') }}" class="rk-admin-brand" title="RakanKampus Admin">
    <svg width="26" height="28" viewBox="0 0 200 220" aria-hidden="true"><line x1="100" y1="14" x2="100" y2="30" stroke="#fff" stroke-width="8"/><circle cx="100" cy="12" r="9" fill="#7ee0b0"/><rect x="56" y="30" width="88" height="64" rx="24" fill="#fff"/><circle cx="54" cy="58" r="14" fill="#7ee0b0"/><circle cx="146" cy="58" r="14" fill="#7ee0b0"/><rect x="72" y="44" width="56" height="38" rx="14" fill="#111c15"/><circle cx="90" cy="62" r="6" fill="#7ee0b0"/><circle cx="110" cy="62" r="6" fill="#7ee0b0"/><rect x="52" y="100" width="96" height="82" rx="26" fill="#fff"/><circle cx="100" cy="132" r="9" fill="#3fb070"/><rect x="68" y="178" width="20" height="36" rx="9" fill="#fff"/><rect x="112" y="178" width="20" height="36" rx="9" fill="#fff"/></svg>
  </a>
  <div class="rk-admin-nav">
    @foreach($adminNavGroups as $group => $items)
      @if(! $loop->first)<div class="rk-admin-sep"></div>@endif
      @foreach($items as $item)
        <a href="{{ route($item['route']) }}{{ $item['hash'] ?? '' }}" class="rk-admin-link {{ $adminNavActive === $item['key'] ? 'active' : '' }}" data-label="{{ $item['label'] }}" aria-label="{{ $item['label'] }}">
          <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{!! $item['icon'] !!}</svg>
          @if($item['badge'] > 0 && $item['key'] !== 'knowledge')
            <span class="rk-admin-badge {{ ($item['tone'] ?? '') === 'red' ? 'red' : '' }}">{{ $item['badge'] }}</span>
          @endif
        </a>
      @endforeach
    @endforeach
  </div>
  <div class="rk-admin-me">
    <span class="av" title="{{ $adminUser->name ?? 'Admin' }} · {{ $adminUser->email ?? '' }}">{{ strtoupper(mb_substr($adminUser->name ?? 'A', 0, 1)) }}</span>
    <form method="POST" action="{{ route('logout') }}"
          onsubmit="return RKDialog.confirmForm(event, { scene: 'signout', title: 'Log out?', message: 'You will be signed out of the admin panel.', confirmText: 'Log Out' })">
      @csrf
      <button type="submit" class="rk-admin-out" aria-label="Log out" data-label="Log out">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><path d="M16 17l5-5-5-5"></path><path d="M21 12H9"></path></svg>
      </button>
    </form>
  </div>
</nav>

<header class="rk-admin-top">
  <div class="rk-admin-crumb">Admin &nbsp;·&nbsp; <b>{{ $adminCrumb }}</b>@if($adminCrumbSub) &nbsp;·&nbsp; {{ \Illuminate\Support\Str::limit($adminCrumbSub, 40) }}@endif
    <span>{{ now()->translatedFormat('l, j F Y') }}</span></div>
  <div class="rk-admin-jump" id="rkJump">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
    <input type="text" id="rkJumpInput" placeholder="Search pages & topics…" autocomplete="off" aria-label="Jump to a page or topic">
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
