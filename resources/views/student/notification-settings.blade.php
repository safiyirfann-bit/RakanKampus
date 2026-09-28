<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('partials.pwa-head')
    <title>{{ __('Notification Settings') }}</title>
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
        /* ---- Quiet time card ---- */
        .dnd-badge { font-size: 11px; font-weight: 800; padding: 4px 10px; border-radius: 99px; background: #f1f5f9; color: #64748b; }
        .dnd-card.on .dnd-badge { background: #312e81; color: #fde68a; }
        .dnd-hero { display: flex; align-items: center; gap: 14px; padding: 12px 14px; border-radius: 18px; margin-bottom: 14px;
            background: linear-gradient(135deg, #f0fdfa, #eef2ff); transition: background .4s ease; }
        .dnd-card.on .dnd-hero { background: linear-gradient(135deg, #1e1b4b, #312e81 60%, #4338ca); }
        .dnd-bot { width: 86px; height: 64px; flex-shrink: 0; overflow: visible; }
        .dnd-bot * { transform-box: fill-box; }
        .dnd-moon, .dnd-stars, .dnd-zz, .dnd-eyes-shut { opacity: 0; transition: opacity .4s; }
        .dnd-card.on .dnd-moon, .dnd-card.on .dnd-stars, .dnd-card.on .dnd-eyes-shut { opacity: 1; }
        .dnd-card.on .dnd-eyes-open { opacity: 0; }
        .dnd-body { animation: dndBob 2.6s ease-in-out infinite; }
        .dnd-card.on .dnd-body { animation: dndBreathe 3.2s ease-in-out infinite; transform-origin: 50% 100%; }
        .dnd-card.on .dnd-zz { animation: dndZz 2.4s ease-in-out infinite; }
        .dnd-card.on .dnd-stars { animation: dndTwinkle 1.6s ease-in-out infinite alternate; }
        .dnd-card.on .dnd-light { fill: #6366f1; }
        @keyframes dndBob { 50% { transform: translateY(-3px); } }
        @keyframes dndBreathe { 50% { transform: scale(1.03, .97) translateY(1px); } }
        @keyframes dndZz { 0% { opacity: 0; transform: translate(0, 6px); } 40%, 70% { opacity: 1; transform: translate(2px, 0); } 100% { opacity: 0; transform: translate(5px, -6px); } }
        @keyframes dndTwinkle { from { opacity: .35; } to { opacity: 1; } }
        .dnd-title { font-weight: 800; color: #14213d; font-size: 15px; margin: 0; }
        .dnd-sub { font-size: 13px; color: #64748b; margin: 2px 0 0; }
        .dnd-card.on .dnd-title { color: #fff; }
        .dnd-card.on .dnd-sub { color: #c7d2fe; }
        .dnd-chips { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 12px; }
        .dnd-chip { border: 1.5px solid #c9ece7; background: #f0fdfa; color: #0f766e; font-weight: 700; font-size: 13px; padding: 7px 13px; border-radius: 99px; cursor: pointer; transition: transform .1s, background .15s; }
        .dnd-chip:hover { background: #ccfbf1; } .dnd-chip:active { transform: scale(.96); }
        .dnd-clear { width: 52px; flex-shrink: 0; border-radius: 14px; border: 1.5px solid #fecaca; background: #fef2f2; color: #dc2626; display: none; align-items: center; justify-content: center; cursor: pointer; }
        .dnd-clear svg { width: 18px; height: 18px; }
        .dnd-card.on .dnd-clear { display: flex; }
        html[data-theme="dark"] .dnd-badge { background: #1e293b; color: #c3cdd9; }
        html[data-theme="dark"] .dnd-hero { background: linear-gradient(135deg, #10232a, #161c33); }
        html[data-theme="dark"] .dnd-title { color: #f1f5f9; } html[data-theme="dark"] .dnd-sub { color: #c3cdd9; }
        html[data-theme="dark"] .dnd-bot .dnd-body rect[fill="#fff"] { fill: #f1f5f9; }
        html[data-theme="dark"] .dnd-bot { filter: drop-shadow(0 0 .6px #cbd5e1) drop-shadow(0 0 .6px #cbd5e1); }
        html[data-theme="dark"] .dnd-chip { background: #10232a; border-color: #1f4a44; color: #5eead4; }
        html[data-theme="dark"] .dnd-clear { background: #2a1616; border-color: #5b2323; color: #f87171; }
        @media (prefers-reduced-motion: reduce) { .dnd-bot * { animation: none !important; } }
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
<style id="rk-dark-theme">
/* Dark theme — generated by scripts/dark-theme/generate.py, do not edit by hand.
   Hand-made fixes go in resources/views/partials/dark-fixes.blade.php */
html[data-theme="dark"] body { background-image: linear-gradient(120deg, #0c1320, #112031, #1d6869, #0c1320); }
html[data-theme="dark"] .dnd-badge { background: #10161f; color: #b0b6be; }
html[data-theme="dark"] .dnd-hero { background: linear-gradient(135deg, #1b3831, #1b2238); }
html[data-theme="dark"] .dnd-card.on .dnd-light { fill: #989af6; }
html[data-theme="dark"] .dnd-title { color: #dee1e9; }
html[data-theme="dark"] .dnd-sub { color: #b0b6be; }
html[data-theme="dark"] .dnd-chip { border: 1.5px solid #284843; background: #1b3831; color: #2fe5d6; }
html[data-theme="dark"] .dnd-chip:hover { background: #22473f; }
html[data-theme="dark"] .dnd-clear { border: 1.5px solid #482828; background: #371a1a; color: #ef9e9e; }
@media (min-width: 861px) {
  html[data-theme="dark"] body { background: #10161f; }
  html[data-theme="dark"] .page-header { background: rgba(23, 32, 45, 0.9); border-bottom: 1px solid #284848; }
  html[data-theme="dark"] .page-header .back-btn { color: #41eedf; }
  html[data-theme="dark"] .page-header .back-btn:hover { background: #1c3b39; }
  html[data-theme="dark"] .page-header h1 { color: #dee1e9; }
  html[data-theme="dark"] .page-header p { color: #b0b6be; }
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

        <a href="{{ route('student.profile') }}" class="back-btn" aria-label="{{ __('Back') }}">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M15 19l-7-7 7-7"/>
            </svg>
        </a>

        <div>
            <h1>{{ __('Notification Settings') }}</h1>
            <p>{{ __('Manage how RakanKampus keeps you informed') }}</p>
        </div>

    </div>

    <div class="max-w-2xl mx-auto px-6 py-6 space-y-6">

        @if(session('success'))
            <div class="rounded-xl bg-green-50 text-green-700 px-4 py-3 text-sm">
                {{ session('success') }}
            </div>
        @endif

        <form id="notifForm" method="POST" action="{{ route('student.profile.notifications.update') }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Push notifications (read by reminders:send-due and the class "starting soon" job) -->
            <div class="bg-white rounded-3xl border border-indigo-100 overflow-hidden shadow-sm">
                <div class="px-5 pt-5 pb-3">
                    <p class="text-[11px] font-bold tracking-wider uppercase text-indigo-500">{{ __('Push notifications') }}</p>
                </div>
                <div class="divide-y divide-indigo-50">
                    <div class="flex items-center justify-between gap-4 px-5 py-4">
                        <div class="flex items-center gap-4">
                            <div class="w-11 h-11 rounded-2xl bg-indigo-50 flex items-center justify-center shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3M12 3a9 9 0 100 18 9 9 0 000-18z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-indigo-900">{{ __('Reminder notifications') }}</p>
                                <p class="text-sm text-indigo-400">{{ __("Exams, assignments & quizzes before they're due") }}</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer shrink-0">
                            <input type="checkbox" name="reminder_notifications" data-notif value="1" {{ $settings['reminder_notifications'] ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-11 h-6 bg-indigo-200 rounded-full peer peer-checked:bg-indigo-600 transition"></div>
                            <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition peer-checked:translate-x-5"></div>
                        </label>
                    </div>
                    <div class="flex items-center justify-between gap-4 px-5 py-4">
                        <div class="flex items-center gap-4">
                            <div class="w-11 h-11 rounded-2xl bg-indigo-50 flex items-center justify-center shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10m-11 9h12a2 2 0 002-2V7a2 2 0 00-2-2H6a2 2 0 00-2 2v11a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-indigo-900">{{ __('Class starting soon') }}</p>
                                <p class="text-sm text-indigo-400">{{ __('A heads-up before each class in your timetable') }}</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer shrink-0">
                            <input type="checkbox" name="class_notifications" data-notif value="1" {{ $settings['class_notifications'] ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-11 h-6 bg-indigo-200 rounded-full peer peer-checked:bg-indigo-600 transition"></div>
                            <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition peer-checked:translate-x-5"></div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Quiet time (Do Not Disturb): quick presets + the RakanKampus date/time picker; saves straight away -->
            <div class="bg-white rounded-3xl border border-indigo-100 p-5 shadow-sm dnd-card" id="dndCard">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-[11px] font-bold tracking-wider uppercase text-indigo-500">{{ __('Quiet time') }}</p>
                    <span class="dnd-badge" id="dndBadge">{{ __('Off') }}</span>
                </div>

                <div class="dnd-hero">
                    <svg class="dnd-bot" viewBox="0 0 120 90" aria-hidden="true">
                        <path class="dnd-moon" d="M96 8 A12 12 0 1 0 108 26 A9.5 9.5 0 0 1 96 8 Z" fill="#fde68a"/>
                        <g class="dnd-stars" fill="#fde68a"><circle cx="18" cy="14" r="1.8"/><circle cx="34" cy="6" r="1.3"/><circle cx="80" cy="42" r="1.4"/></g>
                        <g class="dnd-body">
                            <line x1="52" y1="18" x2="52" y2="26" stroke="#14213d" stroke-width="3" stroke-linecap="round"/><circle class="dnd-light" cx="52" cy="16" r="4" fill="#2ec4c6"/>
                            <rect x="30" y="26" width="44" height="32" rx="12" fill="#14213d"/><circle cx="29" cy="41" r="7" fill="#2ec4c6"/><circle cx="75" cy="41" r="7" fill="#2ec4c6"/>
                            <rect x="38" y="33" width="28" height="18" rx="7" fill="#fff"/>
                            <g class="dnd-eyes-open"><circle cx="46" cy="42" r="3" fill="#14213d"/><circle cx="58" cy="42" r="3" fill="#14213d"/></g>
                            <g class="dnd-eyes-shut"><path d="M42 42 q4 3 8 0 M54 42 q4 3 8 0" stroke="#14213d" stroke-width="2.2" fill="none" stroke-linecap="round"/></g>
                            <rect x="32" y="60" width="40" height="24" rx="11" fill="#fff" stroke="#14213d" stroke-width="2.5"/><circle cx="52" cy="72" r="3.5" fill="#2ec4c6"/>
                        </g>
                        <g class="dnd-zz" fill="#94a3b8" font-family="Segoe UI,Arial,sans-serif" font-weight="800"><text x="76" y="34" font-size="11">z</text><text x="84" y="26" font-size="9">z</text></g>
                    </svg>
                    <div class="min-w-0">
                        <p class="dnd-title" id="dndTitle">{{ __('Notifications are on') }}</p>
                        <p class="dnd-sub" id="dndSub">{{ __('Need a break? Pause all notifications for a while.') }}</p>
                    </div>
                </div>

                <div class="dnd-chips">
                    <button type="button" class="dnd-chip" data-preset="1h">{{ __('1 hour') }}</button>
                    <button type="button" class="dnd-chip" data-preset="3h">{{ __('3 hours') }}</button>
                    <button type="button" class="dnd-chip" data-preset="tomorrow">🌙 {{ __('Until 7 AM') }}</button>
                </div>

                <input type="hidden" id="dnd_until" name="dnd_until" value="{{ $settings['dnd_until'] }}">
                <div class="flex gap-2 items-stretch">
                    <button type="button" class="rkp-field empty" id="dndField">
                        <span><small>{{ __('Do Not Disturb Until') }}</small><b id="dndFieldText">{{ __('Pick date & time') }}</b></span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8z"></path></svg>
                    </button>
                    <button type="button" class="dnd-clear" id="dndClear" aria-label="{{ __('Turn off') }}" title="{{ __('Turn off') }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
                    </button>
                </div>
                <p class="text-xs text-indigo-400 mt-2">{{ __('No notifications are sent until this time.') }}</p>
            </div>

            <!-- Save -->
            <button type="submit" class="w-full rounded-2xl bg-indigo-600 py-4 text-lg font-bold text-white shadow-lg shadow-indigo-200 hover:bg-indigo-700 transition duration-200">

                {{ __('Save Notification Settings') }}

            </button>

        </form>

    </div>

@include('partials.rk-picker')
<script>
// Turning a notification off asks first with a RakanKampus popup (the robot mutes a ringing bell),
// then saves straight away; a toast confirms it with UNDO. Turning one on just saves + toast.
(function () {
    const form = document.getElementById('notifForm');
    const INFO = {
        reminder_notifications: {
            prop: 'clock',
            title: @js(__('Turn off reminder notifications?')),
            message: @js(__("You won't get alerts before your exams, assignments and quizzes are due. You can turn it back on anytime.")),
            offText: @js(__('Reminder notifications off')), onText: @js(__('Reminder notifications on')),
            onSub: @js(__("We'll remind you before things are due")),
        },
        class_notifications: {
            prop: 'cal',
            title: @js(__('Turn off class alerts?')),
            message: @js(__("You won't get a heads-up before your classes start. You can turn it back on anytime.")),
            offText: @js(__('Class alerts off')), onText: @js(__('Class alerts on')),
            onSub: @js(__("We'll let you know before each class")),
        },
    };
    const T = { turnOff: @js(__('Turn off')), keepOn: @js(__('Keep on')), saved: @js(__('Saved')), oops: @js(__('Oops!')), failed: @js(__('Could not save your settings. Please try again.')) };

    async function save(cb, info) {
        try {
            const res = await fetch(form.action, { method: 'POST', body: new FormData(form), headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            if (!res.ok) throw new Error(res.status);
        } catch (e) {
            cb.checked = !cb.checked;                     // put the switch back the way it was
            RKDialog.alert({ scene: 'oops', title: T.oops, message: T.failed });
            return;
        }
        const on = cb.checked;
        RKToast.show({
            text: on ? info.onText : info.offText,
            sub: on ? info.onSub : T.saved,
            undo: on ? null : () => { cb.checked = true; save(cb, info); },
        });
    }

    document.querySelectorAll('input[data-notif]').forEach(cb => cb.addEventListener('change', async () => {
        const info = INFO[cb.name];
        if (!cb.checked) {
            cb.checked = true;                            // stays on until they confirm
            const ok = await RKDialog.confirm({
                scene: 'mute', prop: info.prop, title: info.title, message: info.message,
                confirmText: T.turnOff, cancelText: T.keepOn,
            });
            if (!ok) return;
            cb.checked = false;
        }
        save(cb, info);
    }));
})();
</script>

<script>
// Quiet time: presets / picker set "Do Not Disturb until", saved straight away with a toast.
(function () {
    const form = document.getElementById('notifForm');
    const card = document.getElementById('dndCard'), input = document.getElementById('dnd_until');
    const T = {
        on: @js(__('Notifications are on')), onSub: @js(__('Need a break? Pause all notifications for a while.')),
        paused: @js(__('Shh… notifications paused')), until: @js(__('Until :time')), off: @js(__('Off')),
        pick: @js(__('Pick date & time')), title: @js(__('Do Not Disturb until')),
        setToast: @js(__('Do Not Disturb is on')), offToast: @js(__('Do Not Disturb is off')), offSub: @js(__('Notifications will come through again')),
        past: @js(__('Pick a time in the future.')), oops: @js(__('Oops!')), failed: @js(__('Could not save your settings. Please try again.')),
    };
    const pad = (n) => String(n).padStart(2, '0');
    const toLocal = (d) => `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
    const parse = (v) => { if (!v) return null; const d = new Date(String(v).replace(' ', 'T').slice(0, 16)); return isNaN(d) ? null : d; };
    const label = (d) => `${RKPicker.formatDate(toLocal(d).slice(0, 10))} · ${RKPicker.formatTime(toLocal(d).slice(11, 16))}`;

    function paint() {
        const d = parse(input.value), active = d && d > new Date();
        card.classList.toggle('on', !!active);
        document.getElementById('dndBadge').textContent = active ? '🌙 ' + RKPicker.formatTime(toLocal(d).slice(11, 16)) : T.off;
        document.getElementById('dndTitle').textContent = active ? T.paused : T.on;
        document.getElementById('dndSub').textContent = active ? T.until.replace(':time', label(d)) : T.onSub;
        const f = document.getElementById('dndField');
        f.classList.toggle('empty', !active);
        document.getElementById('dndFieldText').textContent = active ? label(d) : T.pick;
    }
    async function save(value) {
        const before = input.value;
        input.value = value;
        try {
            const res = await fetch(form.action, { method: 'POST', body: new FormData(form), headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            if (!res.ok) throw new Error(res.status);
        } catch (e) {
            input.value = before; paint();
            RKDialog.alert({ scene: 'oops', title: T.oops, message: T.failed });
            return;
        }
        paint();
        const d = parse(value);
        if (d) RKToast.show({ text: T.setToast, sub: T.until.replace(':time', label(d)), undo: () => save(before) });
        else RKToast.show({ text: T.offToast, sub: T.offSub });
    }
    function set(d) { if (d <= new Date()) { RKDialog.alert({ scene: 'oops', title: T.oops, message: T.past }); return; } save(toLocal(d)); }

    document.querySelectorAll('.dnd-chip').forEach(b => b.addEventListener('click', () => {
        const now = new Date(), d = new Date(now);
        if (b.dataset.preset === '1h') d.setHours(d.getHours() + 1);
        if (b.dataset.preset === '3h') d.setHours(d.getHours() + 3);
        if (b.dataset.preset === 'tomorrow') { if (now.getHours() >= 7) d.setDate(d.getDate() + 1); d.setHours(7, 0, 0, 0); }
        set(d);
    }));
    document.getElementById('dndField').addEventListener('click', () => {
        const cur = parse(input.value) && parse(input.value) > new Date() ? parse(input.value) : new Date(Date.now() + 3600e3);
        RKPicker.dateTime({
            title: T.title, date: toLocal(cur).slice(0, 10), time: toLocal(cur).slice(11, 16),
            onDone(date, time) { set(new Date(`${date}T${time}`)); },
        });
    });
    document.getElementById('dndClear').addEventListener('click', () => save(''));
    paint();
})();
</script>

</body>
</html>