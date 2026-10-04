{{--
  "Notify me" alert rows, shared by the Timetable (Add/Edit Class) and Reminders modals.
  Each alert is a row: [bell] [− n +] [min / hours / days] "before <when>" [✕], max 3.

  Markup:  <div id="xRows"></div> <button type="button" class="nt-add" id="xAdd">…</button>
  Script:  const nt = RKNotify.create({
             rows: 'xRows', add: 'xAdd',
             when: (minutes) => 'Sun 6:00 PM',   // label under "before" ('' to hide)
             minRows: 0,                          // 1 = the last alert can't be removed
             allowZero: false,                    // true = "0 min before" (at the time itself)
             maxDays: 14, emptyText: '…',
           });
           nt.set([1080, 15]);  nt.minutes();  // -> [1080, 15]  (minutes, largest first)
           nt.render();                         // e.g. after the date/time changes
--}}
<style>
  .nt-head { display: flex; align-items: center; justify-content: space-between; margin: 14px 0 6px; }
  .nt-head .field-label { margin: 0; }
  .nt-head small { font-size: 11px; font-weight: 700; color: #94a3b8; }
  .nt-row { display: flex; align-items: center; gap: 8px; padding: 7px 8px; border: 1.5px solid #e2e8f0; border-radius: 13px; margin-bottom: 7px; }
  .nt-bell { width: 30px; height: 30px; flex: none; border-radius: 9px; background: linear-gradient(135deg, #14213d, #2ec4c6); display: grid; place-items: center; color: #fff; }
  .nt-bell svg { width: 15px; height: 15px; }
  .nt-stp { display: flex; align-items: center; flex: none; border: 1.5px solid #e2e8f0; border-radius: 10px; overflow: hidden; }
  .nt-stp button { width: 26px; height: 32px; border: 0; background: transparent; color: #64748b; font-size: 16px; font-weight: 800; cursor: pointer; font-family: inherit; }
  .nt-stp button:active { background: #f1f5f9; }
  /* page modals style every text input; keep the stepper number bare */
  html body .nt-row .nt-stp input[type="text"] { background: transparent !important; border: 0 !important; box-shadow: none !important; padding: 0 !important; height: 32px; }
  .nt-row .nt-stp input[type="text"] { width: 34px; padding: 0; margin: 0; border: 0; border-radius: 0; text-align: center; font-weight: 800; font-size: 14px; color: #14213d; background: transparent; outline: none; box-shadow: none; font-family: inherit; }
  .nt-row select { width: auto; flex: none; margin: 0; padding: 7px 8px; font-size: 13px; font-weight: 600; border: 1.5px solid #e2e8f0; border-radius: 10px; color: #14213d; background-color: #fff; font-family: inherit; outline: none; }
  .nt-row select:focus { border-color: #2ec4c6; }
  .nt-when { flex: 1; min-width: 0; font-size: 12px; color: #64748b; line-height: 1.25; }
  .nt-when b { display: block; font-size: 11px; color: #0f766e; font-weight: 700; }
  .nt-del { width: 26px; height: 26px; flex: none; border: 0; border-radius: 8px; background: transparent; color: #ef4444; font-size: 15px; cursor: pointer; }
  .nt-del:hover { background: #fef2f2; }
  .nt-del:disabled { visibility: hidden; }
  .nt-add { width: 100%; padding: 9px; border: 1.5px dashed #cbd5e1; border-radius: 13px; background: transparent; color: #64748b; font-weight: 700; font-size: 12.5px; cursor: pointer; font-family: inherit; }
  .nt-add:hover:not(:disabled) { border-color: #2ec4c6; color: #0f766e; }
  .nt-add:disabled { color: #cbd5e1; border-color: #e2e8f0; cursor: default; }
  .nt-empty { font-size: 12px; color: #94a3b8; margin: 0 0 7px; }
  @media (max-width: 420px) { .nt-bell { display: none; } .nt-row { gap: 6px; padding: 7px 6px 7px 8px; } }

  html[data-theme="dark"] .nt-row, html[data-theme="dark"] .nt-stp { border-color: #283648; }
  html[data-theme="dark"] .nt-stp button { color: #b0b6be; }
  html[data-theme="dark"] .nt-row .nt-stp input[type="text"] { color: #dee1e9 !important; }
  html[data-theme="dark"] .nt-row select { background-color: #10161f; border-color: #283648; color: #dee1e9; }
  html[data-theme="dark"] .nt-when { color: #94a3b8; }
  html[data-theme="dark"] .nt-when b { color: #41eedf; }
  html[data-theme="dark"] .nt-add { border-color: #283648; color: #94a3b8; }
  html[data-theme="dark"] .nt-add:disabled { color: #3b4a5e; }
  html[data-theme="dark"] .nt-del:hover { background: #2a1a1f; }
</style>
<script>
window.RKNotify = (function () {
  const MAX = 3;
  const UNITS = { min: 1, hour: 60, day: 1440 };
  const LIMIT = { min: 300, hour: 23, day: 14 };
  const BELL = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 1 1 12 0c0 7 3 9 3 9H3s3-2 3-9"></path><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"></path></svg>';
  const registry = {};
  let seq = 0;

  function toRow(m) {
    m = Math.max(0, Math.round(Number(m) || 0));
    if (m >= 1440 && m % 1440 === 0 && m / 1440 <= LIMIT.day) return { n: m / 1440, u: 'day' };
    if (m >= 60 && m % 60 === 0 && m / 60 <= LIMIT.hour) return { n: m / 60, u: 'hour' };
    if (m <= LIMIT.min) return { n: m, u: 'min' };
    return m >= 1440 ? { n: Math.min(LIMIT.day, Math.round(m / 1440)), u: 'day' } : { n: Math.round(m / 60), u: 'hour' };
  }

  function unitLabel(u, n) {
    return ({ min: t('min'), hour: n === 1 ? t('hour') : t('hours'), day: n === 1 ? t('day') : t('days') })[u];
  }

  function create(opts) {
    const id = 'nt' + (++seq);
    const o = Object.assign({ minRows: 0, allowZero: false, maxDays: LIMIT.day, emptyText: t('No alerts for this class.'), when: () => '' }, opts);
    let list = [];

    const api = {
      set(minutesList) { list = (minutesList || []).slice(0, MAX).map(toRow); api.render(); },
      minutes() {
        const seen = new Set();
        return list.map(a => a.n * UNITS[a.u]).filter(m => !seen.has(m) && seen.add(m)).sort((a, b) => b - a);
      },
      render() {
        const wrap = document.getElementById(o.rows);
        const add = document.getElementById(o.add);
        if (!wrap) return;
        const lastOne = list.length <= o.minRows;
        wrap.innerHTML = list.length ? list.map((a, i) => {
          const when = o.when(a.n * UNITS[a.u]);
          return `<div class="nt-row">
            <span class="nt-bell">${BELL}</span>
            <span class="nt-stp">
              <button type="button" aria-label="−" onclick="RKNotify._step('${id}',${i},-1)">−</button>
              <input type="text" inputmode="numeric" value="${a.n}" aria-label="${t('Notify me')}" onchange="RKNotify._type('${id}',${i},this.value)">
              <button type="button" aria-label="+" onclick="RKNotify._step('${id}',${i},1)">+</button>
            </span>
            <select aria-label="${t('Unit')}" onchange="RKNotify._unit('${id}',${i},this.value)">
              ${Object.keys(UNITS).map(u => `<option value="${u}" ${u === a.u ? 'selected' : ''}>${unitLabel(u, a.n)}</option>`).join('')}
            </select>
            <span class="nt-when">${t('before')}${when ? `<b>${when}</b>` : ''}</span>
            <button type="button" class="nt-del" aria-label="${t('Remove')}" ${lastOne ? 'disabled' : ''} onclick="RKNotify._remove('${id}',${i})">✕</button>
          </div>`;
        }).join('') : `<p class="nt-empty">${o.emptyText}</p>`;
        if (add) {
          add.disabled = list.length >= MAX;
          add.textContent = add.disabled ? t('Max 3 alerts') : '+ ' + t('Add alert');
          add.onclick = () => api.add();
        }
      },
      add() {
        if (list.length >= MAX) return;
        // Suggest one not already used: 15 min → 1 hour → 1 day …
        const used = new Set(api.minutes());
        const next = [15, 60, 1440, 30, 180, 4320, 5].find(m => !used.has(m)) || 120;
        list.push(toRow(next));
        api.render();
      },
      _clamp(a) {
        const lo = (a.u === 'min' && o.allowZero) ? 0 : 1;
        a.n = Math.min(a.u === 'day' ? o.maxDays : LIMIT[a.u], Math.max(lo, Math.round(a.n) || lo));
      },
      _list: () => list,
    };
    registry[id] = api;
    return api;
  }

  const row = (id, i) => registry[id] && registry[id]._list()[i];
  return {
    create,
    _step(id, i, d) { const a = row(id, i); if (!a) return; a.n += d; registry[id]._clamp(a); registry[id].render(); },
    _type(id, i, v) { const a = row(id, i); if (!a) return; a.n = parseInt(v, 10); registry[id]._clamp(a); registry[id].render(); },
    _unit(id, i, u) { const a = row(id, i); if (!a) return; a.u = u; registry[id]._clamp(a); registry[id].render(); },
    _remove(id, i) { const l = registry[id] && registry[id]._list(); if (!l) return; l.splice(i, 1); registry[id].render(); },
  };
})();
</script>
