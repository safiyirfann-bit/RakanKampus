<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
@include('partials.font')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('partials.pwa-head')
    <title>{{ __('Edit Profile') }}</title>
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
            z-index: -1;
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
        body > div:not(.bg-blob) { animation: fadeInUp 0.45s ease both; }

        .page-header {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 20px 20px 18px;
            position: relative;
            z-index: 1;
        }
        .page-header .back-btn {
            width: 38px; height: 38px; min-width: 38px; border-radius: 50%;
            background: rgba(255,255,255,0.12);
            display: flex; align-items: center; justify-content: center;
            color: #fff; text-decoration: none; transition: background 0.15s ease;
        }
        .page-header .back-btn:hover { background: rgba(255,255,255,0.2); }
        .page-header .back-btn svg { width: 18px; height: 18px; stroke: currentColor; }
        .page-header h1 { font-size: 18px; font-weight: 800; color: #fff; margin: 0; }
        .page-header p { font-size: 12.5px; color: #bfe9ea; margin: 2px 0 0; }

  @keyframes gradientShift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
  }

  @media (min-width: 861px) {
    body {
        background: #f0fafa;
    }
    .page-header {
        background: rgba(255,255,255,0.9);
        backdrop-filter: blur(8px);
        border-bottom: 1px solid #dbeeee;
        padding: 16px 24px;
    }
    .page-header .back-btn { background: transparent; color: #0d9488; }
    .page-header .back-btn:hover { background: #eafbfa; }
    .page-header h1 { color: #14213d; }
    .page-header p { color: #64748b; }
  }
</style>
<style>
    .matric-msg:empty { display: none; }
    .matric-msg { font-size: 13px; margin: 8px 4px 0; line-height: 1.4; display: flex; align-items: flex-start; gap: 6px; }
    .matric-hint { color: #94a3b8; }
    .matric-ok { color: #0f766e; font-weight: 600; }
    .matric-err { color: #b91c1c; background: #fef2f2; border: 1px solid #fecaca; border-radius: 10px; padding: 8px 12px; font-weight: 500; }
    .matric-input-err { border-color: #f87171 !important; box-shadow: 0 0 0 3px rgba(248, 113, 113, 0.18) !important; }
    .matric-input-ok { border-color: #2dd4bf !important; }
</style>
<style>
/* Edit Profile, "glass neon": see-through fields on the gradient, light-teal labels,
   white text, and a neon glow on the field being edited. On PC the gradient becomes
   a rounded panel (the page background there is light). */
.ep-wrap { max-width: 42rem; margin: 0 auto; padding: 8px 20px 40px; position: relative; z-index: 1; }
.ep-panel { position: relative; }
.ep-av-btn { position: relative; display: block; border: 0; padding: 0; background: none; cursor: pointer; }
.ep-av { width: 92px; height: 92px; border-radius: 50%; overflow: hidden; display: grid; place-items: center;
  background: linear-gradient(135deg, #0d9488, #2ec4c6); color: #fff; font-size: 32px; font-weight: 800;
  border: 3px solid rgba(255,255,255,.7); box-shadow: 0 12px 28px rgba(0,0,0,.25); transition: transform .2s; }
.ep-av-btn:hover .ep-av { transform: scale(1.04); }
.ep-av-edit { position: absolute; right: -2px; bottom: 0; width: 32px; height: 32px; border-radius: 50%; display: grid; place-items: center;
  background: #fff; color: #0f766e; box-shadow: 0 4px 12px rgba(0,0,0,.25); }
.ep-av-edit svg { width: 15px; height: 15px; }
.ep-name { text-align: center; color: #fff; margin: 12px 0 22px; }
.ep-name b { display: block; font-size: 18px; font-weight: 800; text-transform: capitalize; min-height: 1.3em; }
.ep-name span { display: block; font-size: 12.5px; color: #c9f3f1; margin-top: 2px; letter-spacing: .02em; }
.ep-flash { margin-bottom: 16px; padding: 11px 14px; border-radius: 14px; font-size: 13.5px; font-weight: 600;
  background: rgba(74,222,128,.18); border: 1px solid rgba(134,239,172,.45); color: #dcfce7; }
.ep-form { display: flex; flex-direction: column; gap: 14px; }
.ep-grid2 { display: grid; grid-template-columns: 1fr; gap: 14px; }
@media (min-width: 768px) { .ep-grid2 { grid-template-columns: 1fr 1fr; } }

.epf { display: flex; align-items: center; gap: 12px; padding: 11px 14px; border-radius: 18px; cursor: text;
  background: rgba(255,255,255,.14); border: 1px solid rgba(255,255,255,.35);
  -webkit-backdrop-filter: blur(14px); backdrop-filter: blur(14px);
  box-shadow: inset 0 1px 0 rgba(255,255,255,.22); transition: background .2s, border-color .2s, box-shadow .2s; }
.epf:hover { background: rgba(255,255,255,.18); }
.epf:focus-within { background: rgba(255,255,255,.22); border-color: #7ff5ec;
  box-shadow: 0 0 0 3px rgba(127,245,236,.25), 0 0 24px rgba(127,245,236,.35); }
.epf-ic { width: 36px; height: 36px; flex: none; border-radius: 12px; display: grid; place-items: center;
  background: rgba(255,255,255,.18); color: #fff; transition: background .2s, color .2s; }
.epf-ic svg { width: 17px; height: 17px; }
.epf:focus-within .epf-ic { background: #7ff5ec; color: #0f2747; }
.epf-body { flex: 1; min-width: 0; }
.epf-body label { display: block; font-size: 10.5px; font-weight: 800; letter-spacing: .1em; text-transform: uppercase; color: #b9fbf5; cursor: text; }
.epf-opt { text-transform: none; letter-spacing: 0; font-weight: 600; color: rgba(255,255,255,.6); }
html body .epf input { display: block; width: 100%; margin: 1px 0 0; padding: 0; border: 0 !important; outline: none; box-shadow: none !important;
  background: transparent !important; color: #fff !important; font-size: 16px; font-weight: 600; font-family: inherit; caret-color: #7ff5ec; }
html body .epf input::placeholder { color: rgba(255,255,255,.45); }
html body .epf input:-webkit-autofill { -webkit-text-fill-color: #fff; transition: background-color 9999s ease-in-out 0s; }
/* live validation states (set on the input by the script below) */
.epf:has(.matric-input-err) { border-color: #fca5a5; box-shadow: 0 0 0 3px rgba(248,113,113,.28); }
.epf:has(.matric-input-ok) { border-color: #5eead4; }
.ep-panel .matric-hint { color: #d9f7f5; }
.ep-panel .matric-ok { color: #a7f3d0; }

.ep-save { margin-top: 6px; width: 100%; padding: 15px; border: 0; border-radius: 18px; cursor: pointer; font-family: inherit;
  font-size: 16px; font-weight: 800; color: #0f2747; background: linear-gradient(90deg, #ffffff, #b9fbf5);
  box-shadow: 0 0 0 1px rgba(255,255,255,.6), 0 10px 26px rgba(127,245,236,.35); transition: transform .15s, box-shadow .2s, opacity .2s; }
.ep-save:hover:not(:disabled) { box-shadow: 0 0 0 1px #fff, 0 12px 32px rgba(127,245,236,.55); }
.ep-save:active:not(:disabled) { transform: scale(.98); }
.ep-save:disabled { cursor: default; color: rgba(255,255,255,.55); background: rgba(255,255,255,.12); box-shadow: inset 0 0 0 1px rgba(255,255,255,.25); }

@media (min-width: 861px) {
  .ep-wrap { padding: 28px 24px 48px; }
  .ep-panel { overflow: hidden; border-radius: 28px; padding: 32px 32px 28px;
    background: linear-gradient(135deg, #14213d 0%, #1b3a5c 35%, #21768a 70%, #2ec4c6 100%);
    box-shadow: 0 20px 50px rgba(15,39,71,.22); }
  .ep-panel::before, .ep-panel::after { content: ""; position: absolute; border-radius: 50%; pointer-events: none; background: rgba(255,255,255,.1); filter: blur(4px); }
  .ep-panel::before { width: 260px; height: 260px; top: -90px; left: -80px; }
  .ep-panel::after { width: 220px; height: 220px; bottom: -80px; right: -60px; }
  .ep-panel > * { position: relative; z-index: 1; }
  html[data-theme="dark"] .ep-panel { background: linear-gradient(135deg, #0c1320 0%, #112031 40%, #17505a 75%, #1d6869 100%); box-shadow: 0 20px 50px rgba(0,0,0,.45); }
}
@media (prefers-reduced-motion: reduce) { .epf, .ep-save, .ep-av { transition: none; } }
</style>
<style id="rk-dark-theme">
/* Dark theme — generated by scripts/dark-theme/generate.py, do not edit by hand.
   Hand-made fixes go in resources/views/partials/dark-fixes.blade.php */
html[data-theme="dark"] body { background-image: linear-gradient(120deg, #0c1320, #112031, #1d6869, #0c1320); }
@media (min-width: 861px) {
  html[data-theme="dark"] body { background: #10161f; }
  html[data-theme="dark"] .page-header { background: rgba(23, 32, 45, 0.9); border-bottom: 1px solid #284848; }
  html[data-theme="dark"] .page-header .back-btn { color: #41eedf; }
  html[data-theme="dark"] .page-header .back-btn:hover { background: #1c3b39; }
  html[data-theme="dark"] .page-header h1 { color: #dee1e9; }
  html[data-theme="dark"] .page-header p { color: #b0b6be; }
}
html[data-theme="dark"] .matric-hint { color: #ced3d9; }
html[data-theme="dark"] .matric-ok { color: #2fe5d6; }
html[data-theme="dark"] .matric-err { color: #eb7979; background: #371a1a; border: 1px solid #482828; }
html[data-theme="dark"] .matric-input-err { box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.37) !important; }
</style>
</head>

<body class="min-h-screen">

@include('partials.app-nav', ['active' => 'profile', 'user' => $user])

<div class="bg-blob b1"></div>
<div class="bg-blob b2"></div>
<div class="bg-blob b3"></div>


<!-- Header -->
<div class="page-header">

    <a href="{{ route('student.profile') }}" class="back-btn" aria-label="{{ __('Back') }}">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M15 19l-7-7 7-7"/>
        </svg>
    </a>

    <h1>{{ __('Edit Profile') }}</h1>

</div>

<div class="ep-wrap">
<div class="ep-panel">

    <!-- Avatar -->
    <div class="flex justify-center">
        <button type="button" onclick="openPhotoModal()" class="ep-av-btn group" aria-label="{{ __('Profile photo') }}">
            <div class="ep-av" id="avatarWrapper">
                @if($user->photo_data)
                    <img src="{{ $user->photo_data }}" class="w-full h-full object-cover" alt="{{ __('Profile photo') }}">
                @else
                    {{ strtoupper(substr($user->first_name ?? 'A', 0, 1) . substr($user->last_name ?? '', 0, 1)) }}
                @endif
            </div>
            <span class="ep-av-edit">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
            </span>
        </button>
    </div>
    @php
        $epDept = \App\Support\MatricNumber::department($user->student_id);
    @endphp
    <div class="ep-name">
        <b id="epNameLive">{{ trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) }}</b>
        <span>{{ collect([$user->student_id, $epDept])->filter()->implode(' · ') }}</span>
    </div>

    @if(session('success'))
        <div class="ep-flash">✓ {{ session('success') }}</div>
    @endif

    <!-- Form -->
    <form action="{{ route('student.profile.update') }}" method="POST" class="ep-form" id="editProfileForm" novalidate>
        @csrf
        @method('PUT')

        <div class="ep-grid2">
        <div>
            <div class="epf">
                <span class="epf-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></span>
                <div class="epf-body">
                    <label>{{ __('First Name') }}</label>
                    <input type="text" name="first_name" id="field_first_name" autocomplete="given-name" value="{{ old('first_name', $user->first_name) }}">
                </div>
            </div>
            <p class="matric-msg" data-msg-for="first_name"></p>
            @error('first_name')
                <p class="matric-msg matric-err" data-server-error="first_name">⚠️ {{ $message }}</p>
            @enderror
        </div>
        <div>
            <div class="epf">
                <span class="epf-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></span>
                <div class="epf-body">
                    <label>{{ __('Last Name') }}</label>
                    <input type="text" name="last_name" id="field_last_name" autocomplete="family-name" value="{{ old('last_name', $user->last_name) }}">
                </div>
            </div>
            <p class="matric-msg" data-msg-for="last_name"></p>
            @error('last_name')
                <p class="matric-msg matric-err" data-server-error="last_name">⚠️ {{ $message }}</p>
            @enderror
        </div>
        </div>

        <div>
            <div class="epf">
                <span class="epf-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg></span>
                <div class="epf-body">
                    <label>{{ __('Email') }}</label>
                    <input type="email" name="email" id="field_email" autocomplete="email" value="{{ old('email', $user->email) }}">
                </div>
            </div>
            <p class="matric-msg" data-msg-for="email"></p>
            @error('email')
                <p class="matric-msg matric-err" data-server-error="email">⚠️ {{ $message }}</p>
            @enderror
        </div>
        <div>
            <div class="epf">
                <span class="epf-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="16" rx="2"/><circle cx="9" cy="10" r="2"/><path d="M15 8h2M15 12h2M6 16c.7-1.3 1.8-2 3-2s2.3.7 3 2"/></svg></span>
                <div class="epf-body">
                    <label>{{ __('Registration Number') }}</label>
                    <input type="text" name="student_id" id="matricInput" maxlength="16" autocomplete="off" placeholder="01DKA23F0456" value="{{ old('student_id', $user->student_id) }}">
                </div>
            </div>
            <p id="matricMsg" class="matric-msg"></p>
            @error('student_id')
                <p class="matric-msg matric-err" data-server-error="student_id">⚠️ {{ $message }}</p>
            @enderror
        </div>
        <div>
            <div class="epf">
                <span class="epf-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.4 1.8.7 2.7a2 2 0 0 1-.5 2.1L8 9.8a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.7.7a2 2 0 0 1 1.7 2z"/></svg></span>
                <div class="epf-body">
                    <label>{{ __('Phone Number') }} <span class="epf-opt">· {{ __('optional') }}</span></label>
                    <input type="tel" name="phone" id="field_phone" autocomplete="tel" placeholder="012-3456789" value="{{ old('phone', $user->phone) }}">
                </div>
            </div>
            <p class="matric-msg" data-msg-for="phone"></p>
            @error('phone')
                <p class="matric-msg matric-err" data-server-error="phone">⚠️ {{ $message }}</p>
            @enderror
        </div>

        <!-- Save Button -->
        {{-- Only lights up (and can be pressed) once something in the form has changed. --}}
        <button type="submit" id="saveProfileBtn" class="ep-save" @if(! $errors->any()) disabled @endif>
            {{ __('Save Changes') }}
        </button>

    </form>

</div>
</div>

<!-- Profile Photo Modal -->
<div id="photoModal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50 p-4">

    <div class="bg-white w-full max-w-md rounded-3xl border border-indigo-100 shadow-2xl animate-scale-in">

        <!-- Header -->
        <div class="flex items-center justify-between p-6 border-b border-indigo-100">

            <div>
                <h2 class="text-2xl font-bold text-indigo-900">{{ __('Profile photo') }}</h2>
                <p class="text-sm text-indigo-400 mt-1">{{ __('Choose a photo for your account.') }}</p>
            </div>

            <button onclick="closePhotoModal()"
                class="w-10 h-10 rounded-full hover:bg-indigo-50 flex items-center justify-center text-indigo-400 hover:text-indigo-600 transition">
                ✕
            </button>

        </div>

        <!-- Preview -->
        <div class="p-6 flex justify-center">

            <div class="w-36 h-36 rounded-full bg-indigo-50 border border-indigo-100 flex flex-col items-center justify-center text-indigo-300 overflow-hidden" id="previewWrapper">

                <img id="previewImg" class="w-full h-full object-cover hidden" alt="{{ __('Preview') }}">

                <svg id="noPhotoIcon" xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7a2 2 0 012-2h2l1-1h4l1 1h2a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V7zm9 3a4 4 0 100 8 4 4 0 000-8z"/>
                </svg>

                <span id="noPhotoText" class="text-sm">{{ __('No photo') }}</span>

            </div>

        </div>

        <!-- Live camera view (hidden until Take selfie is pressed) -->
        <div id="cameraView" class="hidden flex-col items-center px-6 pb-4">

            <video id="cameraVideo" autoplay playsinline muted class="w-full rounded-2xl bg-black"></video>
            <canvas id="cameraCanvas" class="hidden"></canvas>

            <div class="grid grid-cols-2 gap-4 w-full mt-4">
                <button type="button" onclick="cancelCamera()"
                    class="rounded-2xl border border-indigo-100 bg-indigo-50/50 py-3 font-medium text-indigo-700 hover:bg-indigo-100 transition">
                    {{ __('Cancel') }}
                </button>
                <button type="button" onclick="snapPhoto()"
                    class="rounded-2xl bg-indigo-600 py-3 font-semibold text-white hover:bg-indigo-700 transition">
                    {{ __('Snap photo') }}
                </button>
            </div>

        </div>

        <!-- Actions -->
        <div id="actionsRow" class="px-6 grid grid-cols-2 gap-4 mb-5">

            <button type="button" onclick="openCamera()"
                class="rounded-2xl border border-indigo-100 bg-indigo-50/50 py-4 flex flex-col items-center gap-2 hover:bg-indigo-100 transition">

                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14m-6 4h6a2 2 0 002-2V8a2 2 0 00-2-2H9a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>

                <span class="font-medium text-indigo-700">{{ __('Take selfie') }}</span>

                <input type="file" accept="image/*" capture="user" class="hidden" id="selfieInput">

            </button>

            <label class="rounded-2xl border border-indigo-100 bg-indigo-50/50 py-4 flex flex-col items-center gap-2 hover:bg-indigo-100 transition cursor-pointer">

                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4-4a3 3 0 014.243 0L16 16m-2-2l1-1a3 3 0 014.243 0L20 14m-6 6H6a2 2 0 01-2-2V6a2 2 0 012-2h12a2 2 0 012 2v12a2 2 0 01-2 2z"/>
                </svg>

                <span class="font-medium text-indigo-700">{{ __('Choose photo') }}</span>

                <input type="file" accept="image/*" class="hidden" id="photoInput">

            </label>

        </div>

        <!-- Upload -->
        <div class="p-6 pt-0">

            <button type="button" id="uploadBtn" onclick="uploadPhoto()"
                class="w-full rounded-2xl bg-indigo-600 py-4 text-lg font-semibold text-white hover:bg-indigo-700 transition shadow-lg shadow-indigo-200">
                {{ __('Upload photo') }}
            </button>

        </div>

    </div>

</div>

<!-- Friendly Alert Modal -->
<div id="alertModal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50 p-4">
    <div class="bg-white w-full max-w-sm rounded-3xl border border-indigo-100 shadow-2xl animate-scale-in p-6 text-center">
        <div id="alertIcon" class="w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
            </svg>
        </div>
        <h2 id="alertTitle" class="text-lg font-bold text-indigo-900 mb-1">{{ __('Notice') }}</h2>
        <p id="alertMessage" class="text-sm text-indigo-400 mb-6">&nbsp;</p>
        <button type="button" onclick="closeAlertModal()"
            class="w-full rounded-2xl bg-indigo-600 py-3 font-semibold text-white hover:bg-indigo-700 transition">
            OK
        </button>
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
// Current saved photo, so the modal can show it instead of always defaulting
// to the "No photo" placeholder when reopened.
let currentPhotoUrl = @json($user->photo_data ?: null);

function openPhotoModal() {
    const modal = document.getElementById('photoModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    resetPreview();
}

function resetPreview() {
    selectedFile = null;
    document.getElementById('photoInput').value = '';
    document.getElementById('selfieInput').value = '';

    const previewImg = document.getElementById('previewImg');
    if (currentPhotoUrl) {
        previewImg.src = currentPhotoUrl;
        previewImg.classList.remove('hidden');
        document.getElementById('noPhotoIcon').classList.add('hidden');
        document.getElementById('noPhotoText').classList.add('hidden');
    } else {
        previewImg.classList.add('hidden');
        document.getElementById('noPhotoIcon').classList.remove('hidden');
        document.getElementById('noPhotoText').classList.remove('hidden');
    }
}

function closePhotoModal() {
    stopCamera();
    const modal = document.getElementById('photoModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

document.getElementById('photoModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closePhotoModal();
    }
});

let selectedFile = null;
let cameraStream = null;

async function openCamera() {
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        // Browser doesn't support live camera access - fall back to native file picker.
        document.getElementById('selfieInput').click();
        return;
    }

    try {
        cameraStream = await navigator.mediaDevices.getUserMedia({
            video: { facingMode: 'user' },
            audio: false,
        });

        document.getElementById('cameraVideo').srcObject = cameraStream;
        document.getElementById('previewWrapper').classList.add('hidden');
        document.getElementById('actionsRow').classList.add('hidden');
        document.getElementById('cameraView').classList.remove('hidden');
        document.getElementById('cameraView').classList.add('flex');
    } catch (err) {
        console.error(err);
        showAlert('error', t('Camera unavailable'), t('Unable to access the camera. Please allow camera permission, or use Choose photo instead.'));
    }
}

function stopCamera() {
    if (cameraStream) {
        cameraStream.getTracks().forEach(track => track.stop());
        cameraStream = null;
    }
    document.getElementById('cameraView').classList.add('hidden');
    document.getElementById('cameraView').classList.remove('flex');
    document.getElementById('previewWrapper').classList.remove('hidden');
    document.getElementById('actionsRow').classList.remove('hidden');
}

function cancelCamera() {
    stopCamera();
}

function snapPhoto() {
    const video = document.getElementById('cameraVideo');
    const canvas = document.getElementById('cameraCanvas');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);

    canvas.toBlob(function (blob) {
        const file = new File([blob], 'selfie.jpg', { type: 'image/jpeg' });
        stopCamera();
        handlePhotoFile(file);
    }, 'image/jpeg', 0.9);
}

function handlePhotoFile(file) {
    selectedFile = file;
    if (selectedFile) {
        // Just preview here — don't upload yet. The user reviews the photo
        // and taps "Upload photo" themselves; uploading immediately on pick
        // (and closing the modal right after) felt like a forced auto-close
        // with no chance to confirm or pick a different photo.
        const reader = new FileReader();
        reader.onload = function(ev) {
            document.getElementById('previewImg').src = ev.target.result;
            document.getElementById('previewImg').classList.remove('hidden');
            document.getElementById('noPhotoIcon').classList.add('hidden');
            document.getElementById('noPhotoText').classList.add('hidden');
        };
        reader.readAsDataURL(selectedFile);
    }
}

document.getElementById('photoInput').addEventListener('change', function(e) {
    handlePhotoFile(e.target.files[0]);
});

document.getElementById('selfieInput').addEventListener('change', function(e) {
    handlePhotoFile(e.target.files[0]);
});

function showAlert(type, title, message) {
    const icon = document.getElementById('alertIcon');
    const isError = type === 'error';
    icon.className = 'w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center ' +
        (isError ? 'bg-red-50 text-red-500' : 'bg-amber-50 text-amber-500');
    document.getElementById('alertTitle').textContent = title;
    document.getElementById('alertMessage').textContent = message;
    const modal = document.getElementById('alertModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeAlertModal() {
    const modal = document.getElementById('alertModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

document.getElementById('alertModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeAlertModal();
    }
});

function uploadPhoto() {
    if (!selectedFile) {
        showAlert('warning', t('Notice'), t('Please choose an image first.'));
        return;
    }

    const formData = new FormData();
    formData.append('photo', selectedFile);

    fetch('{{ route("profile.photo.upload") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: formData
    })
        // Read as text first, then try to parse JSON — a validation failure (wrong
        // file type, too large, etc.) without an Accept header would otherwise come
        // back as an HTML page, and res.json() on that throws and lands in .catch()
        // with no useful message, which is why this used to just say "an error
        // occurred" for every failure reason.
        .then(res => res.text().then((text) => {
            let data = null;
            try { data = JSON.parse(text); } catch (e) { /* not JSON */ }
            return { ok: res.ok, data };
        }))
        .then(({ ok, data }) => {
            if (ok && data && data.success) {
                document.getElementById('avatarWrapper').innerHTML =
                    `<img src="${data.photoUrl}" class="w-full h-full object-cover" alt="${t('Profile photo')}">`;
                currentPhotoUrl = data.photoUrl;
                closePhotoModal();
                return;
            }

            const validationMsg = data && data.errors && data.errors.photo && data.errors.photo[0];
            showAlert('error', t('Failed'), validationMsg || (data && data.message) || t('Failed to upload image.'));
        })
        .catch(err => {
            console.error(err);
            showAlert('error', t('Error'), t('An error occurred during upload.'));
        });
}
</script>
<script>
// Save Changes stays greyed out until the student actually changes a field
// (and greys out again if they change it back to what it was).
(function () {
    const form = document.getElementById('editProfileForm');
    const btn = document.getElementById('saveProfileBtn');
    if (!form || !btn) return;
    const fields = Array.from(form.querySelectorAll('input:not([type=hidden]), select, textarea'));
    const initial = fields.map(f => f.value);
    const hadErrors = !btn.disabled;
    function check() {
        const changed = fields.some((f, i) => f.value !== initial[i]);
        btn.disabled = !(changed || hadErrors);
    }
    fields.forEach(f => { f.addEventListener('input', check); f.addEventListener('change', check); });
})();
</script>
@php
    $formText = [
        'required' => __('This field is required.'),
        'email' => __('Please enter a valid email address, e.g. name@example.com.'),
        'phone' => __('Please enter a valid phone number, e.g. 012-3456789.'),
        'hint' => __('12 characters, e.g. 01DKA23F0456'),
        'empty' => __('Please enter your matric number.'),
        'format' => __('Matric number must be 12 characters, e.g. 01DKA23F0456.'),
        'puo' => __('Only PUO matric numbers (starting with 01) are accepted.'),
        'code' => __('":code" is not a PUO programme code.'),
    ];
    $matricCodes = config('programs.codes');
@endphp
<script>
// Friendly inline validation for every field on Edit Profile (replaces the
// browser's plain bubbles). Same rules as the server. Messages appear under
// each box once the student has touched it, or when they press Save.
(function () {
    const form = document.getElementById('editProfileForm');
    if (!form) return;
    const T = @json($formText);
    const PROGRAMMES = @json($matricCodes);

    const rules = {
        first_name: v => v.trim() ? null : T.required,
        last_name: v => v.trim() ? null : T.required,
        email: v => !v.trim() ? T.required : (/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(v.trim()) ? null : T.email),
        phone: v => !v.trim() ? null : (/^\+?[0-9][0-9\s-]{7,14}$/.test(v.trim()) ? null : T.phone),
        student_id: v => {
            v = v.replace(/[\s-]/g, '').toUpperCase();
            if (v === '') return T.empty;
            const m = v.match(/^(\d{2})([A-Z]{3})(\d{2})([A-Z])(\d{4})$/);
            if (!m) return T.format;
            if (m[1] !== '01') return T.puo;
            if (!PROGRAMMES[m[2]]) return T.code.replace(':code', m[2]);
            return null;
        },
    };

    const fields = Object.keys(rules).map(name => {
        const input = form.querySelector(`[name="${name}"]`);
        const msg = name === 'student_id' ? document.getElementById('matricMsg') : form.querySelector(`[data-msg-for="${name}"]`);
        return input && msg ? { name, input, msg, touched: input.value.trim() !== '' } : null;
    }).filter(Boolean);

    function show(f, force) {
        const error = rules[f.name](f.input.value);
        f.msg.className = 'matric-msg';
        f.input.classList.remove('matric-input-err', 'matric-input-ok');
        f.msg.textContent = '';

        if (error && (f.touched || force)) {
            f.msg.textContent = '⚠️ ' + error;
            f.msg.classList.add('matric-err');
            f.input.classList.add('matric-input-err');
        } else if (f.name === 'student_id') {
            const code = f.input.value.replace(/[\s-]/g, '').toUpperCase().slice(2, 5);
            if (!error) {
                f.msg.textContent = '✓ ' + PROGRAMMES[code];
                f.msg.classList.add('matric-ok');
                f.input.classList.add('matric-input-ok');
            } else {
                f.msg.textContent = T.hint;
                f.msg.classList.add('matric-hint');
            }
        }
        return !error;
    }

    fields.forEach(f => {
        f.input.addEventListener('input', () => {
            if (f.name === 'student_id') f.input.value = f.input.value.toUpperCase();
            // hide the server's message for this field once the student edits it
            form.querySelectorAll(`[data-server-error="${f.name}"]`).forEach(el => el.remove());
            show(f, false);
        });
        f.input.addEventListener('blur', () => { f.touched = true; show(f, false); });
        show(f, false);
    });

    form.addEventListener('submit', (e) => {
        let firstBad = null;
        fields.forEach(f => { f.touched = true; if (!show(f, true) && !firstBad) firstBad = f; });
        if (firstBad) { e.preventDefault(); firstBad.input.focus(); }
    });
})();

// Name under the avatar follows what's typed.
(function () {
    const f = document.getElementById('field_first_name'), l = document.getElementById('field_last_name'), out = document.getElementById('epNameLive');
    if (!f || !l || !out) return;
    const sync = () => { out.textContent = (f.value.trim() + ' ' + l.value.trim()).trim(); };
    f.addEventListener('input', sync); l.addEventListener('input', sync);
    // tapping anywhere on a glass field focuses its input
    document.querySelectorAll('.epf').forEach(el => el.addEventListener('click', e => { if (e.target.tagName !== 'INPUT') el.querySelector('input')?.focus(); }));
})();
</script>
</body>
</html>