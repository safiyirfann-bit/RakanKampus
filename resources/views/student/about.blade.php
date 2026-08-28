<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About RakanKampus</title>
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
    <div class="bg-white border-b border-slate-200 px-6 py-5 flex items-center gap-4">

        <a href="{{ route('student.profile') }}"
           class="text-indigo-600 hover:text-indigo-700 text-2xl transition">
            ←
        </a>

        <div>
            <h1 class="text-2xl font-bold text-slate-900">About RakanKampus</h1>
            <p class="text-sm text-slate-500">Learn more about this application</p>
        </div>

    </div>

    <div class="max-w-md mx-auto p-5 space-y-5">

        <!-- App Card -->
        <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm text-center">

            <div class="w-20 h-20 mx-auto rounded-3xl bg-indigo-50 flex items-center justify-center mb-4">
                <span class="text-2xl font-bold text-indigo-600">RK</span>
            </div>

            <h2 class="text-2xl font-bold text-slate-900">RakanKampus</h2>

            <p class="text-slate-500 mt-1">Campus AI Assistant for Students</p>

            <span class="inline-flex items-center gap-2 mt-4 px-4 py-2 rounded-full bg-indigo-50 text-indigo-700 text-sm font-semibold">
                <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                Version 1.0.0
            </span>

        </div>

        <!-- About -->
        <div class="bg-white rounded-3xl border border-slate-200 p-5 shadow-sm">

            <div class="flex items-center gap-3 mb-4">

                <div class="w-11 h-11 rounded-2xl bg-indigo-50 flex items-center justify-center">

                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20 10 10 0 000-20z"/>
                    </svg>

                </div>

                <div>
                    <h3 class="font-bold text-slate-900">About</h3>
                    <p class="text-sm text-slate-500">Purpose of the application</p>
                </div>

            </div>

            <p class="text-sm leading-7 text-slate-600">
                RakanKampus is a student support application developed to help students access campus information quickly and efficiently. The system combines an AI-powered chatbot with a structured knowledge base to provide accurate answers related to campus services, academic matters, and student activities.
            </p>

        </div>

        <!-- Main Features -->
        <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm">

            <div class="px-5 pt-4 pb-2">
                <p class="text-[11px] font-bold tracking-wider uppercase text-indigo-600">Main features</p>
            </div>

            <div class="divide-y divide-slate-100">

                <div class="flex items-center gap-4 px-5 py-4">

                    <div class="w-11 h-11 rounded-2xl bg-indigo-50 flex items-center justify-center">

                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 10h8m-8 4h5M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4-.8L3 20l1.1-3.3A7.938 7.938 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>

                    </div>

                    <div>
                        <p class="font-semibold text-slate-900">AI Chatbot</p>
                        <p class="text-sm text-slate-500">Ask campus-related questions anytime</p>
                    </div>

                </div>

                <div class="flex items-center gap-4 px-5 py-4">

                    <div class="w-11 h-11 rounded-2xl bg-indigo-50 flex items-center justify-center">

                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5s3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.253"/>
                        </svg>

                    </div>

                    <div>
                        <p class="font-semibold text-slate-900">Knowledge Base</p>
                        <p class="text-sm text-slate-500">Accurate information from campus sources</p>
                    </div>

                </div>

                <div class="flex items-center gap-4 px-5 py-4">

                    <div class="w-11 h-11 rounded-2xl bg-indigo-50 flex items-center justify-center">

                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 2l7 4v6c0 5-3.5 9-7 10-3.5-1-7-5-7-10V6l7-4z"/>
                        </svg>

                    </div>

                    <div>
                        <p class="font-semibold text-slate-900">Privacy & Security</p>
                        <p class="text-sm text-slate-500">Control your data and account safety</p>
                    </div>

                </div>

            </div>

        </div>

        <!-- Project Information -->
        <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm">

            <div class="px-5 pt-4 pb-2">
                <p class="text-[11px] font-bold tracking-wider uppercase text-indigo-600">Project information</p>
            </div>

            <div class="divide-y divide-slate-100">

                <div class="flex items-center justify-between px-5 py-4">
                    <span class="text-slate-600">Developed by</span>
                    <span class="font-medium text-slate-900">Final Year Project Team</span>
                </div>

                <div class="flex items-center justify-between px-5 py-4">
                    <span class="text-slate-600">Department</span>
                    <span class="font-medium text-slate-900">JTMK</span>
                </div>

                <div class="flex items-center justify-between px-5 py-4">
                    <span class="text-slate-600">Platform</span>
                    <span class="font-medium text-slate-900">Laravel Web Application</span>
                </div>

                <div class="flex items-center justify-between px-5 py-4">
                    <span class="text-slate-600">Release</span>
                    <span class="font-medium text-slate-900">2026</span>
                </div>

            </div>

        </div>

        <!-- Footer -->
        <div class="text-center py-2">
            <p class="text-sm font-medium text-slate-700">Made for students</p>
            <p class="text-xs text-slate-400 mt-1">© 2026 RakanKampus</p>
        </div>

    </div>

</body>
</html>