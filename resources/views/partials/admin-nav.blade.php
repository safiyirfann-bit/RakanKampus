{{--
  Admin sidebar (PC only) — "Green Hero" look: white sidebar, grouped menu with
  count badges, admin card + logout at the bottom. Also pulls in the shared admin
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
  $adminNavGroups = [
    'Overview' => [
      ['key' => 'dashboard', 'route' => 'admin.dashboard', 'label' => 'Dashboard', 'icon' => '<rect x="3" y="3" width="7" height="9" rx="1.5"></rect><rect x="14" y="3" width="7" height="5" rx="1.5"></rect><rect x="14" y="12" width="7" height="9" rx="1.5"></rect><rect x="3" y="16" width="7" height="5" rx="1.5"></rect>', 'badge' => 0],
      ['key' => 'analytics', 'route' => 'admin.analytics', 'label' => 'Analytics', 'icon' => '<path d="M3 3v18h18"></path><path d="M7 16v-4"></path><path d="M12 16V8"></path><path d="M17 16v-7"></path>', 'badge' => 0],
    ],
    'Chatbot' => [
      ['key' => 'knowledge', 'route' => 'admin.dashboard', 'hash' => '#topics', 'label' => 'Knowledge base', 'icon' => '<path d="M4 19.5V5a2 2 0 0 1 2-2h13v16H6.5A2.5 2.5 0 0 0 4 21.5"></path><path d="M8 7h7M8 11h5"></path>', 'badge' => $adminTopics, 'tone' => 'soft'],
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
  .rk-admin-sidebar {
    position: fixed; left: 0; top: 0; bottom: 0; width: 248px; z-index: 38;
    display: flex; flex-direction: column; padding: 18px 14px;
    background: #ffffff; border-right: 1px solid #e3ece5;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
  }
  .rk-admin-brand { display: flex; align-items: center; gap: 11px; padding: 4px 8px 14px; text-decoration: none; }
  .rk-admin-brand .logo { width: 40px; height: 40px; border-radius: 13px; background: #ecfdf5; display: grid; place-items: center; box-shadow: inset 0 0 0 1px #d7e8da; }
  .rk-admin-brand b { display: block; font-size: 16px; color: #1f2937; }
  .rk-admin-brand small { display: block; font-size: 10.5px; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; color: #6b8f76; margin-top: 1px; }
  .rk-admin-nav { flex: 1; min-height: 0; overflow-y: auto; }
  .rk-admin-grp { font-size: 10.5px; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; color: #6b8f76; margin: 14px 12px 6px; }
  .rk-admin-link {
    display: flex; align-items: center; gap: 11px; padding: 10px 12px; border-radius: 12px; margin-bottom: 2px;
    color: #475569; font-size: 14px; font-weight: 600; text-decoration: none; transition: background .15s, color .15s;
  }
  .rk-admin-link svg { width: 18px; height: 18px; stroke: currentColor; flex-shrink: 0; }
  .rk-admin-link:hover { background: #f1f7f3; color: #2f4f3a; }
  .rk-admin-link.active { background: linear-gradient(120deg, #3f7a52, #5f9370); color: #fff; box-shadow: 0 8px 18px rgba(63, 122, 82, .25); }
  .rk-admin-badge { margin-left: auto; min-width: 22px; height: 20px; padding: 0 7px; border-radius: 99px; font-size: 11px; font-weight: 800; display: flex; align-items: center; justify-content: center; }
  .rk-admin-badge.soft { background: #ecfdf5; color: #3f7a52; }
  .rk-admin-badge.red { background: #fee2e2; color: #dc2626; }
  .rk-admin-link.active .rk-admin-badge { background: rgba(255,255,255,.25); color: #fff; }
  .rk-admin-me { display: flex; align-items: center; gap: 10px; padding: 11px 12px; border-radius: 16px; background: #f4f8f5; margin-top: 10px; }
  .rk-admin-me .av { width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg, #3f7a52, #5f9370); color: #fff; display: grid; place-items: center; font-weight: 800; flex-shrink: 0; }
  .rk-admin-me .who { min-width: 0; }
  .rk-admin-me b { display: block; font-size: 13px; color: #1f2937; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .rk-admin-me small { display: block; font-size: 11px; color: #64748b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .rk-admin-me form { margin-left: auto; }
  .rk-admin-me button { border: 0; background: #fff; width: 34px; height: 34px; border-radius: 10px; color: #dc2626; cursor: pointer; display: grid; place-items: center; box-shadow: 0 1px 2px rgba(0,0,0,.06); }
  .rk-admin-me button:hover { background: #fee2e2; }
  .rk-admin-me button svg { width: 17px; height: 17px; }
  body { margin-left: 248px !important; }
</style>

<nav class="rk-admin-sidebar" aria-label="Admin">
  <a href="{{ route('admin.dashboard') }}" class="rk-admin-brand">
    <span class="logo"><x-brand-logo size="30" /></span>
    <span><b>RakanKampus</b><small>Admin panel</small></span>
  </a>
  <div class="rk-admin-nav">
    @foreach($adminNavGroups as $group => $items)
      <div class="rk-admin-grp">{{ $group }}</div>
      @foreach($items as $item)
        <a href="{{ route($item['route']) }}{{ $item['hash'] ?? '' }}" class="rk-admin-link {{ $adminNavActive === $item['key'] ? 'active' : '' }}">
          <svg viewBox="0 0 24 24" fill="none" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round">{!! $item['icon'] !!}</svg>
          <span>{{ $item['label'] }}</span>
          @if($item['badge'] > 0)
            <span class="rk-admin-badge {{ $item['tone'] ?? 'soft' }}">{{ $item['badge'] }}</span>
          @endif
        </a>
      @endforeach
    @endforeach
  </div>
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
