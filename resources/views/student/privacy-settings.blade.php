<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Settings</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        html, body { overflow-x: hidden; }
        body {
            background: linear-gradient(120deg, #f472b6, #fb923c, #a78bfa, #f472b6);
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
    
  @keyframes gradientShift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
  }
</style>
</head>

<body class="min-h-screen">

<div class="bg-blob b1"></div>
<div class="bg-blob b2"></div>
<div class="bg-blob b3"></div>


    <!-- Header -->
    <div class="bg-white border-b border-indigo-100 px-6 py-5 flex items-center gap-4">

        <a href="{{ route('student.profile') }}"
           class="text-indigo-500 hover:text-indigo-700 text-2xl transition">
            ←
        </a>

        <div>
            <h1 class="text-2xl font-bold text-indigo-900">Privacy Settings</h1>
            <p class="text-sm text-indigo-400">Control your data and visibility</p>
        </div>

    </div>

    <div class="max-w-md mx-auto p-5 space-y-5">

        <!-- Privacy Status -->
        <div class="bg-white rounded-3xl border border-indigo-100 p-5 shadow-sm">

            <div class="flex items-center gap-4 mb-4">

                <div class="w-14 h-14 rounded-2xl bg-indigo-50 flex items-center justify-center">

                    <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 2l7 4v5c0 5-3.5 9.7-7 11-3.5-1.3-7-6-7-11V6l7-4z"/>
                    </svg>

                </div>

                <div class="flex-1">
                    <h2 class="font-bold text-indigo-900">Privacy Protected</h2>
                    <p class="text-sm text-indigo-400">You are in control of your personal data</p>
                </div>

            </div>

            <div class="w-full bg-indigo-100 rounded-full h-2 overflow-hidden">
                <div class="bg-indigo-600 h-2 rounded-full w-[92%]"></div>
            </div>

            <div class="flex items-center justify-between mt-3 text-sm">
                <span class="text-indigo-500">Privacy level</span>
                <span class="font-semibold text-indigo-700">High</span>
            </div>

        </div>

        <!-- Profile Visibility -->
        <div class="bg-white rounded-3xl border border-indigo-100 overflow-hidden shadow-sm">

            <div class="px-5 pt-4 pb-2">
                <p class="text-[11px] font-bold tracking-wider uppercase text-indigo-500">Profile visibility</p>
            </div>

            <div class="divide-y divide-indigo-50">

                <div class="flex items-center justify-between px-5 py-4">

                    <div class="flex items-center gap-4">

                        <div class="w-11 h-11 rounded-2xl bg-indigo-50 flex items-center justify-center">

                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>

                        </div>

                        <div>
                            <p class="font-semibold text-indigo-900">Show my profile</p>
                            <p class="text-sm text-indigo-400">Allow other students to find your profile</p>
                        </div>

                    </div>

                    <button class="relative w-11 h-6 bg-indigo-600 rounded-full">
                        <span class="absolute top-1 right-1 w-4 h-4 bg-white rounded-full"></span>
                    </button>

                </div>

                <div class="flex items-center justify-between px-5 py-4">

                    <div class="flex items-center gap-4">

                        <div class="w-11 h-11 rounded-2xl bg-indigo-50 flex items-center justify-center">

                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M8 7V3m8 4V3m-9 8h10m-11 9h12a2 2 0 002-2V7a2 2 0 00-2-2H6a2 2 0 00-2 2v11a2 2 0 002 2z"/>
                            </svg>

                        </div>

                        <div>
                            <p class="font-semibold text-indigo-900">Show faculty and course</p>
                            <p class="text-sm text-indigo-400">Display academic information publicly</p>
                        </div>

                    </div>

                    <button class="relative w-11 h-6 bg-indigo-600 rounded-full">
                        <span class="absolute top-1 right-1 w-4 h-4 bg-white rounded-full"></span>
                    </button>

                </div>

            </div>

        </div>

        <!-- AI & Chat Privacy -->
        <div class="bg-white rounded-3xl border border-indigo-100 overflow-hidden shadow-sm">

            <div class="px-5 pt-4 pb-2">
                <p class="text-[11px] font-bold tracking-wider uppercase text-indigo-500">AI &amp; chat privacy</p>
            </div>

            <div class="divide-y divide-indigo-50">

                <div class="flex items-center justify-between px-5 py-4">

                    <div class="flex items-center gap-4">

                        <div class="w-11 h-11 rounded-2xl bg-indigo-50 flex items-center justify-center">

                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M8 10h8m-8 4h5M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4-.8L3 20l1.1-3.3A7.938 7.938 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>

                        </div>

                        <div>
                            <p class="font-semibold text-indigo-900">Save chat history</p>
                            <p class="text-sm text-indigo-400">Keep conversations for future reference</p>
                        </div>

                    </div>

                    <button class="relative w-11 h-6 bg-indigo-600 rounded-full">
                        <span class="absolute top-1 right-1 w-4 h-4 bg-white rounded-full"></span>
                    </button>

                </div>

                <div class="flex items-center justify-between px-5 py-4">

                    <div class="flex items-center gap-4">

                        <div class="w-11 h-11 rounded-2xl bg-indigo-50 flex items-center justify-center">

                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M4 7h16M4 12h10M4 17h7"/>
                            </svg>

                        </div>

                        <div>
                            <p class="font-semibold text-indigo-900">Anonymous analytics</p>
                            <p class="text-sm text-indigo-400">Help improve chatbot responses</p>
                        </div>

                    </div>

                    <button class="relative w-11 h-6 bg-indigo-200 rounded-full">
                        <span class="absolute top-1 left-1 w-4 h-4 bg-white rounded-full"></span>
                    </button>

                </div>

            </div>

        </div>

        <!-- Data Control -->
        <div class="bg-white rounded-3xl border border-indigo-100 overflow-hidden shadow-sm">

            <div class="px-5 pt-4 pb-2">
                <p class="text-[11px] font-bold tracking-wider uppercase text-indigo-500">Data control</p>
            </div>

            <div class="divide-y divide-indigo-50">

                <a href="#" class="flex items-center justify-between px-5 py-4 hover:bg-indigo-50 transition">

                    <div class="flex items-center gap-4">

                        <div class="w-11 h-11 rounded-2xl bg-indigo-50 flex items-center justify-center">

                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5m0 0l5-5m-5 5V4"/>
                            </svg>

                        </div>

                        <div>
                            <p class="font-semibold text-indigo-900">Download my data</p>
                            <p class="text-sm text-indigo-400">Get a copy of your profile and chats</p>
                        </div>

                    </div>

                    <span class="text-indigo-300 text-lg">›</span>

                </a>

                <a href="#" class="flex items-center justify-between px-5 py-4 hover:bg-red-50 transition">

                    <div class="flex items-center gap-4">

                        <div class="w-11 h-11 rounded-2xl bg-red-50 flex items-center justify-center">

                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M6 7h12M9 7V5h6v2m-7 3v7m4-7v7m4-7v7M5 7l1 12a2 2 0 002 2h8a2 2 0 002-2l1-12"/>
                            </svg>

                        </div>

                        <div>
                            <p class="font-semibold text-red-600">Clear chat history</p>
                            <p class="text-sm text-red-400">Remove all saved conversations</p>
                        </div>

                    </div>

                    <span class="text-red-300 text-lg">›</span>

                </a>

            </div>

        </div>

        <!-- Save Button -->
        <button class="w-full rounded-2xl bg-indigo-600 py-4 text-lg font-bold text-white shadow-lg shadow-indigo-200 hover:bg-indigo-700 transition duration-200">
            Save Privacy Settings
        </button>

    </div>

</body>
</html>