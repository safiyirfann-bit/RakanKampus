<!DOCTYPE html>
<html lang="en">
<head>
@include('partials.font')
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Create Account - RakanKampus</title>
{{--
  Create Account — same look as the Login page (theme gradient + white glass sheet).
  Top: a glass "student card" that fills in live as the student types
       (name → initials avatar, matric number → programme name, password → "secured").
  Phones: card on top, form below in 2 short steps (details → password).
  Wider screens: split card, student card on the left, the whole form on the right.
--}}
<style>
:root{
  --purple:#0d9488; --pink:#2ec4c6; --blue:#2ec4c6; --amber:#14213d;
  --ink:#14213d; --muted:#64748b; --soft:#94a3b8; --field:#f0fafa; --link:#0d9488; --link2:#0f766e;
}
*{margin:0;padding:0;box-sizing:border-box}
html,body{min-height:100%}
body{
  min-height:100vh;overflow-x:hidden;color:var(--ink);
  font-family:'Plus Jakarta Sans', -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Helvetica,Arial,sans-serif;
  background:linear-gradient(120deg,var(--purple),var(--pink),var(--blue),var(--purple));background-size:300% 300%;
  animation:gradientShift 15s ease infinite;
}
@keyframes gradientShift{0%,100%{background-position:0% 50%}50%{background-position:100% 50%}}
.blob{position:fixed;border-radius:50%;filter:blur(50px);opacity:.55;pointer-events:none;z-index:0}
.blob-1{width:300px;height:300px;background:var(--amber);top:-80px;left:-90px;animation:floatA 12s ease-in-out infinite alternate}
.blob-2{width:280px;height:280px;background:var(--blue);top:30%;right:-110px;animation:floatA 15s ease-in-out infinite alternate-reverse}
@keyframes floatA{to{transform:translate(40px,50px) scale(1.15)}}

.wrap{position:relative;z-index:1;min-height:100vh;display:flex;flex-direction:column}
.shell{display:flex;flex-direction:column;flex:1}

/* ---------- live student card ---------- */
.hero{display:flex;align-items:center;justify-content:center;padding:46px 22px 30px;min-height:250px}
.idcard{position:relative;width:min(330px,100%);aspect-ratio:1.62;border-radius:22px;padding:0;container-type:inline-size;color:#fff;overflow:hidden;
  background:linear-gradient(135deg,rgba(255,255,255,.38),rgba(255,255,255,.12));border:1px solid rgba(255,255,255,.55);
  -webkit-backdrop-filter:blur(12px);backdrop-filter:blur(12px);box-shadow:0 20px 40px rgba(15,39,71,.25);
  transform:rotate(-3deg);animation:cardFloat 4.5s ease-in-out infinite}
@keyframes cardFloat{50%{transform:rotate(-1deg) translateY(-6px)}}
.idcard:after{content:"";position:absolute;inset:0;pointer-events:none;
  background:linear-gradient(110deg,transparent 30%,rgba(255,255,255,.45) 45%,transparent 60%);transform:translateX(-100%);animation:shine 4s ease-in-out infinite}
@keyframes shine{55%,100%{transform:translateX(100%)}}
.idc-top{margin:4.8cqw 5.4cqw 0;display:flex;justify-content:space-between;align-items:center;font-size:3.2cqw;font-weight:800;letter-spacing:.35cqw}
.idc-top svg{width:10.3cqw;height:10.3cqw;filter:drop-shadow(0 3px 6px rgba(15,39,71,.3))}
.idc-body{display:flex;gap:4.2cqw;margin:3.6cqw 5.4cqw 0;align-items:center}
.idc-ph{width:20cqw;height:24cqw;border-radius:4.2cqw;background:rgba(255,255,255,.3);border:1px solid rgba(255,255,255,.45);
  display:grid;place-items:center;font-size:7.8cqw;font-weight:800;flex:none;transition:transform .25s}
.idc-ph.pop{animation:phPop .35s ease}
@keyframes phPop{50%{transform:scale(1.12)}}
.idc-info{min-width:0;flex:1}
.idc-lbl{font-size:2.7cqw;opacity:.85;letter-spacing:.8px;text-transform:uppercase}
.idc-val{font-size:4.8cqw;font-weight:800;min-height:6.4cqw;margin-bottom:1.8cqw;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.idc-val.dim{opacity:.55;font-weight:600}
.idc-chip{position:absolute;right:5.4cqw;top:17.5cqw;width:10.3cqw;height:7.9cqw;border-radius:1.8cqw;background:linear-gradient(135deg,#fde68a,#f59e0b);opacity:.9}
.idc-foot{position:absolute;left:5.4cqw;right:5.4cqw;bottom:3.9cqw;display:flex;justify-content:space-between;align-items:center;font-size:3.2cqw;font-weight:700;gap:3cqw}
.idc-foot span:first-child{white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.idc-lock{display:inline-flex;align-items:center;gap:1.2cqw;padding:.6cqw 2.4cqw;border-radius:999px;background:rgba(255,255,255,.25);flex:none;transition:background .2s}
.idc-lock.ok{background:#22c55e}

/* ---------- form sheet ---------- */
.sheet{flex:1;background:rgba(255,255,255,.9);-webkit-backdrop-filter:blur(18px);backdrop-filter:blur(18px);
  border-radius:30px 30px 0 0;padding:26px 22px 22px;box-shadow:0 -12px 34px rgba(15,39,71,.18)}
.head h1{font-size:25px;font-weight:800;letter-spacing:-.4px}
.head p{font-size:13.5px;color:var(--muted);margin:3px 0 16px}

.sr{position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0 0 0 0);white-space:nowrap}
.row{display:grid;grid-template-columns:1fr 1fr;gap:10px}
.field{margin-bottom:11px;min-width:0}
.box{position:relative;display:flex;align-items:center;height:50px;border-radius:25px;background:var(--field);border:1.5px solid transparent;
  padding:0 16px;gap:10px;transition:border-color .2s,box-shadow .2s,background .2s}
.box:focus-within{background:#fff;border-color:var(--purple);box-shadow:0 0 0 4px rgba(46,196,198,.2)}
.box > svg{width:18px;height:18px;flex:none;color:var(--soft)}
.box:focus-within > svg{color:var(--purple)}
.input{flex:1;min-width:0;height:100%;border:0;outline:0;background:transparent;font-size:15px;color:var(--ink);font-family:inherit}
.input::placeholder{color:var(--soft)}
.box:has(.matric-input-err){border-color:#f87171;box-shadow:0 0 0 4px rgba(248,113,113,.18)}
.box:has(.matric-input-ok){border-color:#86efac}
.eye{width:34px;height:34px;border:0;background:none;color:var(--soft);cursor:pointer;border-radius:50%;display:grid;place-items:center;flex:none;margin-right:-8px}
.eye svg{width:19px;height:19px}.eye .off{display:none}.eye.on .on{display:none}.eye.on .off{display:block}.eye.on{color:var(--purple)}

/* password strength */
.pw-box{background:var(--field);border-radius:18px;padding:12px 16px;margin:2px 0 12px}
.pw-head{display:flex;justify-content:space-between;align-items:center;font-size:11.5px;font-weight:800;letter-spacing:.05em;text-transform:uppercase;color:var(--muted)}
.strength{font-size:11.5px;font-weight:800;text-transform:uppercase;transition:color .2s}
.bar-track{height:6px;border-radius:9px;background:#dbeeee;margin:9px 0 10px;overflow:hidden}
.bar-fill{height:100%;width:0;border-radius:9px;transition:width .25s ease,background .25s ease}
.req-list{list-style:none;display:grid;grid-template-columns:1fr 1fr;gap:6px 16px}
.req-list li{display:flex;align-items:center;gap:8px;font-size:12.5px;font-weight:600;color:var(--muted)}
.req-list .icon{width:18px;height:18px;border-radius:50%;display:grid;place-items:center;flex-shrink:0;background:#fee2e2;color:#dc2626;transition:background .2s,color .2s}
.req-list .icon svg{width:10px;height:10px}
.req-list .icon-check{display:none}
.req-list li.valid{color:var(--ink)}
.req-list li.valid .icon{background:#dcfce7;color:#15803d}
.req-list li.valid .icon-cross{display:none}
.req-list li.valid .icon-check{display:block}

.matric-msg{font-size:12px;margin:6px 6px 0;line-height:1.35}
.matric-msg:empty{display:none}
.matric-hint{color:var(--soft)}
.matric-ok{color:#15803d;font-weight:700}
.matric-err,.password-error{color:#b91c1c;background:#fef2f2;border:1px solid #fecaca;border-radius:12px;padding:6px 10px;font-weight:600}
.password-error{display:none;font-size:12.5px;margin:2px 0 10px}
.error-banner{background:#fef2f2;border:1px solid #fecaca;color:#b91c1c;border-radius:14px;padding:10px 14px;margin-bottom:14px;font-size:13.5px}
.error-banner ul{list-style:none;display:grid;gap:3px}

.btn{width:100%;height:50px;border:0;border-radius:25px;cursor:pointer;font-family:inherit;font-size:15.5px;font-weight:800;color:#fff;
  background:linear-gradient(90deg,var(--purple),var(--pink));box-shadow:0 10px 24px rgba(13,148,136,.35);transition:transform .15s,box-shadow .15s}
.btn:hover{transform:translateY(-1px)}.btn:active{transform:translateY(1px)}
.btn.ghost{background:var(--field);color:var(--link);box-shadow:none}
.actions{margin-top:6px}
.btn-next,.btn-back{display:none}
.signin{text-align:center;font-size:14px;color:var(--muted);margin-top:16px}
.signin a{color:var(--link2);font-weight:800;text-decoration:none}
.copy{text-align:center;font-size:11.5px;color:var(--soft);margin-top:14px}

/* stepper (phones) */
.steps{display:none;align-items:center;gap:8px;margin:0 0 16px;font-size:12px;font-weight:700;color:var(--soft)}
.steps .dot{width:26px;height:26px;border-radius:50%;display:grid;place-items:center;font-weight:800;font-size:12px;background:#dbeeee;color:var(--link);flex-shrink:0;transition:all .2s}
.steps .dot.on{background:linear-gradient(135deg,var(--purple),var(--pink));color:#fff}
.steps small{font-size:12px;white-space:nowrap}
.steps small.on{color:var(--ink)}
.steps .ln{flex:1;height:3px;border-radius:9px;background:#dbeeee;overflow:hidden}
.steps .ln i{display:block;height:100%;width:0;background:linear-gradient(90deg,var(--purple),var(--pink));transition:width .35s}

/* ================= wider screens: split card ================= */
@media (min-width:861px){
  .wrap{align-items:center;justify-content:center;padding:32px}
  .shell{flex:none;flex-direction:row;width:100%;max-width:1000px;min-height:600px;border-radius:32px;overflow:hidden;
    background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.3);box-shadow:0 30px 70px rgba(15,39,71,.3)}
  .hero{flex:.9;padding:48px 40px}
  .sheet{flex:1.1;border-radius:0;padding:40px 44px 28px;box-shadow:none;display:flex;flex-direction:column;justify-content:center}
}
@media (min-width:1100px){
  .shell{max-width:1120px;min-height:min(640px,88vh)}
  .hero{padding:48px}
  .idcard{width:min(380px,100%)}
  .sheet{padding:40px 56px 28px}
  .head h1{font-size:30px;letter-spacing:-.5px}
  .head p{font-size:14.5px;margin:4px 0 18px}
}
/* ================= phones: 2 steps ================= */
@media (max-width:860px){
  .blob{filter:blur(38px)}
  .blob-1{width:62vw;height:62vw;top:-40px;left:-50px}
  .blob-2{width:64vw;height:64vw;top:170px;right:-80px}
  .steps{display:flex}
  .row.split{grid-template-columns:1fr}
  .form[data-step="1"] .s2{display:none}
  .form[data-step="2"] .s1{display:none}
  .form[data-step="1"] .btn-submit{display:none}
  .form[data-step="1"] .btn-next{display:block}
  .form[data-step="2"] .actions{display:flex;gap:10px}
  .form[data-step="2"] .btn-back{display:block;width:38%}
  .form[data-step="2"] .signin{display:none}
  .form .step-in{animation:stepIn .25s ease}
  @keyframes stepIn{from{opacity:0;transform:translateX(14px)}to{opacity:1;transform:none}}
}
/* phones: everything fits on one screen — the card shrinks with the screen height */
@media (max-width:860px){
  .wrap{min-height:100vh;min-height:100dvh}
  .hero{flex:1 1 auto;min-height:0;padding:max(14px,env(safe-area-inset-top)) 20px 16px}
  .idcard{width:clamp(170px,calc((100dvh - 450px) * 1.62),330px);max-width:86vw}
  .sheet{flex:none;padding:20px 20px 14px}
  .head h1{font-size:22px}
  .head p{font-size:12.5px;margin:2px 0 12px}
  .steps{margin-bottom:12px}
  .field{margin-bottom:9px}
  .box{height:46px;border-radius:23px;padding:0 14px}
  .input{font-size:15px}
  .row{column-gap:8px;row-gap:0}
  .matric-msg{margin-top:4px}
  .btn{height:46px;border-radius:23px}
  .signin{margin-top:12px;font-size:13.5px}
  .copy{display:none}
  .pw-box{padding:9px 14px;margin-bottom:8px}
  .bar-track{margin:7px 0 8px}
  .req-list{gap:4px 10px}
  .req-list li{font-size:11.5px;gap:6px}
  .req-list .icon{width:16px;height:16px}
}
@media (max-width:860px) and (max-height:700px){
  .head p{display:none}
  .head h1{margin-bottom:10px}
}
@media (prefers-reduced-motion:reduce){body,.blob,.idcard,.idcard:after{animation:none !important}}

  /* ---- student theme (navy-teal), layout "B": phone = still navy→teal gradient like Reminders;
     PC = light mint page, white card, navy-teal left panel ---- */
  body{background:linear-gradient(160deg,#14213d,#1b3a5c 55%,#2ec4c6) fixed !important;animation:none !important}
  .blob-1{background:rgba(127,245,236,.45) !important;opacity:.35 !important}
  .blob-2{background:rgba(255,255,255,.5) !important;opacity:.25 !important}
  @media (min-width:861px){
    body{background:#eefafa !important}
    .blob-1{background:#2ec4c6 !important;opacity:.28 !important;filter:blur(80px) !important}
    .blob-2{background:#14213d !important;opacity:.14 !important;filter:blur(80px) !important}
    .shell{background:#fff !important;border:0 !important;box-shadow:0 30px 70px rgba(15,39,71,.16) !important}
    .hero{background:linear-gradient(160deg,#14213d 0%,#1b3a5c 45%,#21768a 75%,#2ec4c6 100%) !important}
    .sheet{background:#fff !important;-webkit-backdrop-filter:none !important;backdrop-filter:none !important}
  }
</style>
</head>
<body>

<div class="blob blob-1"></div>
<div class="blob blob-2"></div>

@php
  $ic = [
    'user' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="8" r="4"/><path d="M4 21c1-4 4-6 8-6s7 2 8 6"/></svg>',
    'mail' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="5" width="18" height="14" rx="3"/><path d="m3 7 9 6 9-6"/></svg>',
    'id'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="5" width="18" height="14" rx="3"/><circle cx="9" cy="11" r="2"/><path d="M14 10h4M14 14h4M6 16c.5-1.5 1.7-2 3-2s2.5.5 3 2"/></svg>',
    'lock' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="10" width="16" height="11" rx="3"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>',
    'eye'  => '<svg class="on" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg><svg class="off" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M3 3l18 18M10.6 5.1A10 10 0 0 1 12 5c6.4 0 10 7 10 7a17 17 0 0 1-3.2 4M6.6 6.6C3.8 8.4 2 12 2 12s3.6 7 10 7a9.6 9.6 0 0 0 5.4-1.6M9.9 9.9a3 3 0 0 0 4.2 4.2"/></svg>',
    'x'    => '<svg class="icon-cross" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="3"><path d="M5 5l10 10M15 5L5 15"/></svg><svg class="icon-check" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="3"><path d="M4 10l4 4 8-8"/></svg>',
  ];
@endphp

<div class="wrap">
 <div class="shell">

  <!-- Live student card -->
  <div class="hero" aria-hidden="true">
    <div class="idcard">
      <div class="idc-top"><span>{{ __('STUDENT CARD') }} · PUO</span><x-brand-logo size="34" /></div>
      <div class="idc-chip"></div>
      <div class="idc-body">
        <div class="idc-ph" id="cardInit">?</div>
        <div class="idc-info">
          <div class="idc-lbl">{{ __('Name') }}</div>
          <div class="idc-val dim" id="cardName">{{ __('Your name') }}</div>
          <div class="idc-lbl">{{ __('Matric number') }}</div>
          <div class="idc-val dim" id="cardMatric">01XXX00X0000</div>
        </div>
      </div>
      <div class="idc-foot"><span id="cardProg">RakanKampus</span><span class="idc-lock" id="cardLock">🔒 <span id="cardLockTxt">{{ __('Password') }}</span></span></div>
    </div>
  </div>

  <!-- Form -->
  <div class="sheet">
    <div class="head">
      <h1 id="formTitle">{{ __('Create Account') }}</h1>
      <p id="formSub">{{ __('Sign up with your email & matric number') }}</p>
    </div>

    <div class="steps" aria-hidden="true">
      <span class="dot on" id="dot1">1</span><small class="on" id="lbl1">{{ __('Your details') }}</small>
      <span class="ln"><i id="stepLine"></i></span>
      <span class="dot" id="dot2">2</span><small id="lbl2">{{ __('Password') }}</small>
    </div>

    <form method="POST" action="{{ route('register') }}" class="form" id="regForm" data-step="1" novalidate>
      @csrf

      @if ($errors->any())
        <div class="error-banner">
          <ul>
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <!-- Step 1: details -->
      <div class="s1">
        <div class="row">
          <div class="field">
            <label class="sr" for="firstName">{{ __('First name') }}</label>
            <div class="box">{!! $ic['user'] !!}<input class="input" type="text" name="first_name" id="firstName" placeholder="{{ __('First name') }}" value="{{ old('first_name') }}" autocomplete="given-name" required></div>
          </div>
          <div class="field">
            <label class="sr" for="lastName">{{ __('Last name') }}</label>
            <div class="box"><input class="input" type="text" name="last_name" id="lastName" placeholder="{{ __('Last name') }}" value="{{ old('last_name') }}" autocomplete="family-name" required></div>
          </div>
        </div>
        <div class="row split">
          <div class="field">
            <label class="sr" for="emailInput">{{ __('Email address') }}</label>
            <div class="box">{!! $ic['mail'] !!}<input class="input" type="email" name="email" id="emailInput" placeholder="you@gmail.com" value="{{ old('email') }}" autocomplete="email" required></div>
          </div>
          <div class="field">
            <label class="sr" for="matricInput">{{ __('Matric Number (PUO)') }}</label>
            <div class="box">{!! $ic['id'] !!}<input class="input" type="text" name="student_id" id="matricInput" placeholder="{{ __('Matric number') }}" value="{{ old('student_id') }}" maxlength="16" autocomplete="off"></div>
            <p id="matricMsg" class="matric-msg"></p>
          </div>
        </div>
      </div>

      <!-- Step 2: password -->
      <div class="s2">
        <div class="row split">
          <div class="field">
            <label class="sr" for="password">{{ __('Password') }}</label>
            <div class="box pw-wrap">{!! $ic['lock'] !!}<input class="input" type="password" name="password" id="password" placeholder="{{ __('Create a password') }}" oninput="checkPassword()" autocomplete="new-password">
              <button type="button" class="eye" onclick="togglePw(this)" aria-label="{{ __('Show password') }}">{!! $ic['eye'] !!}</button></div>
          </div>
          <div class="field">
            <label class="sr" for="password_confirmation">{{ __('Confirm password') }}</label>
            <div class="box pw-wrap">{!! $ic['lock'] !!}<input class="input" type="password" name="password_confirmation" id="password_confirmation" placeholder="{{ __('Re-enter your password') }}" oninput="clearError()" autocomplete="new-password">
              <button type="button" class="eye" onclick="togglePw(this)" aria-label="{{ __('Show password') }}">{!! $ic['eye'] !!}</button></div>
          </div>
        </div>
        <div class="pw-box">
          <div class="pw-head"><span>{{ __('Password strength') }}</span><span class="strength" id="strengthLabel">-</span></div>
          <div class="bar-track"><div class="bar-fill" id="strengthBar"></div></div>
          <ul class="req-list">
            <li id="req-length"><span class="icon">{!! $ic['x'] !!}</span><span class="txt">{{ __('At least 6 characters') }}</span></li>
            <li id="req-upper"><span class="icon">{!! $ic['x'] !!}</span><span class="txt">{{ __('One uppercase letter (A-Z)') }}</span></li>
            <li id="req-lower"><span class="icon">{!! $ic['x'] !!}</span><span class="txt">{{ __('One lowercase letter (a-z)') }}</span></li>
            <li id="req-number"><span class="icon">{!! $ic['x'] !!}</span><span class="txt">{{ __('One number (0-9)') }}</span></li>
            <li id="req-special"><span class="icon">{!! $ic['x'] !!}</span><span class="txt">{{ __('One special character (!@#$%^&*)') }}</span></li>
          </ul>
        </div>
        <p class="password-error" id="passwordError"></p>
      </div>

      <div class="actions">
        <button type="button" class="btn ghost btn-back" onclick="goStep(1)">← {{ __('Back') }}</button>
        <button type="button" class="btn btn-next" onclick="nextStep()">{{ __('Next') }} →</button>
        <button type="submit" class="btn btn-submit">{{ __('Create account') }}</button>
      </div>

      <p class="signin">{{ __('Already have an account?') }} <a href="{{ route('login') }}">{{ __('Sign in') }}</a></p>
    </form>

    <p class="copy">© {{ date('Y') }} RakanKampus · Politeknik Ungku Omar</p>
  </div>

 </div>
</div>

@php
  $matricText = [
    'hint' => __('12 characters, e.g. 01DKA23F0456'),
    'empty' => __('Please enter your matric number.'),
    'format' => __('Matric number must be 12 characters, e.g. 01DKA23F0456.'),
    'puo' => __('Only PUO matric numbers (starting with 01) are accepted.'),
    'code' => __('":code" is not a PUO programme code.'),
  ];
  $matricCodes = config('programs.codes');
  $T = [
    'create' => __('Create Account'),
    'createSub' => __('Sign up with your email & matric number'),
    'almost' => __('Almost there, :name!'),
    'almostSub' => __('Create a password for your account'),
    'fillAll' => __('Please fill in your name and email.'),
    'badEmail' => __('Please enter a valid email address.'),
    'weak' => __('Weak'), 'fair' => __('Fair'), 'good' => __('Good'), 'strong' => __('Strong'),
    'rules' => __('Please fulfill all password requirements above.'),
    'mismatch' => __('Passwords do not match.'),
  ];
  // Server said the password was wrong → reopen the form on the password step (mobile)
  $startStep = ($errors->has('password') && ! $errors->hasAny(['first_name', 'last_name', 'email', 'student_id'])) ? 2 : 1;
@endphp
<script>
const T = @json($T);
const form = document.getElementById('regForm');
const mobile = window.matchMedia('(max-width: 860px)');

// ---------- password strength ----------
function pwChecks(p) {
  return {
    length: p.length >= 6,
    upper: /[A-Z]/.test(p),
    lower: /[a-z]/.test(p),
    number: /[0-9]/.test(p),
    special: /[!@#$%^&*(),.?":{}|<>_\-+=]/.test(p),
  };
}
function checkPassword() {
  const p = document.getElementById('password').value;
  const c = pwChecks(p);
  Object.keys(c).forEach(k => document.getElementById('req-' + k).classList.toggle('valid', c[k]));
  const score = Object.values(c).filter(Boolean).length;
  const [pct, text, color] =
    p.length === 0 ? [0, '-', ''] :
    score <= 2 ? [25, T.weak, '#ef4444'] :
    score === 3 ? [50, T.fair, '#f97316'] :
    score === 4 ? [75, T.good, '#eab308'] : [100, T.strong, '#16a34a'];
  const bar = document.getElementById('strengthBar');
  bar.style.width = pct + '%'; bar.style.background = color;
  const label = document.getElementById('strengthLabel');
  label.textContent = text; label.style.color = color;
  clearError();
}
function clearError() { document.getElementById('passwordError').style.display = 'none'; }
function showPwError(msg, focusId) {
  const el = document.getElementById('passwordError');
  el.textContent = msg; el.style.display = 'block';
  document.getElementById(focusId).focus();
}
function togglePw(btn) {
  const input = btn.parentElement.querySelector('input');
  const show = input.type === 'password';
  input.type = show ? 'text' : 'password';
  btn.classList.toggle('on', show);
}

// ---------- steps (mobile) ----------
function goStep(n) {
  form.dataset.step = n;
  document.getElementById('dot1').textContent = n === 2 ? '✓' : '1';
  document.getElementById('dot2').classList.toggle('on', n === 2);
  document.getElementById('stepLine').style.width = n === 2 ? '100%' : '0';
  document.getElementById('lbl1').classList.toggle('on', n === 1);
  document.getElementById('lbl2').classList.toggle('on', n === 2);
  // The friendly "Almost there" heading is for the 2-step (mobile) layout only
  const first = document.getElementById('firstName').value.trim();
  const two = mobile.matches && n === 2;
  document.getElementById('formTitle').textContent = two && first ? T.almost.replace(':name', first) : T.create;
  document.getElementById('formSub').textContent = two ? T.almostSub : T.createSub;
  const part = form.querySelector(n === 2 ? '.s2' : '.s1');
  part.classList.remove('step-in'); void part.offsetWidth; part.classList.add('step-in');
  if (mobile.matches) (n === 2 ? document.getElementById('password') : document.getElementById('firstName')).focus({ preventScroll: true });
}
function markBad(input, bad) { input.classList.toggle('matric-input-err', bad); }
// Step 1 must be complete before moving on (and before submitting on desktop)
function stepOneOk() {
  const req = ['firstName', 'lastName', 'emailInput'].map(id => document.getElementById(id));
  let firstBad = null;
  req.forEach(i => { const bad = i.value.trim() === ''; markBad(i, bad); if (bad && !firstBad) firstBad = i; });
  const email = document.getElementById('emailInput');
  if (!firstBad && !/^\S+@\S+\.\S+$/.test(email.value.trim())) { markBad(email, true); firstBad = email; }
  if (firstBad) { firstBad.focus(); return false; }
  if (window.matricCheck && !window.matricCheck()) { document.getElementById('matricInput').focus(); return false; }
  return true;
}
function nextStep() { if (stepOneOk()) goStep(2); }
['firstName', 'lastName', 'emailInput'].forEach(id => document.getElementById(id).addEventListener('input', e => markBad(e.target, false)));
// Enter on step 1 (mobile) = Next, not submit
form.addEventListener('keydown', (e) => {
  if (e.key === 'Enter' && mobile.matches && form.dataset.step === '1' && e.target.tagName === 'INPUT') { e.preventDefault(); nextStep(); }
});

form.addEventListener('submit', (e) => {
  if (!stepOneOk()) { e.preventDefault(); goStep(1); return; }
  const p = document.getElementById('password').value;
  if (!Object.values(pwChecks(p)).every(Boolean)) { e.preventDefault(); goStep(2); showPwError(T.rules, 'password'); return; }
  if (p !== document.getElementById('password_confirmation').value) { e.preventDefault(); goStep(2); showPwError(T.mismatch, 'password_confirmation'); return; }
});

goStep({{ $startStep }});
mobile.addEventListener('change', () => goStep(Number(form.dataset.step)));
if (!mobile.matches) document.getElementById('firstName').blur();
</script>
<script>
// Friendly inline check for the matric number. Same rules as the server:
// 12 characters, starts with 01 (PUO), and a real PUO programme code.
(function () {
  const input = document.getElementById('matricInput');
  const msg = document.getElementById('matricMsg');
  if (!input || !msg) return;
  const PROGRAMMES = @json($matricCodes);
  const TX = @json($matricText);
  let touched = input.value.trim() !== '';

  function check() {
    const v = input.value.replace(/[\s-]/g, '').toUpperCase();
    if (v === '') return { ok: false, text: TX.empty };
    const m = v.match(/^(\d{2})([A-Z]{3})(\d{2})([A-Z])(\d{4})$/);
    if (!m) return { ok: false, text: TX.format };
    if (m[1] !== '01') return { ok: false, text: TX.puo };
    if (!PROGRAMMES[m[2]]) return { ok: false, text: TX.code.replace(':code', m[2]) };
    return { ok: true, text: '✓ ' + PROGRAMMES[m[2]] };
  }
  function show(force) {
    const r = check();
    msg.classList.remove('matric-ok', 'matric-err', 'matric-hint');
    input.classList.remove('matric-input-err', 'matric-input-ok');
    if (r.ok) {
      msg.textContent = r.text; msg.classList.add('matric-ok'); input.classList.add('matric-input-ok');
    } else if (touched || force) {
      msg.textContent = '⚠️ ' + r.text; msg.classList.add('matric-err'); input.classList.add('matric-input-err');
    } else {
      msg.textContent = TX.hint; msg.classList.add('matric-hint');
    }
    return r.ok;
  }
  window.matricCheck = () => { touched = true; return show(true); };
  input.addEventListener('input', () => { input.value = input.value.toUpperCase(); show(false); });
  input.addEventListener('blur', () => { touched = true; show(false); });
  show(false);
})();
</script>
<script>
// ---------- live student card ----------
(function () {
  const PROG = @json($matricCodes);
  const CT = @json(['name' => __('Your name'), 'pw' => __('Password'), 'secured' => __('Secured')]);
  const $ = id => document.getElementById(id);
  const first = $('firstName'), last = $('lastName'), matric = $('matricInput'), pw = $('password');
  let lastInit = '';
  function update() {
    const f = first.value.trim(), l = last.value.trim();
    const name = (f + ' ' + l).trim();
    $('cardName').textContent = name || CT.name;
    $('cardName').classList.toggle('dim', !name);
    const init = ((f[0] || '') + (l[0] || '')).toUpperCase() || '?';
    if (init !== lastInit) { const ph = $('cardInit'); ph.textContent = init; ph.classList.remove('pop'); void ph.offsetWidth; ph.classList.add('pop'); lastInit = init; }
    const m = matric.value.replace(/[\s-]/g, '').toUpperCase();
    $('cardMatric').textContent = m || '01XXX00X0000';
    $('cardMatric').classList.toggle('dim', !m);
    const code = (m.match(/^01([A-Z]{3})/) || [])[1];
    $('cardProg').textContent = (code && PROG[code]) || 'RakanKampus';
    const ok = Object.values(pwChecks(pw.value)).every(Boolean);
    $('cardLock').classList.toggle('ok', ok);
    $('cardLockTxt').textContent = ok ? CT.secured : CT.pw;
  }
  [first, last, matric, pw].forEach(i => i.addEventListener('input', update));
  update();
})();
</script>
</body>
</html>
