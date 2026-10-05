{{--
  "Report a problem" inside the RakanKampus Assistant (AI upload) popup on Reminders and Timetable.
  A small link under the upload; tapping it opens a short form (what went wrong + details) that
  goes straight to Admin → Inbox, like Help & Support → Report a problem.
  @include('partials.ai-report', ['context' => 'Reminders'])
--}}
@php
    $aiRepCtx = $context ?? 'AI upload';
@endphp
@once
<style>
  .air-link { display: flex; justify-content: center; align-items: center; gap: 6px; margin: 12px 0 0; font-size: 12.5px; color: #64748b; }
  .air-link button { border: 0; background: none; padding: 0; cursor: pointer; font-family: inherit; font-weight: 800; font-size: 12.5px; color: #0d9488; display: inline-flex; align-items: center; gap: 4px; }
  .air-link button svg { width: 12px; height: 12px; transition: transform .2s; }
  .air-link button[aria-expanded="true"] svg { transform: rotate(180deg); }
  .air-box { display: none; margin-top: 10px; border: 1px solid #fde2e2; background: #fff7f7; border-radius: 16px; padding: 12px; animation: airIn .2s ease; }
  .air-box.open { display: block; }
  @keyframes airIn { from { opacity: 0; transform: translateY(-4px); } }
  .air-box small { display: block; font-size: 10.5px; font-weight: 800; letter-spacing: .06em; color: #dc2626; margin-bottom: 8px; }
  .air-chips { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 8px; }
  .air-chips button { border: 1px solid #e2e8f0; background: #fff; color: #334155; border-radius: 999px; padding: 6px 11px; font-family: inherit; font-weight: 600; font-size: 12px; cursor: pointer; }
  .air-chips button.on { background: #14213d; border-color: #14213d; color: #fff; }
  .air-box textarea { width: 100%; box-sizing: border-box; min-height: 64px; resize: vertical; border: 1px solid #e2e8f0; border-radius: 12px; padding: 9px 11px; font-family: inherit; font-weight: 500; font-size: 13px; color: #14213d; outline: none; background: #fff; }
  .air-box textarea:focus { border-color: #2ec4c6; box-shadow: 0 0 0 3px rgba(46,196,198,.15); }
  .air-att[hidden] { display: none; }
  .air-att { display: flex; gap: 6px; align-items: center; font-size: 11.5px; color: #475569; background: #f1f5f9; border-radius: 10px; padding: 6px 10px; margin-bottom: 8px; }
  .air-send { width: 100%; margin-top: 8px; border: 0; border-radius: 12px; padding: 11px; cursor: pointer; font-family: inherit; font-weight: 800; font-size: 13px; color: #fff; background: #0d9488; transition: filter .15s; }
  .air-send:hover { filter: brightness(1.06); }
  .air-send:disabled { opacity: .6; cursor: default; }
  .air-msg { margin: 8px 2px 0; font-size: 12px; }
  .air-msg.err { color: #b91c1c; }
  .air-ok { display: none; gap: 8px; align-items: center; margin-top: 10px; font-size: 12.5px; color: #15803d; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; padding: 10px 12px; }
  .air-ok.show { display: flex; }
  html[data-theme="dark"] .air-link { color: #94a3b8; }
  html[data-theme="dark"] .air-link button { color: #41eedf; }
  html[data-theme="dark"] .air-box { background: #2a1a1f; border-color: #4a2a30; }
  html[data-theme="dark"] .air-chips button { background: #17202d; border-color: #283648; color: #dee1e9; }
  html[data-theme="dark"] .air-chips button.on { background: #41eedf; border-color: #41eedf; color: #0b1424; }
  html[data-theme="dark"] .air-box textarea { background: #10161f; border-color: #283648; color: #dee1e9; }
  html[data-theme="dark"] .air-att { background: #17202d; color: #b0b6be; }
  html[data-theme="dark"] .air-ok { background: #13291c; border-color: #1f4a30; color: #86efac; }
</style>
<script>
window.RKAiReport = {
  toggle(btn) {
    const box = document.getElementById('airBox');
    const open = !box.classList.contains('open');
    box.classList.toggle('open', open);
    btn.setAttribute('aria-expanded', open ? 'true' : 'false');
    document.getElementById('airOk').classList.remove('show');
    if (open) {
      // attach the AI error shown in this popup, if there is one
      const err = document.getElementById('aiErrorMsg');
      const shown = err && err.offsetParent !== null && err.textContent.trim();
      const att = document.getElementById('airAtt');
      att.hidden = !shown;
      att.dataset.err = shown || '';
      document.getElementById('airAttText').textContent = shown ? t('Error message attached') : '';
      if (shown) this.pick(document.querySelector('#airChips [data-k="Upload failed"]'));
      setTimeout(() => document.getElementById('airText').focus(), 50);
    }
  },
  pick(b) {
    if (!b) return;
    document.querySelectorAll('#airChips button').forEach(x => x.classList.toggle('on', x === b));
  },
  async send(btn) {
    const kind = document.querySelector('#airChips button.on')?.dataset.k || 'Other';
    const text = document.getElementById('airText').value.trim();
    const err = document.getElementById('airAtt').dataset.err || '';
    const msg = document.getElementById('airMsg');
    const report = [`[AI upload · ${btn.dataset.ctx}] ${kind}`, text, err ? `Error shown: ${err}` : ''].filter(Boolean).join('\n');
    msg.textContent = ''; msg.className = 'air-msg';
    btn.disabled = true;
    try {
      const res = await fetch(btn.dataset.url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': btn.dataset.csrf },
        body: JSON.stringify({ issue_type: 'Reminders or timetable', issue_report: report }),
      });
      if (!res.ok) throw new Error();
      document.getElementById('airText').value = '';
      document.getElementById('airBox').classList.remove('open');
      document.getElementById('airToggle').setAttribute('aria-expanded', 'false');
      document.getElementById('airOk').classList.add('show');
    } catch (e) {
      msg.textContent = t('Could not send the report. Please try again.');
      msg.className = 'air-msg err';
    } finally {
      btn.disabled = false;
    }
  },
};
</script>
@endonce
<p class="air-link">{{ __('Something not working?') }}
  <button type="button" id="airToggle" aria-expanded="false" aria-controls="airBox" onclick="RKAiReport.toggle(this)">{{ __('Report a problem') }}
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
  </button>
</p>
<div class="air-box" id="airBox">
  <small>⚠ {{ __('REPORT A PROBLEM') }}</small>
  <div class="air-chips" id="airChips">
    @foreach(['AI read it wrong', 'Upload failed', 'Missing dates', 'Other'] as $i => $k)
      <button type="button" data-k="{{ $k }}" class="{{ $i === 0 ? 'on' : '' }}" onclick="RKAiReport.pick(this)">{{ __($k) }}</button>
    @endforeach
  </div>
  <div class="air-att" id="airAtt" hidden>📎 <span id="airAttText"></span></div>
  <textarea id="airText" maxlength="1500" placeholder="{{ __('Explain what happened…') }}"></textarea>
  <button type="button" class="air-send" data-ctx="{{ $aiRepCtx }}" data-url="{{ route('student.help.report') }}" data-csrf="{{ csrf_token() }}" onclick="RKAiReport.send(this)">{{ __('Send report') }}</button>
  <p class="air-msg" id="airMsg"></p>
</div>
<div class="air-ok" id="airOk">✓ {{ __('Sent! The admin team will look into it.') }}</div>
