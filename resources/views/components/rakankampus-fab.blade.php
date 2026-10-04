{{--
  Floating "RakanKampus AI" button (Reminders / Timetable → opens the AI capture modal).
  - A round robot button with a rainbow border. Every few seconds it stretches
    open to show "RakanKampus AI · Upload & I'll fill it in", then folds back.
  - The robot is alive: it bobs, waves, blinks and hops when the button opens.
  - Hover / keyboard focus keeps it open.
  Positioning (fixed, bottom-right) still comes from the page's own .ai-fab rules.
--}}
@once
<style>
  html body button.ai-fab.rkfab {
    background: transparent !important; border: 0; padding: 0; overflow: visible; cursor: pointer;
    border-radius: 999px; box-shadow: none; isolation: isolate;
  }
  .rkfab .rkfab-pill {
    position: relative; display: flex; align-items: center; height: 62px; width: 62px; padding: 4px;
    border-radius: 999px; border: 2.5px solid transparent; overflow: hidden;
    background-image: linear-gradient(#fff, #fff), linear-gradient(120deg, #22d3ee, #a78bfa, #f472b6, #22d3ee);
    background-origin: border-box; background-clip: padding-box, border-box; background-size: 100% 100%, 300% 300%;
    box-shadow: 0 12px 26px rgba(15, 39, 71, .2), 0 0 0 0 rgba(45, 212, 191, .4);
    animation: rkfabOpen 7s ease-in-out infinite, rkfabHue 6s linear infinite, rkfabPulse 2.4s ease-out infinite;
  }
  html body button.ai-fab.rkfab:hover .rkfab-pill,
  html body button.ai-fab.rkfab:focus-visible .rkfab-pill { animation: rkfabHue 6s linear infinite; width: 252px; }
  .rkfab .rkfab-bubble {
    position: relative; flex: none; width: 50px; height: 50px; border-radius: 50%; display: grid; place-items: center;
    background: radial-gradient(circle at 35% 30%, #ffffff, #ccfbf1 60%, #a5f3fc);
  }
  .rkfab .rkfab-bot { width: 40px !important; height: 40px !important; animation: rkfabHop 7s ease-in-out infinite; transform-origin: 50% 100%; }
  .rkfab .rkfab-bot .rb-walk { animation: none !important; }   /* stay in the circle… */
  .rkfab .rkfab-bot .rb-face { animation: rkfabLook 7s steps(1, end) infinite !important; }   /* …but look left / right */
  .rkfab .rkfab-ai {
    position: absolute; top: -2px; right: -4px; z-index: 2; font-size: 9px; font-weight: 800; color: #fff; letter-spacing: .02em;
    background: linear-gradient(90deg, #8b5cf6, #ec4899); padding: 2px 6px; border-radius: 999px; border: 2px solid #fff;
    animation: rkfabBadge 7s ease-in-out infinite;
  }
  .rkfab .rkfab-text { padding: 0 14px 0 10px; white-space: nowrap; text-align: left; line-height: 1.25; opacity: 0; animation: rkfabText 7s ease-in-out infinite; }
  .rkfab .rkfab-text b { display: block; font-size: 13.5px; font-weight: 800; color: #14213d; }
  .rkfab .rkfab-text b i { font-style: normal; display: inline-block; font-size: 9px; font-weight: 800; color: #fff; background: linear-gradient(90deg, #8b5cf6, #ec4899); padding: 1px 6px; border-radius: 999px; margin-left: 4px; vertical-align: 2px; }
  .rkfab .rkfab-text small { display: block; font-size: 10.5px; font-weight: 600; color: #64748b; }
  html body button.ai-fab.rkfab:hover .rkfab-text, html body button.ai-fab.rkfab:focus-visible .rkfab-text { animation: none; opacity: 1; }

  @keyframes rkfabOpen { 0%, 18% { width: 62px; } 30%, 72% { width: 252px; } 84%, 100% { width: 62px; } }
  @keyframes rkfabText { 0%, 24% { opacity: 0; } 32%, 68% { opacity: 1; } 76%, 100% { opacity: 0; } }
  @keyframes rkfabBadge { 0%, 18%, 84%, 100% { opacity: 1; transform: scale(1); } 26%, 76% { opacity: 0; transform: scale(.4); } }
  @keyframes rkfabHop { 0%, 22%, 40%, 100% { transform: translateY(0) scale(1); } 27% { transform: translateY(-9px) scale(.95, 1.06); } 32% { transform: translateY(0) scale(1.08, .92); } 36% { transform: translateY(-3px); } }
  @keyframes rkfabLook { 0% { transform: scaleX(1); } 50% { transform: scaleX(-1); } 60% { transform: scaleX(1); } }
  @keyframes rkfabHue { to { background-position: 0 0, 300% 0; } }
  @keyframes rkfabPulse {
    0% { box-shadow: 0 12px 26px rgba(15, 39, 71, .2), 0 0 0 0 rgba(45, 212, 191, .45); }
    100% { box-shadow: 0 12px 26px rgba(15, 39, 71, .2), 0 0 0 14px rgba(45, 212, 191, 0); }
  }
  html body button.ai-fab.rkfab:active .rkfab-pill { transform: scale(.96); }
  html body button.ai-fab.rkfab:focus-visible { outline: none; }
  html body button.ai-fab.rkfab:focus-visible .rkfab-pill { box-shadow: 0 0 0 3px #2ec4c6, 0 12px 26px rgba(15, 39, 71, .2); }

  html[data-theme="dark"] .rkfab .rkfab-pill { background-image: linear-gradient(#172233, #172233), linear-gradient(120deg, #22d3ee, #a78bfa, #f472b6, #22d3ee); box-shadow: 0 12px 26px rgba(0,0,0,.5); }
  html[data-theme="dark"] .rkfab .rkfab-text b { color: #e2e8f0; }
  html[data-theme="dark"] .rkfab .rkfab-text small { color: #a5b4c3; }
  html[data-theme="dark"] .rkfab .rkfab-ai { border-color: #172233; }

  @media (prefers-reduced-motion: reduce) {
    .rkfab .rkfab-pill, .rkfab .rkfab-bot, .rkfab .rkfab-text, .rkfab .rkfab-ai { animation: none !important; }
    .rkfab .rkfab-text { opacity: 0; }
  }
</style>
@endonce
<button type="button" {{ $attributes->merge(['class' => 'ai-fab rkfab', 'aria-label' => __('RakanKampus AI — upload a file and I will fill it in')]) }}>
  <span class="rkfab-pill">
    <span class="rkfab-bubble">
      <x-brand-bot-animated :size="40" class="rkfab-bot" />
      <span class="rkfab-ai">AI</span>
    </span>
    <span class="rkfab-text">
      <b>RakanKampus<i>AI</i></b>
      <small>{{ __('Upload & I\'ll fill it in') }}</small>
    </span>
  </span>
</button>
