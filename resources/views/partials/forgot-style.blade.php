{{-- Look for the forgot-password steps and the sign-up email check: same as the Login page —
     theme gradient, the robot chatting on top, the form in a white glass sheet below
     (phones), or a split card (wider screens). Used with partials.forgot-head. --}}
<style>
  :root{ --fp-purple:#a78bfa; --fp-pink:#f472b6; --fp-blue:#60a5fa; --fp-amber:#f59e0b;
    --fp-ink:#2e1065; --fp-muted:#7c6aa8; --fp-soft:#a596c9; --fp-field:#f5f0ff; --fp-link:#7c3aed; }
  *{box-sizing:border-box}
  html,body{min-height:100%;margin:0}
  body{font-family:'Plus Jakarta Sans', -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Helvetica,Arial,sans-serif;color:var(--fp-ink);min-height:100vh;overflow-x:hidden;
    background:linear-gradient(120deg,var(--fp-purple),var(--fp-pink),var(--fp-blue),var(--fp-purple));background-size:300% 300%;animation:fpShift 15s ease infinite}
  @keyframes fpShift{0%,100%{background-position:0% 50%}50%{background-position:100% 50%}}
  .fp-blob{position:fixed;border-radius:50%;filter:blur(50px);opacity:.55;pointer-events:none;z-index:0}
  .fp-b1{width:300px;height:300px;background:var(--fp-amber);top:-80px;left:-90px;animation:fpFloat 12s ease-in-out infinite alternate}
  .fp-b2{width:280px;height:280px;background:var(--fp-blue);top:30%;right:-110px;animation:fpFloat 15s ease-in-out infinite alternate-reverse}
  @keyframes fpFloat{to{transform:translate(40px,50px) scale(1.15)}}

  .fp{position:relative;z-index:1;min-height:100vh;display:flex;flex-direction:column}
  .fp-shell{flex:1;display:flex;flex-direction:column}

  /* ---- robot chat ---- */
  .fp-hero{min-height:270px;display:flex;align-items:center;justify-content:center;padding:44px 22px 28px}
  .fp-chat{width:100%;max-width:330px;display:flex;flex-direction:column;gap:9px;min-height:170px}
  .fp-hdr{display:flex;align-items:center;gap:10px;margin-bottom:4px}
  .fp-hdr .fp-av{width:48px;height:48px;display:grid;place-items:center;filter:drop-shadow(0 6px 10px rgba(46,16,101,.3));animation:fpBob 3s ease-in-out infinite}
  @keyframes fpBob{50%{transform:translateY(-3px)}}
  .fp-hdr b{display:block;color:#fff;font-size:16px}
  .fp-hdr span{display:flex;align-items:center;gap:6px;color:rgba(255,255,255,.9);font-size:12px}
  .fp-hdr span:before{content:"";width:7px;height:7px;border-radius:50%;background:#4ade80;box-shadow:0 0 0 3px rgba(74,222,128,.3)}
  .fp-bub{align-self:flex-start;max-width:88%;padding:10px 14px;border-radius:18px;border-bottom-left-radius:6px;font-size:14px;line-height:1.4;color:#fff;
    background:rgba(255,255,255,.22);border:1px solid rgba(255,255,255,.38);-webkit-backdrop-filter:blur(8px);backdrop-filter:blur(8px);animation:fpPop .35s ease both}
  .fp-bub.warn{background:rgba(220,38,38,.3);border-color:rgba(254,202,202,.6)}
  .fp-typing{align-self:flex-start;display:flex;gap:5px;padding:12px 14px;border-radius:18px;background:rgba(255,255,255,.22);animation:fpPop .25s ease both}
  .fp-typing i{width:7px;height:7px;border-radius:50%;background:#fff;animation:fpDot 1s infinite}
  .fp-typing i:nth-child(2){animation-delay:.15s}.fp-typing i:nth-child(3){animation-delay:.3s}
  @keyframes fpDot{50%{transform:translateY(-4px);opacity:.5}}
  @keyframes fpPop{from{opacity:0;transform:translateY(10px) scale(.96)}}

  /* ---- sheet ---- */
  .fp-sheet{flex:1;background:rgba(255,255,255,.9);-webkit-backdrop-filter:blur(18px);backdrop-filter:blur(18px);border-radius:30px 30px 0 0;
    padding:24px 22px 20px;box-shadow:0 -12px 34px rgba(46,16,101,.18)}
  .fp-sheet h1{margin:0 0 4px;font-weight:800;font-size:25px;letter-spacing:-.4px;color:var(--fp-ink)}
  .fp-lead{color:var(--fp-muted);font-size:14px;line-height:1.55;margin:0 0 18px}
  .fp-lead b{color:var(--fp-ink)}

  .fp-stepper{list-style:none;padding:0;margin:0 0 16px;display:flex;align-items:center;gap:6px}
  .fp-stepper li:not(.bar){display:flex;align-items:center;gap:6px}
  .fp-stepper .dot{width:24px;height:24px;border-radius:50%;display:grid;place-items:center;font-size:11.5px;font-weight:800;background:#ede9fe;color:var(--fp-link);flex:none}
  .fp-stepper .dot svg{width:12px;height:12px}
  .fp-stepper .lbl{font-size:12px;font-weight:700;color:var(--fp-soft);white-space:nowrap}
  .fp-stepper li.on .dot,.fp-stepper li.done .dot{background:linear-gradient(135deg,var(--fp-purple),var(--fp-pink));color:#fff}
  .fp-stepper li.on .lbl{color:var(--fp-ink)}
  .fp-stepper .bar{flex:1;height:3px;border-radius:99px;background:#ede9fe;min-width:14px}
  .fp-stepper .bar.done{background:linear-gradient(90deg,var(--fp-purple),var(--fp-pink))}

  .fp-msg{display:flex;gap:10px;align-items:flex-start;padding:11px 14px;border-radius:14px;margin-bottom:14px;font-size:13.5px;line-height:1.45}
  .fp-msg.err{background:#fef2f2;border:1px solid #fecaca;color:#b91c1c}
  .fp-msg.ok{background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d}

  .field{margin-bottom:12px}
  .field label{display:block;font-size:12px;font-weight:700;color:var(--fp-muted);margin:0 0 6px 6px}
  .fp-input,.pw-wrap{position:relative}
  .fp-input > svg{position:absolute;left:18px;top:50%;transform:translateY(-50%);width:19px;height:19px;color:var(--fp-soft);pointer-events:none}
  .fp-sheet input[type=email],.fp-sheet input[type=password],.pw-wrap input[type=text]{
    width:100%;height:52px;border-radius:26px;border:1.5px solid transparent;background:var(--fp-field);padding:0 18px;font-size:15px;color:var(--fp-ink);
    font-family:inherit;outline:none;transition:border-color .2s,box-shadow .2s,background .2s}
  .fp-input input{padding-left:48px !important}
  .fp-sheet input::placeholder{color:var(--fp-soft)}
  .fp-sheet input:focus{background:#fff;border-color:var(--fp-purple);box-shadow:0 0 0 4px rgba(167,139,250,.2)}
  .fp-input:focus-within > svg{color:var(--fp-purple)}

  .btn-signin{position:relative;overflow:hidden;width:100%;height:52px;border:0;border-radius:26px;cursor:pointer;font-family:inherit;font-size:16px;font-weight:800;color:#fff;
    display:flex;align-items:center;justify-content:center;gap:8px;margin-top:4px;
    background:linear-gradient(90deg,var(--fp-purple),var(--fp-pink));box-shadow:0 10px 24px rgba(244,114,182,.35);transition:transform .15s}
  .btn-signin:hover:not(:disabled){transform:translateY(-1px)}
  .btn-signin svg{width:18px;height:18px}
  .btn-signin:disabled{opacity:.5;cursor:not-allowed;box-shadow:none}

  .fp-chips{display:flex;flex-wrap:wrap;justify-content:center;gap:8px;margin-top:14px}
  .fp-chips span{display:inline-flex;align-items:center;gap:6px;padding:6px 11px;border-radius:99px;font-size:12px;color:var(--fp-muted);background:var(--fp-field)}
  .fp-chips svg{width:14px;height:14px}
  .fp-foot{margin-top:14px;display:flex;justify-content:center}
  .fp-back{display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border-radius:99px;color:var(--fp-link);font-size:14px;font-weight:700;text-decoration:none}
  .fp-back:hover{background:var(--fp-field)}
  .fp-back svg{width:16px;height:16px}
  .fp-copy{text-align:center;font-size:11.5px;color:var(--fp-soft);margin:12px 0 0}

  /* 6 code boxes */
  .otp{display:flex;gap:8px;justify-content:center;margin-bottom:16px}
  .otp input{width:48px;height:58px;padding:0;text-align:center;font-size:24px;font-weight:800;border-radius:16px;border:1.5px solid #ddd6fe;
    background:#f3edff;color:var(--fp-ink);outline:none;font-family:'SFMono-Regular',Menlo,Consolas,monospace;transition:all .15s}
  .otp input:focus{background:#fff;border-color:var(--fp-purple);box-shadow:0 0 0 4px rgba(167,139,250,.2)}
  .otp input.filled{background:#fff;border-color:var(--fp-purple)}
  .otp .gap{width:6px}
  .otp.shake{animation:fpShake .4s}
  .fp-resend{text-align:center;margin-top:12px;color:var(--fp-muted);font-size:14px}
  .fp-resend button{background:none;border:0;color:var(--fp-link);font-weight:800;font-size:14px;cursor:pointer;padding:0;font-family:inherit}
  .fp-resend button:disabled{color:var(--fp-soft);font-weight:600;cursor:default}

  /* password */
  .pw-wrap input{padding-right:50px !important}
  .pw-eye{position:absolute;right:8px;top:50%;transform:translateY(-50%);width:38px;height:38px;border:0;border-radius:50%;background:transparent;color:var(--fp-soft);cursor:pointer;display:grid;place-items:center}
  .pw-eye:hover{color:var(--fp-purple)}
  .pw-eye svg{width:20px;height:20px}
  .pw-meter{display:flex;gap:6px;margin:10px 6px 0}
  .pw-meter i{flex:1;height:5px;border-radius:99px;background:#ede9fe;transition:background .2s}
  .pw-hint,.pw-match{font-size:12.5px;color:var(--fp-muted);margin:6px 6px 0;min-height:16px}

  @media (min-width:861px){
    .fp{align-items:center;justify-content:center;padding:32px}
    .fp-shell{flex:none;flex-direction:row;width:100%;max-width:960px;min-height:560px;border-radius:32px;overflow:hidden;
      background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.3);box-shadow:0 30px 70px rgba(46,16,101,.3)}
    .fp-hero{flex:1;padding:48px}
    .fp-sheet{flex:1;border-radius:0;padding:44px 48px 28px;box-shadow:none;display:flex;flex-direction:column;justify-content:center}
  }
  @media (min-width:1100px){
    .fp-shell{max-width:1080px;min-height:min(620px,86vh)}
    .fp-hero{padding:56px}
    .fp-chat{max-width:400px;gap:10px}
    .fp-hdr .fp-av{width:52px;height:52px}.fp-hdr .fp-av svg{width:52px;height:52px}
    .fp-hdr b{font-size:17px}.fp-hdr span{font-size:12.5px}
    .fp-bub{font-size:15px;padding:11px 15px}
    .fp-sheet{padding:48px 64px 32px}
    .fp-sheet h1{font-size:30px;letter-spacing:-.5px}
    .fp-lead{font-size:14.5px;margin-bottom:20px}
    .otp input{width:52px;height:62px;font-size:26px}
  }
  @media (max-width:860px){
    .fp-blob{filter:blur(38px)}
    .fp-b1{width:62vw;height:62vw;top:-40px;left:-50px}
    .fp-b2{width:64vw;height:64vw;top:170px;right:-80px}
  }
  /* phones: fit on one screen — the chat area takes what the form leaves */
  @media (max-width:860px){
    .fp{min-height:100vh;min-height:100dvh}
    .fp-hero{flex:1 1 auto;min-height:0;padding:max(18px,env(safe-area-inset-top)) 20px 14px;align-items:flex-end}
    .fp-chat{min-height:0;gap:7px}
    .fp-hdr .fp-av{width:42px;height:42px}
    .fp-bub{font-size:13.5px;padding:9px 13px}
    .fp-sheet{flex:none;padding:20px 20px 14px}
    .fp-sheet h1{font-size:22px}
    .fp-lead{font-size:13px;margin-bottom:14px}
    .fp-stepper{margin-bottom:12px}
    .fp-sheet input[type=email],.fp-sheet input[type=password],.pw-wrap input[type=text]{height:48px}
    .btn-signin{height:48px}
    .field{margin-bottom:10px}
    .fp-chips{margin-top:10px}
    .fp-foot{margin-top:8px}
    .fp-copy{display:none}
  }
  @media (max-width:860px) and (max-height:700px){
    .fp-hdr{display:none}
    .fp-chips{display:none}
    .fp-lead{display:none}
  }
  @media (max-width:400px){ .otp{gap:6px} .otp .gap{width:2px} .otp input{width:40px;height:52px;font-size:21px} .fp-stepper .lbl{font-size:11px} }
  @media (prefers-reduced-motion:reduce){ body,.fp-blob,.fp-av{animation:none !important} }
  @keyframes fpShake{20%,60%{transform:translateX(-6px)}40%,80%{transform:translateX(6px)}}
</style>
