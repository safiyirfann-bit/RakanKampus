<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help & Support</title>
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
            <h1 class="text-2xl font-bold text-slate-900">Help & Support</h1>
            <p class="text-sm text-slate-500">Get help and report issues</p>
        </div>

    </div>

    <div class="max-w-md mx-auto p-5 space-y-5">

        <!-- FAQ -->
        <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm">

            <div class="px-5 pt-4 pb-2">
                <p class="text-[11px] font-bold tracking-wider uppercase text-indigo-600">Frequently asked questions</p>
            </div>

            <div class="divide-y divide-slate-100">

                <details class="group px-5 py-4">
                    <summary class="flex items-center justify-between cursor-pointer list-none">
                        <span class="font-medium text-slate-900">How do I use the AI chatbot?</span>
                        <span class="text-slate-400 group-open:rotate-180 transition">⌄</span>
                    </summary>
                    <p class="mt-3 text-sm leading-6 text-slate-500">
                        Open the chat page, type your question, and the chatbot will answer based on campus information provided by the system.
                    </p>
                </details>

                <details class="group px-5 py-4">
                    <summary class="flex items-center justify-between cursor-pointer list-none">
                        <span class="font-medium text-slate-900">How can I change my password?</span>
                        <span class="text-slate-400 group-open:rotate-180 transition">⌄</span>
                    </summary>
                    <p class="mt-3 text-sm leading-6 text-slate-500">
                        Go to Profile & Settings → Change Password and enter your current and new password.
                    </p>
                </details>

                <details class="group px-5 py-4">
                    <summary class="flex items-center justify-between cursor-pointer list-none">
                        <span class="font-medium text-slate-900">How do I clear chat history?</span>
                        <span class="text-slate-400 group-open:rotate-180 transition">⌄</span>
                    </summary>
                    <p class="mt-3 text-sm leading-6 text-slate-500">
                        Open Privacy Settings and choose “Clear chat history” to remove saved conversations.
                    </p>
                </details>

                <details class="group px-5 py-4">
                    <summary class="flex items-center justify-between cursor-pointer list-none">
                        <span class="font-medium text-slate-900">Where does the chatbot get information?</span>
                        <span class="text-slate-400 group-open:rotate-180 transition">⌄</span>
                    </summary>
                    <p class="mt-3 text-sm leading-6 text-slate-500">
                        The chatbot uses information stored in the RakanKampus knowledge base and campus guideline dataset.
                    </p>
                </details>

            </div>

        </div>

        <!-- Report a Problem -->
        <div class="bg-white rounded-3xl border border-slate-200 p-5 shadow-sm space-y-4">

            <div>
                <p class="text-[11px] font-bold tracking-wider uppercase text-indigo-600 mb-1">Report a problem</p>
                <h3 class="font-semibold text-slate-900">Tell us what happened</h3>
                <p class="text-sm text-slate-500 mt-1">
                    Submit a technical issue or feedback about the system.
                </p>
            </div>

            <div class="space-y-2">
                <label class="text-sm font-medium text-slate-700">Issue type</label>
                <select class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none">
                    <option>Chatbot issue</option>
                    <option>Login problem</option>
                    <option>Profile problem</option>
                    <option>Other</option>
                </select>
            </div>

            <div class="space-y-2">
                <label class="text-sm font-medium text-slate-700">Describe the issue</label>
                <textarea rows="5"
                          class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none resize-none"
                          placeholder="Explain what happened and when it occurred..."></textarea>
            </div>

            <button class="w-full rounded-2xl bg-indigo-600 py-3.5 text-base font-semibold text-white shadow-lg shadow-indigo-200 hover:bg-indigo-700 transition duration-200">
                Submit Report
            </button>

        </div>

        <!-- App Information -->
        <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm">

            <div class="px-5 pt-4 pb-2">
                <p class="text-[11px] font-bold tracking-wider uppercase text-indigo-600">Application information</p>
            </div>

            <div class="divide-y divide-slate-100">

                <div class="flex items-center justify-between px-5 py-4">
                    <span class="text-slate-600">Version</span>
                    <span class="font-medium text-slate-900">1.0.0</span>
                </div>

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
                    <span class="font-medium text-slate-900">Laravel Web App</span>
                </div>

            </div>

        </div>

    </div>

</body>
</html>