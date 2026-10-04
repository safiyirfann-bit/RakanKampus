{{--
  Floating "RakanKampus" button (Reminders / Timetable → opens the AI capture modal).
  - The robot strolls from one end of the button to the other, passing
    behind the word "RakanKampus", turning round at each end.
  - A beam of light runs round the border like a loading ring, with a soft
    pulsing glow outside it.
  Positioning (fixed, bottom-right) still comes from the page's own .ai-fab rules.
--}}
@once
<style>
  html body button.ai-fab.rkfab {
    background: transparent !important; border: none; padding: 0; overflow: visible;
    border-radius: 999px; cursor: pointer; isolation: isolate;
    box-shadow: 0 8px 18px rgba(0,0,0,0.22);
    animation: rkfabGlow 2.4s ease-in-out infinite;
  }
  /* the spinning light, clipped to a thin ring */
  .rkfab .rkfab-ring {
    position: relative; display: block; border-radius: 999px; padding: 2.5px; overflow: hidden;
    background: rgba(46, 196, 198, 0.25);
  }
  .rkfab .rkfab-ring::before {
    content: ''; position: absolute; left: 50%; top: 50%; width: 170%; aspect-ratio: 1;
    transform: translate(-50%, -50%) rotate(0deg);
    background: conic-gradient(from 0deg, transparent 0 58%, #22d3ee 70%, #34d399 80%, #facc15 90%, #ffffff 95%, transparent 100%);
    animation: rkfabSpin 2.4s linear infinite;
  }
  .rkfab .rkfab-body {
    position: relative; z-index: 1; display: block; overflow: hidden;
    border-radius: 999px; background: #ffffff;
    width: 176px; height: 42px;
  }
  .rkfab .rkfab-label {
    position: absolute; inset: 0; z-index: 2; display: flex; align-items: center; justify-content: center;
    font-size: 13px; font-weight: 800; letter-spacing: 0.01em; color: #14213d; pointer-events: none;
    text-shadow: 0 0 4px #fff, 0 0 8px #fff, 0 0 2px #fff;
  }
  /* the robot's lane — it walks the full width, underneath the label */
  html body .rkfab .rkfab-bot {
    position: absolute; z-index: 1; bottom: 3px; left: 4px;
    width: 46px; height: 32px; opacity: .95;
    animation: rkfabWalk 7s ease-in-out infinite;
  }
  .rkfab .rkfab-bot .rb-walk { animation: none; }            /* the lane does the walking */
  .rkfab .rkfab-bot .rb-face { animation-duration: 7s; }       /* turn round at each end */

  @keyframes rkfabWalk { 0%, 100% { left: 2px; } 46%, 54% { left: calc(100% - 48px); } }
  @keyframes rkfabSpin { to { transform: translate(-50%, -50%) rotate(360deg); } }
  @keyframes rkfabGlow {
    0%, 100% { box-shadow: 0 8px 18px rgba(0,0,0,.22), 0 0 10px rgba(34, 211, 238, .45); }
    50%      { box-shadow: 0 8px 18px rgba(0,0,0,.22), 0 0 18px rgba(250, 204, 21, .45); }
  }

  html body button.ai-fab.rkfab:hover .rkfab-ring::before { animation-duration: 1s; }
  html body button.ai-fab.rkfab:active { transform: scale(.97); }
  html body button.ai-fab.rkfab:focus-visible { outline: 3px solid #2ec4c6; outline-offset: 3px; }

  html[data-theme="dark"] .rkfab .rkfab-body { background: #172233; }
  html[data-theme="dark"] .rkfab .rkfab-label { color: #e2e8f0; text-shadow: 0 0 4px #172233, 0 0 8px #172233, 0 0 2px #172233; }
  /* navy robot on a dark button: give it a light outline + teal glow so it still reads */
  html[data-theme="dark"] .rkfab .rkfab-bot { background: none !important; padding: 0 !important; filter: drop-shadow(0 0 0.6px #e2e8f0) drop-shadow(0 0 0.6px #e2e8f0) drop-shadow(0 0 4px rgba(46,196,198,.55)); }
  html[data-theme="dark"] .rkfab .rkfab-bot .rb-shadow { fill: #000; }

  @media (prefers-reduced-motion: reduce) {
    html body button.ai-fab.rkfab, .rkfab .rkfab-ring::before, html body .rkfab .rkfab-bot { animation: none !important; }
  }
</style>
@endonce
<button type="button" {{ $attributes->merge(['class' => 'ai-fab rkfab', 'aria-label' => 'RakanKampus']) }}>
  <span class="rkfab-ring">
    <span class="rkfab-body">
      <x-brand-bot-animated :size="32" class="rkfab-bot" />
      <span class="rkfab-label">RakanKampus</span>
    </span>
  </span>
</button>
