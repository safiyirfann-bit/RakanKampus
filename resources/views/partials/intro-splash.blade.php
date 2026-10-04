{{--
  Intro before the Login page (±7 s, Ghibli-style 2D):
  the RakanKampus robot floats down, four students (Aiman, Nurul, Mei Ling, Haziq)
  walk in, each gets help along a rainbow of light (timetable, answers, reminders,
  campus info), everyone celebrates, then the scene fades into the real login card.

  - Plays once a day per browser (localStorage "rk_intro_day"); add ?intro=1 to the
    URL to play it again (handy for demos).
  - "Skip ›" ends it straight away. Skipped for people who ask for reduced motion.
  - Pure SVG + JS, no libraries, nothing to download.
--}}
@php
  $rkiText = [
    'cal' => __('Class timetable'),
    'chat' => __('Question answered'),
    'clock' => __('Task reminder'),
    'book' => __('Campus info'),
  ];
@endphp
<style>
  #rki{position:fixed;inset:0;z-index:9999;overflow:hidden;
    background:linear-gradient(180deg,#6fb7e6 0%,#b9def4 29%,#fff1d6 46%,#9fc4c9 58%,#8cc265 64%,#6aa84f 100%);
    font-family:'Plus Jakarta Sans', -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Helvetica,Arial,sans-serif;transition:opacity .2s}
  #rki-stage{position:absolute;left:50%;top:50%;width:360px;height:640px;transform-origin:50% 50%}
  /* the sky, hills and grass run on past the 360×640 scene, so any screen shape is filled edge to edge */
  #rki-art{position:absolute;inset:0;width:360px;height:640px;transform-origin:50% 60%;overflow:visible}
  .rki-cap{position:absolute;left:0;right:0;top:62px;text-align:center;font-weight:800;font-size:21px;color:#fff;text-shadow:0 2px 10px rgba(40,70,110,.45);opacity:0}
  .rki-cap small{display:block;font-size:12.5px;font-weight:600;margin-top:3px}
  .rki-pill{position:absolute;left:50%;width:250px;margin-left:-125px;top:128px;background:rgba(255,253,246,.95);color:#4a3b2e;border:1.5px solid #c9b79c;border-radius:14px;padding:8px 10px;font-size:12.5px;font-weight:700;box-shadow:0 8px 18px rgba(60,40,20,.15);text-align:center;opacity:0}
  .rki-pill b{color:#3f9b5a}
  #rki-skip{position:absolute;top:calc(14px + env(safe-area-inset-top,0px));right:16px;z-index:2;font:800 13px/1 inherit;font-family:inherit;padding:9px 14px;border-radius:99px;border:0;cursor:pointer;background:rgba(255,255,255,.8);color:#4a3b2e;box-shadow:0 4px 12px rgba(0,0,0,.12)}
  #rki-skip:hover{background:#fff}
  /* the real login card rises in as the intro fades */
  @keyframes rkiCardIn{from{opacity:0;transform:translateY(40px) scale(.96)}to{opacity:1;transform:none}}
  .rki-enter .card{animation:rkiCardIn .8s cubic-bezier(.2,.8,.2,1) both}
</style>
<div id="rki" aria-hidden="true">
  <div id="rki-stage">
    <svg id="rki-art" viewBox="0 0 360 640"></svg>
    <div class="rki-cap" id="rki-cap1">{{ __("You're not alone") }}<small>{{ __('RakanKampus helps every student') }}</small></div>
    <div class="rki-cap" id="rki-cap2">{{ __('Learning made easier') }}<small>{{ __('With RakanKampus, every day') }}</small></div>
    <div id="rki-pills"></div>
  </div>
  <button type="button" id="rki-skip">{{ __('Skip') }} ›</button>
</div>
<script>
(function () {
  const root = document.getElementById('rki');
  const TX = @json($rkiText);
  const today = new Date().toDateString();
  let seen = null; try { seen = localStorage.getItem('rk_intro_day'); } catch (e) {}
  const force = /[?&]intro=1/.test(location.search);
  const reduce = window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches;
  if (!force && (seen === today || reduce)) { root.remove(); return; }
  try { localStorage.setItem('rk_intro_day', today); } catch (e) {}
@verbatim
  const D = 9, LINE = '#5b4636';
  const $ = (id) => document.getElementById('rki-' + id);
  const clamp = (x, a = 0, b = 1) => Math.min(b, Math.max(a, x));
  const lerp = (a, b, k) => a + (b - a) * k;
  const ease = (k) => k < .5 ? 2 * k * k : 1 - Math.pow(-2 * k + 2, 2) / 2;
  const seg = (t, a, b) => ease(clamp((t - a) / (b - a)));
  const pulse = (t, a, d) => { const k = (t - a) / d; return k < 0 || k > 1 ? 0 : Math.sin(k * Math.PI); };

  // ================= characters (soft hand-drawn look, warm brown lines) =================
  const STUDENTS = [
    { id: 'aiman', name: 'Aiman', hair: 'short', hairC: '#2f2521', skin: '#f1c9a5', shirt: '#e9b949', pants: '#6b5a4a', fx: 62, fy: 580, sc: 1.0, from: -90, help: '📅 ' + TX.cal, icon: 'cal' },
    { id: 'nurul', name: 'Nurul', hair: 'hijab', hairC: '#d99aa5', skin: '#f3cfb0', shirt: '#b8a6d9', pants: '#6d6399', skirt: true, fx: 298, fy: 580, sc: 1.0, from: 450, help: '💬 ' + TX.chat, icon: 'chat' },
    { id: 'mei', name: 'Mei Ling', hair: 'long', hairC: '#231d2c', skin: '#f6d8bd', shirt: '#f6ecd9', pants: '#b5654e', skirt: true, ribbon: '#d9534f', fx: 100, fy: 488, sc: .8, from: -90, help: '⏰ ' + TX.clock, icon: 'clock' },
    { id: 'haziq', name: 'Haziq', hair: 'side', hairC: '#4a3222', skin: '#e5b58d', shirt: '#8fb996', pants: '#3e4c6b', glasses: true, fx: 262, fy: 488, sc: .8, from: 450, help: '🏫 ' + TX.book, icon: 'book' },
  ];
  function person(p) {
    const L = `stroke="${LINE}" stroke-width="1.7" stroke-linejoin="round" stroke-linecap="round"`;
    const limb = (d, c, w) => `<path d="${d}" stroke="${LINE}" stroke-width="${w + 3.4}" stroke-linecap="round" fill="none"/><path d="${d}" stroke="${c}" stroke-width="${w}" stroke-linecap="round" fill="none"/>`;
    let backHair = '', frontHair = '', ears = `<ellipse cx="24" cy="60" rx="4.5" ry="6" fill="${p.skin}" ${L}/><ellipse cx="76" cy="60" rx="4.5" ry="6" fill="${p.skin}" ${L}/>`;
    if (p.hair === 'long') backHair = `<path d="M22 52 Q16 104 28 116 Q50 122 72 116 Q84 104 78 52 Z" fill="${p.hairC}" ${L}/>`;
    if (p.hair === 'hijab') { ears = ''; backHair = `<path d="M17 60 Q14 112 30 124 Q50 130 70 124 Q86 112 83 60 Q82 22 50 21 Q18 22 17 60 Z" fill="${p.hairC}" ${L}/><path d="M22 70 Q20 108 32 118" stroke="#c0808c" stroke-width="2" fill="none"/>`; }
    if (p.hair === 'short') frontHair = `<path d="M23 60 Q20 27 50 25 Q80 27 77 60 Q73 45 64 41 Q60 47 52 42 Q44 48 38 43 Q30 47 23 60 Z" fill="${p.hairC}" ${L}/><path d="M40 30 Q48 27 58 30" stroke="#5a4a44" stroke-width="2" fill="none" opacity=".6"/>`;
    if (p.hair === 'side') frontHair = `<path d="M23 62 Q18 26 54 24 Q82 27 77 58 Q72 38 50 42 Q40 44 34 50 Q28 54 23 62 Z" fill="${p.hairC}" ${L}/><path d="M50 30 Q62 30 70 38" stroke="#7a5a44" stroke-width="2" fill="none" opacity=".7"/>`;
    if (p.hair === 'long') frontHair = `<path d="M23 64 Q20 26 50 24 Q80 26 77 64 Q74 46 64 38 Q56 48 44 40 Q32 48 23 64 Z" fill="${p.hairC}" ${L}/><path d="M66 30 l9 -6 l2 10 z M66 30 l-4 -9 l10 1 z" fill="${p.ribbon}" ${L}/>`;
    if (p.hair === 'hijab') frontHair = `<path d="M21 64 Q20 26 50 26 Q80 26 79 64 L73 62 Q72 36 50 35 Q28 36 27 62 Z" fill="${p.hairC}" ${L}/><path d="M27 76 Q50 98 73 76 L82 98 Q50 114 18 98 Z" fill="${p.hairC}" ${L}/>`;
    const bottom = p.skirt
      ? `<path d="M29 122 L71 122 L79 184 Q50 190 21 184 Z" fill="${p.pants}" ${L}/><path d="M40 130 L36 182 M60 130 L64 182" stroke="rgba(0,0,0,.12)" stroke-width="2"/>`
      : `<path d="M30 122 L70 122 L70 134 Q50 138 30 134 Z" fill="${p.pants}" ${L}/>`;
    const legs = [42, 58].map((x, i) => `<g class="leg" data-cx="${x}" data-cy="126">${p.skirt ? '' : `<rect x="${x - 7}" y="124" width="14" height="62" rx="6" fill="${p.pants}" ${L}/>`}<ellipse cx="${x + (i ? 2 : -2)}" cy="190" rx="10" ry="6" fill="#5b4636"/></g>`).join('');
    const arm = (side) => { const sx = side < 0 ? 30 : 70, hx = side < 0 ? 20 : 80;
      return `<g class="arm" data-cx="${sx}" data-cy="98">${limb(`M${sx} 98 Q${hx} 108 ${hx + side * 1} 124`, p.shirt, 11)}${limb(`M${hx + side} 124 L${hx + side * 2} 136`, p.skin, 8)}<circle cx="${hx + side * 2}" cy="140" r="6" fill="${p.skin}" ${L}/></g>`; };
    return `<g id="rki-${p.id}"><ellipse cx="50" cy="194" rx="32" ry="6" fill="#3d5a2a" opacity=".22"/>
    <g class="body">${backHair}${legs}${arm(-1)}
      <path d="M30 96 Q50 88 70 96 L73 128 Q50 134 27 128 Z" fill="${p.shirt}" ${L}/>
      <path d="M58 96 Q66 98 70 104 L72 126 Q64 129 58 128 Z" fill="rgba(0,0,0,.08)"/>
      ${p.hair === 'hijab' ? '' : `<path d="M43 92 L50 101 L57 92" fill="#fffaf0" ${L}/>`}
      ${bottom}${arm(1)}
      <rect x="44" y="80" width="12" height="14" rx="4" fill="${p.skin}" ${L}/>
      <g class="head" data-cx="50" data-cy="84">${ears}<ellipse cx="50" cy="60" rx="26" ry="28" fill="${p.skin}" ${L}/>
        <path d="M62 44 Q74 54 72 72 Q70 82 60 86 Q70 70 62 44 Z" fill="rgba(160,90,60,.10)"/>
        ${frontHair}
        <path d="M36 51 Q41 48 46 51 M54 51 Q59 48 64 51" stroke="${LINE}" stroke-width="1.8" fill="none" stroke-linecap="round"/>
        <g class="eyes" data-cx="50" data-cy="61"><ellipse cx="41" cy="61" rx="3.3" ry="4.4" fill="#3b2a20"/><ellipse cx="59" cy="61" rx="3.3" ry="4.4" fill="#3b2a20"/><circle cx="42.2" cy="59.4" r="1.2" fill="#fff"/><circle cx="60.2" cy="59.4" r="1.2" fill="#fff"/></g>
        ${p.glasses ? `<g fill="none" stroke="${LINE}" stroke-width="1.6"><circle cx="41" cy="61" r="7"/><circle cx="59" cy="61" r="7"/><path d="M48 61 h4"/></g>` : ''}
        <ellipse cx="34" cy="70" rx="4.5" ry="2.6" fill="#f2998f" opacity=".45"/><ellipse cx="66" cy="70" rx="4.5" ry="2.6" fill="#f2998f" opacity=".45"/>
        <path d="M49 66 q1 2 2 0" stroke="${LINE}" stroke-width="1.2" fill="none"/>
        <path class="mouth" d="M45 73 Q50 76 55 73" stroke="${LINE}" stroke-width="1.7" fill="none" stroke-linecap="round"/>
      </g></g></g>`;
  }
  function robot(id) {
    const L = `stroke="${LINE}" stroke-width="1.8" stroke-linejoin="round"`;
    return `<g id="rki-${id}"><g class="body">
      <g class="arm" data-cx="34" data-cy="104"><path d="M34 100 Q14 108 12 128 Q12 138 22 136 Q30 122 38 116 Z" fill="#efe7d6" ${L}/></g>
      <g class="arm" data-cx="106" data-cy="104"><path d="M106 100 Q126 108 128 128 Q128 138 118 136 Q110 122 102 116 Z" fill="#efe7d6" ${L}/></g>
      <path d="M30 108 Q30 80 70 78 Q110 80 110 108 L106 146 Q70 166 34 146 Z" fill="#f7f1e4" ${L}/>
      <path d="M86 84 Q106 92 106 112 L103 144 Q92 152 82 154 Q96 120 86 84 Z" fill="rgba(150,110,70,.12)"/>
      <circle cx="70" cy="118" r="15" fill="#5cc7b5" ${L}/><text x="70" y="123.5" text-anchor="middle" font-family="Arial" font-weight="900" font-size="14" fill="#fff">AI</text>
      <line x1="70" y1="14" x2="70" y2="-2" stroke="${LINE}" stroke-width="2.4"/><circle class="bulb" cx="70" cy="-6" r="6.5" fill="#ffd66b" ${L}/>
      <circle cx="24" cy="50" r="9" fill="#e7c07a" ${L}/><circle cx="116" cy="50" r="9" fill="#e7c07a" ${L}/>
      <ellipse cx="70" cy="50" rx="46" ry="38" fill="#fbf7ee" ${L}/>
      <path d="M92 20 Q116 34 114 58 Q110 80 90 86 Q108 60 92 20 Z" fill="rgba(150,110,70,.10)"/>
      <rect x="36" y="32" width="68" height="38" rx="19" fill="#2e3a46" ${L}/>
      <g class="eyes" data-cx="70" data-cy="50"><ellipse cx="56" cy="50" rx="7" ry="8.5" fill="#8ff5e2" filter="url(#rki-glow)"/><ellipse cx="84" cy="50" rx="7" ry="8.5" fill="#8ff5e2" filter="url(#rki-glow)"/><circle cx="58" cy="47" r="2" fill="#fff"/><circle cx="86" cy="47" r="2" fill="#fff"/></g>
      <path class="mouth" d="M64 62 Q70 66 76 62" stroke="#8ff5e2" stroke-width="2" fill="none" stroke-linecap="round"/>
      <ellipse cx="70" cy="170" rx="20" ry="5" fill="#bff7ec" opacity=".6" filter="url(#rki-glow)"/>
    </g></g>`;
  }
  const ICON = {
    cal: `<rect x="-9" y="-8" width="18" height="16" rx="3" fill="#fff" stroke="${LINE}" stroke-width="1.6"/><path d="M-9 -3 h18" stroke="${LINE}" stroke-width="1.6"/><rect x="-5" y="1" width="4" height="3" fill="#e9b949"/><rect x="1" y="1" width="4" height="3" fill="#e9b949"/>`,
    chat: `<path d="M-10 -8 h20 a3 3 0 0 1 3 3 v9 a3 3 0 0 1 -3 3 h-12 l-6 5 v-5 h-2 a3 3 0 0 1 -3 -3 v-9 a3 3 0 0 1 3 -3z" fill="#fff" stroke="${LINE}" stroke-width="1.6"/><g fill="${LINE}"><circle cx="-5" cy="-.5" r="1.6"/><circle cx="0" cy="-.5" r="1.6"/><circle cx="5" cy="-.5" r="1.6"/></g>`,
    clock: `<circle r="9" fill="#fff" stroke="${LINE}" stroke-width="1.6"/><path d="M0 -5 V0 L4 3" stroke="${LINE}" stroke-width="1.8" fill="none" stroke-linecap="round"/>`,
    book: `<path d="M-10 -7 h8 a2 2 0 0 1 2 1 a2 2 0 0 1 2 -1 h8 v15 h-8 a2 2 0 0 0 -2 1 a2 2 0 0 0 -2 -1 h-8z" fill="#fff" stroke="${LINE}" stroke-width="1.6"/><path d="M0 -6 V9" stroke="${LINE}" stroke-width="1.4"/>`,
  };
  const ICON_BG = { cal: '#fde2a8', chat: '#cfe3f6', clock: '#e2d8f5', book: '#cdeccf' };

  // ================= scenery: painted sky, clouds, hills, little campus =================
  function cloud(x, y, s, id) {
    return `<g id="rki-${id}" transform="translate(${x} ${y}) scale(${s})"><g fill="#fff">
      <circle cx="0" cy="0" r="26"/><circle cx="28" cy="-12" r="32"/><circle cx="62" cy="-4" r="26"/><circle cx="86" cy="8" r="18"/><circle cx="-22" cy="10" r="16"/><rect x="-22" y="4" width="110" height="22" rx="11"/></g>
      <path d="M-34 18 Q30 34 100 18 Q60 30 -34 18Z" fill="#dbe7f3"/></g>`;
  }
  const art = $('art');
  art.innerHTML = `<defs>
      <linearGradient id="rki-sky" gradientUnits="userSpaceOnUse" x1="0" y1="0" x2="0" y2="640"><stop offset="0" stop-color="#6fb7e6"/><stop offset=".45" stop-color="#b9def4"/><stop offset=".72" stop-color="#fff1d6"/></linearGradient>
      <linearGradient id="rki-grass" gradientUnits="userSpaceOnUse" x1="0" y1="430" x2="0" y2="640"><stop offset="0" stop-color="#a8d672"/><stop offset="1" stop-color="#6aa84f"/></linearGradient>
      <radialGradient id="rki-sunG"><stop offset="0" stop-color="#fffbe6"/><stop offset=".4" stop-color="#fff2b8" stop-opacity=".8"/><stop offset="1" stop-color="#fff2b8" stop-opacity="0"/></radialGradient>
      <filter id="rki-glow"><feGaussianBlur stdDeviation="2.4" result="b"/><feMerge><feMergeNode in="b"/><feMergeNode in="SourceGraphic"/></feMerge></filter>
      <filter id="rki-soft"><feGaussianBlur stdDeviation="1.2"/></filter>
      <linearGradient id="rki-beam" x1="0" x2="1"><stop offset="0" stop-color="#ffb347"/><stop offset=".5" stop-color="#ff7eb6"/><stop offset="1" stop-color="#3fd0c0"/></linearGradient><filter id="rki-bglow" x="-50%" y="-50%" width="200%" height="200%"><feGaussianBlur stdDeviation="5"/></filter>
    </defs>
    <rect x="-600" y="-900" width="1560" height="1700" fill="url(#rki-sky)"/>
    <circle cx="290" cy="150" r="110" fill="url(#rki-sunG)"/>
    ${cloud(20, 200, .9, 'cl1')}${cloud(220, 118, .7, 'cl2')}${cloud(150, 270, .5, 'cl3')}
    <path d="M-600 380 L-10 372 Q60 318 130 350 Q200 300 280 342 Q330 320 380 340 L960 346 V420 H-600Z" fill="#9fc4c9" opacity=".85"/>
    <g transform="translate(196 318)">
      <rect x="0" y="16" width="58" height="30" fill="#fbf1de" stroke="${LINE}" stroke-width="1.2"/><path d="M-6 18 L29 0 L64 18 Z" fill="#4f9a8f" stroke="${LINE}" stroke-width="1.2"/>
      <rect x="66" y="24" width="30" height="22" fill="#fbf1de" stroke="${LINE}" stroke-width="1.2"/><path d="M62 26 L81 14 L100 26 Z" fill="#c9674f" stroke="${LINE}" stroke-width="1.2"/>
      <g fill="#8fb3c9">${[6, 20, 34, 72, 84].map(x => `<rect x="${x}" y="${x > 60 ? 30 : 24}" width="7" height="8"/>`).join('')}</g><rect x="24" y="34" width="10" height="12" fill="#b08968"/>
    </g>
    <path d="M-600 410 L-10 404 Q90 360 190 392 Q280 368 380 388 L960 392 V1600 H-600Z" fill="#8cc265"/>
    <path d="M-600 436 L-10 430 Q120 400 240 424 Q320 410 380 420 L960 424 V1600 H-600Z" fill="url(#rki-grass)"/>
    <g transform="translate(18 350)"><rect x="-4" y="30" width="9" height="46" rx="3" fill="#7a5a3c" stroke="${LINE}" stroke-width="1.2"/>
      <g fill="#5f9e46" stroke="${LINE}" stroke-width="1.2"><circle cx="-16" cy="22" r="22"/><circle cx="14" cy="18" r="24"/><circle cx="0" cy="-2" r="24"/></g><path d="M-8 -14 Q6 -18 16 -8" stroke="#8cc265" stroke-width="3" fill="none"/></g>
    <g id="rki-flowers">${(() => { let r = 7; const rnd = () => (r = (r * 9301 + 49297) % 233280) / 233280; let out = '';
      for (let i = 0; i < 34; i++) { const x = rnd() * 360, y = 432 + rnd() * 200, s = .7 + (y - 432) / 200 * .8;
        out += i % 2 ? `<g transform="translate(${x} ${y}) scale(${s})"><path d="M0 0 v-7" stroke="#4d7f3a" stroke-width="1.3"/><g fill="${['#fff', '#ffd6e0', '#fff3a8', '#e6d9ff'][i % 4]}"><circle cx="-2" cy="-8" r="2"/><circle cx="2" cy="-8" r="2"/><circle cx="0" cy="-10.5" r="2"/><circle cx="0" cy="-6" r="2"/></g><circle cy="-8" r="1.3" fill="#f2b33d"/></g>`
                   : `<path transform="translate(${x} ${y}) scale(${s})" d="M-6 0 Q-5 -8 -3 -10 Q-2 -5 0 -12 Q2 -5 3 -9 Q5 -4 6 0 Z" fill="#5e9c43" opacity=".8"/>`; }
      return out; })()}</g>
    <g id="rki-specks">${Array.from({ length: 16 }, (_, i) => `<circle cx="${(i * 83) % 350}" cy="${200 + (i * 53) % 330}" r="${i % 3 ? 1.6 : 2.4}" fill="#fffbe0" opacity=".85"/>`).join('')}</g>
    <g id="rki-beams"></g>
    <g id="rki-back"></g><g id="rki-botLayer"></g><g id="rki-front"></g>
    <g id="rki-icons"></g>
    <g id="rki-petals"></g>`;
  // characters in depth order: back row, robot, front row
  const put = (layer, html) => $(layer).insertAdjacentHTML('beforeend', html);
  STUDENTS.forEach(p => put(p.sc < 1 ? 'back' : 'front', person(p)));
  put('botLayer', robot('ai'));
  // light beams + travelling icons
  STUDENTS.forEach((p, i) => {
    put('beams', `<g id="rki-bmg${i}" opacity="0"><path class="bw" fill="none" stroke="#fffbe8" stroke-width="14" stroke-linecap="round" filter="url(#rki-bglow)"/><path class="bc" fill="none" stroke="url(#rki-beam)" stroke-width="4.5" stroke-linecap="round"/><path class="bh" fill="none" stroke="#fff" stroke-width="1.6" stroke-linecap="round" opacity=".8"/></g>`);
    put('icons', `<g id="rki-ic${i}" opacity="0"><circle r="15" fill="${ICON_BG[p.icon]}" stroke="${LINE}" stroke-width="1.8"/>${ICON[p.icon]}</g>`);
    $('pills').insertAdjacentHTML('beforeend', `<div class="rki-pill" id="rki-pl${i}">${p.name} · ${p.help} <b>✓</b></div>`);
  });
  put('petals', Array.from({ length: 26 }, (_, i) => `<path class="pt" d="M0 0 q4 -6 8 0 q-4 6 -8 0z" fill="${['#ffc2d1', '#fff', '#ffe08a', '#c8f0c0'][i % 4]}" stroke="${LINE}" stroke-width=".6" opacity="0"/>`).join(''));

  // ================= timeline =================
  const SEND = [2.1, 2.95, 3.8, 4.65], FLY = .75;
  const headOf = (p) => ({ x: p.fx, y: p.fy - (200 - 60) * p.sc });
  const AI = { x: 180, base: 522, sc: 1.02 };
  const bulb = { x: AI.x, y: AI.base - 172 * AI.sc - 6 * AI.sc + 6 };
  function arcPath(p) { const h = headOf(p), cx = (bulb.x + h.x) / 2, cy = Math.min(bulb.y, h.y) - 90; return `M${bulb.x} ${bulb.y} Q${cx} ${cy} ${h.x} ${h.y - 24 * p.sc}`; }
  STUDENTS.forEach((p, i) => $('bmg' + i).querySelectorAll('path').forEach(el => el.setAttribute('d', arcPath(p))));
  const setT = (el, s) => el.setAttribute('transform', s);
  const rot = (el, a) => setT(el, `rotate(${a} ${el.dataset.cx} ${el.dataset.cy})`);
  const blink = (el, t, per, off) => { const on = ((t + off) % per) > per - .12; setT(el, on ? `translate(0 ${el.dataset.cy}) scale(1 .12) translate(0 ${-el.dataset.cy})` : ''); };
  const fade = (el, t, a, b, f = .25) => { el.style.opacity = clamp(Math.min((t - a) / f, (b - t) / f)); };

  function update(t) {
    // scenery life
    setT($('cl1'), `translate(${20 + t * 4} 200) scale(.9)`); setT($('cl2'), `translate(${220 - t * 3} 118) scale(.7)`); setT($('cl3'), `translate(${150 + t * 2} 270) scale(.5)`);
    [...$('specks').children].forEach((c, i) => { c.setAttribute('transform', `translate(${Math.sin(t * .8 + i) * 6} ${-((t * 10 + i * 13) % 60)})`); c.setAttribute('opacity', .4 + .5 * Math.abs(Math.sin(t * 1.5 + i))); });
    // robot floats down from the sky, bobs, waves, sends help
    const land = seg(t, 0, 1.3), ai = $('ai');
    const ay = lerp(-260, 0, land) + Math.sin(t * 2.4) * 4 - pulse(t, 5.9, .5) * 26;
    setT(ai, `translate(${AI.x - 70 * AI.sc} ${AI.base - 172 * AI.sc + ay}) scale(${AI.sc}) rotate(${Math.sin(t * 1.7) * 2} 70 90)`);
    const [aL, aR] = ai.querySelectorAll('.arm');
    let wave = pulse(t, 1.5, .8);
    rot(aL, -wave * 70 + Math.sin(t * 16) * 10 * wave - SEND.reduce((a, s, i) => a + (STUDENTS[i].fx < 180 ? pulse(t, s - .1, .45) * 60 : 0), 0) - pulse(t, 5.9, .6) * 90);
    rot(aR, wave * 70 - Math.sin(t * 16) * 10 * wave + SEND.reduce((a, s, i) => a + (STUDENTS[i].fx > 180 ? pulse(t, s - .1, .45) * 60 : 0), 0) + pulse(t, 5.9, .6) * 90);
    blink(ai.querySelector('.eyes'), t, 2.6, 0);
    ai.querySelector('.bulb').setAttribute('fill', `hsl(45 100% ${70 + Math.sin(t * 7) * 12}%)`);
    // students walk in, wave, receive help (hop + cheer), celebrate together
    STUDENTS.forEach((p, i) => {
      const g = $(p.id), walkEnd = 1.3 + i * .12, wk = clamp(t / walkEnd), walking = t < walkEnd;
      const x = lerp(p.from, p.fx, ease(wk) * .4 + wk * .6);
      const step = walking ? Math.sin(t * 15 + i) : 0;
      const got = SEND[i] + FLY;
      const hop = pulse(t, got, .45) * 26 + pulse(t, 5.85 + i * .1, .45) * 24 + (walking ? Math.abs(step) * 3 : 0);
      setT(g, `translate(${x - 50 * p.sc} ${p.fy - 200 * p.sc - hop * p.sc}) scale(${p.sc})`);
      const [l1, l2] = g.querySelectorAll('.leg'); rot(l1, step * 22); rot(l2, -step * 22);
      const [armL, armR] = g.querySelectorAll('.arm');
      const toward = p.fx < 180 ? 1 : -1;                 // arm on the robot's side
      const hi = pulse(t, 1.6 + i * .08, .8), cheer = pulse(t, got, .6), party = pulse(t, 5.8, 1);
      const near = toward > 0 ? armR : armL, far = toward > 0 ? armL : armR;
      rot(near, toward * (-(hi * 150 + Math.sin(t * 16) * 14 * hi) - cheer * 160 - party * 160) + (walking ? step * 20 : 0));
      rot(far, toward * (party * 150) - (walking ? step * 20 : 0) + toward * cheer * 30);
      rot(g.querySelector('.head'), Math.sin(t * 1.3 + i) * 3 + toward * 4 * clamp(t - 1.5));
      blink(g.querySelector('.eyes'), t, 2.9 + i * .37, i * .7);
      const happy = cheer > .05 || party > .05 || hi > .05;
      const m = g.querySelector('.mouth'); m.setAttribute('d', happy ? 'M44 71 Q50 80 56 71 Z' : 'M45 73 Q50 76 55 73'); m.setAttribute('fill', happy ? '#b5503c' : 'none');
      // beam + icon
      const bmg = $('bmg' + i), bm = bmg.querySelector('.bc'), len = bm.getTotalLength();
      const draw = seg(t, SEND[i] - .25, SEND[i] + .1), off = 1 - seg(t, 5.7, 6.0);
      bmg.querySelectorAll('path').forEach(el => { el.setAttribute('stroke-dasharray', len); el.setAttribute('stroke-dashoffset', len * (1 - draw)); });
      bmg.setAttribute('opacity', draw > 0 ? off * (.88 + .12 * Math.sin(t * 9 + i)) : 0);
      const k = (t - SEND[i]) / FLY, ic = $('ic' + i);
      if (k >= 0 && k <= 1.12) { const pt = bm.getPointAtLength(len * ease(clamp(k))); const s = k > 1 ? 1 - (k - 1) * 8 : Math.min(1, k * 5); setT(ic, `translate(${pt.x} ${pt.y}) scale(${Math.max(.01, s)}) rotate(${Math.sin(k * 6) * 12})`); ic.setAttribute('opacity', 1); }
      else ic.setAttribute('opacity', 0);
      fade($('pl' + i), t, got, i === 3 ? 5.75 : got + .8, .15);
    });
    // petals rain during the celebration
    [...document.querySelectorAll('.pt')].forEach((pt, i) => { const k = (t - 5.8 - (i % 7) * .08) / 1.6;
      if (k < 0 || k > 1) { pt.setAttribute('opacity', 0); return; }
      pt.setAttribute('opacity', 1 - k * .3); setT(pt, `translate(${(i * 47) % 360 + Math.sin(k * 6 + i) * 18} ${-20 + k * 520 + (i % 5) * 20}) rotate(${k * 400 + i * 30}) scale(1.4)`); });
    fade($('cap1'), t, .3, 5.7); fade($('cap2'), t, 5.8, 7.1);
    // finale: the painting fades away and reveals the real login card underneath
    if (t >= 6.9) enter();
    root.style.opacity = 1 - seg(t, 7.0, 7.7); art.style.transform = `scale(${1 + seg(t, 6.9, 7.7) * .08})`;
  }

  // fit the 360×640 scene to any screen (phone: full screen, desktop: centred, sky/grass fill the sides)
  const stage = $('stage');
  function fit() { const s = Math.min(innerWidth / 360, innerHeight / 640); stage.style.transform = `translate(-50%,-50%) scale(${s})`; root.classList.toggle('wide', innerWidth > 360 * s + 40); }
  fit(); addEventListener('resize', fit);

  let entered = false, done = false, raf = 0;
  function enter() { if (entered) return; entered = true; document.body.classList.add('rki-enter'); }
  function finish() {
    if (done) return; done = true; cancelAnimationFrame(raf); enter();
    root.style.transition = 'opacity .35s'; root.style.opacity = 0;
    setTimeout(() => root.remove(), 380);
    removeEventListener('resize', fit);
  }
  $('skip').addEventListener('click', finish);
  addEventListener('keydown', function esc(e) { if (e.key === 'Escape') { finish(); removeEventListener('keydown', esc); } });
  const t0 = performance.now();
  (function tick() {
    const t = (performance.now() - t0) / 1000;
    if (t >= 7.75) { finish(); return; }
    update(t); raf = requestAnimationFrame(tick);
  })();
@endverbatim
})();
</script>
