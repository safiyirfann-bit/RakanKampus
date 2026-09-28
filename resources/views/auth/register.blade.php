<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Create Account - RakanKampus</title>
{{--
  Create Account — same glass look as the Login page.
  Desktop: one wide card, brand panel on the left, the form two fields per row
           on the right, so everything fits on one screen.
  Mobile:  the same form split into 2 short steps
           (1: name, email, matric → 2: password), no long scrolling.
--}}
<style>
:root{
  --purple:#a78bfa; --pink:#f472b6; --blue:#60a5fa; --amber:#f59e0b;
  --field-bg:rgba(255,255,255,.16); --field-border:rgba(255,255,255,.34);
  --muted:#f3e8ff; --ph:rgba(255,255,255,.62);
}
*{margin:0;padding:0;box-sizing:border-box}
body{
  min-height:100vh;display:flex;flex-direction:column;align-items:center;justify-content:center;
  padding:28px 16px;overflow-x:hidden;position:relative;color:#fff;
  font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Helvetica,Arial,sans-serif;
  background:linear-gradient(120deg,#a78bfa,#f472b6,#60a5fa,#a78bfa);background-size:300% 300%;
  animation:gradientShift 15s ease infinite;
}
@keyframes gradientShift{0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}}
.blob{position:fixed;border-radius:50%;filter:blur(50px);opacity:.45;pointer-events:none;z-index:0}
.blob-1{width:320px;height:320px;background:var(--amber);top:-60px;left:-80px;animation:floatA 14s ease-in-out infinite}
.blob-2{width:260px;height:260px;background:var(--blue);bottom:-60px;right:-60px;animation:floatB 18s ease-in-out infinite}
@keyframes floatA{50%{transform:translate(40px,60px) scale(1.15)}}
@keyframes floatB{50%{transform:translate(-30px,-40px) scale(1.1)}}

.card{
  position:relative;z-index:1;width:100%;max-width:900px;display:grid;grid-template-columns:300px 1fr;
  background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.3);border-radius:26px;overflow:hidden;
  backdrop-filter:blur(12px);-webkit-backdrop-filter:blur(12px);box-shadow:0 24px 60px rgba(0,0,0,.25);
}

/* ---- left: brand panel ---- */
.side{
  padding:34px 26px;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;
  background:linear-gradient(160deg,rgba(255,255,255,.26),rgba(255,255,255,.05));border-right:1px solid rgba(255,255,255,.28);
}
.mascot-wrap{position:relative;animation:bob 4s ease-in-out infinite}
@keyframes bob{50%{transform:translateY(-8px)}}
.mascot{width:96px;height:96px;filter:drop-shadow(0 10px 18px rgba(0,0,0,.22))}
.bubble{position:absolute;top:-4px;right:-26px;background:#fff;border-radius:12px;padding:5px 8px;display:flex;gap:3px;box-shadow:0 6px 14px rgba(0,0,0,.18)}
.bubble span{width:5px;height:5px;border-radius:50%;background:var(--purple);animation:dots 1.2s infinite ease-in-out}
.bubble span:nth-child(2){animation-delay:.15s}.bubble span:nth-child(3){animation-delay:.3s}
@keyframes dots{0%,60%,100%{transform:translateY(0);opacity:.6}30%{transform:translateY(-3px);opacity:1}}
.brand{font-size:24px;font-weight:800;margin-top:10px}
.tagline{font-size:13.5px;color:var(--muted);margin-top:2px}
.perks{margin-top:24px;display:grid;gap:10px;width:100%;text-align:left}
.perks div{display:flex;align-items:center;gap:10px;font-size:13px;font-weight:600;background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.18);padding:9px 12px;border-radius:12px}
.perks svg{width:28px;height:28px;padding:6px;border-radius:9px;background:#fff;color:#c026d3;flex-shrink:0}

/* ---- right: form ---- */
.main{padding:30px 34px 26px}
.head h1{font-size:25px;font-weight:800;letter-spacing:-.01em}
.head p{font-size:13.5px;color:var(--muted);margin:3px 0 18px}
.head .mini{display:none}

.row{display:grid;grid-template-columns:1fr 1fr;gap:12px}
.field{margin-bottom:13px;min-width:0}
label{display:block;font-size:11.5px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;margin-bottom:6px}
.input{
  width:100%;height:46px;padding:0 14px;border-radius:12px;border:1px solid var(--field-border);background:var(--field-bg);
  color:#fff;font-size:15px;outline:none;font-family:inherit;transition:border-color .15s,background .15s,box-shadow .15s;
}
.input::placeholder{color:var(--ph)}
.input:focus{border-color:rgba(255,255,255,.75);background:rgba(255,255,255,.22);box-shadow:0 0 0 3px rgba(255,255,255,.14)}
.pw-wrap{position:relative}
.pw-wrap .input{padding-right:44px}
.eye{position:absolute;right:6px;top:50%;transform:translateY(-50%);width:34px;height:34px;border:0;background:none;color:rgba(255,255,255,.85);cursor:pointer;border-radius:8px;display:grid;place-items:center}
.eye svg{width:19px;height:19px}.eye .off{display:none}.eye.on .on{display:none}.eye.on .off{display:block}

/* password strength: 4-part bar + 5 small rule tags */
.meter{display:flex;gap:4px;margin:8px 0 7px}
.meter i{flex:1;height:5px;border-radius:9px;background:rgba(255,255,255,.3);transition:background .2s}
.rules{display:flex;flex-wrap:wrap;gap:5px;list-style:none}
.rules li{font-size:11px;font-weight:700;padding:3px 8px;border-radius:99px;background:rgba(255,255,255,.18);color:#fff;transition:background .2s,color .2s}
.rules li.valid{background:#bbf7d0;color:#166534}
.strength{float:right;font-size:11px;font-weight:800;letter-spacing:.04em;text-transform:uppercase}

/* matric + inline messages */
.matric-msg{font-size:12px;margin-top:6px;line-height:1.35}
.matric-msg:empty{display:none}
.matric-hint{color:var(--muted)}
.matric-ok{color:#bbf7d0;font-weight:700}
.matric-err,.password-error{color:#fff;background:rgba(220,38,38,.28);border:1px solid rgba(254,202,202,.55);border-radius:10px;padding:6px 10px;font-weight:600}
.password-error{display:none;font-size:12.5px;margin:2px 0 10px}
.matric-input-err{border-color:#fecaca !important;box-shadow:0 0 0 3px rgba(248,113,113,.3) !important}
.matric-input-ok{border-color:#bbf7d0 !important}
.error-banner{background:rgba(239,68,68,.2);border:1px solid rgba(254,202,202,.55);border-radius:14px;padding:10px 14px;margin-bottom:14px;font-size:13.5px}
.error-banner ul{list-style:none;display:grid;gap:3px}

.btn{
  width:100%;height:48px;border:0;border-radius:12px;cursor:pointer;font-family:inherit;
  font-size:15.5px;font-weight:800;color:#fff;background:linear-gradient(120deg,var(--purple),var(--pink));
  box-shadow:0 8px 20px rgba(120,40,140,.25);transition:transform .1s,box-shadow .15s;
}
.btn:hover{box-shadow:0 10px 24px rgba(0,0,0,.25)}.btn:active{transform:translateY(1px)}
.btn.ghost{background:rgba(255,255,255,.2);box-shadow:none;border:1px solid rgba(255,255,255,.3)}
.actions{margin-top:6px}
.btn-next,.btn-back{display:none}
.signin{text-align:center;font-size:14px;color:var(--muted);margin-top:14px}
.signin a{color:#fff;font-weight:800;text-decoration:underline}

/* stepper (mobile only) */
.steps{display:none;align-items:center;gap:8px;margin:0 0 16px}
.steps .dot{width:28px;height:28px;border-radius:50%;display:grid;place-items:center;font-weight:800;font-size:13px;background:rgba(255,255,255,.25);flex-shrink:0;transition:background .2s,color .2s}
.steps .dot.on{background:#fff;color:#c026d3}
.steps small{font-size:12px;font-weight:700;white-space:nowrap}
.steps .ln{flex:1;height:3px;border-radius:9px;background:rgba(255,255,255,.3);transition:background .2s}
.steps .ln.on{background:#fff}

footer{position:relative;z-index:1;margin-top:18px;color:var(--muted);font-size:13px;text-align:center}

/* ================= mobile: 2 steps ================= */
@media (max-width:760px){
  body{justify-content:flex-start;padding-top:22px}
  .card{grid-template-columns:1fr;max-width:460px}
  .side{display:none}
  .main{padding:24px 22px 22px}
  .head{display:flex;align-items:center;gap:12px;margin-bottom:14px}
  .head .mini{display:block;width:58px;height:58px;flex-shrink:0;filter:drop-shadow(0 6px 10px rgba(0,0,0,.18))}
  .head h1{font-size:22px}
  .head p{margin:2px 0 0;font-size:12.5px}
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
@media (prefers-reduced-motion:reduce){body,.blob,.mascot-wrap,.bubble span{animation:none !important}}
</style>
</head>
<body>

<div class="blob blob-1"></div>
<div class="blob blob-2"></div>

<div class="card">

  <!-- Brand panel (desktop) -->
  <aside class="side">
    <div class="mascot-wrap">
      <x-brand-logo size="96" class="mascot" />
      <div class="bubble"><span></span><span></span><span></span></div>
    </div>
    <div class="brand">RakanKampus</div>
    <div class="tagline">{{ __('Your Politeknik AI Assistant') }}</div>
    <div class="perks">
      <div><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a8 8 0 0 1-11.6 7.1L4 20l1-4.6A8 8 0 1 1 21 12z"/></svg>{{ __('Ask anything about PUO') }}</div>
      <div><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="13" r="8"/><path d="M12 9v4l2.5 2M5 3 2 6M19 3l3 3"/></svg>{{ __('Reminders for assignments & quizzes') }}</div>
      <div><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="16" rx="3"/><path d="M3 10h18M8 3v4M16 3v4"/></svg>{{ __('Your class timetable in one place') }}</div>
    </div>
  </aside>

  <!-- Form -->
  <div class="main">
    <div class="head">
      <x-brand-logo size="58" class="mini" />
      <div>
        <h1 id="formTitle">{{ __('Create Account') }}</h1>
        <p id="formSub">{{ __('Sign up with your PUO email & matric number') }}</p>
      </div>
    </div>

    <div class="steps" aria-hidden="true">
      <span class="dot on" id="dot1">1</span><small>{{ __('Your details') }}</small>
      <span class="ln" id="stepLine"></span>
      <span class="dot" id="dot2">2</span><small>{{ __('Password') }}</small>
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
            <label for="firstName">{{ __('First name') }}</label>
            <input class="input" type="text" name="first_name" id="firstName" placeholder="e.g. Ahmad" value="{{ old('first_name') }}" autocomplete="given-name" required>
          </div>
          <div class="field">
            <label for="lastName">{{ __('Last name') }}</label>
            <input class="input" type="text" name="last_name" id="lastName" placeholder="e.g. Razif" value="{{ old('last_name') }}" autocomplete="family-name" required>
          </div>
        </div>
        <div class="row split">
          <div class="field">
            <label for="emailInput">{{ __('Email address') }}</label>
            <input class="input" type="email" name="email" id="emailInput" placeholder="you@student.puo.edu.my" value="{{ old('email') }}" autocomplete="email" required>
          </div>
          <div class="field">
            <label for="matricInput">{{ __('Matric Number (PUO)') }}</label>
            <input class="input" type="text" name="student_id" id="matricInput" placeholder="e.g. 01DKA23F0456" value="{{ old('student_id') }}" maxlength="16" autocomplete="off">
            <p id="matricMsg" class="matric-msg"></p>
          </div>
        </div>
      </div>

      <!-- Step 2: password -->
      <div class="s2">
        <div class="row split">
          <div class="field">
            <label for="password">{{ __('Password') }} <span class="strength" id="strengthLabel"></span></label>
            <div class="pw-wrap">
              <input class="input" type="password" name="password" id="password" placeholder="{{ __('Create a password') }}" oninput="checkPassword()" autocomplete="new-password">
              <button type="button" class="eye" onclick="togglePw(this)" aria-label="{{ __('Show password') }}">
                <svg class="on" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>
                <svg class="off" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M3 3l18 18M10.6 5.1A10 10 0 0 1 12 5c6.4 0 10 7 10 7a17 17 0 0 1-3.2 4M6.6 6.6C3.8 8.4 2 12 2 12s3.6 7 10 7a9.6 9.6 0 0 0 5.4-1.6M9.9 9.9a3 3 0 0 0 4.2 4.2"/></svg>
              </button>
            </div>
            <div class="meter" id="meter"><i></i><i></i><i></i><i></i></div>
            <ul class="rules">
              <li id="req-length">6+ {{ __('characters') }}</li>
              <li id="req-upper">A-Z</li>
              <li id="req-lower">a-z</li>
              <li id="req-number">0-9</li>
              <li id="req-special">!@#$</li>
            </ul>
          </div>
          <div class="field">
            <label for="password_confirmation">{{ __('Confirm password') }}</label>
            <div class="pw-wrap">
              <input class="input" type="password" name="password_confirmation" id="password_confirmation" placeholder="{{ __('Re-enter your password') }}" oninput="clearError()" autocomplete="new-password">
              <button type="button" class="eye" onclick="togglePw(this)" aria-label="{{ __('Show password') }}">
                <svg class="on" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>
                <svg class="off" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M3 3l18 18M10.6 5.1A10 10 0 0 1 12 5c6.4 0 10 7 10 7a17 17 0 0 1-3.2 4M6.6 6.6C3.8 8.4 2 12 2 12s3.6 7 10 7a9.6 9.6 0 0 0 5.4-1.6M9.9 9.9a3 3 0 0 0 4.2 4.2"/></svg>
              </button>
            </div>
          </div>
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
  </div>
</div>

<footer>© 2026 RakanKampus · Politeknik Ungku Omar</footer>

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
    'createSub' => __('Sign up with your PUO email & matric number'),
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
const mobile = window.matchMedia('(max-width: 760px)');

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
  const [bars, text, color] =
    p.length === 0 ? [0, '', ''] :
    score <= 2 ? [1, T.weak, '#fca5a5'] :
    score === 3 ? [2, T.fair, '#fdba74'] :
    score === 4 ? [3, T.good, '#fde047'] : [4, T.strong, '#86efac'];
  document.querySelectorAll('#meter i').forEach((el, i) => el.style.background = i < bars ? color : '');
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
  document.getElementById('stepLine').classList.toggle('on', n === 2);
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
</body>
</html>
