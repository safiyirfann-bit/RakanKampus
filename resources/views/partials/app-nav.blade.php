@php
  $navActive = $active ?? 'home';
  $navUser = $user ?? auth()->user();
  $navItems = [
    ['key' => 'home', 'route' => 'student.home', 'label' => __('Home'), 'icon' => '<path d="M3 11.5 12 4l9 7.5"></path><path d="M5 10v9a1 1 0 0 0 1 1h4v-6h4v6h4a1 1 0 0 0 1-1v-9"></path>'],
    ['key' => 'chat', 'route' => 'student.chat', 'label' => __('Chat'), 'icon' => '<path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>'],
    ['key' => 'reminders', 'route' => 'student.reminders', 'label' => __('Reminders'), 'icon' => '<path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.7 21a2 2 0 0 1-3.4 0"></path>'],
    ['key' => 'timetable', 'route' => 'student.timetable', 'label' => __('Timetable'), 'icon' => '<rect x="3" y="4" width="18" height="17" rx="2"></rect><path d="M3 9h18"></path><path d="M8 3v4"></path><path d="M16 3v4"></path>'],
    ['key' => 'profile', 'route' => 'student.profile', 'label' => __('Profile'), 'icon' => '<circle cx="12" cy="8" r="4"></circle><path d="M4 20c0-4 4-6 8-6s8 2 8 6"></path>'],
  ];

  // Reminders due in the next 7 days → badge on the sidebar (red dot when collapsed)
  $navDueSoon = $navUser ? $navUser->reminders()->whereBetween('due_at', [now(), now()->addDays(7)])->count() : 0;

  // Mobile bottom bar keeps the same 5 destinations/icons as the desktop sidebar
  // above, just reordered so Chat sits in the middle as the raised circular button.
  $tabOrder = ['home', 'reminders', 'chat', 'timetable', 'profile'];
  $tabItems = collect($tabOrder)
    ->map(fn ($key) => collect($navItems)->firstWhere('key', $key))
    ->filter()
    ->values();
@endphp
<style>
  /* Desktop sidebar: floating rounded panel that can fold into an icon rail (state remembered) */
  .rk-sidebar {
    --rk-w: 224px;
    position: fixed; left: 12px; top: 12px; bottom: 12px; width: var(--rk-w);
    background: linear-gradient(165deg, #0f2747, #134a63 60%, #155e75);
    border-radius: 24px; box-shadow: 0 16px 34px rgba(15, 39, 71, .28);
    display: none; flex-direction: column;
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    z-index: 38; transition: width .25s ease;
  }
  html.rk-nav-mini .rk-sidebar { --rk-w: 76px; }
  .rk-sidebar-brand { display: flex; align-items: center; gap: 10px; padding: 20px 18px 10px; flex-shrink: 0; min-height: 70px; }
  .rk-sidebar-brand svg { flex: none; }
  .rk-sidebar-brand span { font-size: 15.5px; font-weight: 800; color: #fff; white-space: nowrap; }
  /* Fold button: a small panel icon inside the header (ChatGPT style). When folded, the logo turns into it on hover. */
  .rk-nav-toggle {
    margin-left: auto; width: 32px; height: 32px; flex: none; border: 0; border-radius: 9px; padding: 0; cursor: pointer;
    background: transparent; color: #a9c2d3; display: grid; place-items: center; transition: background .15s, color .15s;
  }
  .rk-nav-toggle:hover { background: rgba(255,255,255,.1); color: #fff; }
  .rk-nav-toggle svg { width: 19px; height: 19px; }
  .rk-brand-logo { position: relative; width: 32px; height: 32px; flex: none; display: grid; place-items: center; }
  html.rk-nav-mini .rk-sidebar-brand { position: relative; }
  html.rk-nav-mini .rk-nav-toggle { position: absolute; left: 50%; top: 19px; margin-left: -16px; opacity: 0; background: rgba(255,255,255,.12); color: #fff; }
  html.rk-nav-mini .rk-sidebar-brand:hover .rk-nav-toggle, html.rk-nav-mini .rk-nav-toggle:focus-visible { opacity: 1; }
  html.rk-nav-mini .rk-sidebar-brand:hover .rk-brand-logo { opacity: 0; }
  .rk-sidebar-nav { flex: 1; min-height: 0; overflow-y: auto; overflow-x: hidden; padding: 4px 12px; display: flex; flex-direction: column; gap: 3px; }
  .rk-nav-label { font-size: 10px; font-weight: 800; letter-spacing: 1.3px; color: rgba(255,255,255,.45); margin: 14px 12px 6px; white-space: nowrap; }
  .rk-nav-link {
    position: relative; display: flex; align-items: center; gap: 12px; padding: 11px 13px; border-radius: 12px;
    text-decoration: none; color: #a9c2d3; font-weight: 600; transition: background .15s, color .15s;
  }
  .rk-nav-link:hover { background: rgba(255,255,255,.06); color: #fff; }
  .rk-nav-link svg { width: 19px; height: 19px; stroke: currentColor; flex-shrink: 0; }
  .rk-nav-link > span.rk-nav-text { font-size: 13.5px; white-space: nowrap; }
  .rk-nav-link.active { background: rgba(255,255,255,.12); color: #fff; font-weight: 700; }
  .rk-nav-link.active::before {
    content: ""; position: absolute; left: -12px; top: 9px; bottom: 9px; width: 4px; border-radius: 0 4px 4px 0;
    background: #2dd4bf; box-shadow: 0 0 12px #2dd4bf;
  }
  .rk-nav-link.active svg { color: #5eead4; }
  .rk-nav-badge { margin-left: auto; min-width: 20px; height: 20px; padding: 0 6px; border-radius: 999px; background: #2dd4bf; color: #053b37; font-size: 10.5px; font-weight: 800; display: grid; place-items: center; }
  .rk-nav-avatar {
    width: 20px; height: 20px; border-radius: 50%; flex-shrink: 0; overflow: hidden;
    display: flex; align-items: center; justify-content: center;
    background: #2ec4c6; color: #14213d; font-size: 8.5px; font-weight: 800;
  }
  .rk-nav-avatar img { width: 100%; height: 100%; object-fit: cover; }
  .rk-nav-link.active .rk-nav-avatar { box-shadow: 0 0 0 2px #5eead4; }
  .rk-sidebar-foot { padding: 10px 12px 14px; flex-shrink: 0; }
  .rk-sidebar-logout {
    display: flex; align-items: center; gap: 10px; width: 100%; padding: 11px 13px; border-radius: 12px;
    background: rgba(239, 68, 68, .14); border: 0; color: #fecaca; font-size: 13px; font-weight: 700;
    cursor: pointer; text-align: left; font-family: inherit; white-space: nowrap; transition: background .15s;
  }
  .rk-sidebar-logout svg { width: 18px; height: 18px; stroke: currentColor; flex-shrink: 0; }
  .rk-sidebar-logout:hover { background: rgba(239, 68, 68, .26); color: #fff; }

  /* folded: icons only, labels as hover tooltips, badge becomes a red dot */
  html.rk-nav-mini .rk-sidebar-brand { padding: 20px 0 10px; justify-content: center; }
  html.rk-nav-mini .rk-sidebar-brand > span:not(.rk-brand-logo),
  html.rk-nav-mini .rk-nav-text { display: none; }
  html.rk-nav-mini .rk-nav-label { font-size: 0; height: 1px; margin: 12px 14px; background: rgba(255,255,255,.14); }
  html.rk-nav-mini .rk-nav-link, html.rk-nav-mini .rk-sidebar-logout { justify-content: center; padding: 13px 0; }
  html.rk-nav-mini .rk-nav-link.active { background: #14b8a6; box-shadow: 0 8px 18px rgba(20,184,166,.4); }
  html.rk-nav-mini .rk-nav-link.active::before { display: none; }
  html.rk-nav-mini .rk-nav-link.active svg { color: #fff; }
  html.rk-nav-mini .rk-nav-badge { position: absolute; top: 8px; right: 14px; min-width: 0; width: 9px; height: 9px; padding: 0; font-size: 0; background: #f43f5e; border: 2px solid #134a63; }
  html.rk-nav-mini [data-tip]:hover::after {
    content: attr(data-tip); position: absolute; left: calc(100% + 14px); top: 50%; transform: translateY(-50%);
    background: #0f2747; color: #fff; font-size: 12px; font-weight: 700; padding: 6px 11px; border-radius: 9px; white-space: nowrap;
    box-shadow: 0 8px 18px rgba(15,39,71,.3); z-index: 5; pointer-events: none;
  }
  html.rk-nav-mini .rk-sidebar-nav { overflow: visible; }

  .rk-tabbar {
    display: none;
    position: fixed;
    left: 16px; right: 16px;
    bottom: calc(14px + env(safe-area-inset-bottom, 0px));
    height: 70px;
    background: #ffffff;
    border-radius: 26px;
    align-items: center;
    justify-content: space-around;
    box-shadow: 0 14px 30px rgba(20, 33, 61, 0.2);
    z-index: 38;
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
  }
  .rk-tab-link { display: flex; flex-direction: column; flex: 1; align-items: center; justify-content: center; gap: 3px; text-decoration: none; color: #94a3b8; }
  .rk-tab-link svg { width: 20px; height: 20px; stroke: currentColor; }
  .rk-tab-link span { font-size: 9.5px; font-weight: 600; }
  .rk-tab-link.active { color: #0d9488; }
  .rk-tab-link.active span { font-weight: 700; }
  .rk-tab-avatar {
    width: 20px; height: 20px; border-radius: 50%; flex-shrink: 0; overflow: hidden;
    display: flex; align-items: center; justify-content: center;
    background: #2ec4c6; color: #14213d; font-size: 9px; font-weight: 700;
  }
  .rk-tab-avatar img { width: 100%; height: 100%; object-fit: cover; }
  .rk-tab-link.active .rk-tab-avatar { box-shadow: 0 0 0 2px #0d9488; }

  .rk-tab-link.elevated {
    flex: 0 0 auto;
    position: relative;
    top: -22px;
    width: 52px; height: 52px;
    border-radius: 50%;
    background: linear-gradient(120deg, #14213d, #2ec4c6);
    box-shadow: 0 8px 18px rgba(20,33,61,0.42);
    color: #fff;
  }
  .rk-tab-link.elevated svg { width: 23px; height: 23px; }
  .rk-tab-link.elevated span { display: none; }
  .rk-tab-link.elevated.active { color: #fff; }

  @media (min-width: 861px) {
    .rk-sidebar { display: flex; }
    body { margin-left: 248px; transition: margin-left .25s ease; }
    html.rk-nav-mini body { margin-left: 100px; }
  }
  @media (max-width: 860px) {
    .rk-tabbar { display: flex; }
    body { padding-bottom: 100px; }
  }
</style>
<style id="rk-dark-theme">
/* Dark theme — generated by scripts/dark-theme/generate.py, do not edit by hand.
   Hand-made fixes go in resources/views/partials/dark-fixes.blade.php */
html[data-theme="dark"] .rk-nav-link { color: #cbd5dc; }
html[data-theme="dark"] .rk-nav-avatar { color: #dee1e9; }
html[data-theme="dark"] .rk-sidebar { background: linear-gradient(165deg, #0b1626, #0f3446 60%, #0f4a55); box-shadow: 0 16px 34px rgba(0,0,0,.5); }
html[data-theme="dark"] .rk-tabbar { background: #17202d; box-shadow: 0 14px 30px rgba(0, 0, 0, 0.41); }
html[data-theme="dark"] .rk-tab-link { color: #ced3d9; }
html[data-theme="dark"] .rk-tab-link.active { color: #41eedf; }
html[data-theme="dark"] .rk-tab-avatar { color: #dee1e9; }
html[data-theme="dark"] .rk-tab-link.active .rk-tab-avatar { box-shadow: 0 0 0 2px #000000; }
html[data-theme="dark"] .rk-tab-link.elevated { box-shadow: 0 8px 18px rgba(0, 0, 0, 0.81); }
</style>

<script>try { if (localStorage.getItem('rk_nav_mini') === '1') document.documentElement.classList.add('rk-nav-mini'); } catch (e) {}</script>
<nav class="rk-sidebar" aria-label="{{ __('Main menu') }}">
  <div class="rk-sidebar-brand">
    <span class="rk-brand-logo"><x-brand-logo size="32" /></span>
    <span>RakanKampus</span>
    <button type="button" class="rk-nav-toggle" id="rkNavToggle" aria-label="{{ __('Collapse menu') }}" title="{{ __('Collapse menu') }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="16" rx="3"/><path d="M9 4v16"/></svg>
    </button>
  </div>
  <div class="rk-sidebar-nav">
    @foreach($navItems as $item)
      @if($item['key'] === 'home')<p class="rk-nav-label">{{ __('MENU') }}</p>@endif
      @if($item['key'] === 'profile')<p class="rk-nav-label">{{ __('ACCOUNT') }}</p>@endif
      <a href="{{ route($item['route']) }}" class="rk-nav-link {{ $navActive === $item['key'] ? 'active' : '' }}" data-tip="{{ $item['label'] }}">
        @if($item['key'] === 'profile')
          <span class="rk-nav-avatar">
            @if($navUser && $navUser->photo_data)
              <img src="{{ $navUser->photo_data }}" alt="">
            @else
              {{ strtoupper(substr($navUser->first_name ?? $navUser->name ?? 'U', 0, 1) . substr($navUser->last_name ?? '', 0, 1)) }}
            @endif
          </span>
        @else
          <svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">{!! $item['icon'] !!}</svg>
        @endif
        <span class="rk-nav-text">{{ $item['label'] }}</span>
        @if($item['key'] === 'reminders' && $navDueSoon > 0)
          <span class="rk-nav-badge" title="{{ __(':n due this week', ['n' => $navDueSoon]) }}">{{ $navDueSoon }}</span>
        @endif
      </a>
    @endforeach
  </div>
  <form method="POST" action="{{ route('logout') }}" class="rk-sidebar-foot"
        onsubmit="return RKDialog.confirmForm(event, { scene: 'signout', title: @js(__('Log out?')), message: @js(__('Are you sure you want to sign out of your RakanKampus account?')), confirmText: @js(__('Log Out')) })">
    @csrf
    <button type="submit" class="rk-sidebar-logout" data-tip="{{ __('Logout') }}">
      <svg viewBox="0 0 24 24" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><path d="M16 17l5-5-5-5"></path><path d="M21 12H9"></path></svg>
      <span class="rk-nav-text">{{ __('Logout') }}</span>
    </button>
  </form>
</nav>
<script>
(function () {
  var btn = document.getElementById('rkNavToggle');
  if (!btn) return;
  var labels = [@js(__('Collapse menu')), @js(__('Expand menu'))];
  function sync() {
    var mini = document.documentElement.classList.contains('rk-nav-mini');
    btn.setAttribute('aria-label', labels[mini ? 1 : 0]); btn.title = labels[mini ? 1 : 0];
  }
  btn.addEventListener('click', function () {
    var mini = document.documentElement.classList.toggle('rk-nav-mini');
    try { localStorage.setItem('rk_nav_mini', mini ? '1' : '0'); } catch (e) {}
    sync();
  });
  sync();
})();
</script>

<nav class="rk-tabbar">
  @foreach($tabItems as $item)
    <a href="{{ route($item['route']) }}"
       class="rk-tab-link {{ $item['key'] === 'chat' ? 'elevated' : '' }} {{ $navActive === $item['key'] ? 'active' : '' }}"
       aria-label="{{ $item['label'] }}">
      @if($item['key'] === 'profile')
        <span class="rk-tab-avatar">
          @if($navUser && $navUser->photo_data)
            <img src="{{ $navUser->photo_data }}" alt="">
          @else
            {{ strtoupper(substr($navUser->first_name ?? $navUser->name ?? 'U', 0, 1) . substr($navUser->last_name ?? '', 0, 1)) }}
          @endif
        </span>
      @else
        <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{!! $item['icon'] !!}</svg>
      @endif
      <span>{{ $item['label'] }}</span>
    </a>
  @endforeach
</nav>
