@extends('layouts.app')

@section('content')

@include('partials.auth-style')
@include('partials.forgot-style')

<div class="blob blob-1"></div>
<div class="blob blob-2"></div>
<div class="blob blob-3"></div>

@php
  $eye = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>';
@endphp

<div class="card">
  <div class="fp-icon">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3 5 6v5c0 4.5 3 8.3 7 10 4-1.7 7-5.5 7-10V6l-7-3Z"/><path d="m9 12 2 2 4-4"/></svg>
  </div>
  <div class="fp-steps"><span></span><span></span><span class="on"></span></div>

  <h1>New password</h1>
  <p class="fp-lead">Code verified! Choose a new password for your account.</p>

  @if ($errors->any())
    <div class="fp-msg err">{{ $errors->first() }}</div>
  @endif

  <form method="POST" action="{{ route('password.new.post') }}" id="pwForm">
    @csrf
    <div class="field">
      <label>New password</label>
      <div class="pw-wrap">
        <input type="password" name="password" id="pw1" placeholder="At least 6 characters" minlength="6" required autofocus autocomplete="new-password">
        <button type="button" class="pw-eye" data-for="pw1" aria-label="Show password">{!! $eye !!}</button>
      </div>
      <div class="pw-meter"><i></i><i></i><i></i><i></i></div>
      <div class="pw-hint" id="pwHint">Use 6+ characters. Mixing letters, numbers and symbols makes it stronger.</div>
    </div>
    <div class="field">
      <label>Confirm password</label>
      <div class="pw-wrap">
        <input type="password" name="password_confirmation" id="pw2" placeholder="Type it again" minlength="6" required autocomplete="new-password">
        <button type="button" class="pw-eye" data-for="pw2" aria-label="Show password">{!! $eye !!}</button>
      </div>
      <div class="pw-match" id="pwMatch"></div>
    </div>
    <button type="submit" class="btn-signin" id="pwBtn" disabled>Save new password</button>
  </form>

  <a class="fp-back" href="{{ route('login') }}">Cancel</a>
</div>

<footer>© 2026 RakanKampus · Politeknik Ungku Omar</footer>

<script>
(function () {
  var p1 = document.getElementById('pw1'), p2 = document.getElementById('pw2');
  var bars = document.querySelectorAll('.pw-meter i'), hint = document.getElementById('pwHint');
  var match = document.getElementById('pwMatch'), btn = document.getElementById('pwBtn');
  var COLORS = ['#f87171', '#fbbf24', '#a3e635', '#4ade80'];
  var WORDS = ['Weak', 'Okay', 'Good', 'Strong'];

  function score(v) {
    if (v.length < 6) return v.length ? 0 : -1;
    var s = 0;
    if (v.length >= 8) s++;
    if (/[a-z]/.test(v) && /[A-Z]/.test(v)) s++;
    if (/\d/.test(v)) s++;
    if (/[^A-Za-z0-9]/.test(v)) s++;
    return Math.min(3, s);
  }
  function update() {
    var s = score(p1.value);
    bars.forEach(function (b, i) { b.style.background = i <= s ? COLORS[Math.max(s, 0)] : ''; });
    hint.textContent = s < 0 ? 'Use 6+ characters. Mixing letters, numbers and symbols makes it stronger.'
      : (p1.value.length < 6 ? 'Too short — at least 6 characters.' : 'Strength: ' + WORDS[s]);
    var same = p2.value && p1.value === p2.value;
    match.textContent = !p2.value ? '' : (same ? '✓ Passwords match' : 'Passwords don\'t match yet');
    match.style.color = same ? '#bbf7d0' : '';
    btn.disabled = !(p1.value.length >= 6 && same);
  }
  p1.addEventListener('input', update); p2.addEventListener('input', update);

  document.querySelectorAll('.pw-eye').forEach(function (b) {
    b.addEventListener('click', function () {
      var f = document.getElementById(b.getAttribute('data-for'));
      f.type = f.type === 'password' ? 'text' : 'password';
      b.style.opacity = f.type === 'text' ? 1 : '';
    });
  });
  document.getElementById('pwForm').addEventListener('submit', function () {
    setTimeout(function () { btn.disabled = true; btn.textContent = 'Saving…'; }, 0);
  });
})();
</script>
@endsection
