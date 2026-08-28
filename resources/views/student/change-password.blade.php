<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
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

        <h1 class="text-2xl font-bold text-indigo-900">Change Password</h1>

    </div>

    <div class="max-w-2xl mx-auto px-6 py-8">

        @if(session('success'))
            <div class="mb-4 rounded-xl bg-green-50 text-green-700 px-4 py-3 text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 rounded-xl bg-red-50 text-red-700 px-4 py-3 text-sm">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Security Card -->
        <div class="bg-white rounded-3xl border border-indigo-100 p-6 shadow-sm mb-6">

            <div class="flex items-center gap-4 mb-4">

                <div class="w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center">

                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2h-1V9a5 5 0 00-10 0v2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                    </svg>

                </div>

                <div>
                    <h2 class="text-lg font-bold text-indigo-900">Update Your Password</h2>
                    <p class="text-sm text-indigo-400">Choose a strong password to keep your account secure.</p>
                </div>

            </div>

            <!-- Password Strength -->
            <div class="mt-4">

                <div class="flex items-center justify-between text-sm mb-2">
                    <span class="text-indigo-500 font-medium">Password Strength</span>
                    <span class="font-semibold" id="strengthLabel" style="color:#94a3b8;">-</span>
                </div>

                <div class="w-full h-2 bg-indigo-100 rounded-full overflow-hidden">
                    <div class="h-full w-0 rounded-full transition-all duration-300" id="strengthBar" style="background:#94a3b8;"></div>
                </div>

            </div>

        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('student.profile.password.update') }}" class="space-y-6" id="changePasswordForm">
            @csrf
            @method('PUT')

            <!-- Current Password -->
            <div>

                <label class="block text-sm font-bold tracking-wide uppercase text-indigo-500 mb-3">
                    Current Password
                </label>

                <div class="relative">

                    <input type="password" name="current_password" id="current_password" placeholder="Enter current password"
                           class="password-field w-full rounded-2xl border border-indigo-200 bg-white px-5 py-4 pr-14 text-lg text-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent transition">

                    <button type="button" onclick="toggleVisibility('current_password', this)" class="absolute right-4 top-1/2 -translate-y-1/2 text-indigo-400 hover:text-indigo-600">

                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>

                    </button>

                </div>

            </div>

            <!-- New Password -->
            <div>

                <label class="block text-sm font-bold tracking-wide uppercase text-indigo-500 mb-3">
                    New Password
                </label>

                <div class="relative">

                    <input type="password" name="password" id="password" placeholder="Enter new password" oninput="checkPassword()"
                           class="password-field w-full rounded-2xl border border-indigo-200 bg-white px-5 py-4 pr-14 text-lg text-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent transition">

                    <button type="button" onclick="toggleVisibility('password', this)" class="absolute right-4 top-1/2 -translate-y-1/2 text-indigo-400 hover:text-indigo-600">

                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>

                    </button>

                </div>

                <!-- Password Tips -->
                <div class="mt-3 space-y-2 text-sm">

                    <div class="flex items-center gap-2 req-item" id="req-length">
                        <span class="req-icon">✗</span>
                        <span>At least 6 characters</span>
                    </div>

                    <div class="flex items-center gap-2 req-item" id="req-case">
                        <span class="req-icon">✗</span>
                        <span>Contains uppercase and lowercase letters</span>
                    </div>

                    <div class="flex items-center gap-2 req-item" id="req-numsym">
                        <span class="req-icon">✗</span>
                        <span>Includes a number and special character</span>
                    </div>

                </div>

                <p id="passwordError" class="text-red-500 text-sm mt-2 hidden"></p>

            </div>

            <!-- Confirm Password -->
            <div>

                <label class="block text-sm font-bold tracking-wide uppercase text-indigo-500 mb-3">
                    Confirm New Password
                </label>

                <div class="relative">

                    <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Re-enter new password" oninput="clearError()"
                           class="password-field w-full rounded-2xl border border-indigo-200 bg-white px-5 py-4 pr-14 text-lg text-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent transition">

                    <button type="button" onclick="toggleVisibility('password_confirmation', this)" class="absolute right-4 top-1/2 -translate-y-1/2 text-indigo-400 hover:text-indigo-600">

                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>

                    </button>

                </div>

            </div>

            <!-- Save Button -->
            <button type="submit"
                    class="w-full rounded-2xl bg-indigo-600 py-4 text-lg font-bold text-white shadow-lg shadow-indigo-200 hover:bg-indigo-700 transition duration-200">

                Update Password

            </button>

        </form>

    </div>

    <style>
        .req-item {
            color: #94a3b8;
            transition: color 0.2s ease;
        }
        .req-item.valid {
            color: #60a5fa;
        }
        .req-item .req-icon {
            width: 16px;
            display: inline-block;
        }
    </style>

    <script>
        function checkPassword() {
            const password = document.getElementById('password').value;

            const checks = {
                length: password.length >= 6,
                case:   /[A-Z]/.test(password) && /[a-z]/.test(password),
                numsym: /[0-9]/.test(password) && /[!@#$%^&*(),.?":{}|<>_\-+=]/.test(password),
            };

            toggleReq('req-length', checks.length);
            toggleReq('req-case', checks.case);
            toggleReq('req-numsym', checks.numsym);

            const score = Object.values(checks).filter(Boolean).length;

            const bar = document.getElementById('strengthBar');
            const label = document.getElementById('strengthLabel');

            let percent = 0;
            let text = '-';
            let color = '#94a3b8';

            if (password.length === 0) {
                percent = 0; text = '-'; color = '#94a3b8';
            } else if (score === 1) {
                percent = 33; text = 'Weak'; color = '#ef4444';
            } else if (score === 2) {
                percent = 66; text = 'Fair'; color = '#f59e0b';
            } else if (score === 3) {
                percent = 100; text = 'Strong'; color = '#22c55e';
            } else {
                percent = 15; text = 'Weak'; color = '#ef4444';
            }

            bar.style.width = percent + '%';
            bar.style.background = color;
            label.textContent = text;
            label.style.color = color;
        }

        function toggleReq(id, valid) {
            const el = document.getElementById(id);
            const icon = el.querySelector('.req-icon');
            if (valid) {
                el.classList.add('valid');
                icon.textContent = '✓';
            } else {
                el.classList.remove('valid');
                icon.textContent = '✗';
            }
        }

        function toggleVisibility(inputId, btn) {
            const input = document.getElementById(inputId);
            input.type = input.type === 'password' ? 'text' : 'password';
        }

        function clearError() {
            document.getElementById('passwordError').classList.add('hidden');
        }

        document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('password_confirmation').value;
            const errorEl = document.getElementById('passwordError');

            const checks = {
                length: password.length >= 6,
                case:   /[A-Z]/.test(password) && /[a-z]/.test(password),
                numsym: /[0-9]/.test(password) && /[!@#$%^&*(),.?":{}|<>_\-+=]/.test(password),
            };

            const allValid = Object.values(checks).every(Boolean);

            if (!allValid) {
                e.preventDefault();
                errorEl.textContent = 'Please fulfill all password requirements above.';
                errorEl.classList.remove('hidden');
                document.getElementById('password').scrollIntoView({ behavior: 'smooth', block: 'center' });
                return;
            }

            if (password !== confirmPassword) {
                e.preventDefault();
                errorEl.textContent = 'Passwords do not match.';
                errorEl.classList.remove('hidden');
                document.getElementById('password_confirmation').scrollIntoView({ behavior: 'smooth', block: 'center' });
                return;
            }

            errorEl.classList.add('hidden');
        });
    </script>

</body>
</html>