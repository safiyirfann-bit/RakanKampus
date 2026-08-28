<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
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

    <h1 class="text-2xl font-bold text-indigo-900">Edit Profile</h1>

</div>

<div class="max-w-2xl mx-auto px-6 py-8">

    <!-- Avatar -->
    <div class="flex justify-center mb-8">

        <button type="button" onclick="openPhotoModal()" class="relative group">

            <div class="w-24 h-24 rounded-full bg-indigo-600 flex items-center justify-center text-white text-4xl font-bold shadow-lg overflow-hidden" id="avatarWrapper">
                @if($user->photo)
                    <img src="{{ Storage::url($user->photo) }}" class="w-full h-full object-cover" alt="Profile photo">
                @else
                    {{ strtoupper(substr($user->first_name ?? 'A', 0, 1) . substr($user->last_name ?? '', 0, 1)) }}
                @endif
            </div>

            <div class="absolute -bottom-1 -right-1 w-10 h-10 rounded-full bg-indigo-600 border-4 border-white flex items-center justify-center text-white shadow-lg group-hover:scale-105 transition">

                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 11l6.768-6.768a2.5 2.5 0 113.536 3.536L12.536 14.536A2 2 0 0111.121 15H9v-2a2 2 0 01.586-1.414z"/>
                </svg>

            </div>

        </button>

    </div>

    @if(session('success'))
        <div class="mb-4 rounded-xl bg-green-50 text-green-700 px-4 py-3 text-sm">
            {{ session('success') }}
        </div>
    @endif

    <!-- Form -->
    <form action="{{ route('student.profile.update') }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- First & Last Name -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            <div>
                <label class="block text-sm font-bold tracking-wide uppercase text-indigo-500 mb-3">
                    First Name
                </label>

                <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}"
                       class="w-full rounded-2xl border border-indigo-200 bg-white px-5 py-4 text-lg text-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent transition">
                @error('first_name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-bold tracking-wide uppercase text-indigo-500 mb-3">
                    Last Name
                </label>

                <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}"
                       class="w-full rounded-2xl border border-indigo-200 bg-white px-5 py-4 text-lg text-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent transition">
                @error('last_name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

        </div>

        <!-- Email -->
        <div>
            <label class="block text-sm font-bold tracking-wide uppercase text-indigo-500 mb-3">
                Email
            </label>

            <input type="email" name="email" value="{{ old('email', $user->email) }}"
                   class="w-full rounded-2xl border border-indigo-200 bg-white px-5 py-4 text-lg text-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent transition">
            @error('email')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Faculty -->
        <div>
            <label class="block text-sm font-bold tracking-wide uppercase text-indigo-500 mb-3">
                Faculty
            </label>

            <input type="text" name="faculty" value="{{ old('faculty', $user->faculty) }}"
                   class="w-full rounded-2xl border border-indigo-200 bg-white px-5 py-4 text-lg text-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent transition">
            @error('faculty')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Phone -->
        <div>
            <label class="block text-sm font-bold tracking-wide uppercase text-indigo-500 mb-3">
                Phone Number
            </label>

            <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}"
                   class="w-full rounded-2xl border border-indigo-200 bg-white px-5 py-4 text-lg text-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent transition">
            @error('phone')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Save Button -->
        <button type="submit"
                class="w-full rounded-2xl bg-indigo-600 py-4 text-lg font-bold text-white shadow-lg shadow-indigo-200 hover:bg-indigo-700 transition duration-200">

            Save Changes

        </button>

    </form>

</div>

<!-- Profile Photo Modal -->
<div id="photoModal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50 p-4">

    <div class="bg-white w-full max-w-md rounded-3xl border border-indigo-100 shadow-2xl animate-scale-in">

        <!-- Header -->
        <div class="flex items-center justify-between p-6 border-b border-indigo-100">

            <div>
                <h2 class="text-2xl font-bold text-indigo-900">Profile photo</h2>
                <p class="text-sm text-indigo-400 mt-1">Choose a photo for your account.</p>
            </div>

            <button onclick="closePhotoModal()"
                class="w-10 h-10 rounded-full hover:bg-indigo-50 flex items-center justify-center text-indigo-400 hover:text-indigo-600 transition">
                ✕
            </button>

        </div>

        <!-- Preview -->
        <div class="p-6 flex justify-center">

            <div class="w-36 h-36 rounded-full bg-indigo-50 border border-indigo-100 flex flex-col items-center justify-center text-indigo-300 overflow-hidden" id="previewWrapper">

                <img id="previewImg" class="w-full h-full object-cover hidden" alt="Preview">

                <svg id="noPhotoIcon" xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7a2 2 0 012-2h2l1-1h4l1 1h2a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V7zm9 3a4 4 0 100 8 4 4 0 000-8z"/>
                </svg>

                <span id="noPhotoText" class="text-sm">No photo</span>

            </div>

        </div>

        <!-- Actions -->
        <div class="px-6 grid grid-cols-2 gap-4 mb-5">

            <button type="button"
                class="rounded-2xl border border-indigo-100 bg-indigo-50/50 py-4 flex flex-col items-center gap-2 hover:bg-indigo-100 transition">

                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14m-6 4h6a2 2 0 002-2V8a2 2 0 00-2-2H9a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>

                <span class="font-medium text-indigo-700">Take selfie</span>

            </button>

            <label class="rounded-2xl border border-indigo-100 bg-indigo-50/50 py-4 flex flex-col items-center gap-2 hover:bg-indigo-100 transition cursor-pointer">

                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4-4a3 3 0 014.243 0L16 16m-2-2l1-1a3 3 0 014.243 0L20 14m-6 6H6a2 2 0 01-2-2V6a2 2 0 012-2h12a2 2 0 012 2v12a2 2 0 01-2 2z"/>
                </svg>

                <span class="font-medium text-indigo-700">Choose photo</span>

                <input type="file" accept="image/*" class="hidden" id="photoInput">

            </label>

        </div>

        <!-- Upload -->
        <div class="p-6 pt-0">

            <button type="button" id="uploadBtn" onclick="uploadPhoto()"
                class="w-full rounded-2xl bg-indigo-600 py-4 text-lg font-semibold text-white hover:bg-indigo-700 transition shadow-lg shadow-indigo-200">
                Upload photo
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
        <h2 id="alertTitle" class="text-lg font-bold text-indigo-900 mb-1">Perhatian</h2>
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
function openPhotoModal() {
    const modal = document.getElementById('photoModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closePhotoModal() {
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

document.getElementById('photoInput').addEventListener('change', function(e) {
    selectedFile = e.target.files[0];
    if (selectedFile) {
        const reader = new FileReader();
        reader.onload = function(ev) {
            document.getElementById('previewImg').src = ev.target.result;
            document.getElementById('previewImg').classList.remove('hidden');
            document.getElementById('noPhotoIcon').classList.add('hidden');
            document.getElementById('noPhotoText').classList.add('hidden');
        };
        reader.readAsDataURL(selectedFile);
    }
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
        showAlert('warning', 'Perhatian', 'Sila pilih gambar dahulu.');
        return;
    }

    const formData = new FormData();
    formData.append('photo', selectedFile);

    fetch('{{ route("profile.photo.upload") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            document.getElementById('avatarWrapper').innerHTML =
                `<img src="${data.photoUrl}" class="w-full h-full object-cover" alt="Profile photo">`;
            closePhotoModal();
        } else {
            showAlert('error', 'Gagal', 'Gagal upload gambar.');
        }
    })
    .catch(err => {
        console.error(err);
        showAlert('error', 'Ralat', 'Ada ralat semasa upload.');
    });
}
</script>
</body>
</html>