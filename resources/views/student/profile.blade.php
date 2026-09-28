<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('partials.pwa-head')
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

    <div class="max-w-md mx-auto p-5 space-y-4 relative z-10">

<!-- Profile Card -->
<div class="bg-white rounded-3xl border border-indigo-100 p-5 flex items-center gap-4 shadow-sm fade-up">

    <div class="w-14 h-14 rounded-full text-white flex items-center justify-center font-bold text-lg overflow-hidden" style="background: linear-gradient(135deg, #14213d, #2ec4c6);">
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
        <div class="bg-white rounded-3xl border border-indigo-100 overflow-hidden shadow-sm fade-up" style="animation-delay: 0.08s;">

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
<div class="bg-white rounded-3xl border border-indigo-100 overflow-hidden shadow-sm fade-up" style="animation-delay: 0.16s;">

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
<div class="bg-white rounded-3xl border border-indigo-100 overflow-hidden shadow-sm fade-up" style="animation-delay: 0.22s;">

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