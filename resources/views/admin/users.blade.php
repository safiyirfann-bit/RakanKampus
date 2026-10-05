<!DOCTYPE html>
<html lang="en">
<head>
@include('partials.font')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Users - RakanKampus Admin</title>
    <style>
        body { margin: 0; }
        .us-stats { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 14px; margin-bottom: 18px; }
        .us-stat b { display: block; font-size: 26px; font-weight: 800; letter-spacing: -.02em; color: var(--a-ink); }
        .us-stat span { font-size: 12.5px; color: var(--a-mute); }
        .us-bar { display: flex; gap: 10px; align-items: center; padding: 14px 18px; border-bottom: 1px solid #eef3ef; }
        .us-search { flex: 1; display: flex; align-items: center; gap: 10px; height: 42px; padding: 0 16px; border-radius: 99px; border: 1px solid var(--a-line); background: #fff; color: #9aa39c; }
        .us-search:focus-within { border-color: #bfe3cc; box-shadow: 0 0 0 4px rgba(63,176,112,.12); }
        .us-search svg { width: 16px; height: 16px; flex: none; }
        .us-search input { flex: 1; border: 0; outline: 0; background: transparent; font: 500 13.5px var(--a-font); color: var(--a-ink); }
        .us-chip { border: 0; cursor: pointer; font: 700 12.5px var(--a-font); padding: 9px 14px; border-radius: 99px; background: #f3f5f2; color: var(--a-ink2); }
        .us-chip.on { background: var(--a-dark); color: #fff; }
        .us-row { display: grid; grid-template-columns: minmax(0, 2.2fr) minmax(0, 1.7fr) 110px 130px 110px 104px; gap: 14px; align-items: center; padding: 12px 18px; border-top: 1px solid #f1f2ef; font-size: 13px; color: var(--a-ink); }
        .us-row.hd { font-size: 10.5px; font-weight: 800; letter-spacing: .06em; color: #8a948d; background: #fafbf9; border-top: 0; text-transform: uppercase; }
        .us-row:not(.hd):hover { background: #fafcfb; }
        .us-u { display: flex; gap: 11px; align-items: center; min-width: 0; }
        .us-av { width: 38px; height: 38px; flex: none; border-radius: 50%; overflow: hidden; display: grid; place-items: center; font-weight: 800; font-size: 12.5px; color: #fff; background: linear-gradient(135deg, #3fb070, #1f6b43); }
        .us-av img { width: 100%; height: 100%; object-fit: cover; }
        .us-u div, .us-m { min-width: 0; }
        .us-u b, .us-m b { display: block; font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; text-transform: capitalize; }
        .us-m b { text-transform: none; }
        .us-u small, .us-m small { display: block; font-size: 12px; color: var(--a-mute); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .us-n { font-size: 12.5px; color: var(--a-ink2); line-height: 1.5; }
        .us-tag { justify-self: start; font-size: 11.5px; font-weight: 800; padding: 4px 10px; border-radius: 99px; background: var(--a-g100); color: var(--a-g800); white-space: nowrap; }
        .us-tag.never { background: #f3f5f2; color: #8a948d; }
        .us-del { justify-self: end; display: inline-flex; align-items: center; gap: 6px; border: 0; cursor: pointer; font: 800 12.5px var(--a-font); padding: 8px 12px; border-radius: 12px; background: #fdecec; color: #d64545; transition: background .15s; }
        .us-del:hover { background: #fbd5d5; }
        .us-del svg { width: 15px; height: 15px; }
        .us-del:disabled { opacity: .5; cursor: default; }
        .us-empty { padding: 30px 18px; text-align: center; color: var(--a-mute); font-size: 13.5px; }
        .us-row.gone { opacity: 0; transform: translateX(20px); transition: opacity .25s, transform .25s; }
    </style>
</head>
<body>

@php
    $initials = fn ($u) => strtoupper(mb_substr($u->first_name ?: ($u->name ?: '?'), 0, 1) . mb_substr($u->last_name ?? '', 0, 1));
@endphp

@include('partials.admin-nav', ['active' => 'users'])

<div class="adm-wrap">

    <div class="adm-ph">
        <div>
            <h1>Users</h1>
            <p>Every student who registered, when they last logged in, and removing accounts.</p>
        </div>
    </div>

    <div class="us-stats">
        <div class="adm-card us-stat"><b id="usTotal">{{ $stats['total'] }}</b><span>Students registered</span></div>
        <div class="adm-card us-stat"><b>{{ $stats['activeWeek'] }}</b><span>Logged in this week</span></div>
        <div class="adm-card us-stat"><b>+{{ $stats['newWeek'] }}</b><span>New this week</span></div>
        <div class="adm-card us-stat"><b>{{ $stats['never'] }}</b><span>Never logged in</span></div>
    </div>

    <section class="adm-card flush">
        <div class="us-bar">
            <label class="us-search">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
                <input type="text" id="usQ" placeholder="Search name, email or matric…" autocomplete="off">
            </label>
            <button type="button" class="us-chip on" data-f="all">All</button>
            <button type="button" class="us-chip" data-f="active">Active this week</button>
            <button type="button" class="us-chip" data-f="never">Never logged in</button>
        </div>

        <div class="us-row hd"><div>Student</div><div>Matric · Programme</div><div>Activity</div><div>Last login</div><div>Registered</div><div></div></div>

        <div id="usList">
            @foreach($students as $u)
                @php
                    $name = trim(($u->first_name ?? '') . ' ' . ($u->last_name ?? '')) ?: ($u->name ?: 'Student');
                    $active = $u->last_login_at && $u->last_login_at->gte(now()->subDays(7));
                @endphp
                <div class="us-row" data-id="{{ $u->id }}"
                     data-q="{{ strtolower($name . ' ' . $u->email . ' ' . $u->student_id) }}"
                     data-state="{{ $u->last_login_at ? ($active ? 'active' : 'old') : 'never' }}">
                    <div class="us-u">
                        <span class="us-av">
                            @if($u->photo_data)<img src="{{ $u->photo_data }}" alt="">@else{{ $initials($u) }}@endif
                        </span>
                        <div><b>{{ $name }}</b><small>{{ $u->email }}</small></div>
                    </div>
                    <div class="us-m"><b>{{ $u->student_id ?: '—' }}</b><small>{{ $u->programme ?: '—' }}</small></div>
                    <div class="us-n">{{ $u->chat_conversations_count }} chats<br>{{ $u->reminders_count }} reminders</div>
                    <div>
                        @if($u->last_login_at)
                            <span class="us-tag" title="{{ $u->last_login_at->format('j M Y, g:i A') }}">{{ $u->last_login_at->diffForHumans() }}</span>
                        @else
                            <span class="us-tag never">Never</span>
                        @endif
                    </div>
                    <div class="us-n">{{ optional($u->created_at)->format('j M Y') }}</div>
                    <button type="button" class="us-del"
                            data-name="{{ $name }}" data-email="{{ $u->email }}"
                            data-lose="{{ $u->chat_conversations_count }} chats · {{ $u->reminders_count }} reminders · {{ $u->class_schedules_count }} classes · login history"
                            onclick="deleteUser(this)">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6"/></svg>
                        Delete
                    </button>
                </div>
            @endforeach
        </div>
        <p class="us-empty" id="usEmpty" @if($students->count()) hidden @endif>No students found.</p>
    </section>
</div>

<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
let usFilter = 'all';

function applyFilter() {
    const q = document.getElementById('usQ').value.trim().toLowerCase();
    let shown = 0;
    document.querySelectorAll('#usList .us-row').forEach(r => {
        const ok = (!q || r.dataset.q.includes(q)) && (usFilter === 'all' || r.dataset.state === usFilter);
        r.hidden = !ok;
        if (ok) shown++;
    });
    document.getElementById('usEmpty').hidden = shown > 0;
}
document.getElementById('usQ').addEventListener('input', applyFilter);
document.querySelectorAll('.us-chip').forEach(c => c.addEventListener('click', () => {
    document.querySelectorAll('.us-chip').forEach(x => x.classList.toggle('on', x === c));
    usFilter = c.dataset.f;
    applyFilter();
}));

async function deleteUser(btn) {
    const row = btn.closest('.us-row');
    const typed = await RKDialog.prompt({
        scene: 'trash',
        title: 'Delete this student?',
        message: btn.dataset.email + ' — the account is removed for good, together with ' + btn.dataset.lose + '. This cannot be undone. Type DELETE to confirm.',
        placeholder: 'Type DELETE',
        confirmText: 'Delete student',
    });
    if (typed === null) return;
    if (typed.trim() !== 'DELETE') {
        RKDialog.alert({ scene: 'oops', title: 'Not deleted', message: 'You need to type DELETE (in capitals) to confirm.' });
        return;
    }
    btn.disabled = true;
    try {
        const res = await fetch(@js(url('/admin/users')) + '/' + row.dataset.id, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        });
        if (!res.ok) throw new Error();
        row.classList.add('gone');
        setTimeout(() => { row.remove(); applyFilter(); }, 260);
        const total = document.getElementById('usTotal');
        total.textContent = Math.max(0, (+total.textContent || 1) - 1);
        RKToast.show({ text: btn.dataset.name + ' deleted' });
    } catch (e) {
        btn.disabled = false;
        RKDialog.alert({ scene: 'oops', title: 'Could not delete', message: 'Something went wrong. Please try again.' });
    }
}
</script>
</body>
</html>
