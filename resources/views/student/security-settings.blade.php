<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Settings</title>
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
            <h1 class="text-2xl font-bold text-indigo-900">Security</h1>
            <p class="text-sm text-indigo-400">Manage your account protection</p>
        </div>

    </div>

    <div class="max-w-md mx-auto p-5 space-y-5">

        <!-- Security Status -->
        <div class="bg-white rounded-3xl border border-indigo-100 p-5 shadow-sm">

            <div class="flex items-center gap-4 mb-4">

                <div class="w-14 h-14 rounded-2xl bg-indigo-50 flex items-center justify-center">

                    <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 2l7 4v6c0 5-3.5 9-7 10-3.5-1-7-5-7-10V6l7-4z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4"/>
                    </svg>

                </div>

                <div class="flex-1">
                    <h2 class="font-bold text-indigo-900">Account Protected</h2>
                    <p class="text-sm text-indigo-400">Your security settings are up to date</p>
                </div>

            </div>

            <div class="w-full bg-indigo-100 rounded-full h-2 overflow-hidden">
                <div class="bg-indigo-600 h-2 rounded-full w-[88%]"></div>
            </div>

            <div class="flex items-center justify-between mt-3 text-sm">
                <span class="text-indigo-500">Security score</span>
                <span class="font-semibold text-indigo-700">88%</span>
            </div>

        </div>

        <!-- Protection -->
        <div class="bg-white rounded-3xl border border-indigo-100 overflow-hidden shadow-sm">

            <div class="px-5 pt-4 pb-2">
                <p class="text-[11px] font-bold tracking-wider uppercase text-indigo-500">Protection</p>
            </div>

            <div class="divide-y divide-indigo-50">

                <!-- 2FA -->
                <div class="flex items-center justify-between px-5 py-4">

                    <div class="flex items-center gap-4">

                        <div class="w-11 h-11 rounded-2xl bg-indigo-50 flex items-center justify-center">

                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2h-1V9a5 5 0 00-10 0v2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                            </svg>

                        </div>

                        <div>
                            <p class="font-semibold text-indigo-900">Two-Factor Authentication</p>
                            <p class="text-sm text-indigo-400">Extra verification when signing in</p>
                        </div>

                    </div>

                    <button class="relative w-11 h-6 bg-indigo-600 rounded-full">
                        <span class="absolute top-1 right-1 w-4 h-4 bg-white rounded-full"></span>
                    </button>

                </div>

                <!-- Biometric -->
                <div class="flex items-center justify-between px-5 py-4">

                    <div class="flex items-center gap-4">

                        <div class="w-11 h-11 rounded-2xl bg-indigo-50 flex items-center justify-center">

                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 11c1.657 0 3-1.343 3-3S13.657 5 12 5 9 6.343 9 8s1.343 3 3 3zm0 2c-2.21 0-4 1.79-4 4v2h8v-2c0-2.21-1.79-4-4-4z"/>
                            </svg>

                        </div>

                        <div>
                            <p class="font-semibold text-indigo-900">Biometric Login</p>
                            <p class="text-sm text-indigo-400">Use fingerprint or Face ID</p>
                        </div>

                    </div>

                    <button class="relative w-11 h-6 bg-indigo-600 rounded-full">
                        <span class="absolute top-1 right-1 w-4 h-4 bg-white rounded-full"></span>
                    </button>

                </div>

                <!-- Login Alerts -->
                <div class="flex items-center justify-between px-5 py-4">

                    <div class="flex items-center gap-4">

                        <div class="w-11 h-11 rounded-2xl bg-indigo-50 flex items-center justify-center">

                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2c0 .5-.2 1-.6 1.4L4 17h5m6 0a3 3 0 11-6 0m6 0H9"/>
                            </svg>

                        </div>

                        <div>
                            <p class="font-semibold text-indigo-900">Login Alerts</p>
                            <p class="text-sm text-indigo-400">Get notified about new sign-ins</p>
                        </div>

                    </div>

                    <button class="relative w-11 h-6 bg-indigo-600 rounded-full">
                        <span class="absolute top-1 right-1 w-4 h-4 bg-white rounded-full"></span>
                    </button>

                </div>

            </div>

        </div>

        <!-- Active Device -->
        <div class="bg-white rounded-3xl border border-indigo-100 p-5 shadow-sm">

            <div class="flex items-center justify-between mb-4">

                <div>
                    <h3 class="font-bold text-indigo-900">Active Device</h3>
                    <p class="text-sm text-indigo-400">This device is currently signed in</p>
                </div>

                <span class="text-xs font-semibold text-green-600 bg-green-50 rounded-full px-3 py-1">Active</span>

            </div>

            <div class="border border-indigo-100 rounded-2xl p-4 flex items-center gap-4">

                <div class="w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center">

                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9.75 17L8 21l4-1 4 1-1.75-4M4 4h16v11H4z"/>
                    </svg>

                </div>

                <div class="flex-1">
                    <p class="font-semibold text-indigo-900">Windows 11 • Chrome</p>
                    <p class="text-sm text-indigo-400">Ipoh, Perak • Just now</p>
                </div>

            </div>

        </div>

        <!-- Security Activity -->
        <div class="bg-white rounded-3xl border border-indigo-100 p-5 shadow-sm">

            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-indigo-900">Recent Activity</h3>
                <button class="text-sm text-indigo-500 font-medium">View all</button>
            </div>

            <div class="space-y-4">

                <div class="flex items-start gap-3">

                    <div class="w-9 h-9 rounded-full bg-green-50 flex items-center justify-center mt-0.5">

                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M5 13l4 4L19 7"/>
                        </svg>

                    </div>

                    <div class="flex-1">
                        <p class="font-medium text-indigo-900">Successful login</p>
                        <p class="text-sm text-indigo-400">Windows 11 • Chrome</p>
                    </div>

                    <span class="text-xs text-indigo-400 mt-1">Now</span>

                </div>

                <div class="flex items-start gap-3">

                    <div class="w-9 h-9 rounded-full bg-indigo-50 flex items-center justify-center mt-0.5">

                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 8v4l3 3"/>
                        </svg>

                    </div>

                    <div class="flex-1">
                        <p class="font-medium text-indigo-900">Password changed</p>
                        <p class="text-sm text-indigo-400">Security settings updated</p>
                    </div>

                    <span class="text-xs text-indigo-400 mt-1">2d</span>

                </div>

            </div>

        </div>

        <!-- Logout Other Devices -->
        <div class="bg-white rounded-3xl border border-red-100 p-5 shadow-sm">

            <div class="flex items-center gap-3 mb-3">

                <div class="w-11 h-11 rounded-2xl bg-red-50 flex items-center justify-center">

                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 16l4-4m0 0l-4-4m4 4H9m4 4v1a2 2 0 01-2 2H6a2 2 0 01-2-2V7a2 2 0 012-2h5a2 2 0 012 2v1"/>
                    </svg>

                </div>

                <div>
                    <h3 class="font-bold text-red-600">Log Out Other Devices</h3>
                    <p class="text-sm text-red-400">Keep this device signed in</p>
                </div>

            </div>

            <button class="w-full rounded-2xl border border-red-200 py-3 font-semibold text-red-600 hover:bg-red-50 transition">
                Log Out Other Devices
            </button>

        </div>

    </div>

</body>
</html>