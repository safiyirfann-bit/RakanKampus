<!DOCTYPE html>
<html lang="ms">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile & Settings</title>
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
        .menu-row { transition: background-color 0.15s ease, transform 0.15s ease; }
        .menu-row:hover { transform: translateX(2px); }
        .menu-row .chevron { transition: transform 0.15s ease; display: inline-block; }
        .menu-row:hover .chevron { transform: translateX(3px); }
        header, .relative, .max-w-md { position: relative; z-index: 1; }
    
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
<div class="bg-white/90 backdrop-blur border-b border-indigo-100 px-6 py-4 flex items-center gap-3 relative z-10">

    <a href="{{ route('student.home') }}"
       class="w-10 h-10 rounded-full hover:bg-indigo-50 flex items-center justify-center text-indigo-500 transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
    </a>

    <div>
        <h1 class="font-bold text-indigo-900">Profile & Settings</h1>
        <p class="text-sm text-indigo-400">Manage your student account</p>
    </div>

</div>

    <div class="max-w-md mx-auto p-5 space-y-4 relative z-10">

<!-- Profile Card -->
<div class="bg-white rounded-3xl border border-indigo-100 p-5 flex items-center gap-4 shadow-sm fade-up">

    <div class="w-14 h-14 rounded-full text-white flex items-center justify-center font-bold text-lg overflow-hidden" style="background: linear-gradient(135deg, #f472b6, #fb923c);">
        @if($user->photo)
            <img src="{{ Storage::url($user->photo) }}" class="w-full h-full object-cover" alt="Profile photo">
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

    @if($user->faculty)
        <span class="inline-block mt-2 text-[11px] font-semibold text-indigo-600 bg-indigo-50 rounded-full px-3 py-1">
            {{ $user->faculty }}
        </span>
    @endif
</div>

</div>

        <!-- Account -->
        <div class="bg-white rounded-3xl border border-indigo-100 overflow-hidden shadow-sm fade-up" style="animation-delay: 0.08s;">

    <div class="px-5 pt-4 pb-2">
        <p class="text-[11px] font-bold tracking-wider uppercase text-indigo-500">Account</p>
    </div>

    <div class="divide-y divide-indigo-50">

        <a href="{{ route('student.profile.edit') }}" class="menu-row flex items-center justify-between px-5 py-4 hover:bg-indigo-50">
            <div class="flex items-center gap-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5h2m-1-1v2m-7 9l9-9 4 4-9 9H5v-4z"/>
                </svg>
                <span class="font-medium text-indigo-900">Edit Profile</span>
            </div>
            <span class="text-indigo-300 chevron">›</span>
        </a>

        <a href="{{ route('student.profile.password') }}" class="menu-row flex items-center justify-between px-5 py-4 hover:bg-indigo-50">
            <div class="flex items-center gap-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2h-1V9a5 5 0 00-10 0v2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                </svg>
                <span class="font-medium text-indigo-900">Change Password</span>
            </div>
            <span class="text-indigo-300 chevron">›</span>
        </a>

        <a href="{{ route('student.profile.notifications') }}" class="menu-row flex items-center justify-between px-5 py-4 hover:bg-indigo-50">
            <div class="flex items-center gap-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2c0 .5-.2 1-.6 1.4L4 17h5m6 0a3 3 0 11-6 0m6 0H9"/>
                </svg>
                <span class="font-medium text-indigo-900">Notification Settings</span>
            </div>
            <span class="text-indigo-300 chevron">›</span>
        </a>

        <a href="#" class="menu-row flex items-center justify-between px-5 py-4 hover:bg-indigo-50">
            <div class="flex items-center gap-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1 13l-2 2m0 0l-2-2m2 2V9m11-4h-4m2-2v4"/>
                </svg>
                <span class="font-medium text-indigo-900">Language & Region</span>
            </div>
            <span class="text-indigo-300 chevron">›</span>
        </a>

    </div>
</div>

<!-- Preferences -->
<div class="bg-white rounded-3xl border border-indigo-100 overflow-hidden shadow-sm fade-up" style="animation-delay: 0.16s;">

    <div class="px-5 pt-4 pb-2">
        <p class="text-[11px] font-bold tracking-wider uppercase text-indigo-500">Preferences</p>
    </div>

    <div class="divide-y divide-indigo-50">

        
        <!-- Privacy Settings -->
        <a href="{{ route('student.profile.privacy') }}"
           class="menu-row flex items-center justify-between px-5 py-4 hover:bg-indigo-50">

            <div class="flex items-center gap-4">

                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 2l7 4v5c0 5-3.5 9.7-7 11-3.5-1.3-7-6-7-11V6l7-4z"/>
                </svg>

                <div>
    <p class="font-medium text-indigo-900">Privacy Settings</p>
    <p class="text-sm text-indigo-400">Control your personal data and visibility</p>
</div>

            </div>

            <span class="text-indigo-300 text-lg chevron">›</span>

        </a>

        <!-- Security -->
        <a href="{{ route('student.profile.security') }}"
           class="menu-row flex items-center justify-between px-5 py-4 hover:bg-indigo-50">

            <div class="flex items-center gap-4">

                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 2l7 4v6c0 5-3.5 9-7 10-3.5-1-7-5-7-10V6l7-4z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4"/>
                </svg>

                <div>
    <p class="font-medium text-indigo-900">Security</p>
    <p class="text-sm text-indigo-400">Protect your account and password</p>
</div>

            </div>

            <span class="text-indigo-300 text-lg chevron">›</span>

        </a>

       <button onclick="openFeedbackModal()"
    class="menu-row w-full flex items-center justify-between px-5 py-4 hover:bg-indigo-50 text-left border-t border-indigo-100">

    <div class="flex items-center gap-4">

        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M8 10h8m-8 4h5M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4-.8L3 20l1.1-3.3A7.938 7.938 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
        </svg>

        <div>
            <p class="font-medium text-indigo-900">Feedback & feature requests</p>
            <p class="text-sm text-indigo-400">Tell us what we can improve</p>
        </div>

    </div>

    <span class="text-indigo-300 text-lg chevron">›</span>

</button>

        <!-- Help -->
        <a href="{{ route('student.help-support') }}"
           class="menu-row flex items-center justify-between px-5 py-4 hover:bg-indigo-50">

            <div class="flex items-center gap-4">

                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M18.364 5.636A9 9 0 105.636 18.364 9 9 0 0018.364 5.636zM12 8v4m0 4h.01"/>
                </svg>

                <div>
    <p class="font-medium text-indigo-900">Help & Support</p>
    <p class="text-sm text-indigo-400">Get assistance and contact support</p>
</div>

            </div>

            <span class="text-indigo-300 text-lg chevron">›</span>

        </a>

        <!-- About -->
        <a href="{{ route('student.about') }}"
           class="menu-row flex items-center justify-between px-5 py-4 hover:bg-indigo-50">

            <div class="flex items-center gap-4">

                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20 10 10 0 000-20z"/>
                </svg>

               <div>
    <p class="font-medium text-indigo-900">About RakanKampus</p>
    <p class="text-sm text-indigo-400">App version and project information</p>
</div>
            </div>

            <span class="text-indigo-300 text-lg chevron">›</span>

        </a>

    </div>

</div>

        <!-- Logout Button -->
<div class="bg-white rounded-3xl border border-indigo-100 overflow-hidden shadow-sm fade-up" style="animation-delay: 0.22s;">

    <button type="button"
            onclick="openLogoutModal()"
            class="menu-row w-full flex items-center justify-between px-5 py-4 hover:bg-indigo-50 text-left">

        <div class="flex items-center gap-4">

            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17 16l4-4m0 0l-4-4m4 4H9m4 4v1a2 2 0 01-2 2H6a2 2 0 01-2-2V7a2 2 0 012-2h5a2 2 0 012 2v1" />
            </svg>

            <div>
                <p class="font-medium text-indigo-900">Log Out</p>
                <p class="text-xs text-indigo-400 mt-1">Sign out of your RakanKampus account</p>
            </div>

        </div>

        <span class="text-indigo-300 text-lg chevron">›</span>

    </button>

</div>

<!-- Logout Confirmation Modal -->
<div id="logoutModal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50 p-4">

    <div class="bg-white w-full max-w-sm rounded-3xl border border-indigo-100 shadow-2xl animate-scale-in p-6">

        <div class="flex justify-center mb-4">

            <div class="w-16 h-16 rounded-full bg-red-50 flex items-center justify-center">

                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 16l4-4m0 0l-4-4m4 4H9m4 4v1a2 2 0 01-2 2H6a2 2 0 01-2-2V7a2 2 0 012-2h5a2 2 0 012 2v1" />
                </svg>

            </div>

        </div>

        <h2 class="text-xl font-bold text-center text-indigo-900 mb-2">
            Log out?
        </h2>

        <p class="text-sm text-indigo-400 text-center mb-6">
            Are you sure you want to sign out of your RakanKampus account?
        </p>

        <div class="flex gap-3">

            <button onclick="closeLogoutModal()"
                    class="flex-1 rounded-2xl border border-indigo-200 py-3 font-medium text-indigo-600 hover:bg-indigo-50 transition">
                Cancel
            </button>

            <form method="POST" action="{{ route('logout') }}" class="flex-1">
                @csrf
                <button type="submit"
                        class="w-full rounded-2xl bg-red-500 py-3 font-semibold text-white hover:bg-red-600 transition">
                    Log Out
                </button>
            </form>

        </div>

    </div>

</div>

<script>
function openLogoutModal() {
    const modal = document.getElementById('logoutModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeLogoutModal() {
    const modal = document.getElementById('logoutModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

document.getElementById('logoutModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeLogoutModal();
    }
});
</script>

<!-- Feedback Modal -->
<div id="feedbackModal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50 p-4">
    <div class="bg-white w-full max-w-lg rounded-3xl border border-indigo-100 shadow-2xl animate-scale-in">

        <form method="POST" action="{{ route('student.feedback.store') }}">
            @csrf

            <!-- Header -->
            <div class="flex items-start justify-between p-6 border-b border-indigo-100">
                <div>
                    <h2 class="text-2xl font-bold text-indigo-900">Help us improve</h2>
                    <p class="text-sm text-indigo-400 mt-1">
                        Share feedback or suggest a feature for RakanKampus.
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
                        Feedback
                    </label>
                    <textarea name="feedback" id="feedbackText" rows="5"
                        maxlength="4000"
                        placeholder="What could we do better?"
                        oninput="updateCounter('feedbackText', 'feedbackCount')"
                        class="w-full rounded-2xl border border-indigo-100 bg-indigo-50/50 px-4 py-4 text-indigo-900 placeholder-indigo-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none resize-none"></textarea>
                    <p class="text-right text-xs text-indigo-300 mt-2"><span id="feedbackCount">0</span>/4000</p>
                </div>

                <!-- Feature Request -->
                <div>
                    <label class="block text-sm font-semibold text-indigo-700 mb-2">
                        Feature request
                    </label>
                    <textarea name="feature_request" id="featureText" rows="5"
                        maxlength="4000"
                        placeholder="What would you like us to add?"
                        oninput="updateCounter('featureText', 'featureCount')"
                        class="w-full rounded-2xl border border-indigo-100 bg-indigo-50/50 px-4 py-4 text-indigo-900 placeholder-indigo-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none resize-none"></textarea>
                    <p class="text-right text-xs text-indigo-300 mt-2"><span id="featureCount">0</span>/4000</p>
                </div>
            </div>

            <!-- Footer -->
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-indigo-100">
                <button type="button" onclick="closeFeedbackModal()"
                    class="px-5 py-2.5 rounded-xl border border-indigo-200 text-indigo-600 hover:bg-indigo-50 transition font-medium">
                    Cancel
                </button>
                <button type="submit"
                    class="px-5 py-2.5 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 transition font-semibold shadow-lg shadow-indigo-200 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M5 12l14-7-4 14-3-5-5-2z"/>
                    </svg>
                    Send feedback
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

// tutup modal bila klik di luar kad modal
document.getElementById('feedbackModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeFeedbackModal();
    }
});
</script>

    </div>

</body>
</html>