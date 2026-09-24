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

  // Mobile bottom bar keeps the same 5 destinations/icons as the desktop sidebar
  // above, just reordered so Chat sits in the middle as the raised circular button.
  $tabOrder = ['home', 'reminders', 'chat', 'timetable', 'profile'];
  $tabItems = collect($tabOrder)
    ->map(fn ($key) => collect($navItems)->firstWhere('key', $key))
    ->filter()
    ->values();
@endphp
<style>
  .rk-sidebar {
    position: fixed;
    left: 0;
    top: 0;
    bottom: 0;
    width: 220px;
    background: linear-gradient(175deg, #14213d, #1b3a5c 55%, #1c4f57);
    display: none;
    flex-direction: column;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    z-index: 38;
  }
  .rk-sidebar-brand { display: flex; align-items: center; gap: 10px; padding: 22px 20px 18px; flex-shrink: 0; }
  .rk-sidebar-brand span { font-size: 15.5px; font-weight: 800; color: #fff; }
  .rk-sidebar-nav { flex: 1; min-height: 0; overflow-y: auto; padding: 8px 12px; display: flex; flex-direction: column; gap: 3px; }
  .rk-nav-link { display: flex; align-items: center; gap: 12px; padding: 11px 13px; border-radius: 10px; text-decoration: none; color: #a9c2d3; font-weight: 500; }
  .rk-nav-link svg { width: 18px; height: 18px; stroke: currentColor; flex-shrink: 0; }
  .rk-nav-link span { font-size: 13px; }
  .rk-nav-link.active { background: rgba(255,255,255,0.14); color: #fff; font-weight: 700; }
  .rk-sidebar-user { display: flex; align-items: center; gap: 10px; padding: 14px 18px; border-top: 1px solid rgba(255,255,255,0.12); flex-shrink: 0; text-decoration: none; }
  .rk-sidebar-user-avatar { width: 32px; height: 32px; border-radius: 50%; background: #2ec4c6; display: flex; align-items: center; justify-content: center; font-size: 11.5px; font-weight: 700; color: #14213d; flex-shrink: 0; overflow: hidden; }
  .rk-sidebar-user-avatar img { width: 100%; height: 100%; object-fit: cover; }
  .rk-sidebar-user-name { font-size: 12px; font-weight: 700; color: #fff; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .rk-sidebar-user-sub { font-size: 10px; color: #9fb8c9; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .rk-sidebar-logout {
    display: flex; align-items: center; gap: 10px;
    padding: 14px 18px;
    flex-shrink: 0;
    background: none; border: none; width: 100%;
    color: #fff; font-size: 13px; font-weight: 700;
    cursor: pointer; text-align: left;
  }
  .rk-sidebar-logout svg { width: 17px; height: 17px; stroke: currentColor; flex-shrink: 0; }
  .rk-sidebar-logout:hover { background: rgba(0,0,0,0.12); }

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
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
  }
  .rk-tab-link { display: flex; flex-direction: column; flex: 1; align-items: center; justify-content: center; gap: 3px; text-decoration: none; color: #94a3b8; }
  .rk-tab-link svg { width: 20px; height: 20px; stroke: currentColor; }
  .rk-tab-link span { font-size: 9.5px; font-weight: 600; }
  .rk-tab-link.active { color: #0d9488; }
  .rk-tab-link.active span { font-weight: 700; }

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
    body { margin-left: 220px; }
  }
  @media (max-width: 860px) {
    .rk-tabbar { display: flex; }
    body { padding-bottom: 100px; }
  }
</style>

<nav class="rk-sidebar">
  <div class="rk-sidebar-brand">
    <x-brand-logo size="30" />
    <span>RakanKampus</span>
  </div>
  <div class="rk-sidebar-nav">
    @foreach($navItems as $item)
      <a href="{{ route($item['route']) }}" class="rk-nav-link {{ $navActive === $item['key'] ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">{!! $item['icon'] !!}</svg>
        <span>{{ $item['label'] }}</span>
      </a>
    @endforeach
  </div>
  <a href="{{ route('student.profile') }}" class="rk-sidebar-user">
    <div class="rk-sidebar-user-avatar">
      @if($navUser && $navUser->photo)
        <img src="{{ Storage::url($navUser->photo) }}" alt="">
      @else
        {{ strtoupper(substr($navUser->first_name ?? $navUser->name ?? 'U', 0, 1) . substr($navUser->last_name ?? '', 0, 1)) }}
      @endif
    </div>
    <div style="min-width: 0;">
      <div class="rk-sidebar-user-name">{{ $navUser->name ?? 'Student' }}</div>
      <div class="rk-sidebar-user-sub">{{ $navUser->student_id ?? '' }}</div>
    </div>
  </a>
  <form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit" class="rk-sidebar-logout">
      <svg viewBox="0 0 24 24" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><path d="M16 17l5-5-5-5"></path><path d="M21 12H9"></path></svg>
      {{ __('Logout') }}
    </button>
  </form>
</nav>

<nav class="rk-tabbar">
  @foreach($tabItems as $item)
    <a href="{{ route($item['route']) }}"
       class="rk-tab-link {{ $item['key'] === 'chat' ? 'elevated' : '' }} {{ $navActive === $item['key'] ? 'active' : '' }}"
       aria-label="{{ $item['label'] }}">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{!! $item['icon'] !!}</svg>
      <span>{{ $item['label'] }}</span>
    </a>
  @endforeach
</nav>
