@extends('layouts.app')

@section('content')

@include('partials.auth-style')
@include('partials.forgot-style')

<div class="blob blob-1"></div>
<div class="blob blob-2"></div>
<div class="blob blob-3"></div>

<div class="card">
  <div class="fp-icon">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="10" width="16" height="11" rx="2.5"/><path d="M8 10V7a4 4 0 0 1 7.5-2"/><circle cx="12" cy="15.5" r="1.4"/></svg>
  </div>
  <div class="fp-steps"><span class="on"></span><span></span><span></span></div>

  <h1>Forgot password?</h1>
  <p class="fp-lead">No worries. Enter the email you registered with and we'll send you a 6-digit code.</p>

  @if ($errors->any())
    <div class="fp-msg err">{{ $errors->first() }}</div>
  @endif

  <form method="POST" action="{{ route('password.email') }}" id="fpForm">
    @csrf
    <div class="field">
      <label>Email</label>
      <input type="email" name="email" placeholder="Enter your email" value="{{ old('email', session('pw_reset_email')) }}" required autofocus autocomplete="email">
    </div>
    <button type="submit" class="btn-signin" id="fpBtn">Send code</button>
  </form>

  <a class="fp-back" href="{{ route('login') }}">← Back to login</a>
</div>

<footer>© 2026 RakanKampus · Politeknik Ungku Omar</footer>

<script>
  document.getElementById('fpForm').addEventListener('submit', function () {
    var b = document.getElementById('fpBtn');
    setTimeout(function () { b.disabled = true; b.textContent = 'Sending…'; }, 0);
  });
</script>
@endsection
