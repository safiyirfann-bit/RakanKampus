<!DOCTYPE html>
<html lang="ms">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Create Account - RakanKampus</title>

<style>
*{
  margin:0;
  padding:0;
  box-sizing:border-box;
}

body{
  min-height:100vh;
  display:flex;
  align-items:center;
  justify-content:center;
  background: linear-gradient(120deg, #a78bfa, #f472b6, #60a5fa, #a78bfa);
    background-size: 300% 300%;
    animation: gradientShift 15s ease infinite;
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
  padding:40px 20px;
}

.wrapper{
  width:100%;
  max-width:600px;
}

.card{
  position:relative;
  background: linear-gradient(165deg, #f5f0ff 0%, #fdf2f8 55%, #eef4ff 100%);
  border-radius:28px;
  padding:48px 44px 38px;
  box-shadow: 0 24px 60px rgba(124, 58, 237, 0.22), 0 2px 8px rgba(124, 58, 237, 0.08);
  overflow:hidden;
}

.card::before{
  content:"";
  position:absolute;
  top:0; left:0; right:0;
  height:6px;
  background: linear-gradient(120deg, #a78bfa, #f472b6, #60a5fa);
}

.title{
  text-align:center;
  font-size:30px;
  font-weight:800;
  letter-spacing:-0.02em;
  color:#101a3d;
  margin-bottom:26px;
}

.logo-box{
  width:120px;
  height:120px;
  margin:0 auto 16px;
  display:flex;
  align-items:center;
  justify-content:center;
  border-radius:50%;
  background: radial-gradient(circle, rgba(167,139,250,0.14), rgba(244,114,182,0.06) 70%, transparent);
  filter: drop-shadow(0 10px 20px rgba(124,58,237,0.25));
  animation: logoBob 4s ease-in-out infinite;
}

.logo-box svg{
  width:100%;
  height:100%;
}

@keyframes logoBob {
  0%, 100% { transform: translateY(0); }
  50%      { transform: translateY(-8px); }
}

.brand{
  text-align:center;
  font-size:22px;
  font-weight:800;
  background: linear-gradient(120deg, #7c3aed, #db2777);
  -webkit-background-clip: text;
  background-clip: text;
  color: transparent;
  margin-bottom:4px;
}

.subtitle{
  text-align:center;
  color: #8b7fae;
  font-size:14px;
  margin-bottom:32px;
}

.form-grid{
  display:grid;
  grid-template-columns:1fr 1fr;
  gap:14px;
  margin-bottom:16px;
}

.field-label{
  display:block;
  font-size:12px;
  font-weight:700;
  letter-spacing:0.04em;
  text-transform:uppercase;
  color: #a78bfa;
  margin-bottom:7px;
}

.input-group{
  margin-bottom:16px;
}

.input{
  width:100%;
  height:52px;
  padding:0 18px;
  border-radius:14px;
  border:1.5px solid #e4d9fb;
  background: #ffffff;
  color:#101a3d;
  font-weight:600;
  font-size:15px;
  outline:none;
  transition: border-color 0.18s ease, background 0.18s ease, box-shadow 0.18s ease;
}

.input::placeholder{
  color: #b3a8d6;
  font-weight:400;
}

.input:focus{
  border-color: #a78bfa;
  background: #ffffff;
  box-shadow: 0 0 0 4px rgba(124,58,237,0.12);
}

.input:hover:not(:focus){
  border-color: #d6c9f0;
}

.input:-webkit-autofill,
.input:-webkit-autofill:hover,
.input:-webkit-autofill:focus {
  -webkit-text-fill-color: #101a3d !important;
  -webkit-box-shadow: 0 0 0px 1000px #ffffff inset !important;
  transition: background-color 5000s ease-in-out 0s;
}

.footer-row{
  margin-top:10px;
  display:flex;
  align-items:center;
  justify-content:space-between;
  gap:16px;
  flex-wrap:wrap;
}

.signin{
  color: #8b7fae;
  font-size:14px;
}

.signin a{
  color:#a78bfa;
  font-weight:700;
  text-decoration:none;
  border-bottom:1.5px solid rgba(124,58,237,0.3);
  transition: border-color 0.15s ease;
}

.signin a:hover{
  border-color: #a78bfa;
}

.submit-btn{
  min-width:190px;
  height:52px;
  border:none;
  border-radius:14px;
  background: linear-gradient(120deg, #a78bfa, #f472b6);
  color:#ffffff;
  font-size:16px;
  font-weight:800;
  cursor:pointer;
  transition: transform 0.15s ease, box-shadow 0.15s ease;
}

.submit-btn:hover{
  transform: translateY(-2px);
  box-shadow: 0 14px 28px rgba(124,58,237,0.32);
}

.submit-btn:active{
  transform: translateY(0);
}

.copyright{
  text-align:center;
  margin-top:22px;
  color: #ffffff;
  font-size:13px;
  text-shadow: 0 1px 3px rgba(0,0,0,0.35);
}

.error-banner{
  background: #fef2f2;
  border: 1.5px solid #fecaca;
  color: #b91c1c;
  padding: 15px 18px;
  border-radius: 16px;
  margin-bottom: 22px;
  font-size: 13.5px;
}

.error-banner ul{
  margin: 0;
  padding-left: 18px;
}

.error-banner li{
  margin-bottom: 2px;
}

.error-banner li:last-child{
  margin-bottom: 0;
}

.password-requirements {
  margin-top: 12px;
  margin-bottom: 6px;
  padding: 18px 20px;
  border-radius: 18px;
  background: #ffffff;
  border: 1.5px solid #ece6fb;
}

.strength-header{
  display:flex;
  align-items:center;
  justify-content:space-between;
  margin-bottom:10px;
}

.strength-title{
  font-size:12px;
  font-weight:700;
  letter-spacing:0.04em;
  text-transform:uppercase;
  color: #8b7fae;
}

.strength-bar-track {
  width: 100%;
  height: 7px;
  border-radius: 6px;
  background: #ece6fb;
  overflow: hidden;
  margin-bottom: 14px;
}

.strength-bar-fill {
  height: 100%;
  width: 0%;
  border-radius: 6px;
  transition: width 0.3s ease, background 0.3s ease;
  background: #ef4444;
}

.strength-label {
  font-size: 13px;
  font-weight: 800;
  color: #ef4444;
  transition: color 0.3s ease;
}

.req-list {
  list-style: none;
  margin: 0;
  padding: 0;
  display: grid;
  gap: 9px;
}

.req-list li {
  font-size: 13px;
  color: #8b7fae;
  display: flex;
  align-items: center;
  gap: 10px;
  transition: color 0.2s ease;
}

.req-list li.valid {
  color: #101a3d;
  font-weight:600;
}

.req-list li .icon {
  width: 18px;
  height: 18px;
  flex-shrink: 0;
  position: relative;
  border-radius: 50%;
  background: #fee2e2;
  transition: background 0.2s ease;
}

.req-list li.valid .icon {
  background: #dcfce7;
}

.req-list li .icon-cross,
.req-list li .icon-check {
  width: 18px;
  height: 18px;
  padding: 4px;
  position: absolute;
  top: 0;
  left: 0;
  transition: opacity 0.2s ease;
}

.req-list li .icon-cross {
  color: #ef4444;
  opacity: 1;
}

.req-list li .icon-check {
  color: #16a34a;
  opacity: 0;
}

.req-list li.valid .icon-cross {
  opacity: 0;
}

.req-list li.valid .icon-check {
  opacity: 1;
}

.password-error{
  color:#dc2626;
  font-size:13px;
  font-weight:600;
  margin-top:10px;
  margin-bottom:4px;
  display:none;
}

@media (max-width:600px){
  .card{
    padding:32px 24px 28px;
  }

  .title{
    font-size:26px;
  }

  .form-grid{
    grid-template-columns:1fr;
  }

  .footer-row{
    flex-direction:column;
    align-items:stretch;
  }

  .submit-btn{
    width:100%;
  }

  .signin{
    text-align:center;
  }
}

  @keyframes gradientShift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
  }
</style>
</head>

<body>

<div class="wrapper">

  <div class="card">

    <h1 class="title">Create Account</h1>

    <div class="logo-box">
      <x-brand-logo size="110" />
    </div>

    <div class="brand">RakanKampus</div>
    <div class="subtitle">Your University AI Assistant</div>

    <form method="POST" action="{{ route('register') }}">
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

      <div class="form-grid">
        <div>
          <label class="field-label">First name</label>
          <input class="input" type="text" name="first_name" placeholder="e.g. Ahmad" value="{{ old('first_name') }}">
        </div>
        <div>
          <label class="field-label">Last name</label>
          <input class="input" type="text" name="last_name" placeholder="e.g. Razif" value="{{ old('last_name') }}">
        </div>
      </div>

      <div class="input-group">
        <label class="field-label">Email address</label>
        <input class="input" type="email" name="email" placeholder="you@graduate.utm.my" value="{{ old('email') }}">
      </div>

      <div class="input-group">
        <label class="field-label">Password</label>
        <input class="input" type="password" name="password" id="password" placeholder="Create a password" oninput="checkPassword()">
      </div>

      <div class="password-requirements">

        <div class="strength-header">
          <span class="strength-title">Password strength</span>
          <span class="strength-label" id="strengthLabel">-</span>
        </div>

        <div class="strength-bar-track">
          <div class="strength-bar-fill" id="strengthBar"></div>
        </div>

        <ul class="req-list">
          <li id="req-length">
            <span class="icon">
              <svg class="icon-cross" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M5 5l10 10M15 5L5 15"/></svg>
              <svg class="icon-check" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M4 10l4 4 8-8"/></svg>
            </span>
            At least 6 characters
          </li>
          <li id="req-upper">
            <span class="icon">
              <svg class="icon-cross" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M5 5l10 10M15 5L5 15"/></svg>
              <svg class="icon-check" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M4 10l4 4 8-8"/></svg>
            </span>
            One uppercase letter (A-Z)
          </li>
          <li id="req-lower">
            <span class="icon">
              <svg class="icon-cross" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M5 5l10 10M15 5L5 15"/></svg>
              <svg class="icon-check" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M4 10l4 4 8-8"/></svg>
            </span>
            One lowercase letter (a-z)
          </li>
          <li id="req-number">
            <span class="icon">
              <svg class="icon-cross" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M5 5l10 10M15 5L5 15"/></svg>
              <svg class="icon-check" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M4 10l4 4 8-8"/></svg>
            </span>
            One number (0-9)
          </li>
          <li id="req-special">
            <span class="icon">
              <svg class="icon-cross" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M5 5l10 10M15 5L5 15"/></svg>
              <svg class="icon-check" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M4 10l4 4 8-8"/></svg>
            </span>
            One special character (!@#$%^&*)
          </li>
        </ul>

        <p class="password-error" id="passwordError"></p>

      </div>

      <div class="input-group" style="margin-top:16px;">
        <label class="field-label">Confirm password</label>
        <input class="input" type="password" name="password_confirmation" id="password_confirmation" placeholder="Re-enter your password" oninput="clearError()">
      </div>

      <div class="input-group">
        <label class="field-label">Student ID</label>
        <input class="input" type="text" name="student_id" placeholder="e.g. D157EKP1085" value="{{ old('student_id') }}">
      </div>

      <div class="footer-row">
        <div class="signin">
          Already have an account?
          <a href="{{ route('login') }}">Sign in</a>
        </div>

        <button type="submit" class="submit-btn">
          Create account
        </button>
      </div>

    </form>

  </div>

  <div class="copyright">
    © 2026 RakanKampus · Universiti Teknologi Malaysia
  </div>

</div>

<script>
function checkPassword() {
  const password = document.getElementById('password').value;

  const checks = {
    length:  password.length >= 6,
    upper:   /[A-Z]/.test(password),
    lower:   /[a-z]/.test(password),
    number:  /[0-9]/.test(password),
    special: /[!@#$%^&*(),.?":{}|<>_\-+=]/.test(password),
  };

  toggleCheck('req-length', checks.length);
  toggleCheck('req-upper', checks.upper);
  toggleCheck('req-lower', checks.lower);
  toggleCheck('req-number', checks.number);
  toggleCheck('req-special', checks.special);

  const score = Object.values(checks).filter(Boolean).length;

  const bar = document.getElementById('strengthBar');
  const label = document.getElementById('strengthLabel');

  let percent = 0;
  let text = '-';
  let color = '#ef4444';

  if (password.length === 0) {
    percent = 0; text = '-'; color = '#b3a8d6';
  } else if (score <= 2) {
    percent = 25; text = 'Weak'; color = '#ef4444';
  } else if (score === 3) {
    percent = 50; text = 'Fair'; color = '#fb923c';
  } else if (score === 4) {
    percent = 75; text = 'Good'; color = '#eab308';
  } else if (score === 5) {
    percent = 100; text = 'Strong'; color = '#16a34a';
  }

  bar.style.width = percent + '%';
  bar.style.background = color;
  label.textContent = text;
  label.style.color = color;
}

function toggleCheck(id, valid) {
  const el = document.getElementById(id);
  if (valid) {
    el.classList.add('valid');
  } else {
    el.classList.remove('valid');
  }
}

function clearError() {
  document.getElementById('passwordError').style.display = 'none';
}

document.querySelector('form').addEventListener('submit', function(e) {
  const password = document.getElementById('password').value;
  const confirmPassword = document.getElementById('password_confirmation').value;
  const errorEl = document.getElementById('passwordError');

  const checks = {
    length:  password.length >= 6,
    upper:   /[A-Z]/.test(password),
    lower:   /[a-z]/.test(password),
    number:  /[0-9]/.test(password),
    special: /[!@#$%^&*(),.?":{}|<>_\-+=]/.test(password),
  };

  const allValid = Object.values(checks).every(Boolean);

  if (!allValid) {
    e.preventDefault();
    errorEl.textContent = 'Please fulfill all password requirements above.';
    errorEl.style.display = 'block';
    document.getElementById('password').scrollIntoView({ behavior: 'smooth', block: 'center' });
    return;
  }

  if (password !== confirmPassword) {
    e.preventDefault();
    errorEl.textContent = 'Passwords do not match.';
    errorEl.style.display = 'block';
    document.getElementById('password_confirmation').scrollIntoView({ behavior: 'smooth', block: 'center' });
    return;
  }

  errorEl.style.display = 'none';
});
</script>
</body>
</html>