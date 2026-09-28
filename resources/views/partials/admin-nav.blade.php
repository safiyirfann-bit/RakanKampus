@php
  $adminNavActive = $active ?? 'dashboard';
  $adminUnanswered = $unansweredCount ?? 0;
  $adminUnreadFeedback = $unreadFeedbackCount ?? 0;
  $adminNavItems = [
    ['key' => 'dashboard', 'route' => 'admin.dashboard', 'label' => 'Dashboard', 'icon' => '<rect x="3" y="3" width="7" height="9" rx="1.5"></rect><rect x="14" y="3" width="7" height="5" rx="1.5"></rect><rect x="14" y="12" width="7" height="9" rx="1.5"></rect><rect x="3" y="16" width="7" height="5" rx="1.5"></rect>', 'badge' => 0],
    ['key' => 'database', 'route' => 'admin.database', 'label' => 'Database', 'icon' => '<ellipse cx="12" cy="5" rx="8" ry="3"></ellipse><path d="M4 5v6c0 1.66 3.58 3 8 3s8-1.34 8-3V5M4 11v6c0 1.66 3.58 3 8 3s8-1.34 8-3v-6"></path>', 'badge' => 0],
    ['key' => 'inbox', 'route' => 'admin.inbox', 'label' => 'Inbox', 'icon' => '<path d="M3 8l7.89 4.26a2 2 0 0 0 2.22 0L21 8m-2 10H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2z"></path>', 'badge' => $adminUnreadFeedback],
    ['key' => 'unanswered', 'route' => 'admin.unanswered.index', 'label' => 'Unanswered', 'icon' => '<path d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"></path>', 'badge' => $adminUnanswered],
  ];
@endphp
<style>
  .rk-admin-sidebar {
    position: fixed;
    left: 0;
    top: 0;
    bottom: 0;
    width: 220px;
    background: linear-gradient(175deg, #2f4f3a, #4a7856 55%, #5f9370);
    display: none;
    flex-direction: column;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    z-index: 38;
  }
  .rk-admin-sidebar-brand { display: flex; align-items: center; gap: 10px; padding: 22px 20px 6px; flex-shrink: 0; }
  .rk-admin-sidebar-brand span { font-size: 15.5px; font-weight: 800; color: #fff; }
  .rk-admin-sidebar-sub { padding: 0 20px 16px; font-size: 11px; color: #d7e8da; flex-shrink: 0; }
  .rk-admin-sidebar-nav { flex: 1; min-height: 0; overflow-y: auto; padding: 8px 12px; display: flex; flex-direction: column; gap: 3px; }
  .rk-admin-nav-link { display: flex; align-items: center; gap: 12px; padding: 11px 13px; border-radius: 10px; text-decoration: none; color: #d7e8da; font-weight: 500; position: relative; }
  .rk-admin-nav-link svg { width: 18px; height: 18px; stroke: currentColor; flex-shrink: 0; }
  .rk-admin-nav-link span { font-size: 13px; }
  .rk-admin-nav-link.active { background: rgba(255,255,255,0.16); color: #fff; font-weight: 700; }
  .rk-admin-nav-badge {
    margin-left: auto;
    min-width: 18px; height: 18px; padding: 0 5px;
    border-radius: 9999px;
    background: #ef4444; color: #fff;
    font-size: 10.5px; font-weight: 700;
    display: flex; align-items: center; justify-content: center;
  }
  .rk-admin-sidebar-logout {
    display: flex; align-items: center; gap: 10px;
    padding: 14px 18px;
    border-top: 1px solid rgba(255,255,255,0.16);
    flex-shrink: 0;
    background: none; border: none; width: 100%;
    color: #fff; font-size: 13px; font-weight: 700;
    cursor: pointer; text-align: left;
  }
  .rk-admin-sidebar-logout svg { width: 17px; height: 17px; stroke: currentColor; flex-shrink: 0; }
  .rk-admin-sidebar-logout:hover { background: rgba(0,0,0,0.12); }

  .rk-admin-tabbar {
    display: none;
    position: fixed;
    left: 16px; right: 16px;
    bottom: calc(14px + env(safe-area-inset-bottom, 0px));
    height: 66px;
    background: #ffffff;
    border-radius: 24px;
    align-items: center;
    justify-content: space-around;
    box-shadow: 0 14px 30px rgba(20, 61, 33, 0.2);
    z-index: 38;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
  }
  .rk-admin-tab-link { display: flex; flex-direction: column; flex: 1; align-items: center; justify-content: center; gap: 3px; text-decoration: none; color: #94a3b8; position: relative; }
  .rk-admin-tab-link svg { width: 19px; height: 19px; stroke: currentColor; }
  .rk-admin-tab-link span { font-size: 9px; font-weight: 600; }
  .rk-admin-tab-link.active { color: #4a7856; }
  .rk-admin-tab-link.active span { font-weight: 700; }
  .rk-admin-tab-badge {
    position: absolute; top: 2px; right: calc(50% - 18px);
    min-width: 15px; height: 15px; padding: 0 3px;
    border-radius: 9999px; background: #ef4444; color: #fff;
    font-size: 9px; font-weight: 700;
    display: flex; align-items: center; justify-content: center;
  }
  .rk-admin-tab-logout { color: #f0757a; }

  @media (min-width: 861px) {
    .rk-admin-sidebar { display: flex; }
    body { margin-left: 220px; }
  }
  @media (max-width: 860px) {
    .rk-admin-tabbar { display: flex; }
    body { padding-bottom: 96px; }
  }
</style>

<nav class="rk-admin-sidebar">
  <div class="rk-admin-sidebar-brand">
    <x-brand-logo size="30" />
    <span>RakanKampus</span>
  </div>
  <div class="rk-admin-sidebar-sub">Administrator</div>
  <div class="rk-admin-sidebar-nav">
    @foreach($adminNavItems as $item)
      <a href="{{ route($item['route']) }}" class="rk-admin-nav-link {{ $adminNavActive === $item['key'] ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">{!! $item['icon'] !!}</svg>
        <span>{{ $item['label'] }}</span>
        @if($item['badge'] > 0)
          <span class="rk-admin-nav-badge">{{ $item['badge'] }}</span>
        @endif
      </a>
    @endforeach
  </div>
  <form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit" class="rk-admin-sidebar-logout">
      <svg viewBox="0 0 24 24" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><path d="M16 17l5-5-5-5"></path><path d="M21 12H9"></path></svg>
      Logout
    </button>
  </form>
</nav>

<nav class="rk-admin-tabbar">
  @foreach($adminNavItems as $item)
    <a href="{{ route($item['route']) }}"
       class="rk-admin-tab-link {{ $adminNavActive === $item['key'] ? 'active' : '' }}"
       aria-label="{{ $item['label'] }}">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{!! $item['icon'] !!}</svg>
      <span>{{ $item['label'] }}</span>
      @if($item['badge'] > 0)
        <span class="rk-admin-tab-badge">{{ $item['badge'] }}</span>
      @endif
    </a>
  @endforeach
  <form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit" class="rk-admin-tab-link rk-admin-tab-logout" aria-label="Logout" style="background:none;border:none;cursor:pointer;">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><path d="M16 17l5-5-5-5"></path><path d="M21 12H9"></path></svg>
      <span>Logout</span>
    </button>
  </form>
</nav>
