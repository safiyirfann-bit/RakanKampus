{{--
  Admin navigation (PC only): a floating rounded left menu like the student sidebar (groups,
  labels, count badges, admin + log out at the bottom; folds into an icon rail, remembered) and a top bar (page name + date,
  quick search with Ctrl K, notification bell listing new unanswered questions + unread messages). Also pulls in the shared admin theme (partials/admin-theme).

  @include('partials.admin-nav', ['active' => 'dashboard'])
  Counts are looked up here when a page doesn't pass them.
--}}
@php
  $adminNavActive = $active ?? 'dashboard';
  $adminUnanswered = $unansweredCount ?? \App\Models\UnansweredQuestion::where('status', 'pending')->count();
  $adminUnreadFeedback = $unreadFeedbackCount ?? \App\Models\Feedback::where('is_read', false)->count();
  $adminTopics = \App\Models\Information::count();
  $adminUser = auth()->user();
  $adminCrumb = ['dashboard' => 'Dashboard', 'analytics' => 'Analytics', 'knowledge' => 'Knowledge base', 'unanswered' => 'Unanswered', 'inbox' => 'Inbox', 'users' => 'Users', ][$adminNavActive] ?? 'Dashboard';
  $adminCrumbSub = $crumb ?? null;
  $adminJump = \App\Models\Information::orderBy('main_topic')->get(['id', 'main_topic']);
  // Bell: what happened lately that needs the admin (new student questions the bot couldn't answer + unread messages)
  $adminNotes = \App\Models\UnansweredQuestion::where('status', 'pending')->orderByDesc('updated_at')->take(6)->get()
    ->map(fn ($q) => ['kind' => 'q', 'title' => $q->question, 'sub' => 'Bot couldn\'t answer · asked ' . $q->asked_count . '×', 'at' => $q->updated_at, 'url' => route('admin.unanswered.index')])
    ->concat(\App\Models\Feedback::where('is_read', false)->latest()->take(6)->get()->map(function ($f) {
        [$label, $text] = filled($f->issue_report) ? [$f->issue_type ?: 'Issue report', $f->issue_report] : (filled($f->feature_request) ? ['Feature request', $f->feature_request] : ['Feedback', $f->feedback]);
        return ['kind' => filled($f->issue_report) ? 'i' : 'f', 'title' => \Illuminate\Support\Str::limit((string) $text, 70), 'sub' => ($f->user_name ?: 'Student') . ' · ' . $label, 'at' => $f->created_at, 'url' => route('admin.inbox')];
    }))
    ->filter(fn ($n) => $n['at'])->sortByDesc(fn ($n) => $n['at']->timestamp)->take(7)->values();
  $adminNavGroups = [
    'Overview' => [
      ['key' => 'dashboard', 'route' => 'admin.dashboard', 'label' => 'Dashboard', 'icon' => '<rect x="3" y="3" width="7" height="9" rx="1.5"></rect><rect x="14" y="3" width="7" height="5" rx="1.5"></rect><rect x="14" y="12" width="7" height="9" rx="1.5"></rect><rect x="3" y="16" width="7" height="5" rx="1.5"></rect>', 'badge' => 0],
      ['key' => 'analytics', 'route' => 'admin.analytics', 'label' => 'Analytics', 'icon' => '<path d="M3 3v18h18"></path><path d="M7 16v-4"></path><path d="M12 16V8"></path><path d="M17 16v-7"></path>', 'badge' => 0],
      ['key' => 'users', 'route' => 'admin.users', 'label' => 'Users', 'icon' => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path>', 'badge' => 0],
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
  /* Left menu: the same floating rounded panel as the student sidebar (labels, groups, active
     neon bar, fold to an icon rail — remembered), in the admin green. */
  body { margin-left: 248px !important; transition: margin-left .25s ease; }
  html.rk-anav-mini body { margin-left: 100px !important; }
  .rk-admin-sidebar {
    --w: 224px; position: fixed; left: 12px; top: 12px; bottom: 12px; width: var(--w); z-index: 38; box-sizing: border-box;
    display: flex; flex-direction: column; border-radius: 24px; font-family: var(--a-font);
    background: linear-gradient(165deg, #0f2a1c, #1d4d33 60%, #2f6b49); box-shadow: 0 16px 34px rgba(15,42,28,.28); transition: width .25s ease;
  }
  html.rk-anav-mini .rk-admin-sidebar { --w: 76px; }
  .rk-admin-head { position: relative; display: flex; align-items: center; gap: 10px; padding: 20px 18px 10px; min-height: 70px; flex-shrink: 0; }
  .rk-admin-brand { width: 32px; height: 32px; flex: none; display: grid; place-items: center; text-decoration: none; transition: opacity .15s; }
  .rk-admin-head b { font-size: 15.5px; font-weight: 800; color: #fff; white-space: nowrap; }
  .rk-admin-head b small { display: block; font-size: 10.5px; font-weight: 700; color: #9fd6b6; letter-spacing: .04em; }
  .rk-admin-fold { margin-left: auto; width: 32px; height: 32px; flex: none; border: 0; border-radius: 9px; padding: 0; cursor: pointer; background: transparent; color: #a9cdb8; display: grid; place-items: center; transition: background .15s, color .15s; }
  .rk-admin-fold:hover { background: rgba(255,255,255,.1); color: #fff; }
  .rk-admin-fold svg { width: 19px; height: 19px; }
  .rk-admin-nav { flex: 1; min-height: 0; overflow-y: auto; overflow-x: hidden; padding: 4px 12px; display: flex; flex-direction: column; gap: 3px; }
  .rk-admin-label { font-size: 10px; font-weight: 800; letter-spacing: 1.3px; text-transform: uppercase; color: rgba(255,255,255,.45); margin: 14px 12px 6px; white-space: nowrap; }
  .rk-admin-link { position: relative; display: flex; align-items: center; gap: 12px; padding: 11px 13px; border-radius: 12px; text-decoration: none; color: #a9cdb8; font-weight: 600; transition: background .15s, color .15s; }
  .rk-admin-link:hover { background: rgba(255,255,255,.06); color: #fff; }
  .rk-admin-link svg { width: 19px; height: 19px; stroke: currentColor; flex-shrink: 0; }
  .rk-admin-text { font-size: 13.5px; white-space: nowrap; }
  .rk-admin-link.active { background: rgba(255,255,255,.12); color: #fff; font-weight: 700; }
  .rk-admin-link.active::before { content: ""; position: absolute; left: -12px; top: 9px; bottom: 9px; width: 4px; border-radius: 0 4px 4px 0; background: var(--a-mint); box-shadow: 0 0 12px var(--a-mint); }
  .rk-admin-link.active svg { color: var(--a-mint); }
  .rk-admin-badge { margin-left: auto; min-width: 20px; height: 20px; padding: 0 6px; border-radius: 999px; font-size: 10.5px; font-weight: 800; display: grid; place-items: center; box-sizing: border-box;
    background: var(--a-mint); color: #0f2a1c; }
  .rk-admin-badge.red { background: #f87171; color: #fff; }
  .rk-admin-badge.soft { background: rgba(255,255,255,.14); color: #d7eee0; }
  .rk-admin-foot { padding: 10px 12px 14px; flex-shrink: 0; display: flex; flex-direction: column; gap: 8px; }
  .rk-admin-me { display: flex; align-items: center; gap: 10px; padding: 8px 10px; border-radius: 14px; background: rgba(255,255,255,.07); min-width: 0; }
  .rk-admin-me .av { width: 34px; height: 34px; flex: none; border-radius: 50%; background: linear-gradient(135deg, #f5c563, #e49b3b); color: #fff; display: grid; place-items: center; font-weight: 800; font-size: 13px; }
  .rk-admin-me div { min-width: 0; }
  .rk-admin-me b { display: block; font-size: 13px; color: #fff; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .rk-admin-me small { display: block; font-size: 11px; color: #9fd6b6; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .rk-admin-foot form { margin: 0; }
  .rk-admin-out { display: flex; align-items: center; gap: 10px; width: 100%; padding: 11px 13px; border-radius: 12px; border: 0; cursor: pointer; text-align: left; white-space: nowrap;
    background: rgba(239,68,68,.14); color: #fecaca; font: 700 13px var(--a-font); transition: background .15s, color .15s; }
  .rk-admin-out svg { width: 18px; height: 18px; flex-shrink: 0; }
  .rk-admin-out:hover { background: rgba(239,68,68,.26); color: #fff; }

  /* folded: icons only, labels as tooltips, badge → dot */
  html.rk-anav-mini .rk-admin-head { justify-content: center; padding: 20px 0 10px; }
  html.rk-anav-mini .rk-admin-head b, html.rk-anav-mini .rk-admin-text, html.rk-anav-mini .rk-admin-me div { display: none; }
  html.rk-anav-mini .rk-admin-fold { position: absolute; left: 50%; top: 19px; margin-left: -16px; opacity: 0; background: rgba(255,255,255,.12); color: #fff; }
  html.rk-anav-mini .rk-admin-head:hover .rk-admin-fold, html.rk-anav-mini .rk-admin-fold:focus-visible { opacity: 1; }
  html.rk-anav-mini .rk-admin-head:hover .rk-admin-brand { opacity: 0; }
  html.rk-anav-mini .rk-admin-label { font-size: 0; height: 1px; margin: 12px 14px; background: rgba(255,255,255,.14); }
  html.rk-anav-mini .rk-admin-link, html.rk-anav-mini .rk-admin-out { justify-content: center; padding: 13px 0; }
  html.rk-anav-mini .rk-admin-me { justify-content: center; padding: 8px 0; background: none; }
  html.rk-anav-mini .rk-admin-link.active { background: var(--a-g500); box-shadow: 0 8px 18px rgba(63,176,112,.4); }
  html.rk-anav-mini .rk-admin-link.active::before { display: none; }
  html.rk-anav-mini .rk-admin-link.active svg { color: #fff; }
  html.rk-anav-mini .rk-admin-badge { position: absolute; top: 8px; right: 14px; min-width: 0; width: 9px; height: 9px; padding: 0; font-size: 0; background: #f87171; border: 2px solid #1d4d33; }
  html.rk-anav-mini .rk-admin-badge.soft { display: none; }
  html.rk-anav-mini [data-tip]:hover::after { content: attr(data-tip); position: absolute; left: calc(100% + 14px); top: 50%; transform: translateY(-50%);
    background: var(--a-dark); color: #fff; font-size: 12px; font-weight: 700; padding: 6px 11px; border-radius: 9px; white-space: nowrap; z-index: 50; pointer-events: none; }
  html.rk-anav-mini .rk-admin-out { position: relative; }

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
  .rk-admin-bell { cursor: pointer; font-family: inherit; }
  .rk-admin-bell i { position: absolute; top: 11px; right: 12px; width: 8px; height: 8px; border-radius: 50%; background: #e24b4b; border: 2px solid #fff; display: none; }
  .rk-admin-bell.has-new i { display: block; }
  .rk-admin-bell[aria-expanded="true"] { background: var(--a-dark); color: #fff; border-color: var(--a-dark); }
  .rk-notes-wrap { position: relative; }
  .rk-notes { display: none; position: absolute; right: 0; top: 56px; width: 380px; background: #fff; border: 1px solid var(--a-line); border-radius: 24px; box-shadow: 0 28px 60px rgba(17,28,21,.16); overflow: hidden; z-index: 60; }
  .rk-notes.open { display: block; animation: rkNotesIn .16s ease-out; }
  @keyframes rkNotesIn { from { opacity: 0; transform: translateY(-6px); } to { opacity: 1; transform: none; } }
  .rk-notes-h { display: flex; align-items: center; gap: 8px; padding: 18px 20px 12px; }
  .rk-notes-h b { font-size: 16px; font-weight: 800; color: var(--a-ink); }
  .rk-notes-h span { font-size: 11.5px; font-weight: 800; padding: 2px 9px; border-radius: 99px; background: #fdecec; color: #d64545; }
  .rk-notes-list { max-height: 380px; overflow-y: auto; }
  .rk-note { display: flex; gap: 12px; align-items: flex-start; padding: 12px 20px; text-decoration: none; color: inherit; position: relative; }
  .rk-note:hover { background: #fafbf9; }
  .rk-note .ic { width: 38px; height: 38px; border-radius: 13px; display: grid; place-items: center; flex-shrink: 0; }
  .rk-note .ic svg { width: 18px; height: 18px; }
  .rk-note .tx { min-width: 0; flex: 1; }
  .rk-note .tx b { display: block; font-size: 13.5px; font-weight: 700; color: var(--a-ink); line-height: 1.35; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; }
  .rk-note .tx small { display: block; font-size: 12px; color: var(--a-mute); margin-top: 3px; }
  .rk-note .tm { font-size: 11.5px; font-weight: 700; color: #9aa39c; white-space: nowrap; }
  .rk-note.new::before { content: ''; position: absolute; left: 8px; top: 26px; width: 6px; height: 6px; border-radius: 50%; background: #e24b4b; }
  .rk-notes-empty { padding: 26px 20px 30px; text-align: center; font-size: 13px; color: var(--a-mute); }
  .rk-notes-empty b { display: block; font-size: 14.5px; color: var(--a-ink); margin: 10px 0 2px; }
  .rk-notes-f { display: flex; border-top: 1px solid var(--a-line); }
  .rk-notes-f a { flex: 1; text-align: center; padding: 13px; font-size: 12.5px; font-weight: 800; color: var(--a-ink); text-decoration: none; }
  .rk-notes-f a + a { border-left: 1px solid var(--a-line); }
  .rk-notes-f a:hover { background: #fafbf9; }
</style>

<script>try { if (localStorage.getItem('rk_admin_nav_mini') === '1') document.documentElement.classList.add('rk-anav-mini'); } catch (e) {}</script>
<nav class="rk-admin-sidebar" aria-label="Admin">
  <div class="rk-admin-head">
    <a href="{{ route('admin.dashboard') }}" class="rk-admin-brand" title="RakanKampus Admin">
      <svg width="30" height="32" viewBox="0 0 200 220" aria-hidden="true"><line x1="100" y1="14" x2="100" y2="30" stroke="#fff" stroke-width="8"/><circle cx="100" cy="12" r="9" fill="#7ee0b0"/><rect x="56" y="30" width="88" height="64" rx="24" fill="#fff"/><circle cx="54" cy="58" r="14" fill="#7ee0b0"/><circle cx="146" cy="58" r="14" fill="#7ee0b0"/><rect x="72" y="44" width="56" height="38" rx="14" fill="#0f2a1c"/><circle cx="90" cy="62" r="6" fill="#7ee0b0"/><circle cx="110" cy="62" r="6" fill="#7ee0b0"/><rect x="52" y="100" width="96" height="82" rx="26" fill="#fff"/><circle cx="100" cy="132" r="9" fill="#3fb070"/><rect x="68" y="178" width="20" height="36" rx="9" fill="#fff"/><rect x="112" y="178" width="20" height="36" rx="9" fill="#fff"/></svg>
    </a>
    <b>RakanKampus<small>ADMIN</small></b>
    <button type="button" class="rk-admin-fold" id="rkAdminFold" aria-label="Collapse menu" title="Collapse menu">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="16" rx="3"/><path d="M9 4v16"/></svg>
    </button>
  </div>
  <div class="rk-admin-nav">
    @foreach($adminNavGroups as $group => $items)
      <p class="rk-admin-label">{{ $group }}</p>
      @foreach($items as $item)
        <a href="{{ route($item['route']) }}{{ $item['hash'] ?? '' }}" class="rk-admin-link {{ $adminNavActive === $item['key'] ? 'active' : '' }}" data-tip="{{ $item['label'] }}" aria-label="{{ $item['label'] }}">
          <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{!! $item['icon'] !!}</svg>
          <span class="rk-admin-text">{{ $item['label'] }}</span>
          @if($item['badge'] > 0)
            <span class="rk-admin-badge {{ $item['tone'] ?? '' }}">{{ $item['badge'] }}</span>
          @endif
        </a>
      @endforeach
    @endforeach
  </div>
  <div class="rk-admin-foot">
    <div class="rk-admin-me" title="{{ $adminUser->name ?? 'Admin' }} · {{ $adminUser->email ?? '' }}">
      <span class="av">{{ strtoupper(mb_substr($adminUser->name ?? 'A', 0, 1)) }}</span>
      <div><b>{{ $adminUser->name ?? 'Admin' }}</b><small>{{ $adminUser->email ?? '' }}</small></div>
    </div>
    <form method="POST" action="{{ route('logout') }}"
          onsubmit="return RKDialog.confirmForm(event, { scene: 'signout', title: 'Log out?', message: 'You will be signed out of the admin panel.', confirmText: 'Log Out' })">
      @csrf
      <button type="submit" class="rk-admin-out" aria-label="Log out" data-tip="Log out">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><path d="M16 17l5-5-5-5"></path><path d="M21 12H9"></path></svg>
        <span class="rk-admin-text">Log out</span>
      </button>
    </form>
  </div>
</nav>
<script>
(function () {
  var btn = document.getElementById('rkAdminFold');
  if (!btn) return;
  function sync() {
    var mini = document.documentElement.classList.contains('rk-anav-mini');
    btn.setAttribute('aria-label', mini ? 'Expand menu' : 'Collapse menu'); btn.title = mini ? 'Expand menu' : 'Collapse menu';
  }
  btn.addEventListener('click', function () {
    var mini = document.documentElement.classList.toggle('rk-anav-mini');
    try { localStorage.setItem('rk_admin_nav_mini', mini ? '1' : '0'); } catch (e) {}
    sync();
  });
  sync();
})();
</script>

<header class="rk-admin-top">
  <div class="rk-admin-crumb">Admin &nbsp;·&nbsp; <b>{{ $adminCrumb }}</b>@if($adminCrumbSub) &nbsp;·&nbsp; {{ \Illuminate\Support\Str::limit($adminCrumbSub, 40) }}@endif
    <span>{{ now()->translatedFormat('l, j F Y') }}</span></div>
  <div class="rk-admin-jump" id="rkJump">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
    <input type="text" id="rkJumpInput" placeholder="Search pages & topics…" autocomplete="off" aria-label="Jump to a page or topic">
    <kbd>Ctrl K</kbd>
    <ul id="rkJumpList" role="listbox"></ul>
  </div>
  <div class="rk-notes-wrap" id="rkNotesWrap">
    <button type="button" class="rk-admin-bell" id="rkBell" aria-label="Notifications" aria-expanded="false" aria-controls="rkNotes"
            data-latest="{{ optional($adminNotes->first())['at']?->timestamp ?? 0 }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
      <i></i>
    </button>
    <div class="rk-notes" id="rkNotes" role="dialog" aria-label="Notifications">
      <div class="rk-notes-h"><b>Notifications</b>@if($adminNotes->count())<span>{{ $adminUnanswered + $adminUnreadFeedback }} pending</span>@endif</div>
      <div class="rk-notes-list">
        @forelse($adminNotes as $n)
          <a class="rk-note" href="{{ $n['url'] }}" data-at="{{ $n['at']->timestamp }}">
            @if($n['kind'] === 'q')
              <span class="ic t-amber"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M9.5 9a2.5 2.5 0 1 1 3.5 2.3c-.7.3-1 1-1 1.7M12 17h.01"/></svg></span>
            @elseif($n['kind'] === 'i')
              <span class="ic t-red"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3 2 20h20L12 3z"/><path d="M12 10v4M12 17h.01"/></svg></span>
            @else
              <span class="ic t-blue"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="3"/><path d="m3 8 9 6 9-6"/></svg></span>
            @endif
            <span class="tx"><b>{{ $n['title'] }}</b><small>{{ $n['sub'] }}</small></span>
            <span class="tm">{{ $n['at']->diffForHumans(null, true, true) }}</span>
          </a>
        @empty
          <div class="rk-notes-empty">
            <span class="ic t-green" style="width:48px;height:48px;border-radius:16px;display:inline-grid;place-items:center"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg></span>
            <b>You're all caught up</b>No new questions or messages from students.
          </div>
        @endforelse
      </div>
      <div class="rk-notes-f">
        <a href="{{ route('admin.unanswered.index') }}">Unanswered{{ $adminUnanswered ? ' (' . $adminUnanswered . ')' : '' }}</a>
        <a href="{{ route('admin.inbox') }}">Inbox{{ $adminUnreadFeedback ? ' (' . $adminUnreadFeedback . ')' : '' }}</a>
      </div>
    </div>
  </div>
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

  // Bell: red dot until the admin has opened the list since the newest item arrived (remembered in this browser)
  var bell = document.getElementById('rkBell'), notes = document.getElementById('rkNotes'), wrap = document.getElementById('rkNotesWrap');
  var seen = 0; try { seen = +localStorage.getItem('rkAdminNotesSeen') || 0; } catch (e) {}
  var latest = +bell.dataset.latest || 0;
  bell.classList.toggle('has-new', latest > seen);
  notes.querySelectorAll('.rk-note').forEach(function (n) { n.classList.toggle('new', +n.dataset.at > seen); });
  function setOpen(open) {
    notes.classList.toggle('open', open);
    bell.setAttribute('aria-expanded', open ? 'true' : 'false');
    if (open && latest) {
      bell.classList.remove('has-new');
      try { localStorage.setItem('rkAdminNotesSeen', String(latest)); } catch (e) {}
    }
  }
  bell.addEventListener('click', function (e) { e.stopPropagation(); setOpen(!notes.classList.contains('open')); });
  document.addEventListener('click', function (e) { if (!wrap.contains(e.target)) setOpen(false); });
  document.addEventListener('keydown', function (e) { if (e.key === 'Escape') setOpen(false); });
})();
</script>
