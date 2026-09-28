@props(['size' => 26])
{{--
  The RakanKampus robot, alive: it strolls left and right (turning around at
  each end), bobs with every step, waves, blinks, and its antenna + chest
  lights pulse. Same drawing as <x-brand-logo>, split into parts so each
  can move. Animation stops for people who ask for reduced motion.
--}}
@once
<style>
  .rkbot { overflow: visible; display: block; }
  .rkbot * { transform-box: fill-box; }
  .rkbot .rb-walk  { animation: rbWalk 5.2s ease-in-out infinite; transform-box: view-box; }
  .rkbot .rb-face  { animation: rbFace 5.2s steps(1, end) infinite; transform-origin: center; }
  .rkbot .rb-bob   { animation: rbBob .52s ease-in-out infinite; }
  .rkbot .rb-legL  { animation: rbStep .52s ease-in-out infinite; }
  .rkbot .rb-legR  { animation: rbStep .52s ease-in-out infinite reverse; }
  .rkbot .rb-wave  { animation: rbWave 1.3s ease-in-out infinite; transform-origin: 100% 100%; }
  .rkbot .rb-eyes  { animation: rbBlink 3.8s ease-in-out infinite; transform-origin: center; }
  .rkbot .rb-light { animation: rbGlow 1.6s ease-in-out infinite; transform-origin: center; }
  .rkbot .rb-chest { animation: rbGlow 1.6s ease-in-out .8s infinite; transform-origin: center; }
  .rkbot .rb-shadow{ animation: rbShadow .52s ease-in-out infinite; transform-origin: center; }

  /* stroll: left → right, turn, right → left, turn */
  @keyframes rbWalk { 0%, 100% { transform: translateX(-55px); } 46%, 54% { transform: translateX(55px); } }
  @keyframes rbFace { 0% { transform: scaleX(1); } 50% { transform: scaleX(-1); } }
  @keyframes rbBob  { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-7px); } }
  @keyframes rbStep { 0%, 100% { transform: translateY(0) rotate(0); } 50% { transform: translateY(-9px) rotate(-8deg); } }
  @keyframes rbWave { 0%, 100% { transform: rotate(0deg); } 25% { transform: rotate(-22deg); } 75% { transform: rotate(14deg); } }
  @keyframes rbBlink { 0%, 44%, 50%, 100% { transform: scaleY(1); } 47% { transform: scaleY(.1); } }
  @keyframes rbGlow { 0%, 100% { opacity: 1; transform: scale(1); } 50% { opacity: .45; transform: scale(.8); } }
  @keyframes rbShadow { 0%, 100% { transform: scaleX(1); opacity: .22; } 50% { transform: scaleX(.8); opacity: .12; } }

  /* hover: stop strolling, do a happy hop and wave faster */
  button:hover > * .rkbot .rb-walk, .rkbot:hover .rb-walk { animation-play-state: paused; }
  button:hover .rkbot .rb-bob { animation: rbHop .45s ease-in-out infinite; }
  button:hover .rkbot .rb-wave { animation-duration: .55s; }
  @keyframes rbHop { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-18px); } }

  @media (prefers-reduced-motion: reduce) {
    .rkbot * { animation: none !important; }
  }
</style>
@endonce
<svg {{ $attributes->merge(['class' => 'rkbot']) }} width="{{ round($size * 1.48) }}" height="{{ $size }}" viewBox="-70 0 340 230" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
  <g class="rb-walk">
    <ellipse class="rb-shadow" cx="100" cy="224" rx="46" ry="6" fill="#0f172a"/>
    <g class="rb-face">
      <!-- legs & feet (step in turn) -->
      <g class="rb-legL">
        <rect x="68" y="176" width="20" height="32" rx="9" fill="#14213d"/>
        <ellipse cx="78" cy="214" rx="17" ry="9" fill="#14213d"/>
      </g>
      <g class="rb-legR">
        <rect x="112" y="176" width="20" height="32" rx="9" fill="#14213d"/>
        <ellipse cx="122" cy="214" rx="17" ry="9" fill="#14213d"/>
      </g>

      <g class="rb-bob">
        <!-- antenna -->
        <line x1="100" y1="14" x2="100" y2="30" stroke="#14213d" stroke-width="5" stroke-linecap="round"/>
        <circle class="rb-light" cx="100" cy="12" r="6" fill="#2ec4c6"/>
        <!-- head -->
        <rect x="56" y="30" width="88" height="64" rx="24" fill="#14213d"/>
        <circle cx="54" cy="58" r="14" fill="#2ec4c6"/>
        <circle cx="146" cy="58" r="14" fill="#2ec4c6"/>
        <rect x="72" y="44" width="56" height="38" rx="14" fill="#ffffff"/>
        <g class="rb-eyes">
          <circle cx="90" cy="62" r="6" fill="#14213d"/>
          <circle cx="110" cy="62" r="6" fill="#14213d"/>
        </g>
        <path d="M90 74 Q100 82 110 74" stroke="#14213d" stroke-width="4" fill="none" stroke-linecap="round"/>
        <!-- body -->
        <rect x="52" y="96" width="96" height="86" rx="26" fill="#ffffff" stroke="#14213d" stroke-width="4"/>
        <circle class="rb-chest" cx="100" cy="128" r="7" fill="#2ec4c6"/>
        <!-- waving arm + hand -->
        <g class="rb-wave">
          <path d="M58 118 Q26 108 22 76" stroke="#14213d" stroke-width="20" stroke-linecap="round" fill="none"/>
          <circle cx="24" cy="80" r="10" fill="#2ec4c6"/>
          <circle cx="22" cy="64" r="15" fill="#ffffff" stroke="#14213d" stroke-width="4"/>
          <line x1="22" y1="50" x2="14" y2="38" stroke="#14213d" stroke-width="4" stroke-linecap="round"/>
          <line x1="22" y1="49" x2="22" y2="36" stroke="#14213d" stroke-width="4" stroke-linecap="round"/>
          <line x1="22" y1="50" x2="30" y2="38" stroke="#14213d" stroke-width="4" stroke-linecap="round"/>
        </g>
        <!-- other arm -->
        <path d="M142 118 Q160 130 158 152" stroke="#14213d" stroke-width="18" stroke-linecap="round" fill="none"/>
        <circle cx="158" cy="158" r="12" fill="#14213d"/>
      </g>
    </g>
  </g>
</svg>
