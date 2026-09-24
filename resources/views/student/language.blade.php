<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('partials.pwa-head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Language</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
        border-bottom: 1px solid #e0e7ff;
        padding: 16px 24px;
    }
    .page-header .back-btn { background: transparent; color: #6366f1; }
    .page-header .back-btn:hover { background: #eef2ff; }
    .page-header h1 { color: #312e81; }
    .page-header p { color: #818cf8; }
  }
</style>
</head>

<body class="min-h-screen">

@include('partials.app-nav', ['active' => 'profile'])

<div class="bg-blob b1"></div>
<div class="bg-blob b2"></div>
<div class="bg-blob b3"></div>

    <!-- Header -->
    <div class="page-header">

        <a href="{{ route('student.profile') }}" class="back-btn" aria-label="Back">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M15 19l-7-7 7-7"/>
            </svg>
        </a>

        <div>
            <h1>{{ __('Language') }}</h1>
            <p>{{ __('Choose the language used across RakanKampus') }}</p>
        </div>

    </div>

    <div class="max-w-md mx-auto p-5 space-y-5">

        @if(session('success'))
            <div class="rounded-xl bg-green-50 text-green-700 px-4 py-3 text-sm text-center">
                {{ session('success') }}
            </div>
        @endif

        <!-- Current language status -->
        <div class="bg-white rounded-3xl border border-indigo-100 p-5 flex items-center gap-4 shadow-sm">

            <div class="w-14 h-14 rounded-2xl bg-indigo-50 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1 13l-2 2m0 0l-2-2m2 2V9m11-4h-4m2-2v4"/>
                </svg>
            </div>

            <div class="flex-1">
                <p class="font-bold text-indigo-900">{{ __('Current language') }}</p>
                <p class="text-sm text-indigo-400">
                    @switch($currentLanguage)
                        @case('ms') Bahasa Melayu @break
                        @case('zh') 中文 (Mandarin) @break
                        @case('ta') தமிழ் (Tamil) @break
                        @default English
                    @endswitch
                </p>
            </div>

        </div>

        <!-- Language options -->
        <form method="POST" action="{{ route('student.profile.language.update') }}">
            @csrf
            @method('PUT')

            <div class="bg-white rounded-3xl border border-indigo-100 overflow-hidden shadow-sm">

                <div class="px-5 pt-4 pb-2">
                    <p class="text-[11px] font-bold tracking-wider uppercase text-indigo-500">{{ __('Available languages') }}</p>
                </div>

                <div class="divide-y divide-indigo-50">

                    @foreach ([
                        'en' => ['English', null],
                        'ms' => ['Bahasa Melayu', null],
                        'zh' => ['中文', 'Mandarin'],
                        'ta' => ['தமிழ்', 'Tamil'],
                    ] as $code => [$native, $english])
                        <label class="flex items-center justify-between px-5 py-4 cursor-pointer hover:bg-indigo-50 transition">

                            <div class="flex items-center gap-4">
                                <div class="w-11 h-11 rounded-2xl bg-indigo-50 flex items-center justify-center font-bold text-indigo-600 text-xs">
                                    {{ strtoupper($code) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-indigo-900">{{ $native }}</p>
                                    @if($english)
                                        <p class="text-sm text-indigo-400">{{ $english }}</p>
                                    @endif
                                </div>
                            </div>

                            <input type="radio" name="language" value="{{ $code }}"
                                   class="w-5 h-5 accent-indigo-600"
                                   {{ $currentLanguage === $code ? 'checked' : '' }}
                                   onchange="this.form.submit()">

                        </label>
                    @endforeach

                </div>

            </div>

        </form>

        <p class="text-xs text-indigo-300 text-center px-4">
            {{ __('Changing your language updates all RakanKampus pages the next time they load.') }}
        </p>

    </div>

</body>
</html>
