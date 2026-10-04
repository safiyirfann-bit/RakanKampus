@props(['hint' => null])
{{--
  Floating AI button (Reminders / Timetable → opens the AI capture modal), shaped like a
  chat bubble: the robot in a round badge, a short line from it ("Need help adding
  dates?") and "typing" dots, bobbing gently. Pass hint="…" to change the line.
  Positioning (fixed, bottom-right) still comes from the page's own .ai-fab rules.
--}}
@once
<style>
  html body button.ai-fab.rkfab {
    background: transparent !important; border: 0; padding: 0; overflow: visible; cursor: pointer;
    border-radius: 22px 22px 6px 22px; box-shadow: none; animation: rkfabBob 3s ease-in-out infinite;
  }
  .rkfab .rkfab-bubble {
    position: relative; display: flex; align-items: center; gap: 10px; padding: 9px 16px 9px 9px; text-align: left;
    border-radius: 22px 22px 6px 22px; background: #fff; border: 2px solid #ccfbf1;
    box-shadow: 0 12px 26px rgba(15, 39, 71, .2); transition: border-color .2s, box-shadow .2s, transform .15s;
  }
  html body button.ai-fab.rkfab:hover .rkfab-bubble { border-color: #5eead4; box-shadow: 0 14px 30px rgba(20, 184, 166, .3); }
  html body button.ai-fab.rkfab:active .rkfab-bubble { transform: scale(.97); }
  html body button.ai-fab.rkfab:focus-visible { outline: none; }
  html body button.ai-fab.rkfab:focus-visible .rkfab-bubble { box-shadow: 0 0 0 3px #2ec4c6, 0 12px 26px rgba(15, 39, 71, .2); }
  .rkfab .rkfab-av {
    position: relative; width: 42px; height: 42px; flex: none; border-radius: 50%; display: grid; place-items: center;
    background: linear-gradient(135deg, #0f2747, #14b8a6);
  }
  .rkfab .rkfab-av::after {   /* little "online" dot */
    content: ""; position: absolute; right: 0; bottom: 1px; width: 10px; height: 10px; border-radius: 50%;
    background: #4ade80; border: 2px solid #fff;
  }
  html body .rkfab .rkfab-bot { width: 34px !important; height: 34px !important; }
  .rkfab .rkfab-bot .rb-walk { animation: none !important; }   /* stays in the badge, still waves & blinks */
  .rkfab .rkfab-text { display: flex; flex-direction: column; min-width: 0; }
  .rkfab .rkfab-text b { font-size: 13px; font-weight: 800; color: #14213d; white-space: nowrap; }
  .rkfab .rkfab-dots { display: flex; gap: 3px; margin-top: 4px; }
  .rkfab .rkfab-dots i { width: 6px; height: 6px; border-radius: 50%; background: #14b8a6; animation: rkfabDot 1s ease-in-out infinite; }
  .rkfab .rkfab-dots i:nth-child(2) { animation-delay: .15s; }
  .rkfab .rkfab-dots i:nth-child(3) { animation-delay: .3s; }
  @keyframes rkfabBob { 50% { transform: translateY(-4px); } }
  @keyframes rkfabDot { 50% { transform: translateY(-3px); opacity: .4; } }
  @media (max-width: 420px) { .rkfab .rkfab-text b { font-size: 12px; } }

  html[data-theme="dark"] .rkfab .rkfab-bubble { background: #172233; border-color: #1f5f59; box-shadow: 0 12px 26px rgba(0,0,0,.5); }
  html[data-theme="dark"] .rkfab .rkfab-text b { color: #e2e8f0; }
  html[data-theme="dark"] .rkfab .rkfab-av::after { border-color: #172233; }
  html[data-theme="dark"] .rkfab .rkfab-dots i { background: #41eedf; }

  @media (prefers-reduced-motion: reduce) {
    html body button.ai-fab.rkfab, .rkfab .rkfab-dots i { animation: none !important; }
  }
</style>
@endonce
@php($hintText = $hint ?? __('Need help adding dates?'))
<button type="button" {{ $attributes->merge(['class' => 'ai-fab rkfab', 'aria-label' => $hintText . ' — ' . __('RakanKampus AI')]) }}>
  <span class="rkfab-bubble">
    <span class="rkfab-av"><x-brand-bot-animated :size="34" class="rkfab-bot" /></span>
    <span class="rkfab-text">
      <b>{{ $hintText }}</b>
      <span class="rkfab-dots" aria-hidden="true"><i></i><i></i><i></i></span>
    </span>
  </span>
</button>
