{{--
  RakanKampus date & time picker (calendar + clock dial), shown as a bottom
  sheet on phones and a centred card on desktop. Used by Reminders and Timetable.

  JS API (all values are plain strings, same format as <input type=date/time>):
    RKPicker.dateTime({ title, subtitle, date: 'YYYY-MM-DD', time: 'HH:MM', marks: ['YYYY-MM-DD', ...],
                        tab: 'date'|'time', onDone(date, time) })
    RKPicker.timeRange({ title, subtitle, start: 'HH:MM', end: 'HH:MM', tab: 'start'|'end',
                         onDone(start, end) })
    RKPicker.date({ title, subtitle, date: 'YYYY-MM-DD', min: 'YYYY-MM-DD', range: ['YYYY-MM-DD','YYYY-MM-DD'],
                    onDone(date) })   — calendar only (used for programme start/end dates)
--}}
<style>
  body > .rkp-overlay, body > .rkp-sheet { animation: none !important; }
  .rkp-overlay { position: fixed; inset: 0; background: rgba(10, 18, 32, 0.5); z-index: 200; opacity: 0; pointer-events: none; transition: opacity .2s ease; }
  .rkp-overlay.open { opacity: 1; pointer-events: auto; }
  .rkp-sheet {
    position: fixed; left: 0; right: 0; bottom: 0; z-index: 201;
    background: #ffffff; color: #14213d; border-radius: 26px 26px 0 0;
    padding: 12px 20px calc(18px + env(safe-area-inset-bottom, 0px));
    max-height: 92vh; overflow-y: auto;
    transform: translateY(105%); transition: transform .28s cubic-bezier(.2,.8,.2,1);
    font-family: inherit; box-shadow: 0 -12px 40px rgba(10,18,32,.25);
  }
  .rkp-sheet.open { transform: translateY(0); }
  @media (min-width: 861px) {
    .rkp-sheet { left: 50%; right: auto; bottom: auto; top: 50%; width: 420px; border-radius: 24px;
      transform: translate(-50%, -46%) scale(.97); opacity: 0; pointer-events: none; transition: transform .2s ease, opacity .2s ease; }
    .rkp-sheet.open { transform: translate(-50%, -50%) scale(1); opacity: 1; pointer-events: auto; }
    .rkp-grab { display: none; }
  }
  .rkp-grab { width: 42px; height: 5px; border-radius: 9px; background: #dbe3ea; margin: 0 auto 12px; }
  .rkp-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 10px; }
  .rkp-title { font-size: 18px; font-weight: 800; color: #14213d; margin: 0; }
  .rkp-sub { font-size: 12.5px; color: #64748b; margin: 3px 0 12px; min-height: 1em; }
  .rkp-x { width: 32px; height: 32px; border-radius: 50%; border: none; background: #f1f5f9; color: #475569; font-size: 18px; cursor: pointer; flex-shrink: 0; }

  .rkp-tabs { display: grid; grid-template-columns: 1fr 1fr; background: #f1f5f9; border-radius: 14px; padding: 4px; margin-bottom: 14px; }
  .rkp-tab { border: none; background: none; text-align: center; padding: 8px 6px; border-radius: 11px; font-size: 12.5px; color: #64748b; font-weight: 600; cursor: pointer; font-family: inherit; }
  .rkp-tab b { display: block; font-size: 15px; color: #334155; margin-top: 1px; }
  .rkp-tab.on { background: #ffffff; box-shadow: 0 2px 8px rgba(15,29,46,.1); }
  .rkp-tab.on b { color: #0f766e; }

  .rkp-chips { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 4px; }
  .rkp-chip { border: 1.5px solid #e2e8f0; background: #ffffff; border-radius: 99px; padding: 6px 12px; font-size: 12.5px; font-weight: 600; color: #334155; cursor: pointer; font-family: inherit; }
  .rkp-chip.on { border-color: #2ec4c6; background: #e6fbfa; color: #0f766e; }

  .rkp-cal-head { display: flex; justify-content: space-between; align-items: center; margin: 12px 0 8px; }
  .rkp-cal-head b { font-size: 16px; color: #14213d; }
  .rkp-nav { display: flex; gap: 6px; }
  .rkp-nav button { width: 32px; height: 32px; border-radius: 10px; border: none; background: #f1f5f9; color: #334155; font-size: 16px; cursor: pointer; }
  .rkp-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 4px; text-align: center; }
  .rkp-w { font-size: 11px; font-weight: 700; color: #94a3b8; padding: 4px 0; }
  .rkp-d { height: 38px; border: none; background: none; border-radius: 12px; font-size: 14px; color: #1f2937; position: relative; cursor: pointer; font-family: inherit; }
  .rkp-d:hover { background: #f1f5f9; }
  .rkp-d.out { color: #cbd5e1; }
  .rkp-d.past { color: #cbd5e1; }
  .rkp-d.today { box-shadow: inset 0 0 0 1.5px #2ec4c6; color: #0f766e; font-weight: 700; }
  .rkp-d.sel { background: #14213d; color: #ffffff; font-weight: 800; box-shadow: none; }
  .rkp-d.mark::after { content: ''; position: absolute; bottom: 4px; left: 50%; margin-left: -2.5px; width: 5px; height: 5px; border-radius: 50%; background: #f59e0b; }

  .rkp-big { display: flex; align-items: center; justify-content: center; gap: 6px; margin: 4px 0 14px; }
  .rkp-num { font-size: 42px; font-weight: 800; padding: 2px 10px; width: 88px; box-sizing: border-box; text-align: center; border-radius: 14px; color: #14213d; background: #f1f5f9; border: none; cursor: text; font-family: inherit; line-height: 1.2; outline: none; caret-color: #2ec4c6; -webkit-appearance: none; appearance: none; }
  .rkp-num.bad { box-shadow: inset 0 0 0 2px #f87171 !important; color: #dc2626; }
  .rkp-num::selection { background: rgba(46,196,198,.3); }
  .rkp-num.on { background: #e6fbfa; color: #0f766e; box-shadow: inset 0 0 0 2px #2ec4c6; }
  .rkp-colon { font-size: 36px; font-weight: 800; color: #94a3b8; }
  .rkp-ampm { display: flex; flex-direction: column; margin-left: 8px; border: 1.5px solid #e2e8f0; border-radius: 12px; overflow: hidden; }
  .rkp-ampm button { border: none; background: #ffffff; padding: 7px 12px; font-size: 13px; font-weight: 700; color: #64748b; cursor: pointer; font-family: inherit; }
  .rkp-ampm button.on { background: #14213d; color: #ffffff; }
  .rkp-dial { width: 240px; height: 240px; border-radius: 50%; background: #f1f5f9; margin: 0 auto; position: relative; touch-action: none; user-select: none; cursor: pointer; }
  .rkp-dial .n { position: absolute; width: 32px; height: 32px; margin: -16px 0 0 -16px; display: flex; align-items: center; justify-content: center; font-size: 14px; color: #334155; font-weight: 600; border-radius: 50%; pointer-events: none; z-index: 2; }
  .rkp-dial .n.on { background: #2ec4c6; color: #ffffff; font-weight: 800; }
  .rkp-hand { position: absolute; left: 120px; top: 120px; height: 3px; background: #2ec4c6; transform-origin: 0 50%; border-radius: 3px; z-index: 1; pointer-events: none; transition: transform .15s ease; }
  .rkp-knob { position: absolute; right: -16px; top: -14.5px; width: 32px; height: 32px; border-radius: 50%; background: rgba(46,196,198,.25); }
  .rkp-center { position: absolute; left: 114px; top: 114px; width: 12px; height: 12px; border-radius: 50%; background: #2ec4c6; z-index: 1; pointer-events: none; }
  .rkp-hint { text-align: center; font-size: 11.5px; color: #94a3b8; margin-top: 8px; }

  .rkp-sum { margin-top: 12px; background: #f0fdfc; border-radius: 12px; padding: 9px 14px; font-size: 12.5px; color: #0f766e; font-weight: 600; display: flex; justify-content: space-between; gap: 8px; }
  .rkp-sum.bad { background: #fef2f2; color: #b91c1c; }
  .rkp-done { margin-top: 14px; width: 100%; border: none; background: linear-gradient(120deg, #14213d, #2ec4c6); color: #ffffff; font-weight: 800; font-size: 15px; padding: 14px; border-radius: 14px; cursor: pointer; font-family: inherit; }
  .rkp-done:disabled { opacity: .45; cursor: not-allowed; }

  /* The field in the form that opens the picker */
  .rkp-d.inr { background: #ccfbf1; color: #0f766e; border-radius: 10px; }
  .rkp-d.off { opacity: .3; cursor: not-allowed; }
  .rkp-field { width: 100%; display: flex; align-items: center; justify-content: space-between; gap: 8px; box-sizing: border-box;
    border: 1.5px solid #e2e8f0; background: #ffffff; border-radius: 12px; padding: 11px 14px; font-size: 14px; color: #14213d;
    cursor: pointer; text-align: left; font-family: inherit; }
  .rkp-field small { display: block; font-size: 11px; color: #94a3b8; font-weight: 600; margin-bottom: 1px; }
  .rkp-field b { font-weight: 700; }
  .rkp-field.empty b { color: #94a3b8; font-weight: 500; }
  .rkp-field svg { width: 18px; height: 18px; color: #0d9488; flex-shrink: 0; }
  .rkp-field:hover { border-color: #b7e4dd; }
  .rkp-days { display: grid; grid-template-columns: repeat(7, 1fr); gap: 6px; }
  .rkp-days button { border: none; text-align: center; padding: 10px 0; border-radius: 12px; background: #f1f5f9; font-size: 12.5px; font-weight: 700; color: #334155; cursor: pointer; font-family: inherit; }
  .rkp-days button.on { background: #14213d; color: #ffffff; }
</style>
<style id="rk-dark-theme">
/* Dark theme — generated by scripts/dark-theme/generate.py, do not edit by hand.
   Hand-made fixes go in resources/views/partials/dark-fixes.blade.php */
html[data-theme="dark"] .rkp-sheet { background: #17202d; color: #dee1e9; box-shadow: 0 -12px 40px rgba(0, 0, 0, 0.5); }
html[data-theme="dark"] .rkp-grab { background: #233748; }
html[data-theme="dark"] .rkp-title { color: #dee1e9; }
html[data-theme="dark"] .rkp-sub { color: #b0b6be; }
html[data-theme="dark"] .rkp-x { background: #10161f; color: #d0d4da; }
html[data-theme="dark"] .rkp-tabs { background: #10161f; }
html[data-theme="dark"] .rkp-tab { color: #b0b6be; }
html[data-theme="dark"] .rkp-tab b { color: #d6dae1; }
html[data-theme="dark"] .rkp-tab.on { background: #17202d; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.23); }
html[data-theme="dark"] .rkp-tab.on b { color: #2fe5d6; }
html[data-theme="dark"] .rkp-chip { border: 1.5px solid #283648; background: #17202d; color: #d6dae1; }
html[data-theme="dark"] .rkp-chip.on { background: #1d3d3b; color: #2fe5d6; }
html[data-theme="dark"] .rkp-cal-head b { color: #dee1e9; }
html[data-theme="dark"] .rkp-nav button { background: #10161f; color: #d6dae1; }
html[data-theme="dark"] .rkp-w { color: #ced3d9; }
html[data-theme="dark"] .rkp-d { color: #dee2e8; }
html[data-theme="dark"] .rkp-d:hover { background: #10161f; }
html[data-theme="dark"] .rkp-d.inr { background: #134e4a; color: #99f6e4; }
html[data-theme="dark"] .rkp-d.today { box-shadow: inset 0 0 0 1.5px #000000; color: #2fe5d6; }
html[data-theme="dark"] .rkp-num { color: #dee1e9; background: #10161f; }
html[data-theme="dark"] .rkp-num.on { background: #1d3d3b; color: #2fe5d6; box-shadow: inset 0 0 0 2px #000000; }
html[data-theme="dark"] .rkp-colon { color: #ced3d9; }
html[data-theme="dark"] .rkp-ampm { border: 1.5px solid #283648; }
html[data-theme="dark"] .rkp-ampm button { background: #17202d; color: #b0b6be; }
html[data-theme="dark"] .rkp-dial { background: #10161f; }
html[data-theme="dark"] .rkp-dial .n { color: #d6dae1; }
html[data-theme="dark"] .rkp-hint { color: #ced3d9; }
html[data-theme="dark"] .rkp-sum { background: #1b3836; color: #2fe5d6; }
html[data-theme="dark"] .rkp-sum.bad { background: #371a1a; color: #eb7979; }
html[data-theme="dark"] .rkp-field { border: 1.5px solid #283648; background: #17202d; color: #dee1e9; }
html[data-theme="dark"] .rkp-field small { color: #ced3d9; }
html[data-theme="dark"] .rkp-field.empty b { color: #ced3d9; }
html[data-theme="dark"] .rkp-field svg { color: #41eedf; }
html[data-theme="dark"] .rkp-field:hover { border-color: #284843; }
html[data-theme="dark"] .rkp-days button { background: #10161f; color: #d6dae1; }
</style>

<div class="rkp-overlay" id="rkpOverlay"></div>
<div class="rkp-sheet" id="rkpSheet" role="dialog" aria-modal="true" aria-labelledby="rkpTitle">
  <div class="rkp-grab"></div>
  <div class="rkp-head">
    <div>
      <p class="rkp-title" id="rkpTitle"></p>
      <p class="rkp-sub" id="rkpSub"></p>
    </div>
    <button type="button" class="rkp-x" id="rkpClose" aria-label="{{ __('Close') }}">×</button>
  </div>
  <div class="rkp-tabs" id="rkpTabs"></div>
  <div id="rkpBody"></div>
  <div id="rkpSum"></div>
  <button type="button" class="rkp-done" id="rkpDone"></button>
</div>

<script>
window.RKPicker = (function () {
  const $ = (id) => document.getElementById(id);
  const pad = (n) => String(n).padStart(2, '0');
  const tt = (s) => (typeof t === 'function' ? t(s) : s);
  const locale = ({ ms: 'ms-MY', zh: 'zh-CN', ta: 'ta-IN' })[window.APP_LOCALE] || 'en-GB';

  let st = null; // current picker state

  // ---------- helpers ----------
  function parseTime(v) {
    const m = /^(\d{1,2}):(\d{2})/.exec(v || '');
    return m ? { h: +m[1], m: +m[2] } : null;
  }
  function fmtTime(v) {
    const p = parseTime(v); if (!p) return '—';
    const h12 = p.h % 12 === 0 ? 12 : p.h % 12;
    return `${h12}:${pad(p.m)} ${p.h < 12 ? 'AM' : 'PM'}`;
  }
  function toISO(d) { return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`; }
  function fromISO(s) { const m = /^(\d{4})-(\d{2})-(\d{2})/.exec(s || ''); return m ? new Date(+m[1], +m[2] - 1, +m[3]) : null; }
  function fmtDate(s, long) {
    const d = fromISO(s); if (!d) return '—';
    return d.toLocaleDateString(locale, long ? { weekday: 'short', day: 'numeric', month: 'short', year: 'numeric' } : { weekday: 'short', day: 'numeric', month: 'short' });
  }
  function minutes(v) { const p = parseTime(v); return p ? p.h * 60 + p.m : null; }

  // ---------- open / close ----------
  function open() {
    $('rkpOverlay').classList.add('open');
    $('rkpSheet').classList.add('open');
  }
  function close() {
    $('rkpOverlay').classList.remove('open');
    $('rkpSheet').classList.remove('open');
    st = null;
  }
  document.addEventListener('DOMContentLoaded', () => {
    $('rkpOverlay').addEventListener('click', close);
    $('rkpClose').addEventListener('click', close);
    $('rkpDone').addEventListener('click', done);
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && st) close(); });
  });

  function done() {
    if (!st) return;
    if (st.kind === 'd') { if (!st.date) return; const cb = st.onDone; const d = st.date; close(); cb && cb(d); return; }
    if (st.kind === 'dt') {
      if (st.tab === 'date' && !st.visitedTime) { st.tab = 'time'; st.visitedTime = true; render(); return; }
      const cb = st.onDone; const d = st.date, tm = st.time; close(); cb && cb(d, tm);
    } else {
      if (st.tab === 'start' && !st.visitedEnd) { st.tab = 'end'; st.visitedEnd = true; st.dialMode = 'h'; render(); return; }
      if (!rangeOk()) return;
      const cb = st.onDone; const a = st.start, b = st.end; close(); cb && cb(a, b);
    }
  }

  // ---------- rendering ----------
  function render() {
    $('rkpTitle').textContent = st.title || '';
    $('rkpSub').textContent = st.subtitle || '';
    const tabs = st.kind === 'd'
      ? [['date', tt('Date'), fmtDate(st.date)]]
      : st.kind === 'dt'
      ? [['date', tt('Date'), fmtDate(st.date)], ['time', tt('Time'), fmtTime(st.time)]]
      : [['start', tt('Start'), fmtTime(st.start)], ['end', tt('End'), fmtTime(st.end)]];
    $('rkpTabs').innerHTML = tabs.map(([k, l, v]) =>
      `<button type="button" class="rkp-tab ${st.tab === k ? 'on' : ''}" data-tab="${k}">${l}<b>${v}</b></button>`).join('');
    $('rkpTabs').querySelectorAll('.rkp-tab').forEach(b => b.onclick = () => {
      st.tab = b.dataset.tab; st.dialMode = 'h';
      if (st.tab === 'time') st.visitedTime = true;
      if (st.tab === 'end') st.visitedEnd = true;
      render();
    });

    if (st.kind === 'd' || (st.kind === 'dt' && st.tab === 'date')) renderCalendar(); else renderClock();

    // summary + main button
    const sum = $('rkpSum');
    if (st.kind === 'range') {
      const a = minutes(st.start), b = minutes(st.end);
      if (a != null && b != null) {
        const ok = b > a, mins = b - a;
        const dur = mins >= 60 ? `${Math.floor(mins / 60)} ${tt('hr')}${mins % 60 ? ' ' + (mins % 60) + ' ' + tt('min') : ''}` : `${mins} ${tt('min')}`;
        sum.innerHTML = `<div class="rkp-sum ${ok ? '' : 'bad'}"><span>${fmtTime(st.start)} – ${fmtTime(st.end)}</span><span>${ok ? dur : tt('End must be after start')}</span></div>`;
      } else sum.innerHTML = '';
    } else if (st.kind === 'd') {
      sum.innerHTML = st.date ? `<div class="rkp-sum"><span>📅 ${fmtDate(st.date, true)}</span><span>${relDays(st.date)}</span></div>` : '';
    } else {
      sum.innerHTML = st.date ? `<div class="rkp-sum"><span>📅 ${fmtDate(st.date, true)} · ${fmtTime(st.time)}</span><span>${relDays(st.date)}</span></div>` : '';
    }
    const btn = $('rkpDone');
    if (st.kind === 'd') {
      btn.textContent = tt('Done');
      btn.disabled = !st.date;
    } else if (st.kind === 'dt') {
      btn.textContent = (st.tab === 'date' && !st.visitedTime) ? tt('Next: Time') + ' →' : tt('Done');
      btn.disabled = !st.date;
    } else {
      btn.textContent = (st.tab === 'start' && !st.visitedEnd) ? tt('Next: End time') + ' →' : tt('Done');
      btn.disabled = st.tab === 'end' || st.visitedEnd ? !rangeOk() : !st.start;
    }
  }

  function rangeOk() { const a = minutes(st.start), b = minutes(st.end); return a != null && b != null && b > a; }
  function relDays(s) {
    const d = fromISO(s); const today = new Date(); today.setHours(0, 0, 0, 0);
    const n = Math.round((d - today) / 86400000);
    if (n === 0) return tt('Today'); if (n === 1) return tt('Tomorrow'); if (n === -1) return tt('Yesterday');
    return n > 0 ? tt('in :count days').replace(':count', n) : tt(':count days ago').replace(':count', -n);
  }

  function renderCalendar() {
    const sel = fromISO(st.date);
    if (!st.view) { const b = sel || new Date(); st.view = new Date(b.getFullYear(), b.getMonth(), 1); }
    const v = st.view, today = new Date(); today.setHours(0, 0, 0, 0);
    const first = new Date(v.getFullYear(), v.getMonth(), 1);
    const offset = (first.getDay() + 6) % 7; // Monday first
    const start = new Date(first); start.setDate(1 - offset);
    const marks = new Set(st.marks || []);
    const wk = [];
    for (let i = 0; i < 7; i++) { const d = new Date(2024, 0, 1 + i); wk.push(d.toLocaleDateString(locale, { weekday: 'narrow' })); }

    const quick = [[tt('Today'), 0], [tt('Tomorrow'), 1], [tt('Next week'), 7]];
    let html = '<div class="rkp-chips">' + quick.map(([l, n]) => {
      const d = new Date(today); d.setDate(d.getDate() + n); const iso = toISO(d);
      return `<button type="button" class="rkp-chip ${st.date === iso ? 'on' : ''}" data-iso="${iso}">${l}</button>`;
    }).join('') + '</div>';
    html += `<div class="rkp-cal-head"><b>${v.toLocaleDateString(locale, { month: 'long', year: 'numeric' })}</b>
      <div class="rkp-nav"><button type="button" data-m="-1" aria-label="${tt('Previous month')}">‹</button><button type="button" data-m="1" aria-label="${tt('Next month')}">›</button></div></div>`;
    html += '<div class="rkp-grid">' + wk.map(w => `<div class="rkp-w">${w}</div>`).join('');
    const rows = Math.ceil((offset + new Date(v.getFullYear(), v.getMonth() + 1, 0).getDate()) / 7);
    for (let i = 0; i < rows * 7; i++) {
      const d = new Date(start); d.setDate(start.getDate() + i);
      const iso = toISO(d);
      const cls = ['rkp-d'];
      if (d.getMonth() !== v.getMonth()) cls.push('out');
      else if (d < today) cls.push('past');
      if (+d === +today) cls.push('today');
      if (iso === st.date) cls.push('sel');
      if (marks.has(iso)) cls.push('mark');
      if (st.range && st.range[0] && st.range[1] && iso >= st.range[0] && iso <= st.range[1] && iso !== st.date) cls.push('inr');
      if (st.min && iso < st.min) cls.push('off');
      html += `<button type="button" class="${cls.join(' ')}" data-iso="${iso}">${d.getDate()}</button>`;
    }
    html += '</div>';
    $('rkpBody').innerHTML = html;
    $('rkpBody').querySelectorAll('[data-iso]').forEach(b => b.onclick = () => {
      if (st.min && b.dataset.iso < st.min) return;
      st.date = b.dataset.iso; const d = fromISO(st.date);
      st.view = new Date(d.getFullYear(), d.getMonth(), 1); render();
    });
    $('rkpBody').querySelectorAll('[data-m]').forEach(b => b.onclick = () => {
      st.view = new Date(st.view.getFullYear(), st.view.getMonth() + (+b.dataset.m), 1); render();
    });
  }

  function currentTimeKey() { return st.kind === 'dt' ? 'time' : st.tab; }

  function renderClock() {
    const key = currentTimeKey();
    let p = parseTime(st[key]);
    if (!p) { p = key === 'end' && parseTime(st.start) ? { h: Math.min(23, parseTime(st.start).h + 1), m: parseTime(st.start).m } : { h: 9, m: 0 }; st[key] = `${pad(p.h)}:${pad(p.m)}`; }
    const pm = p.h >= 12, h12 = p.h % 12 === 0 ? 12 : p.h % 12;
    const mode = st.dialMode || 'h';
    const ty = st.typed;   // what the student is typing right now (kept as typed, e.g. "4" before "45")

    let html = `<div class="rkp-big">
      <input type="text" class="rkp-num ${mode === 'h' ? 'on' : ''} ${ty && ty.mode === 'h' && ty.bad ? 'bad' : ''}" data-mode="h" value="${ty && ty.mode === 'h' ? ty.v : h12}" inputmode="numeric" maxlength="2" autocomplete="off" aria-label="${tt('Hour')}"><span class="rkp-colon">:</span>
      <input type="text" class="rkp-num ${mode === 'm' ? 'on' : ''} ${ty && ty.mode === 'm' && ty.bad ? 'bad' : ''}" data-mode="m" value="${ty && ty.mode === 'm' ? ty.v : pad(p.m)}" inputmode="numeric" maxlength="2" autocomplete="off" aria-label="${tt('Minutes')}">
      <div class="rkp-ampm"><button type="button" class="${!pm ? 'on' : ''}" data-ap="am">AM</button><button type="button" class="${pm ? 'on' : ''}" data-ap="pm">PM</button></div>
    </div>`;
    const R = 96, C = 120;
    const val = mode === 'h' ? h12 : p.m;
    const angle = mode === 'h' ? (val % 12) * 30 - 90 : val * 6 - 90;
    html += `<div class="rkp-dial" id="rkpDial"><div class="rkp-hand" style="width:${R}px;transform:rotate(${angle}deg)"><span class="rkp-knob"></span></div><div class="rkp-center"></div>`;
    for (let i = 1; i <= 12; i++) {
      const a = (i / 12) * 2 * Math.PI - Math.PI / 2;
      const label = mode === 'h' ? i : pad((i % 12) * 5);
      const on = mode === 'h' ? i === h12 : (i % 12) * 5 === p.m;
      html += `<div class="n ${on ? 'on' : ''}" style="left:${C + R * Math.cos(a)}px;top:${C + R * Math.sin(a)}px">${label}</div>`;
    }
    html += `</div><p class="rkp-hint">${mode === 'h' ? tt('Type the hour, or tap / drag the clock') : tt('Type the minutes, or tap / drag the clock')}</p>`;
    $('rkpBody').innerHTML = html;

    // The big hour / minute boxes can be typed into (numbers only); the clock follows along.
    const focusNum = (mode) => { const el = $('rkpBody').querySelector(`.rkp-num[data-mode="${mode}"]`); if (el) { el.focus(); el.select(); } };
    $('rkpBody').querySelectorAll('.rkp-num').forEach(inp => {
      const mode = inp.dataset.mode;
      inp.addEventListener('focus', () => {
        if (st.keepCaret) { st.keepCaret = false; return; }
        if ((st.dialMode || 'h') !== mode) { st.typed = null; st.dialMode = mode; render(); focusNum(mode); return; }
        setTimeout(() => inp.select(), 0);
      });
      inp.addEventListener('mouseup', (e) => { e.preventDefault(); inp.select(); });   // tap selects the number so typing replaces it
      inp.addEventListener('input', () => {
        const v = inp.value.replace(/\D/g, '').slice(0, 2); inp.value = v;
        const n = parseInt(v, 10), q = parseTime(st[key]);
        const ok = v !== '' && (mode === 'h' ? n >= 1 && n <= 12 : n >= 0 && n <= 59);
        if (ok) {
          if (mode === 'h') st[key] = `${pad((n % 12) + (q.h >= 12 ? 12 : 0))}:${pad(q.m)}`;
          else st[key] = `${pad(q.h)}:${pad(n)}`;
        }
        // hour done (2 digits, or 2–9 typed) → jump to the minutes
        if (ok && mode === 'h' && (v.length === 2 || n > 1)) { st.typed = null; st.dialMode = 'm'; render(); focusNum('m'); return; }
        // re-draw (clock hand, tab label, summary) but keep the digits and caret as typed
        st.typed = { mode, v, bad: v !== '' && !ok };
        render();
        const el = $('rkpBody').querySelector(`.rkp-num[data-mode="${mode}"]`);
        if (el) { st.keepCaret = true; el.focus(); el.setSelectionRange(v.length, v.length); }
      });
      inp.addEventListener('keydown', (e) => {
        const q = parseTime(st[key]);
        if (e.key === 'ArrowUp' || e.key === 'ArrowDown') {
          e.preventDefault(); const d = e.key === 'ArrowUp' ? 1 : -1;
          if (mode === 'h') st[key] = `${pad((q.h + d + 24) % 24)}:${pad(q.m)}`; else st[key] = `${pad(q.h)}:${pad((q.m + d + 60) % 60)}`;
          render(); focusNum(mode);
        } else if (e.key === 'Enter') { e.preventDefault(); inp.blur(); }
        else if (e.key === 'Backspace' && mode === 'm' && inp.value === '') { st.dialMode = 'h'; render(); focusNum('h'); }
      });
      inp.addEventListener('blur', () => {
        st.typed = null; st.keepCaret = false;
        const q = parseTime(st[key]); inp.classList.remove('bad');
        inp.value = mode === 'h' ? (q.h % 12 === 0 ? 12 : q.h % 12) : pad(q.m);
      });
    });
    $('rkpBody').querySelectorAll('[data-ap]').forEach(b => b.onclick = () => {
      const q = parseTime(st[key]); let h = q.h % 12; if (b.dataset.ap === 'pm') h += 12;
      st[key] = `${pad(h)}:${pad(q.m)}`; render();
    });

    // Tap = snaps minutes to 5; drag = any minute. Lifting the finger after
    // choosing the hour moves straight on to the minutes, like Android's clock.
    const dial = $('rkpDial');
    let dragging = false;
    const update = (e, snap) => {
      const r = dial.getBoundingClientRect();
      const x = e.clientX - (r.left + r.width / 2), y = e.clientY - (r.top + r.height / 2);
      let deg = Math.atan2(y, x) * 180 / Math.PI + 90; if (deg < 0) deg += 360;
      const q = parseTime(st[key]);
      if ((st.dialMode || 'h') === 'h') {
        let h = Math.round(deg / 30) % 12;
        st[key] = `${pad(h + (q.h >= 12 ? 12 : 0))}:${pad(q.m)}`;
      } else {
        let m = Math.round(deg / 6) % 60;
        if (snap) m = (Math.round(m / 5) * 5) % 60;
        st[key] = `${pad(q.h)}:${pad(m)}`;
      }
      const nv = parseTime(st[key]);
      const ang = (st.dialMode || 'h') === 'h' ? ((nv.h % 12) * 30 - 90) : (nv.m * 6 - 90);
      dial.querySelector('.rkp-hand').style.transform = `rotate(${ang}deg)`;
      const nums = $('rkpBody').querySelectorAll('.rkp-num');
      nums[0].value = nv.h % 12 === 0 ? 12 : nv.h % 12;
      nums[1].value = pad(nv.m);
    };
    dial.addEventListener('pointerdown', (e) => { dragging = true; try { dial.setPointerCapture(e.pointerId); } catch (_) {} update(e, true); });
    dial.addEventListener('pointermove', (e) => { if (dragging) update(e, false); });
    const finish = () => {
      if (!dragging) return;
      dragging = false;
      if ((st.dialMode || 'h') === 'h') st.dialMode = 'm';
      render();
    };
    dial.addEventListener('pointerup', finish);
    dial.addEventListener('pointercancel', finish);
  }

  // ---------- public ----------
  return {
    dateTime(opts) {
      st = Object.assign({ kind: 'dt', tab: opts.tab || 'date', dialMode: 'h', visitedTime: opts.tab === 'time' || !!opts.date }, opts);
      if (!st.time) st.time = '09:00';
      st.view = null;
      render(); open();
    },
    timeRange(opts) {
      st = Object.assign({ kind: 'range', tab: opts.tab || 'start', dialMode: 'h', visitedEnd: opts.tab === 'end' || !!(opts.start && opts.end) }, opts);
      render(); open();
    },
    date(opts) {
      st = Object.assign({ kind: 'd', tab: 'date' }, opts);
      st.view = null;
      render(); open();
    },
    formatTime: fmtTime,
    formatDate: fmtDate,
    close,
  };
})();
</script>
