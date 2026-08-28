<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notification Settings</title>
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
            <h1 class="text-2xl font-bold text-indigo-900">Notification Settings</h1>
            <p class="text-sm text-indigo-400">Manage how RakanKampus keeps you informed</p>
        </div>

    </div>

    <div class="max-w-2xl mx-auto px-6 py-6 space-y-6">

        @if(session('success'))
            <div class="rounded-xl bg-green-50 text-green-700 px-4 py-3 text-sm">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('student.profile.notifications.update') }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Academic -->
            <div class="bg-white rounded-3xl border border-indigo-100 overflow-hidden shadow-sm">

                <div class="px-5 pt-5 pb-3">
                    <p class="text-[11px] font-bold tracking-wider uppercase text-indigo-500">Academic</p>
                </div>

                <div class="divide-y divide-indigo-50">

                    <!-- Course Announcements -->
                    <div class="flex items-center justify-between px-5 py-4">

                        <div class="flex items-center gap-4">

                            <div class="w-11 h-11 rounded-2xl bg-indigo-50 flex items-center justify-center">

                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 13V7a2 2 0 00-2-2h-1V3H8v2H7a2 2 0 00-2 2v6l-2 2v1h18v-1l-2-2z"/>
                                </svg>

                            </div>

                            <div>
                                <p class="font-semibold text-indigo-900">Course Announcements</p>
                                <p class="text-sm text-indigo-400">Receive lecturer and course updates</p>
                            </div>

                        </div>

                        <label class="relative inline-flex items-center cursor-pointer">

                            <input type="checkbox" name="course_announcements" value="1" {{ $settings['course_announcements'] ? 'checked' : '' }} class="sr-only peer">

                            <div class="w-11 h-6 bg-indigo-200 peer-focus:outline-none rounded-full peer peer-checked:bg-indigo-600 transition"></div>

                            <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition peer-checked:translate-x-5"></div>

                        </label>

                    </div>

                    <!-- Exam Alerts -->
                    <div class="flex items-center justify-between px-5 py-4">

                        <div class="flex items-center gap-4">

                            <div class="w-11 h-11 rounded-2xl bg-indigo-50 flex items-center justify-center">

                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10m-11 9h12a2 2 0 002-2V7a2 2 0 00-2-2H6a2 2 0 00-2 2v11a2 2 0 002 2z"/>
                                </svg>

                            </div>

                            <div>
                                <p class="font-semibold text-indigo-900">Exam Alerts</p>
                                <p class="text-sm text-indigo-400">Timetable and reminder notifications</p>
                            </div>

                        </div>

                        <label class="relative inline-flex items-center cursor-pointer">

                            <input type="checkbox" name="exam_alerts" value="1" {{ $settings['exam_alerts'] ? 'checked' : '' }} class="sr-only peer">

                            <div class="w-11 h-6 bg-indigo-200 rounded-full peer peer-checked:bg-indigo-600 transition"></div>

                            <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition peer-checked:translate-x-5"></div>

                        </label>

                    </div>

                    <!-- Fee Reminders -->
                    <div class="flex items-center justify-between px-5 py-4">

                        <div class="flex items-center gap-4">

                            <div class="w-11 h-11 rounded-2xl bg-indigo-50 flex items-center justify-center">

                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-2.2 0-4 1.3-4 3s1.8 3 4 3 4 1.3 4 3-1.8 3-4 3m0-12V6m0 2v12"/>
                                </svg>

                            </div>

                            <div>
                                <p class="font-semibold text-indigo-900">Fee Payment Reminders</p>
                                <p class="text-sm text-indigo-400">Get reminded before payment deadlines</p>
                            </div>

                        </div>

                        <label class="relative inline-flex items-center cursor-pointer">

                            <input type="checkbox" name="fee_reminders" value="1" {{ $settings['fee_reminders'] ? 'checked' : '' }} class="sr-only peer">

                            <div class="w-11 h-6 bg-indigo-200 rounded-full peer peer-checked:bg-indigo-600 transition"></div>

                            <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition peer-checked:translate-x-5"></div>

                        </label>

                    </div>

                </div>

            </div>

            <!-- RakanKampus -->
            <div class="bg-white rounded-3xl border border-indigo-100 overflow-hidden shadow-sm">

                <div class="px-5 pt-5 pb-3">
                    <p class="text-[11px] font-bold tracking-wider uppercase text-indigo-500">RakanKampus</p>
                </div>

                <div class="divide-y divide-indigo-50">

                    <!-- Chatbot Replies -->
                    <div class="flex items-center justify-between px-5 py-4">

                        <div class="flex items-center gap-4">

                            <div class="w-11 h-11 rounded-2xl bg-indigo-50 flex items-center justify-center">

                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h8M8 14h5M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4-.8L3 20l1.1-3.3A7.938 7.938 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>

                            </div>

                            <div>
                                <p class="font-semibold text-indigo-900">Chatbot Replies</p>
                                <p class="text-sm text-indigo-400">Notify when new AI responses arrive</p>
                            </div>

                        </div>

                        <label class="relative inline-flex items-center cursor-pointer">

                            <input type="checkbox" name="chatbot_replies" value="1" {{ $settings['chatbot_replies'] ? 'checked' : '' }} class="sr-only peer">

                            <div class="w-11 h-6 bg-indigo-200 rounded-full peer peer-checked:bg-indigo-600 transition"></div>

                            <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition peer-checked:translate-x-5"></div>

                        </label>

                    </div>

                    <!-- System Updates -->
                    <div class="flex items-center justify-between px-5 py-4">

                        <div class="flex items-center gap-4">

                            <div class="w-11 h-11 rounded-2xl bg-indigo-50 flex items-center justify-center">

                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20 10 10 0 000-20z"/>
                                </svg>

                            </div>

                            <div>
                                <p class="font-semibold text-indigo-900">System Updates</p>
                                <p class="text-sm text-indigo-400">New features and maintenance alerts</p>
                            </div>

                        </div>

                        <label class="relative inline-flex items-center cursor-pointer">

                            <input type="checkbox" name="system_updates" value="1" {{ $settings['system_updates'] ? 'checked' : '' }} class="sr-only peer">

                            <div class="w-11 h-6 bg-indigo-200 rounded-full peer peer-checked:bg-indigo-600 transition"></div>

                            <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition peer-checked:translate-x-5"></div>

                        </label>

                    </div>

                    <!-- Events -->
                    <div class="flex items-center justify-between px-5 py-4">

                        <div class="flex items-center gap-4">

                            <div class="w-11 h-11 rounded-2xl bg-indigo-50 flex items-center justify-center">

                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10m-11 9h12a2 2 0 002-2V7a2 2 0 00-2-2H6a2 2 0 00-2 2v11a2 2 0 002 2z"/>
                                </svg>

                            </div>

                            <div>
                                <p class="font-semibold text-indigo-900">Events & Promotions</p>
                                <p class="text-sm text-indigo-400">Campus activities and special announcements</p>
                            </div>

                        </div>

                        <label class="relative inline-flex items-center cursor-pointer">

                            <input type="checkbox" name="events_promotions" value="1" {{ $settings['events_promotions'] ? 'checked' : '' }} class="sr-only peer">

                            <div class="w-11 h-6 bg-indigo-200 rounded-full peer peer-checked:bg-indigo-600 transition"></div>

                            <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition peer-checked:translate-x-5"></div>

                        </label>

                    </div>

                </div>

            </div>

            <!-- Delivery -->
            <div class="bg-white rounded-3xl border border-indigo-100 p-5 shadow-sm">

                <div class="mb-4">
                    <p class="text-[11px] font-bold tracking-wider uppercase text-indigo-500">Delivery</p>
                </div>

                <div class="space-y-5">

                    <div>
                        <label class="block text-sm font-semibold text-indigo-900 mb-2">Preferred Method</label>

                        <select name="preferred_method" class="w-full rounded-2xl border border-indigo-200 bg-white px-4 py-3 text-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-400">

                            <option value="Push notifications" {{ $settings['preferred_method'] === 'Push notifications' ? 'selected' : '' }}>Push notifications</option>
                            <option value="Email only" {{ $settings['preferred_method'] === 'Email only' ? 'selected' : '' }}>Email only</option>
                            <option value="Push + Email" {{ $settings['preferred_method'] === 'Push + Email' ? 'selected' : '' }}>Push + Email</option>

                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-indigo-900 mb-2">Do Not Disturb Until</label>

                        <input type="datetime-local" name="dnd_until" value="{{ $settings['dnd_until'] }}"
                               class="w-full rounded-2xl border border-indigo-200 bg-white px-4 py-3 text-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    </div>

                </div>

            </div>

            <!-- Save -->
            <button type="submit" class="w-full rounded-2xl bg-indigo-600 py-4 text-lg font-bold text-white shadow-lg shadow-indigo-200 hover:bg-indigo-700 transition duration-200">

                Save Notification Settings

            </button>

        </form>

    </div>

</body>
</html>