{{-- Extra bits for the 3 forgot-password steps (sits on top of partials.auth-style). --}}
<style>
  .card.fp-card { max-width: 480px; padding: 30px 40px 32px; animation: fpRise .55s cubic-bezier(.2,.9,.3,1) both; overflow: hidden; }
  .card.fp-card::before {               /* soft light sweep at the top of the glass */
    content: ""; position: absolute; inset: 0 0 auto 0; height: 140px; pointer-events: none;
    background: radial-gradient(120% 100% at 50% 0%, rgba(255,255,255,0.22), transparent 70%);
  }

  /* robot + status badge */
  .fp-hero { position: relative; width: 120px; height: 112px; margin: 0 auto 6px; display: grid; place-items: center; }
  .fp-ring { position: absolute; width: 112px; height: 112px; border-radius: 50%; background: radial-gradient(circle, rgba(255,255,255,0.35), rgba(255,255,255,0) 68%); animation: fpPulse 3.2s ease-in-out infinite; }
  .fp-bot { position: relative; filter: drop-shadow(0 10px 18px rgba(60,20,120,0.3)); animation: bob 4s ease-in-out infinite; }
  .fp-badge {
    position: absolute; right: 2px; bottom: 6px; width: 40px; height: 40px; border-radius: 14px;
    display: grid; place-items: center; color: #fff;
    background: linear-gradient(135deg, #f59e0b, #f472b6); border: 3px solid rgba(255,255,255,0.9);
    box-shadow: 0 8px 18px rgba(120,40,120,0.35); animation: fpWiggle 3.6s ease-in-out infinite;
  }
  .fp-badge-2 { background: linear-gradient(135deg, #60a5fa, #a78bfa); }
  .fp-badge-3 { background: linear-gradient(135deg, #34d399, #22c55e); }
  .fp-badge svg { width: 20px; height: 20px; }

  /* stepper */
  .fp-stepper { list-style: none; padding: 0; margin: 4px auto 18px; display: flex; align-items: center; justify-content: center; gap: 6px; max-width: 340px; }
  .fp-stepper li:not(.bar) { display: flex; flex-direction: column; align-items: center; gap: 5px; min-width: 56px; }
  .fp-stepper .dot {
    width: 28px; height: 28px; border-radius: 50%; display: grid; place-items: center;
    font-size: 13px; font-weight: 800; color: rgba(255,255,255,0.85);
    background: rgba(255,255,255,0.14); border: 1.5px solid rgba(255,255,255,0.35); transition: all .25s;
  }
  .fp-stepper .dot svg { width: 14px; height: 14px; }
  .fp-stepper .lbl { font-size: 11.5px; font-weight: 600; color: rgba(255,255,255,0.7); white-space: nowrap; }
  .fp-stepper li.on .dot { background: #fff; color: #7c3aed; border-color: #fff; box-shadow: 0 0 0 5px rgba(255,255,255,0.22); }
  .fp-stepper li.on .lbl { color: #fff; }
  .fp-stepper li.done .dot { background: rgba(255,255,255,0.9); color: #16a34a; border-color: transparent; }
  .fp-stepper .bar { flex: 1; height: 3px; border-radius: 99px; background: rgba(255,255,255,0.25); margin-bottom: 20px; min-width: 22px; }
  .fp-stepper .bar.done { background: rgba(255,255,255,0.9); }

  .fp-card h1 { font-size: 28px; margin-bottom: 8px; }
  .fp-lead { color: var(--text-muted); font-size: 14.5px; line-height: 1.6; text-align: center; margin: 0 auto 22px; max-width: 360px; }
  .fp-lead b { color: #fff; }

  .fp-msg { display: flex; gap: 10px; align-items: flex-start; padding: 12px 14px; border-radius: 14px; margin-bottom: 18px; font-size: 14px; color: #fff; text-align: left; animation: fpRise .35s ease both; }
  .fp-msg::before { content: "!"; flex: none; width: 20px; height: 20px; border-radius: 50%; display: grid; place-items: center; font-size: 12px; font-weight: 800; background: rgba(255,255,255,0.9); color: #dc2626; }
  .fp-msg.err { background: rgba(220,38,38,0.22); border: 1px solid rgba(254,202,202,0.55); }
  .fp-msg.ok  { background: rgba(22,163,74,0.22); border: 1px solid rgba(187,247,208,0.6); }
  .fp-msg.ok::before { content: "✓"; color: #16a34a; }

  /* input with an icon */
  .fp-input { position: relative; }
  .fp-input > svg { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); width: 19px; height: 19px; color: rgba(255,255,255,0.75); pointer-events: none; }
  .fp-input input { padding-left: 46px; height: 52px; border-radius: 14px; }
  .fp-card input:focus { border-color: #fff; background: rgba(255,255,255,0.24); box-shadow: 0 0 0 4px rgba(255,255,255,0.18); }

  /* main button: arrow + shine */
  .fp-card .btn-signin { position: relative; overflow: hidden; height: 52px; border-radius: 14px; display: flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 10px 24px rgba(120,40,140,0.28); }
  .fp-card .btn-signin svg { width: 18px; height: 18px; transition: transform .2s; }
  .fp-card .btn-signin:hover:not(:disabled) svg { transform: translateX(3px); }
  .fp-card .btn-signin::after { content: ""; position: absolute; top: 0; left: -60%; width: 40%; height: 100%; background: linear-gradient(100deg, transparent, rgba(255,255,255,0.35), transparent); transform: skewX(-20deg); animation: fpShine 3.8s ease-in-out infinite; }
  .fp-card .btn-signin:disabled { opacity: .55; cursor: not-allowed; box-shadow: none; }
  .fp-card .btn-signin:disabled::after { display: none; }

  /* little info chips under the form */
  .fp-chips { display: flex; flex-wrap: wrap; justify-content: center; gap: 8px; margin-top: 16px; }
  .fp-chips span { display: inline-flex; align-items: center; gap: 6px; padding: 6px 11px; border-radius: 99px; font-size: 12px; color: #fff; background: rgba(255,255,255,0.13); border: 1px solid rgba(255,255,255,0.22); }
  .fp-chips svg { width: 14px; height: 14px; opacity: .9; }

  .fp-foot { margin-top: 22px; padding-top: 16px; border-top: 1px solid rgba(255,255,255,0.18); display: flex; justify-content: center; }
  .fp-back { display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; border-radius: 99px; color: #fff; font-size: 14px; font-weight: 600; text-decoration: none; opacity: .85; transition: background .15s, opacity .15s; }
  .fp-back:hover { opacity: 1; background: rgba(255,255,255,0.14); }
  .fp-back svg { width: 16px; height: 16px; }

  /* 6 code boxes */
  .otp { display: flex; gap: 8px; justify-content: center; margin-bottom: 18px; }
  .otp input { width: 50px; height: 60px; padding: 0; text-align: center; font-size: 24px; font-weight: 800; border-radius: 14px; font-family: 'SFMono-Regular', Menlo, Consolas, monospace; }
  .otp input.filled { background: rgba(255,255,255,0.3); border-color: #fff; }
  .otp .gap { width: 6px; }
  .otp.shake { animation: fpShake .4s; }
  .fp-resend { text-align: center; margin-top: 14px; color: var(--text-muted); font-size: 14px; }
  .fp-resend button { background: none; border: 0; color: #fff; font-weight: 700; font-size: 14px; cursor: pointer; text-decoration: underline; padding: 0; }
  .fp-resend button:disabled { color: var(--text-muted); text-decoration: none; cursor: default; font-weight: 500; }

  /* password with eye + strength */
  .pw-wrap { position: relative; }
  .pw-wrap input { padding-right: 48px; }
  .pw-eye { position: absolute; right: 8px; top: 50%; transform: translateY(-50%); width: 36px; height: 36px; border: 0; border-radius: 10px; background: transparent; color: #fff; cursor: pointer; display: grid; place-items: center; opacity: .8; }
  .pw-eye:hover { opacity: 1; background: rgba(255,255,255,0.12); }
  .pw-eye svg { width: 20px; height: 20px; }
  .pw-meter { display: flex; gap: 6px; margin-top: 10px; }
  .pw-meter i { flex: 1; height: 5px; border-radius: 99px; background: rgba(255,255,255,0.25); transition: background .2s; }
  .pw-hint { font-size: 12.5px; color: var(--text-muted); margin-top: 6px; min-height: 16px; }
  .pw-match { font-size: 12.5px; margin-top: 6px; min-height: 16px; color: var(--text-muted); }

  @media (max-width: 480px) {
    .card.fp-card { padding: 26px 22px 26px; }
    .fp-card h1 { font-size: 25px; }
  }
  @media (max-width: 400px) { .otp { gap: 6px; } .otp .gap { width: 2px; } .otp input { width: 40px; height: 52px; font-size: 21px; } }
  @media (prefers-reduced-motion: reduce) { .fp-card *, .card.fp-card { animation: none !important; } }

  @keyframes fpRise { from { transform: translateY(14px); opacity: 0; } to { transform: none; opacity: 1; } }
  @keyframes fpPulse { 0%,100% { transform: scale(.92); opacity: .7; } 50% { transform: scale(1.06); opacity: 1; } }
  @keyframes fpWiggle { 0%,86%,100% { transform: rotate(0); } 89% { transform: rotate(-12deg); } 93% { transform: rotate(10deg); } 96% { transform: rotate(-5deg); } }
  @keyframes fpShine { 0%,70% { left: -60%; } 100% { left: 130%; } }
  @keyframes fpShake { 20%,60% { transform: translateX(-6px); } 40%,80% { transform: translateX(6px); } }
</style>
