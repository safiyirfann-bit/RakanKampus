<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
@include('partials.font')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('partials.pwa-head')
    @include('partials.profile-bg')
    <title>{{ __('Profile & Settings') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        indigo: {
                            50: '#e6fbfa',
                            100: '#dbeeee',
                            200: '#b8e6e6',
                            300: '#94a3b8',
                            400: '#64748b',
                            500: '#0d9488',
                            600: '#0d9488',
                            700: '#0f766e',
                            900: '#14213d',
                        },
                    },
                },
            },
        };
    </script>
    <style>
        html, body { overflow-x: hidden; }
        body {
            background: linear-gradient(120deg, #14213d, #1b3a5c, #2ec4c6, #14213d);
    background-size: 300% 300%;
    animation: gradientShift 15s ease infinite;
        }
        .bg-blob {
            position: fixed;
            border-radius: 9999px;
            filter: blur(50px);
            opacity: 0.5;
            z-index: 0;
            pointer-events: none;
            animation: blobFloat 16s ease-in-out infinite;
        }
        .bg-blob.b1 { width: 260px; height: 260px; top: -70px; left: -80px; background: rgba(255,255,255,0.28); animation-duration: 17s; }
        .bg-blob.b2 { width: 220px; height: 220px; top: 35%; right: -90px; background: rgba(255,255,255,0.20); animation-duration: 20s; animation-delay: -4s; }
        .bg-blob.b3 { width: 200px; height: 200px; bottom: -60px; left: 15%; background: rgba(255,255,255,0.24); animation-duration: 15s; animation-delay: -8s; }
        @keyframes blobFloat {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(18px, -26px) scale(1.06); }
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(14px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-up { animation: fadeInUp 0.5s ease both; }
        /* Menu icons: each in a soft tile coloured by category */
        .menu-icon { width: 38px; height: 38px; border-radius: 12px; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .menu-icon svg { width: 19px; height: 19px; }
        .mi-edit   { color: #0d9488; background: #e6fbfa; }
        .mi-lock   { color: #2563eb; background: #e8f0fe; }
        .mi-bell   { color: #d97706; background: #fef3e2; }
        .mi-lang   { color: #7c3aed; background: #f1ebfe; }
        .mi-moon   { color: #4f46e5; background: #ecebfd; }
        .mi-shield { color: #059669; background: #e3f7ee; }
        .mi-chat   { color: #0891b2; background: #e2f6fa; }
        .mi-help   { color: #db2777; background: #fce8f2; }
        .mi-info   { color: #475569; background: #eef1f5; }
        .mi-logout { color: #dc2626; background: #fdecec; }
        .menu-chevron { color: #cbd5e1; }
        .menu-chevron svg { width: 18px; height: 18px; display: block; }
        .menu-row .menu-icon { transition: transform 0.15s ease; }
        .menu-row:hover .menu-icon { transform: scale(1.06); }
        .menu-row { transition: background-color 0.15s ease, transform 0.15s ease; }
        .menu-row:hover { transform: translateX(2px); }
        .menu-row .chevron { transition: transform 0.15s ease; display: inline-block; }
        .menu-row:hover .chevron { transform: translateX(3px); }
        header, .relative, .max-w-md { position: relative; z-index: 1; }

        .profile-header {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 20px 20px 18px;
            position: relative;
            z-index: 1;
        }
        .profile-header .back-btn {
            width: 38px; height: 38px; min-width: 38px; border-radius: 50%;
            background: rgba(255,255,255,0.12);
            display: flex; align-items: center; justify-content: center;
            color: #fff; text-decoration: none; transition: background 0.15s ease;
        }
        .profile-header .back-btn:hover { background: rgba(255,255,255,0.2); }
        .profile-header .back-btn svg { width: 18px; height: 18px; stroke: currentColor; }
        .profile-header h1 { font-size: 18px; font-weight: 800; color: #fff; margin: 0; }
        .profile-header p { font-size: 12.5px; color: #bfe9ea; margin: 2px 0 0; }

  @keyframes gradientShift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
  }

  @media (min-width: 861px) {
    body {
        background: #f0fafa;
    }
    .profile-header {
        background: rgba(255,255,255,0.9);
        backdrop-filter: blur(8px);
        border-bottom: 1px solid #dbeeee;
        padding: 16px 24px;
    }
    .profile-header .back-btn { background: transparent; color: #0d9488; }
    .profile-header .back-btn:hover { background: #eafbfa; }
    .profile-header h1 { color: #14213d; }
    .profile-header p { color: #64748b; }
  }
</style>
<style>
/* Profile & Settings, "glass": the profile card and menus are see-through glass on the
   gradient, white text, light-teal labels; the icon of the row you hover/press glows neon.
   Same look as Edit Profile. On PC the gradient is a rounded panel (light page there). */
html body .pg-wrap .pg-card { background: rgba(255,255,255,.14) !important; border: 1px solid rgba(255,255,255,.35) !important;
  -webkit-backdrop-filter: blur(14px); backdrop-filter: blur(14px); box-shadow: inset 0 1px 0 rgba(255,255,255,.22) !important; }
html body .pg-wrap .pg-av { background: linear-gradient(135deg, #0d9488, #2ec4c6); border: 3px solid rgba(255,255,255,.7); box-shadow: 0 8px 20px rgba(0,0,0,.25); width: 62px; height: 62px; flex: none; }
html body .pg-wrap .text-indigo-900 { color: #fff !important; }
html body .pg-wrap .pg-hero .text-indigo-500 { color: #e0fbf8 !important; }
html body .pg-wrap .text-indigo-400 { color: #c9f3f1 !important; }
html body .pg-wrap p.text-indigo-500 { color: #b9fbf5 !important; letter-spacing: .1em; }
html body .pg-wrap .pg-hero span.bg-indigo-50 { background: rgba(127,245,236,.2) !important; color: #b9fbf5 !important; border: 1px solid rgba(127,245,236,.4); }
html body .pg-wrap .divide-indigo-50 > * + *, html body .pg-wrap .border-indigo-100 { border-color: rgba(255,255,255,.14) !important; }
html body .pg-wrap .menu-row:hover, html body .pg-wrap .menu-row:focus-visible { background: rgba(255,255,255,.1) !important; outline: none; }
html body .pg-wrap .menu-icon { background: rgba(255,255,255,.18) !important; color: #fff !important; transition: background .2s, color .2s, box-shadow .2s, transform .15s; }
html body .pg-wrap .menu-row:hover .menu-icon, html body .pg-wrap .menu-row:active .menu-icon, html body .pg-wrap .menu-row:focus-visible .menu-icon {
  background: #7ff5ec !important; color: #0f2747 !important; box-shadow: 0 0 16px rgba(127,245,236,.6); }
html body .pg-wrap .menu-chevron { color: rgba(255,255,255,.55) !important; }
html body .pg-wrap .menu-icon.mi-logout { background: rgba(248,113,113,.25) !important; color: #fecaca !important; }
html body .pg-wrap .menu-row:hover .menu-icon.mi-logout { background: #fca5a5 !important; color: #7f1d1d !important; box-shadow: 0 0 16px rgba(248,113,113,.5); }
html body .pg-wrap .text-red-600 { color: #fecaca !important; font-weight: 700; }
@media (min-width: 861px) {
  html body .pg-wrap { max-width: 36rem; margin-top: 28px; margin-bottom: 48px; padding: 26px; border-radius: 28px; overflow: hidden;
    background: linear-gradient(160deg, #14213d 0%, #1b3a5c 35%, #21768a 70%, #2ec4c6 100%); box-shadow: 0 20px 50px rgba(15,39,71,.22); }
  html[data-theme="dark"] body .pg-wrap { background: linear-gradient(160deg, #0c1320 0%, #112031 40%, #17505a 75%, #1d6869 100%); box-shadow: 0 20px 50px rgba(0,0,0,.45); }
}
</style>
<style>
/* PC profile (≥861px): cover + tabs layout (.pfw). Phones keep the glass list (.pg-wrap cards). */
.pfw { display: none; }
@media (min-width: 861px) {
  .pfw { display: block; position: relative; z-index: 1; max-width: 1100px; padding: 6px 40px 48px; }
  html body .pg-wrap { max-width: none !important; margin: 0 !important; padding: 0 !important; background: none !important; box-shadow: none !important; border-radius: 0 !important; overflow: visible !important; }
  .pg-wrap > .pg-card { display: none !important; }

  /* header like Reminders / Timetable: no white bar */
  html body .profile-header { background: transparent !important; border: 0 !important; backdrop-filter: none !important; padding: 32px 40px 16px !important; }
  html body .profile-header h1 { color: #14213d !important; font-size: 22px !important; }
  html body .profile-header p { color: #0d9488 !important; font-size: 13px !important; }
  html[data-theme="dark"] body .profile-header h1 { color: #dee1e9 !important; }
  html[data-theme="dark"] body .profile-header p { color: #41eedf !important; }

  .pfw-cover { position: relative; height: 230px; border-radius: 24px; overflow: hidden; background: linear-gradient(135deg, #14213d, #2ec4c6); box-shadow: 0 14px 34px rgba(15,39,71,.16); }
  .pfw-cover img, .pfw-cover .pfw-art { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; display: block; }
  .pfw-cover [hidden] { display: none; }
  .pfw-cover::after { content: ""; position: absolute; inset: 0; pointer-events: none; background: linear-gradient(180deg, rgba(0,0,0,0) 55%, rgba(0,0,0,.18)); }
  .pfw-cover.busy::before { content: ""; position: absolute; z-index: 3; inset: 0; background: rgba(15,39,71,.45) url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 50 50'%3E%3Ccircle cx='25' cy='25' r='18' fill='none' stroke='%23fff' stroke-width='4' stroke-dasharray='80 40'%3E%3CanimateTransform attributeName='transform' type='rotate' from='0 25 25' to='360 25 25' dur='.9s' repeatCount='indefinite'/%3E%3C/circle%3E%3C/svg%3E") center / 44px no-repeat; }
  .pfw-cover-actions { position: absolute; z-index: 2; top: 14px; right: 14px; display: flex; gap: 8px; }
  .pfw-cbtn { display: inline-flex; align-items: center; gap: 7px; height: 36px; padding: 0 14px; border-radius: 12px; cursor: pointer; border: 1px solid rgba(255,255,255,.4);
    background: rgba(15,39,71,.35); -webkit-backdrop-filter: blur(10px); backdrop-filter: blur(10px); color: #fff; font: 700 12.5px 'Plus Jakarta Sans', sans-serif; transition: background .15s; }
  .pfw-cbtn:hover { background: rgba(15,39,71,.55); }
  .pfw-cbtn svg { width: 16px; height: 16px; }
  .pfw-cbtn-icon { width: 36px; padding: 0; justify-content: center; }
  .pfw-cbtn-icon:hover { background: rgba(220,38,38,.6); }

  .pfw-who { position: relative; display: flex; align-items: flex-end; gap: 18px; padding: 0 22px; margin-top: -54px; margin-bottom: 22px; z-index: 2; }
  .pfw-av { width: 112px; height: 112px; flex: none; border-radius: 50%; overflow: hidden; display: grid; place-items: center; text-decoration: none;
    background: linear-gradient(135deg, #0d9488, #2ec4c6); color: #fff; font-size: 38px; font-weight: 800; border: 5px solid #f0fafa; box-shadow: 0 10px 26px rgba(15,39,71,.25); transition: transform .2s; }
  .pfw-av:hover { transform: scale(1.03); }
  .pfw-av img { width: 100%; height: 100%; object-fit: cover; }
  .pfw-name { flex: 1; min-width: 0; padding-bottom: 6px; }
  .pfw-name b { display: block; font-size: 22px; font-weight: 800; color: #14213d; text-transform: capitalize; }
  .pfw-name span { display: block; font-size: 13px; color: #64748b; margin-top: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .pfw-acts { display: flex; gap: 8px; padding-bottom: 8px; flex: none; }
  .pfw-btn { display: inline-flex; align-items: center; gap: 7px; height: 40px; padding: 0 16px; border-radius: 12px; border: 0; cursor: pointer; text-decoration: none;
    font: 800 13px 'Plus Jakarta Sans', sans-serif; transition: transform .15s, box-shadow .2s; }
  .pfw-btn svg { width: 16px; height: 16px; }
  .pfw-btn-main { background: linear-gradient(90deg, #14213d, #2ec4c6); color: #fff; box-shadow: 0 8px 18px rgba(46,196,198,.3); }
  .pfw-btn-main:hover { box-shadow: 0 10px 24px rgba(46,196,198,.45); }
  .pfw-btn-out { background: #fdecec; color: #dc2626; }
  .pfw-btn-out:hover { background: #fbd5d5; }
  .pfw-btn:active { transform: scale(.97); }

  .pfw-tabs { display: flex; gap: 4px; border-bottom: 1.5px solid #dbeeee; margin-bottom: 16px; }
  .pfw-tabs button { border: 0; background: none; cursor: pointer; padding: 10px 16px; font: 700 14px 'Plus Jakarta Sans', sans-serif; color: #64748b; border-radius: 10px 10px 0 0; }
  .pfw-tabs button:hover { color: #14213d; background: rgba(46,196,198,.06); }
  .pfw-tabs button.on { color: #0f766e; box-shadow: inset 0 -3px 0 #2ec4c6; }
  .pfw-pane { display: none; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px; animation: pfwIn .25s ease; }
  .pfw-pane.on { display: grid; }
  @keyframes pfwIn { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: none; } }
  .pfw-card { display: flex; align-items: center; gap: 14px; padding: 16px; border-radius: 18px; text-decoration: none; color: #14213d;
    background: #fff; border: 1px solid #e3eef0; box-shadow: 0 6px 16px rgba(15,39,71,.06); transition: transform .15s, box-shadow .2s, border-color .2s; }
  .pfw-card:hover { transform: translateY(-2px); border-color: #2ec4c6; box-shadow: 0 0 0 1px #2ec4c6, 0 12px 26px rgba(46,196,198,.2); }
  .pfw-ic { width: 44px; height: 44px; flex: none; border-radius: 13px; display: grid; place-items: center; color: #fff; background: linear-gradient(135deg, #14213d, #2ec4c6); }
  .pfw-ic svg { width: 20px; height: 20px; }
  .pfw-card b { display: block; font-size: 14px; font-weight: 800; }
  .pfw-card small { display: block; font-size: 12px; color: #64748b; margin-top: 2px; line-height: 1.35; }

  html[data-theme="dark"] .pfw-av { border-color: #10161f; }
  html[data-theme="dark"] .pfw-name b { color: #dee1e9; }
  html[data-theme="dark"] .pfw-name span, html[data-theme="dark"] .pfw-card small { color: #94a3b8; }
  html[data-theme="dark"] .pfw-tabs { border-color: #283648; }
  html[data-theme="dark"] .pfw-tabs button { color: #94a3b8; }
  html[data-theme="dark"] .pfw-tabs button:hover { color: #dee1e9; }
  html[data-theme="dark"] .pfw-tabs button.on { color: #41eedf; }
  html[data-theme="dark"] .pfw-card { background: #17202d; border-color: #283648; color: #dee1e9; box-shadow: none; }
  html[data-theme="dark"] .pfw-btn-out { background: #391c1c; color: #ef9e9e; }
}
@media (min-width: 861px) and (max-width: 1100px) { .pfw-pane { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
@media (prefers-reduced-motion: reduce) { .pfw-pane { animation: none; } }
</style>
<style id="rk-dark-theme">
/* Dark theme — generated by scripts/dark-theme/generate.py, do not edit by hand.
   Hand-made fixes go in resources/views/partials/dark-fixes.blade.php */
html[data-theme="dark"] body { background-image: linear-gradient(120deg, #0c1320, #112031, #1d6869, #0c1320); }
html[data-theme="dark"] .mi-edit { color: #41eedf; background: #1d3d3b; }
html[data-theme="dark"] .mi-lock { color: #98b5f6; background: #1c273b; }
html[data-theme="dark"] .mi-bell { color: #fbbc73; background: #3d311d; }
html[data-theme="dark"] .mi-lang { color: #bb98f6; background: #251c39; }
html[data-theme="dark"] .mi-moon { color: #a19df1; background: #1d1c3a; }
html[data-theme="dark"] .mi-shield { color: #32f8bb; background: #1f3f31; }
html[data-theme="dark"] .mi-chat { color: #52d7f7; background: #1e393f; }
html[data-theme="dark"] .mi-help { color: #ef9fc2; background: #3b1d2c; }
html[data-theme="dark"] .mi-info { color: #d0d4da; background: #1d2a3c; }
html[data-theme="dark"] .mi-logout { color: #ef9e9e; background: #391c1c; }
@media (min-width: 861px) {
  html[data-theme="dark"] body { background: #10161f; }
  html[data-theme="dark"] .profile-header { background: rgba(23, 32, 45, 0.9); border-bottom: 1px solid #284848; }
  html[data-theme="dark"] .profile-header .back-btn { color: #41eedf; }
  html[data-theme="dark"] .profile-header .back-btn:hover { background: #1c3b39; }
  html[data-theme="dark"] .profile-header h1 { color: #dee1e9; }
  html[data-theme="dark"] .profile-header p { color: #b0b6be; }
}
</style>
</head>

<body class="min-h-screen">

@include('partials.app-nav', ['active' => 'profile', 'user' => $user])

<div class="bg-blob b1"></div>
<div class="bg-blob b2"></div>
<div class="bg-blob b3"></div>

    <!-- Header -->
<div class="profile-header">


    <div>
        <h1>{{ __('Profile & Settings') }}</h1>
        <p>{{ __('Manage your student account') }}</p>
    </div>

</div>


{{-- PC layout: cover picture (uploadable) + avatar + tabs of setting cards. Phones keep the list below. --}}
@php
    $pfwDept = \App\Support\MatricNumber::department($user->student_id);
    $pfwInitials = strtoupper(substr($user->first_name ?? 'A', 0, 1) . substr($user->last_name ?? '', 0, 1));
@endphp
<div class="pfw">
    <div class="pfw-cover" id="pfwCover">
        <img id="pfwCoverImg" alt="{{ __('Cover picture') }}" @if($user->cover_data) src="{{ $user->cover_data }}" @else hidden @endif>
        <svg class="pfw-art" id="pfwCoverArt" viewBox="0 0 1000 220" preserveAspectRatio="xMidYMid slice" aria-hidden="true" @if($user->cover_data) hidden @endif>
            <defs>
                <linearGradient id="pfwSky" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#14213d"/><stop offset=".55" stop-color="#1b5e7a"/><stop offset="1" stop-color="#2ec4c6"/></linearGradient>
                <radialGradient id="pfwSun"><stop offset="0" stop-color="#fef3c7"/><stop offset="1" stop-color="#fde68a" stop-opacity="0"/></radialGradient>
            </defs>
            <rect width="1000" height="220" fill="url(#pfwSky)"/>
            <circle cx="860" cy="62" r="70" fill="url(#pfwSun)" opacity=".5"/><circle cx="860" cy="62" r="26" fill="#fde68a" opacity=".95"/>
            <g fill="#fff" opacity=".18"><ellipse cx="200" cy="60" rx="60" ry="14"/><ellipse cx="240" cy="50" rx="40" ry="12"/><ellipse cx="620" cy="40" rx="50" ry="11"/></g>
            <g fill="#0f2747" opacity=".55">
                <rect x="300" y="128" width="130" height="92"/><rect x="440" y="96" width="120" height="124"/><rect x="570" y="120" width="90" height="100"/><rect x="670" y="140" width="110" height="80"/>
                <polygon points="440,96 500,70 560,96"/>
            </g>
            <g fill="#7ff5ec" opacity=".55">
                <rect x="456" y="112" width="14" height="10" rx="2"/><rect x="483" y="112" width="14" height="10" rx="2"/><rect x="510" y="112" width="14" height="10" rx="2"/><rect x="537" y="112" width="14" height="10" rx="2"/>
                <rect x="456" y="138" width="14" height="10" rx="2"/><rect x="510" y="138" width="14" height="10" rx="2"/><rect x="537" y="138" width="14" height="10" rx="2"/>
                <rect x="318" y="146" width="14" height="10" rx="2"/><rect x="345" y="146" width="14" height="10" rx="2"/><rect x="398" y="146" width="14" height="10" rx="2"/>
                <rect x="586" y="138" width="14" height="10" rx="2"/><rect x="630" y="138" width="14" height="10" rx="2"/><rect x="690" y="158" width="14" height="10" rx="2"/><rect x="740" y="158" width="14" height="10" rx="2"/>
            </g>
            <rect x="0" y="212" width="1000" height="8" fill="#0f2747" opacity=".4"/>
            <text x="975" y="200" text-anchor="end" fill="#fff" opacity=".75" font-size="15" font-weight="800" font-family="Plus Jakarta Sans, sans-serif" letter-spacing="1">{{ $pfwDept ? $pfwDept . ' · ' : '' }}POLITEKNIK UNGKU OMAR</text>
        </svg>
        <div class="pfw-cover-actions">
            <label class="pfw-cbtn" id="pfwCoverBtn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 8h3l2-3h6l2 3h3a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V9a1 1 0 0 1 1-1z"/><circle cx="12" cy="13" r="3.5"/></svg><span>{{ __('Change cover') }}</span>
                <input type="file" accept="image/jpeg,image/png,image/webp" id="pfwCoverInput" hidden>
            </label>
            <button type="button" class="pfw-cbtn pfw-cbtn-icon" id="pfwCoverRemove" onclick="removeCover()" aria-label="{{ __('Remove cover') }}" title="{{ __('Remove cover') }}" @if(! $user->cover_data) hidden @endif><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="M19 6l-1 14H6L5 6"/></svg></button>
        </div>
    </div>

    <div class="pfw-who">
        <a href="{{ route('student.profile.edit') }}" class="pfw-av" aria-label="{{ __('Edit Profile') }}">
            @if($user->photo_data)
                <img src="{{ $user->photo_data }}" alt="{{ __('Profile photo') }}">
            @else
                {{ $pfwInitials }}
            @endif
        </a>
        <div class="pfw-name">
            <b>{{ trim($user->first_name . ' ' . $user->last_name) }}</b>
            <span>{{ collect([$user->student_id, $user->email, $user->programme])->filter()->implode(' · ') }}</span>
        </div>
        <div class="pfw-acts">
            <a href="{{ route('student.profile.edit') }}" class="pfw-btn pfw-btn-main"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg> {{ __('Edit Profile') }}</a>
            <button type="button" class="pfw-btn pfw-btn-out" onclick="openLogoutModal()"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="m16 17 5-5-5-5"/><path d="M21 12H9"/></svg> {{ __('Log Out') }}</button>
        </div>
    </div>

    <div class="pfw-tabs" role="tablist">
        <button type="button" class="on" data-tab="account" onclick="pfwTab('account')">{{ __('Account') }}</button>
        <button type="button" data-tab="prefs" onclick="pfwTab('prefs')">{{ __('Preferences') }}</button>
        <button type="button" data-tab="support" onclick="pfwTab('support')">{{ __('Support') }}</button>
    </div>
        <div class="pfw-pane on" data-pane="account">
            <a href="{{ route('student.profile.password') }}" class="pfw-card"><span class="pfw-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="11" width="16" height="10" rx="2.5"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/><circle cx="12" cy="16" r="1.2" fill="currentColor"/></svg></span><span><b>{{ __('Change Password') }}</b><small>{{ __('Keep your account safe') }}</small></span></a>
            <a href="{{ route('student.profile.notifications') }}" class="pfw-card"><span class="pfw-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.9 1.9 0 0 0 3.4 0"/></svg></span><span><b>{{ __('Notification Settings') }}</b><small>{{ __('Classes, reminders and do not disturb') }}</small></span></a>
            <a href="{{ route('student.profile.language') }}" class="pfw-card"><span class="pfw-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M3 12h18"/><path d="M12 3a14 14 0 0 1 0 18a14 14 0 0 1 0-18"/></svg></span><span><b>{{ __('Language') }}</b><small>{{ __('English, Melayu, 中文, தமிழ்') }}</small></span></a>
            <a href="{{ route('student.profile.appearance') }}" class="pfw-card"><span class="pfw-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8z"/></svg></span><span><b>{{ __('Appearance') }}</b><small>{{ __('Light, dark or device setting') }}</small></span></a>
        </div>
        <div class="pfw-pane" data-pane="prefs">
            <a href="{{ route('student.profile.privacy-security') }}" class="pfw-card"><span class="pfw-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-3.5 8-10V5l-8-3-8 3v7c0 6.5 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg></span><span><b>{{ __('Privacy & Security') }}</b><small>{{ __('Devices, sign-in history and your data') }}</small></span></a>
            <a href="#" onclick="openFeedbackModal(); return false;" class="pfw-card"><span class="pfw-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/><path d="M8 9h8M8 13h5"/></svg></span><span><b>{{ __('Feedback & feature requests') }}</b><small>{{ __('Tell us what we can improve') }}</small></span></a>
        </div>
        <div class="pfw-pane" data-pane="support">
            <a href="{{ route('student.help-support') }}" class="pfw-card"><span class="pfw-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M9.1 9a3 3 0 0 1 5.8 1c0 2-3 3-3 3"/><circle cx="12" cy="17" r=".8" fill="currentColor"/></svg></span><span><b>{{ __('Help & Support') }}</b><small>{{ __('Get assistance and contact support') }}</small></span></a>
            <a href="{{ route('student.about') }}" class="pfw-card"><span class="pfw-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 11v5"/><circle cx="12" cy="8" r=".8" fill="currentColor"/></svg></span><span><b>{{ __('About RakanKampus') }}</b><small>{{ __('App version and project information') }}</small></span></a>
        </div>
</div>

<script>
function pfwTab(key) {
    document.querySelectorAll('.pfw-tabs [data-tab]').forEach(b => b.classList.toggle('on', b.dataset.tab === key));
    document.querySelectorAll('.pfw-pane').forEach(p => p.classList.toggle('on', p.dataset.pane === key));
    try { localStorage.setItem('rk_profile_tab', key); } catch (e) {}
}
try { const k = localStorage.getItem('rk_profile_tab'); if (k && document.querySelector(`.pfw-pane[data-pane="${k}"]`)) pfwTab(k); } catch (e) {}

// Cover picture: upload (server resizes to 1600px JPEG) or remove → back to the campus artwork.
(function () {
    const input = document.getElementById('pfwCoverInput');
    if (!input) return;
    const csrf = @js(csrf_token());
    input.addEventListener('change', async () => {
        const file = input.files && input.files[0];
        input.value = '';
        if (!file) return;
        if (file.size > 8 * 1024 * 1024) return pfwOops(t('The image is too large (maximum 8MB).'));
        const cover = document.getElementById('pfwCover');
        cover.classList.add('busy');
        const fd = new FormData();
        fd.append('cover', file);
        try {
            const res = await fetch(@js(route('profile.cover.upload')), { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }, body: fd });
            const data = await res.json().catch(() => null);
            if (!res.ok || !data || !data.success) throw new Error((data && (data.message || (data.errors && Object.values(data.errors)[0][0]))) || 'fail');
            setCover(data.coverUrl);
        } catch (e) {
            pfwOops(e.message && e.message !== 'fail' ? e.message : t('Could not upload the cover. Please try again.'));
        } finally {
            cover.classList.remove('busy');
        }
    });
})();

function setCover(url) {
    const img = document.getElementById('pfwCoverImg');
    // toggleAttribute (not .hidden) so it also works on the <svg> artwork
    if (url) img.src = url; else img.removeAttribute('src');
    img.toggleAttribute('hidden', !url);
    document.getElementById('pfwCoverArt').toggleAttribute('hidden', !!url);
    document.getElementById('pfwCoverRemove').toggleAttribute('hidden', !url);
}

async function removeCover() {
    const ok = await RKDialog.confirm({
        scene: 'trash',
        title: t('Remove cover picture?'),
        message: t('Your profile will go back to the default campus picture.'),
        confirmText: t('Remove'),
    });
    if (!ok) return;
    const csrf = @js(csrf_token());
    const res = await fetch(@js(route('profile.cover.remove')), { method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' } });
    if (res.ok) setCover(null); else pfwOops(t('Something went wrong. Please try again.'));
}

function pfwOops(message) {
    if (window.RKDialog && RKDialog.alert) RKDialog.alert({ scene: 'oops', title: t('Oops!'), message });
}
</script>

    <div class="max-w-md mx-auto p-5 space-y-4 relative z-10 pg-wrap">

<!-- Profile Card -->
<div class="bg-white rounded-3xl border border-indigo-100 p-5 flex items-center gap-4 shadow-sm fade-up pg-card pg-hero">

    <div class="w-14 h-14 rounded-full text-white flex items-center justify-center font-bold text-lg overflow-hidden pg-av">
        @if($user->photo_data)
            <img src="{{ $user->photo_data }}" class="w-full h-full object-cover" alt="{{ __('Profile photo') }}">
        @else
            {{ strtoupper(substr($user->first_name ?? 'A', 0, 1) . substr($user->last_name ?? '', 0, 1)) }}
        @endif
    </div>

    <div class="flex-1">
    <div class="font-bold text-indigo-900">{{ $user->first_name }} {{ $user->last_name }}</div>
    @if($user->student_id)
        <div class="text-sm text-indigo-500">{{ $user->student_id }}</div>
    @endif
    <div class="text-xs text-indigo-400">{{ $user->email }}</div>

    @if($user->programme)
        <span class="inline-block mt-2 text-[11px] font-semibold text-indigo-600 bg-indigo-50 rounded-full px-3 py-1">
            {{ $user->programme }}
        </span>
    @endif
</div>

</div>

        <!-- Account -->
        <div class="bg-white rounded-3xl border border-indigo-100 overflow-hidden shadow-sm fade-up pg-card" style="animation-delay: 0.08s;">

    <div class="px-5 pt-4 pb-2">
        <p class="text-[11px] font-bold tracking-wider uppercase text-indigo-500">{{ __('Account') }}</p>
    </div>

    <div class="divide-y divide-indigo-50">

        <a href="{{ route('student.profile.edit') }}" class="menu-row flex items-center justify-between px-5 py-4 hover:bg-indigo-50">
            <div class="flex items-center gap-4">
                <span class="menu-icon mi-edit" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg></span>
                <span class="font-medium text-indigo-900">{{ __('Edit Profile') }}</span>
            </div>
            <span class="chevron menu-chevron" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 6 6 6-6 6"/></svg></span>
        </a>

        <a href="{{ route('student.profile.password') }}" class="menu-row flex items-center justify-between px-5 py-4 hover:bg-indigo-50">
            <div class="flex items-center gap-4">
                <span class="menu-icon mi-lock" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="11" width="16" height="10" rx="2.5"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/><circle cx="12" cy="16" r="1.2" fill="currentColor"/></svg></span>
                <span class="font-medium text-indigo-900">{{ __('Change Password') }}</span>
            </div>
            <span class="chevron menu-chevron" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 6 6 6-6 6"/></svg></span>
        </a>

        <a href="{{ route('student.profile.notifications') }}" class="menu-row flex items-center justify-between px-5 py-4 hover:bg-indigo-50">
            <div class="flex items-center gap-4">
                <span class="menu-icon mi-bell" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.9 1.9 0 0 0 3.4 0"/></svg></span>
                <span class="font-medium text-indigo-900">{{ __('Notification Settings') }}</span>
            </div>
            <span class="chevron menu-chevron" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 6 6 6-6 6"/></svg></span>
        </a>

        <a href="{{ route('student.profile.language') }}" class="menu-row flex items-center justify-between px-5 py-4 hover:bg-indigo-50">
            <div class="flex items-center gap-4">
                <span class="menu-icon mi-lang" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M3 12h18"/><path d="M12 3a14 14 0 0 1 0 18a14 14 0 0 1 0-18"/></svg></span>
                <span class="font-medium text-indigo-900">{{ __('Language') }}</span>
            </div>
            <span class="chevron menu-chevron" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 6 6 6-6 6"/></svg></span>
        </a>

        <a href="{{ route('student.profile.appearance') }}" class="menu-row flex items-center justify-between px-5 py-4 hover:bg-indigo-50">
            <div class="flex items-center gap-4">
                <span class="menu-icon mi-moon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8z"/></svg></span>
                <span class="font-medium text-indigo-900">{{ __('Appearance') }}</span>
            </div>
            <span class="chevron menu-chevron" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 6 6 6-6 6"/></svg></span>
        </a>

    </div>
</div>

<!-- Preferences -->
<div class="bg-white rounded-3xl border border-indigo-100 overflow-hidden shadow-sm fade-up pg-card" style="animation-delay: 0.16s;">

    <div class="px-5 pt-4 pb-2">
        <p class="text-[11px] font-bold tracking-wider uppercase text-indigo-500">{{ __('Preferences') }}</p>
    </div>

    <div class="divide-y divide-indigo-50">

        
        <!-- Privacy & Security -->
        <a href="{{ route('student.profile.privacy-security') }}"
           class="menu-row flex items-center justify-between px-5 py-4 hover:bg-indigo-50">

            <div class="flex items-center gap-4">

                <span class="menu-icon mi-shield" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-3.5 8-10V5l-8-3-8 3v7c0 6.5 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg></span>

                <div>
                    <p class="font-medium text-indigo-900">{{ __('Privacy & Security') }}</p>
                    <p class="text-sm text-indigo-400">{{ __('Devices, sign-in history and your data') }}</p>
                </div>

            </div>

            <span class="chevron menu-chevron" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 6 6 6-6 6"/></svg></span>

        </a>

       <button onclick="openFeedbackModal()"
    class="menu-row w-full flex items-center justify-between px-5 py-4 hover:bg-indigo-50 text-left border-t border-indigo-100">

    <div class="flex items-center gap-4">

        <span class="menu-icon mi-chat" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/><path d="M8 9h8M8 13h5"/></svg></span>

        <div>
            <p class="font-medium text-indigo-900">{{ __('Feedback & feature requests') }}</p>
            <p class="text-sm text-indigo-400">{{ __('Tell us what we can improve') }}</p>
        </div>

    </div>

    <span class="chevron menu-chevron" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 6 6 6-6 6"/></svg></span>

</button>

        <!-- Help -->
        <a href="{{ route('student.help-support') }}"
           class="menu-row flex items-center justify-between px-5 py-4 hover:bg-indigo-50">

            <div class="flex items-center gap-4">

                <span class="menu-icon mi-help" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M9.1 9a3 3 0 0 1 5.8 1c0 2-3 3-3 3"/><circle cx="12" cy="17" r=".8" fill="currentColor"/></svg></span>

                <div>
    <p class="font-medium text-indigo-900">{{ __('Help & Support') }}</p>
    <p class="text-sm text-indigo-400">{{ __('Get assistance and contact support') }}</p>
</div>

            </div>

            <span class="chevron menu-chevron" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 6 6 6-6 6"/></svg></span>

        </a>

        <!-- About -->
        <a href="{{ route('student.about') }}"
           class="menu-row flex items-center justify-between px-5 py-4 hover:bg-indigo-50">

            <div class="flex items-center gap-4">

                <span class="menu-icon mi-info" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 11v5"/><circle cx="12" cy="8" r=".8" fill="currentColor"/></svg></span>

               <div>
    <p class="font-medium text-indigo-900">{{ __('About RakanKampus') }}</p>
    <p class="text-sm text-indigo-400">{{ __('App version and project information') }}</p>
</div>
            </div>

            <span class="chevron menu-chevron" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 6 6 6-6 6"/></svg></span>

        </a>

    </div>

</div>

        <!-- Logout Button -->
<div class="bg-white rounded-3xl border border-indigo-100 overflow-hidden shadow-sm fade-up pg-card" style="animation-delay: 0.22s;">

    <button type="button"
            onclick="openLogoutModal()"
            class="menu-row w-full flex items-center justify-between px-5 py-4 hover:bg-indigo-50 text-left">

        <div class="flex items-center gap-4">

            <span class="menu-icon mi-logout" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="m16 17 5-5-5-5"/><path d="M21 12H9"/></svg></span>

            <div>
                <p class="font-medium text-red-600">{{ __('Log Out') }}</p>
                <p class="text-xs text-indigo-400 mt-1">{{ __('Sign out of your RakanKampus account') }}</p>
            </div>

        </div>

        <span class="chevron menu-chevron" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 6 6 6-6 6"/></svg></span>

    </button>

</div>

<!-- Log out: RakanKampus popup (partials/rk-dialog) — the robot waves goodbye and the phone locks -->
<form id="logoutForm" method="POST" action="{{ route('logout') }}" class="hidden">@csrf</form>
<script>
async function openLogoutModal() {
    const ok = await RKDialog.confirm({
        scene: 'signout',
        title: @js(__('Log out?')),
        message: @js(__('Are you sure you want to sign out of your RakanKampus account?')),
        confirmText: @js(__('Log Out')),
    });
    if (ok) document.getElementById('logoutForm').submit();
}
</script>

<!-- Feedback Modal -->
<div id="feedbackModal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50 p-4">
    <div class="bg-white w-full max-w-lg rounded-3xl border border-indigo-100 shadow-2xl animate-scale-in">

        <form method="POST" action="{{ route('student.feedback.store') }}">
            @csrf

            <!-- Header -->
            <div class="flex items-start justify-between p-6 border-b border-indigo-100">
                <div>
                    <h2 class="text-2xl font-bold text-indigo-900">{{ __('Help us improve') }}</h2>
                    <p class="text-sm text-indigo-400 mt-1">
                        {{ __('Share feedback or suggest a feature for RakanKampus.') }}
                    </p>
                </div>
                <button type="button" onclick="closeFeedbackModal()"
                    class="w-10 h-10 rounded-full hover:bg-indigo-50 flex items-center justify-center text-indigo-400 hover:text-indigo-600 transition">
                    ✕
                </button>
            </div>

            <!-- Content -->
            <div class="p-6 space-y-5">
                <!-- Feedback -->
                <div>
                    <label class="block text-sm font-semibold text-indigo-700 mb-2">
                        {{ __('Feedback') }}
                    </label>
                    <textarea name="feedback" id="feedbackText" rows="5"
                        maxlength="4000"
                        placeholder="{{ __('What could we do better?') }}"
                        oninput="updateCounter('feedbackText', 'feedbackCount')"
                        class="w-full rounded-2xl border border-indigo-100 bg-indigo-50/50 px-4 py-4 text-indigo-900 placeholder-indigo-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none resize-none"></textarea>
                    <p class="text-right text-xs text-indigo-300 mt-2"><span id="feedbackCount">0</span>/4000</p>
                </div>

                <!-- Feature Request -->
                <div>
                    <label class="block text-sm font-semibold text-indigo-700 mb-2">
                        {{ __('Feature request') }}
                    </label>
                    <textarea name="feature_request" id="featureText" rows="5"
                        maxlength="4000"
                        placeholder="{{ __('What would you like us to add?') }}"
                        oninput="updateCounter('featureText', 'featureCount')"
                        class="w-full rounded-2xl border border-indigo-100 bg-indigo-50/50 px-4 py-4 text-indigo-900 placeholder-indigo-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none resize-none"></textarea>
                    <p class="text-right text-xs text-indigo-300 mt-2"><span id="featureCount">0</span>/4000</p>
                </div>
            </div>

            <!-- Footer -->
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-indigo-100">
                <button type="button" onclick="closeFeedbackModal()"
                    class="px-5 py-2.5 rounded-xl border border-indigo-200 text-indigo-600 hover:bg-indigo-50 transition font-medium">
                    {{ __('Cancel') }}
                </button>
                <button type="submit"
                    class="px-5 py-2.5 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 transition font-semibold shadow-lg shadow-indigo-200 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M5 12l14-7-4 14-3-5-5-2z"/>
                    </svg>
                    {{ __('Send feedback') }}
                </button>
            </div>
        </form>

    </div>
</div>

<style>
@keyframes scaleIn {
    from {
        opacity: 0;
        transform: scale(0.95);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}
.animate-scale-in {
    animation: scaleIn 0.2s ease-out;
}
</style>

<script>
function openFeedbackModal() {
    const modal = document.getElementById('feedbackModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeFeedbackModal() {
    const modal = document.getElementById('feedbackModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function updateCounter(textareaId, counterId) {
    const textarea = document.getElementById(textareaId);
    document.getElementById(counterId).textContent = textarea.value.length;
}

// close modal when clicking outside the modal card
document.getElementById('feedbackModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeFeedbackModal();
    }
});
</script>

    </div>

</body>
</html>