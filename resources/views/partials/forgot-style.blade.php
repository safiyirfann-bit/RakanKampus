{{-- Extra bits for the 3 forgot-password steps (sits on top of partials.auth-style). --}}
<style>
  .fp-icon {
    width: 74px; height: 74px; margin: 0 auto 14px; border-radius: 24px;
    display: grid; place-items: center;
    background: rgba(255,255,255,0.18); border: 1px solid rgba(255,255,255,0.35);
    box-shadow: 0 10px 30px rgba(80,40,160,0.25);
    animation: fpPop .5s cubic-bezier(.2,1.4,.4,1) both;
  }
  .fp-icon svg { width: 36px; height: 36px; color: #fff; }
  .fp-steps { display: flex; justify-content: center; gap: 8px; margin: 0 0 18px; }
  .fp-steps span { width: 26px; height: 6px; border-radius: 99px; background: rgba(255,255,255,0.3); }
  .fp-steps span.on { background: #fff; width: 40px; }
  .fp-lead { color: var(--text-muted); font-size: 14.5px; line-height: 1.55; text-align: center; margin: -6px 0 22px; }
  .fp-lead b { color: #fff; }
  .fp-msg { padding: 13px 16px; border-radius: 14px; margin-bottom: 18px; text-align: center; font-size: 14px; color: #fff; }
  .fp-msg.err { background: rgba(239,68,68,0.18); border: 1px solid rgba(239,68,68,0.45); }
  .fp-msg.ok  { background: rgba(34,197,94,0.18); border: 1px solid rgba(34,197,94,0.45); }
  .fp-back { display: block; text-align: center; margin-top: 20px; color: var(--text-muted); font-size: 14px; text-decoration: none; }
  .fp-back:hover { color: #fff; text-decoration: underline; }
  .btn-signin:disabled { opacity: .55; cursor: not-allowed; box-shadow: none; }

  /* 6 code boxes */
  .otp { display: flex; gap: 8px; justify-content: center; margin-bottom: 18px; }
  .otp input {
    width: 48px; height: 58px; padding: 0; text-align: center;
    font-size: 24px; font-weight: 800; border-radius: 14px;
    font-family: 'SFMono-Regular', Menlo, Consolas, monospace;
  }
  .otp input.filled { background: rgba(255,255,255,0.26); border-color: rgba(255,255,255,0.7); }
  .otp.shake { animation: fpShake .4s; }
  .fp-resend { text-align: center; margin-top: 16px; color: var(--text-muted); font-size: 14px; }
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

  @media (max-width: 400px) { .otp { gap: 6px; } .otp input { width: 42px; height: 52px; font-size: 21px; } }
  @keyframes fpPop { from { transform: scale(.6); opacity: 0; } to { transform: scale(1); opacity: 1; } }
  @keyframes fpShake { 20%,60% { transform: translateX(-6px); } 40%,80% { transform: translateX(6px); } }
</style>
