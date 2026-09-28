{{--
  RakanKampus dialogs & toasts — replace the browser's grey confirm() / alert() / prompt().
  The RakanKampus robot acts out what's about to happen (carries a timetable to the
  bin, files a reminder into History, waves goodbye, scratches its head…).

    await RKDialog.confirm({ scene, title, message, pill, list: [{label, meta}], warn,
                             confirmText, cancelText, tone: 'danger'|'primary' })   → true/false
    await RKDialog.prompt({ scene, title, message, value, placeholder, confirmText }) → string|null
    await RKDialog.alert({ scene, title, message, okText })
    RKDialog.confirmForm(event, opts)   // for <form onsubmit="return RKDialog.confirmForm(event, {...})">
    RKToast.show({ text, sub, undo: () => {}, duration })

  Scenes: timetable, reminder, trash, chat, signout, write, oops, happy
  Included from partials/pwa-head, so every student page has it.
--}}
<style>
  .rkd-overlay { position: fixed; inset: 0; z-index: 300; background: rgba(10, 18, 32, .55); backdrop-filter: blur(2px);
    opacity: 0; pointer-events: none; transition: opacity .2s ease; }
  .rkd-overlay.open { opacity: 1; pointer-events: auto; }
  .rkd {
    position: fixed; z-index: 301; left: 50%; top: 50%; width: min(380px, calc(100vw - 36px));
    background: #ffffff; color: #14213d; border-radius: 26px; padding: 22px 22px 20px; text-align: center;
    box-shadow: 0 30px 60px rgba(0,0,0,.35); font-family: inherit;
    transform: translate(-50%, -46%) scale(.96); opacity: 0; pointer-events: none;
    transition: transform .22s cubic-bezier(.2,.8,.2,1), opacity .2s ease;
  }
  .rkd.open { transform: translate(-50%, -50%) scale(1); opacity: 1; pointer-events: auto; }
  @media (max-width: 860px) {
    .rkd.sheet { left: 0; right: 0; top: auto; bottom: 0; width: auto; border-radius: 26px 26px 0 0;
      padding-bottom: calc(20px + env(safe-area-inset-bottom, 0px)); transform: translateY(105%); opacity: 1; }
    .rkd.sheet.open { transform: translateY(0); }
    .rkd.sheet .rkd-grab { display: block; }
  }
  .rkd-grab { display: none; width: 42px; height: 5px; border-radius: 9px; background: #dbe3ea; margin: -8px auto 10px; }
  .rkd-title { font-size: 19px; font-weight: 800; margin: 0 0 6px; color: #14213d; }
  .rkd-msg { font-size: 14px; color: #64748b; line-height: 1.5; margin: 0; }
  .rkd-pill { display: inline-flex; gap: 6px; align-items: center; margin: 12px 0 0; background: #fef2f2; color: #b91c1c;
    font-size: 12.5px; font-weight: 700; padding: 6px 12px; border-radius: 99px; }
  .rkd-list { margin: 12px 0 0; text-align: left; background: #f8fafc; border-radius: 14px; padding: 4px 12px; max-height: 150px; overflow-y: auto; }
  .rkd-list div { font-size: 13px; color: #334155; padding: 7px 0; border-bottom: 1px solid #eef2f5; display: flex; justify-content: space-between; gap: 10px; }
  .rkd-list div:last-child { border: 0; }
  .rkd-list span { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
  .rkd-list small { color: #94a3b8; flex-shrink: 0; }
  .rkd-warn { font-size: 12px; color: #b91c1c; margin: 10px 0 0; font-weight: 600; }
  .rkd-input { width: 100%; box-sizing: border-box; margin-top: 14px; border: 1.5px solid #e2e8f0; border-radius: 12px;
    padding: 12px 14px; font-size: 15px; color: #14213d; background: #fff; outline: none; font-family: inherit; }
  .rkd-input:focus { border-color: #2ec4c6; box-shadow: 0 0 0 3px rgba(46,196,198,.18); }
  .rkd-btns { display: flex; gap: 10px; margin-top: 18px; }
  .rkd-btn { flex: 1; padding: 13px; border-radius: 14px; font-weight: 800; font-size: 14.5px; border: none; cursor: pointer; font-family: inherit; }
  .rkd-cancel { background: #f1f5f9; color: #334155; }
  .rkd-ok.danger { background: #dc2626; color: #fff; box-shadow: 0 8px 18px rgba(220,38,38,.3); }
  .rkd-ok.primary { background: linear-gradient(120deg, #14213d, #2ec4c6); color: #fff; }
  .rkd-btn:active { transform: scale(.98); }

  /* ---- robot scenes ---- */
  .rkd-scene { width: 100%; height: 128px; display: block; overflow: visible; margin: -8px 0 4px; }
  .rkd-scene * { transform-box: fill-box; }
  .rkd-scene .walker, .rkd-scene .item, .rkd-scene .target, .rkd-scene .poof { transform-box: view-box; }
  .rkd-scene .light { animation: rkdGlow 1.3s ease-in-out infinite; transform-origin: center; }
  .rkd-scene .eyes { animation: rkdBlink 3.2s infinite; transform-origin: center; }
  /* carry the thing, throw it in, wave, walk back — 4.6s loop */
  .carry .walker { animation: rkdWalk 4.6s linear infinite; }
  .carry .flip { animation: rkdFlip 4.6s steps(1, end) infinite; transform-origin: center; }
  .carry .bob, .write .bob { animation: rkdBob .38s ease-in-out infinite; }
  .carry .legL { animation: rkdStep .38s ease-in-out infinite; }
  .carry .legR { animation: rkdStep .38s ease-in-out infinite reverse; }
  .carry .armsUp { animation: rkdUp 4.6s steps(1, end) infinite; }
  .carry .armsDown { animation: rkdDown 4.6s steps(1, end) infinite; }
  .carry .waveArm { animation: rkdWave .5s ease-in-out infinite; transform-origin: 0% 100%; }
  .carry .item { animation: rkdItem 4.6s linear infinite; }
  .carry .lid { animation: rkdLid 4.6s ease-in-out infinite; transform-origin: 0% 100%; }
  .carry .wob { animation: rkdWob 4.6s ease-in-out infinite; transform-origin: 50% 100%; }
  .carry .poof { animation: rkdPoof 4.6s ease-out infinite; opacity: 0; }
  @keyframes rkdWalk { 0% { transform: translateX(0); } 36% { transform: translateX(118px); } 60% { transform: translateX(118px); } 94%, 100% { transform: translateX(0); } }
  @keyframes rkdFlip { 0% { transform: scaleX(1); } 60% { transform: scaleX(-1); } 94% { transform: scaleX(1); } }
  @keyframes rkdUp { 0% { opacity: 1; } 42% { opacity: 0; } 97% { opacity: 1; } }
  @keyframes rkdDown { 0% { opacity: 0; } 42% { opacity: 1; } 97% { opacity: 0; } }
  @keyframes rkdItem { 0% { transform: translate(60px, 26px); opacity: 1; } 36% { transform: translate(178px, 26px) rotate(0); }
    44% { transform: translate(222px, -2px) rotate(25deg); } 52% { transform: translate(262px, 58px) rotate(120deg) scale(.55); opacity: 1; }
    53% { opacity: 0; } 97% { opacity: 0; transform: translate(60px, 26px); } 100% { opacity: 1; transform: translate(60px, 26px); } }
  @keyframes rkdLid { 0%, 36% { transform: rotate(0); } 42%, 52% { transform: rotate(-70deg) translateY(-4px); } 58%, 100% { transform: rotate(0); } }
  @keyframes rkdWob { 0%, 53% { transform: rotate(0); } 56% { transform: rotate(-6deg); } 59% { transform: rotate(5deg); } 62%, 100% { transform: rotate(0); } }
  @keyframes rkdPoof { 0%, 53% { opacity: 0; transform: translateY(0) scale(.5); } 58% { opacity: 1; } 72% { opacity: 0; transform: translateY(-14px) scale(1.4); } 100% { opacity: 0; } }
  @keyframes rkdBob { 50% { transform: translateY(-6px); } }
  @keyframes rkdStep { 50% { transform: translateY(-8px); } }
  @keyframes rkdWave { 50% { transform: rotate(-30deg); } }
  @keyframes rkdGlow { 50% { opacity: .4; } }
  @keyframes rkdBlink { 0%, 45%, 50%, 100% { transform: scaleY(1); } 47% { transform: scaleY(.1); } }
  /* sign out: wave bye, phone locks */
  .signout .waveArm { animation: rkdWave .9s ease-in-out infinite; transform-origin: 0% 100%; }
  .signout .upper { animation: rkdNod 1.8s ease-in-out infinite; transform-origin: 50% 100%; }
  .signout .scr { animation: rkdScr 3s ease-in-out infinite; }
  .signout .lock { animation: rkdLock 3s ease-in-out infinite; transform-origin: center; }
  .signout .zz { animation: rkdZz 3s ease-in-out infinite; }
  @keyframes rkdNod { 50% { transform: rotate(-4deg); } }
  @keyframes rkdScr { 0%, 30% { fill: #2ec4c6; } 50%, 100% { fill: #1e293b; } }
  @keyframes rkdLock { 0%, 40% { opacity: 0; transform: translateY(-22px) scale(.4); } 60%, 100% { opacity: 1; transform: translateY(0) scale(1); } }
  @keyframes rkdZz { 0%, 55% { opacity: 0; transform: translateY(6px); } 75%, 95% { opacity: 1; transform: translateY(0); } 100% { opacity: 0; } }
  /* write: pencil scribbles on a note */
  .write .pencil { animation: rkdScribble .9s ease-in-out infinite; }
  .write .line2 { animation: rkdInk 1.8s ease-in-out infinite; transform-origin: 0% 50%; }
  @keyframes rkdScribble { 0%, 100% { transform: translate(0, 0) rotate(0); } 25% { transform: translate(10px, -2px) rotate(-6deg); } 50% { transform: translate(20px, 1px) rotate(4deg); } 75% { transform: translate(8px, 2px) rotate(-3deg); } }
  @keyframes rkdInk { 0% { transform: scaleX(0); } 60%, 100% { transform: scaleX(1); } }
  /* oops: scratch head, question marks pop */
  .oops .scratch { animation: rkdScratch .5s ease-in-out infinite; transform-origin: 0% 100%; }
  .oops .upper { animation: rkdTilt 2.4s ease-in-out infinite; transform-origin: 50% 100%; }
  .oops .q { animation: rkdQ 1.8s ease-in-out infinite; transform-origin: center; }
  .oops .q2 { animation-delay: .6s; }
  @keyframes rkdScratch { 50% { transform: rotate(-12deg); } }
  @keyframes rkdTilt { 0%, 100% { transform: rotate(0); } 50% { transform: rotate(6deg); } }
  @keyframes rkdQ { 0%, 100% { opacity: 0; transform: translateY(6px) scale(.6); } 40%, 70% { opacity: 1; transform: translateY(0) scale(1); } }
  /* happy (toast) */
  .happy .bob { animation: rkdHop .7s ease-in-out infinite; }
  .happy .waveArm { animation: rkdWave .6s ease-in-out infinite; transform-origin: 0% 100%; }
  @keyframes rkdHop { 50% { transform: translateY(-16px); } }

  /* ---- toast ---- */
  .rkt { position: fixed; z-index: 310; left: 50%; bottom: 24px; width: min(420px, calc(100vw - 32px));
    transform: translate(-50%, 140%); transition: transform .3s cubic-bezier(.2,.8,.2,1);
    background: #0f1d2e; color: #fff; border-radius: 16px; padding: 12px 14px 14px; display: flex; align-items: center; gap: 12px;
    box-shadow: 0 14px 30px rgba(0,0,0,.35); font-family: inherit; overflow: hidden; }
  .rkt.open { transform: translate(-50%, 0); }
  @media (max-width: 860px) { .rkt { bottom: calc(100px + env(safe-area-inset-bottom, 0px)); } }
  .rkt-bot { width: 40px; height: 40px; flex-shrink: 0; background: #ffffff; border-radius: 50%; padding: 3px; overflow: visible; }
  .rkt-txt { flex: 1; min-width: 0; font-size: 13.5px; }
  .rkt-txt b { display: block; }
  .rkt-txt span { color: #94a3b8; font-size: 12.5px; }
  .rkt-undo { background: none; border: none; color: #5eead4; font-weight: 800; font-size: 13px; cursor: pointer; padding: 6px 4px; font-family: inherit; letter-spacing: .04em; }
  .rkt-bar { position: absolute; left: 0; bottom: 0; height: 3px; background: #5eead4; width: 100%; transform-origin: 0 50%; }

  /* dark mode */
  html[data-theme="dark"] .rkd { background: #131d2b; color: #e2e8f0; box-shadow: 0 30px 60px rgba(0,0,0,.6); }
  html[data-theme="dark"] .rkd-title { color: #f1f5f9; }
  html[data-theme="dark"] .rkd-msg { color: #94a3b8; }
  html[data-theme="dark"] .rkd-grab { background: #334155; }
  html[data-theme="dark"] .rkd-pill { background: rgba(248,113,113,.14); color: #fca5a5; }
  html[data-theme="dark"] .rkd-warn { color: #fca5a5; }
  html[data-theme="dark"] .rkd-list { background: #0d1520; }
  html[data-theme="dark"] .rkd-list div { color: #cbd5e1; border-color: #1e293b; }
  html[data-theme="dark"] .rkd-input { background: #0d1520; border-color: #334155; color: #f1f5f9; }
  html[data-theme="dark"] .rkd-cancel { background: #1e293b; color: #e2e8f0; }
  html[data-theme="dark"] .rkd-scene { filter: drop-shadow(0 0 .7px #cbd5e1) drop-shadow(0 0 .7px #cbd5e1); }
  html[data-theme="dark"] .rkt { background: #1e2d42; }

  @media (prefers-reduced-motion: reduce) { .rkd-scene *, .rkt-bot * { animation: none !important; } }
</style>

<script>
(function () {
  const tt = (s) => (typeof t === 'function' ? t(s) : s);
  const esc = (s) => { const d = document.createElement('div'); d.textContent = s == null ? '' : String(s); return d.innerHTML; };

  // ---------- robot + props (same drawing as the logo) ----------
  function robot(arms, face) {
    const legs = '<g class="legL"><rect x="68" y="176" width="20" height="32" rx="9" fill="#14213d"/><ellipse cx="78" cy="214" rx="17" ry="9" fill="#14213d"/></g><g class="legR"><rect x="112" y="176" width="20" height="32" rx="9" fill="#14213d"/><ellipse cx="122" cy="214" rx="17" ry="9" fill="#14213d"/></g>';
    const eyes = face === 'focus'
      ? '<g class="eyes"><circle cx="92" cy="60" r="6" fill="#14213d"/><circle cx="112" cy="60" r="6" fill="#14213d"/></g><path d="M92 75 L108 75" stroke="#14213d" stroke-width="4" stroke-linecap="round"/>'
      : face === 'puzzled'
      ? '<path d="M82 50 L94 47 M106 52 L118 55" stroke="#14213d" stroke-width="4" stroke-linecap="round"/><g class="eyes"><circle cx="90" cy="62" r="6" fill="#14213d"/><circle cx="110" cy="63" r="4" fill="#14213d"/></g><path d="M92 76 Q100 72 108 77" stroke="#14213d" stroke-width="4" fill="none" stroke-linecap="round"/>'
      : '<g class="eyes"><circle cx="90" cy="62" r="6" fill="#14213d"/><circle cx="110" cy="62" r="6" fill="#14213d"/></g><path d="M90 74 Q100 82 110 74" stroke="#14213d" stroke-width="4" fill="none" stroke-linecap="round"/>';
    const head = '<line x1="100" y1="14" x2="100" y2="30" stroke="#14213d" stroke-width="5" stroke-linecap="round"/><circle class="light" cx="100" cy="12" r="6" fill="#2ec4c6"/><rect x="56" y="30" width="88" height="64" rx="24" fill="#14213d"/><circle cx="54" cy="58" r="14" fill="#2ec4c6"/><circle cx="146" cy="58" r="14" fill="#2ec4c6"/><rect x="72" y="44" width="56" height="38" rx="14" fill="#fff"/>' + eyes;
    const body = '<rect x="52" y="96" width="96" height="86" rx="26" fill="#fff" stroke="#14213d" stroke-width="4"/><circle class="light" cx="100" cy="128" r="7" fill="#2ec4c6"/>';
    const up = '<g class="armsUp"><path d="M58 112 Q30 80 50 30" stroke="#14213d" stroke-width="18" stroke-linecap="round" fill="none"/><circle cx="52" cy="26" r="12" fill="#2ec4c6"/><path d="M142 112 Q170 80 150 30" stroke="#14213d" stroke-width="18" stroke-linecap="round" fill="none"/><circle cx="148" cy="26" r="12" fill="#2ec4c6"/></g>';
    const leftDown = '<path d="M58 118 Q40 140 44 160" stroke="#14213d" stroke-width="18" stroke-linecap="round" fill="none"/><circle cx="44" cy="164" r="12" fill="#14213d"/>';
    const wave = '<g class="waveArm"><path d="M142 118 Q176 108 178 76" stroke="#14213d" stroke-width="18" stroke-linecap="round" fill="none"/><circle cx="178" cy="68" r="13" fill="#fff" stroke="#14213d" stroke-width="4"/></g>';
    const down = '<g class="armsDown">' + leftDown + wave + '</g>';
    const scratch = leftDown + '<g class="scratch"><path d="M142 116 Q172 96 150 44" stroke="#14213d" stroke-width="18" stroke-linecap="round" fill="none"/><circle cx="148" cy="40" r="12" fill="#2ec4c6"/></g>';
    const writeArm = leftDown + '<path d="M142 118 Q168 140 186 150" stroke="#14213d" stroke-width="18" stroke-linecap="round" fill="none"/><circle cx="190" cy="152" r="12" fill="#2ec4c6"/>';
    const armsHtml = arms === 'both' ? up + down : arms === 'down' ? down : arms === 'scratch' ? scratch : arms === 'write' ? writeArm : up;
    return '<g class="bob">' + legs + '<g class="upper">' + head + body + armsHtml + '</g></g>';
  }

  const PROPS = {
    card: '<g><rect x="-26" y="-22" width="52" height="44" rx="6" fill="#fff" stroke="#14213d" stroke-width="3"/><rect x="-26" y="-22" width="52" height="11" rx="5" fill="#2ec4c6"/><g fill="#94a3b8"><rect x="-20" y="-5" width="9" height="6" rx="1.5"/><rect x="-6" y="-5" width="9" height="6" rx="1.5"/><rect x="8" y="-5" width="9" height="6" rx="1.5"/><rect x="-20" y="5" width="9" height="6" rx="1.5"/><rect x="-6" y="5" width="9" height="6" rx="1.5" fill="#f59e0b"/><rect x="8" y="5" width="9" height="6" rx="1.5"/></g></g>',
    clock: '<g><circle cx="0" cy="2" r="19" fill="#fff" stroke="#14213d" stroke-width="3"/><path d="M0 -6 V2 L7 7" stroke="#6366f1" stroke-width="3" fill="none" stroke-linecap="round"/><circle cx="-14" cy="-16" r="6" fill="#6366f1"/><circle cx="14" cy="-16" r="6" fill="#6366f1"/></g>',
    bubbles: '<g><rect x="-26" y="-20" width="36" height="20" rx="8" fill="#2ec4c6"/><path d="M-18 0 l-4 7 l10-7z" fill="#2ec4c6"/><rect x="-6" y="-2" width="34" height="18" rx="8" fill="#e8f1f8" stroke="#94a3b8" stroke-width="2"/><g fill="#fff"><circle cx="-16" cy="-10" r="2.2"/><circle cx="-8" cy="-10" r="2.2"/><circle cx="0" cy="-10" r="2.2"/></g></g>',
    box: '<g><rect x="-22" y="-18" width="44" height="36" rx="5" fill="#e0e7ff" stroke="#6366f1" stroke-width="3"/><path d="M-22 -8h44" stroke="#6366f1" stroke-width="3"/><rect x="-8" y="-3" width="16" height="6" rx="2" fill="#6366f1"/></g>',
  };
  const BIN = '<g class="target" transform="translate(262 94)"><g class="wob"><path d="M-24 -18h48l-5 50a5 5 0 0 1-5 4h-28a5 5 0 0 1-5-4z" fill="#fca5a5"/><path d="M-10 -8v32M0 -8v32M10 -8v32" stroke="#dc2626" stroke-width="3.5" stroke-linecap="round"/></g><g class="lid"><rect x="-28" y="-28" width="56" height="9" rx="4" fill="#dc2626"/><rect x="-8" y="-35" width="16" height="9" rx="3" fill="#dc2626"/></g></g>';
  const HISTORY = '<g class="target" transform="translate(262 94)"><g class="wob"><rect x="-30" y="-16" width="60" height="50" rx="6" fill="#e0e7ff"/><rect x="-14" y="-4" width="28" height="10" rx="4" fill="#6366f1"/><text x="0" y="24" text-anchor="middle" font-size="10" font-weight="800" fill="#4f46e5" font-family="Segoe UI,Arial,sans-serif">HISTORY</text></g><g class="lid"><rect x="-34" y="-26" width="68" height="11" rx="4" fill="#6366f1"/></g></g>';

  function carry(prop, target) {
    return '<svg class="rkd-scene carry" viewBox="0 0 320 140" aria-hidden="true"><ellipse cx="160" cy="132" rx="150" ry="4" fill="#0f172a" opacity=".06"/>' + target +
      '<g class="walker"><g class="flip"><g transform="translate(18 34) scale(.42)">' + robot('both', 'focus') + '</g></g></g>' +
      '<g class="item">' + PROPS[prop] + '</g><g class="poof"><circle cx="262" cy="64" r="4" fill="#94a3b8"/><circle cx="250" cy="58" r="3" fill="#cbd5e1"/><circle cx="274" cy="56" r="3" fill="#cbd5e1"/></g></svg>';
  }
  const SCENES = {
    timetable: () => carry('card', BIN),
    reminder: () => carry('clock', HISTORY),
    trash: () => carry('box', BIN),
    chat: () => carry('bubbles', BIN),
    signout: () => '<svg class="rkd-scene signout" viewBox="0 0 320 140" aria-hidden="true"><g transform="translate(60 30) scale(.44)">' + robot('down', 'happy') + '</g>' +
      '<g transform="translate(205 20)"><rect x="0" y="0" width="58" height="100" rx="12" fill="#14213d"/><rect class="scr" x="5" y="10" width="48" height="80" rx="6" fill="#2ec4c6"/>' +
      '<g transform="translate(29 52)"><g class="lock"><rect x="-12" y="-4" width="24" height="18" rx="4" fill="#fff"/><path d="M-7 -4 v-6 a7 7 0 0 1 14 0 v6" stroke="#fff" stroke-width="4" fill="none"/></g></g></g>' +
      '<g class="zz" font-family="Segoe UI,Arial,sans-serif" font-weight="800" fill="#94a3b8"><text x="268" y="30" font-size="14">z</text><text x="280" y="18" font-size="11">z</text></g></svg>',
    write: () => '<svg class="rkd-scene write" viewBox="0 0 320 140" aria-hidden="true"><g transform="translate(70 30) scale(.44)">' + robot('write', 'focus') + '</g>' +
      '<g transform="translate(170 64)"><rect x="0" y="0" width="84" height="58" rx="8" fill="#fff" stroke="#14213d" stroke-width="3"/><rect x="12" y="14" width="56" height="5" rx="2.5" fill="#cbd5e1"/><rect class="line2" x="12" y="28" width="44" height="5" rx="2.5" fill="#2ec4c6"/><rect x="12" y="42" width="30" height="5" rx="2.5" fill="#cbd5e1"/>' +
      '<g class="pencil"><g transform="translate(14 8) rotate(40)"><rect x="0" y="-4" width="34" height="9" rx="2" fill="#f59e0b"/><path d="M34 -4 L44 0.5 L34 5 Z" fill="#fde68a"/><rect x="-6" y="-4" width="7" height="9" rx="2" fill="#fb7185"/></g></g></g></svg>',
    oops: () => '<svg class="rkd-scene oops" viewBox="0 0 320 140" aria-hidden="true"><g transform="translate(116 28) scale(.46)">' + robot('scratch', 'puzzled') + '</g>' +
      '<g font-family="Segoe UI,Arial,sans-serif" font-weight="900" fill="#f59e0b"><text class="q" x="206" y="44" font-size="26">?</text><text class="q q2" x="224" y="26" font-size="18">?</text></g></svg>',
  };
  const miniBot = () => '<svg class="rkt-bot happy" viewBox="-10 -10 220 250" aria-hidden="true">' + robot('down', 'happy') + '</svg>';

  // ---------- dialog ----------
  let els = null, resolver = null, mode = null;
  function build() {
    if (els) return els;
    const ov = document.createElement('div'); ov.className = 'rkd-overlay';
    const box = document.createElement('div'); box.className = 'rkd'; box.setAttribute('role', 'dialog'); box.setAttribute('aria-modal', 'true');
    document.body.append(ov, box);
    ov.addEventListener('click', () => finish(false));
    document.addEventListener('keydown', (e) => {
      if (!box.classList.contains('open')) return;
      if (e.key === 'Escape') finish(false);
      if (e.key === 'Enter' && mode === 'prompt') { e.preventDefault(); finish(true); }
    });
    return (els = { ov, box });
  }
  function finish(ok) {
    if (!els || !resolver) return;
    const input = els.box.querySelector('.rkd-input');
    const val = mode === 'prompt' ? (ok ? input.value : null) : mode === 'alert' ? undefined : !!ok;
    const r = resolver; resolver = null;
    els.ov.classList.remove('open'); els.box.classList.remove('open');
    r(val);
  }
  function open(opts, kind) {
    const { ov, box } = build();
    if (resolver) finish(false);
    mode = kind;
    const tone = opts.tone || (kind === 'confirm' ? 'danger' : 'primary');
    const scene = SCENES[opts.scene] ? SCENES[opts.scene]() : '';
    const list = (opts.list || []).length
      ? '<div class="rkd-list">' + opts.list.map(i => '<div><span>' + esc(i.label) + '</span>' + (i.meta ? '<small>' + esc(i.meta) + '</small>' : '') + '</div>').join('') + '</div>' : '';
    box.classList.toggle('sheet', !!opts.sheet || (opts.list || []).length > 0);
    box.innerHTML = '<div class="rkd-grab"></div>' + scene +
      '<p class="rkd-title">' + esc(opts.title || '') + '</p>' +
      (opts.message ? '<p class="rkd-msg">' + esc(opts.message) + '</p>' : '') +
      (opts.pill ? '<div class="rkd-pill">' + esc(opts.pill) + '</div>' : '') + list +
      (opts.warn ? '<p class="rkd-warn">' + esc(opts.warn) + '</p>' : '') +
      (kind === 'prompt' ? '<input class="rkd-input" type="text" maxlength="120">' : '') +
      '<div class="rkd-btns">' +
        (kind !== 'alert' ? '<button type="button" class="rkd-btn rkd-cancel">' + esc(opts.cancelText || tt('Cancel')) + '</button>' : '') +
        '<button type="button" class="rkd-btn rkd-ok ' + tone + '">' + esc(opts.confirmText || (kind === 'alert' ? tt('OK') : tt('Confirm'))) + '</button>' +
      '</div>';
    box.setAttribute('aria-label', opts.title || '');
    const input = box.querySelector('.rkd-input');
    if (input) { input.value = opts.value || ''; input.placeholder = opts.placeholder || ''; }
    box.querySelector('.rkd-cancel')?.addEventListener('click', () => finish(false));
    box.querySelector('.rkd-ok').addEventListener('click', () => finish(true));
    requestAnimationFrame(() => {
      ov.classList.add('open'); box.classList.add('open');
      setTimeout(() => { if (input) { input.focus(); input.select(); } else box.querySelector('.rkd-ok').focus(); }, 60);
    });
    return new Promise((res) => { resolver = res; });
  }

  window.RKDialog = {
    confirm: (o) => open(o || {}, 'confirm'),
    prompt: (o) => open(o || {}, 'prompt'),
    alert: (o) => open(typeof o === 'string' ? { scene: 'oops', title: tt('Oops!'), message: o } : Object.assign({ scene: 'oops' }, o), 'alert'),
    // <form onsubmit="return RKDialog.confirmForm(event, {...})">
    confirmForm(event, o) {
      event.preventDefault();
      const form = event.target;
      open(o || {}, 'confirm').then(ok => { if (ok) HTMLFormElement.prototype.submit.call(form); });
      return false;
    },
  };

  // ---------- toast ----------
  let tEl = null, tTimer = null;
  window.RKToast = {
    show(o) {
      o = o || {};
      if (!tEl) { tEl = document.createElement('div'); tEl.className = 'rkt'; tEl.setAttribute('role', 'status'); document.body.appendChild(tEl); }
      clearTimeout(tTimer);
      const dur = o.duration || (o.undo ? 5000 : 3000);
      tEl.innerHTML = miniBot() + '<div class="rkt-txt"><b>' + esc(o.text || '') + '</b>' + (o.sub ? '<span>' + esc(o.sub) + '</span>' : '') + '</div>' +
        (o.undo ? '<button type="button" class="rkt-undo">' + esc(tt('UNDO')) + '</button>' : '') + '<i class="rkt-bar"></i>';
      const bar = tEl.querySelector('.rkt-bar');
      bar.animate([{ transform: 'scaleX(1)' }, { transform: 'scaleX(0)' }], { duration: dur, easing: 'linear', fill: 'forwards' });
      if (o.undo) tEl.querySelector('.rkt-undo').addEventListener('click', () => { hide(); o.undo(); });
      requestAnimationFrame(() => tEl.classList.add('open'));
      tTimer = setTimeout(hide, dur);
      function hide() { clearTimeout(tTimer); tEl.classList.remove('open'); }
    },
  };
})();
</script>
