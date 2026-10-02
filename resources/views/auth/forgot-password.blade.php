@extends('layouts.app')

@section('content')

@include('partials.forgot-style')

  @include('partials.forgot-head', ['step' => 1, 'lines' => $errors->any()
      ? [[__('Hmm, let\'s try that again.'), true]]
      : [__('Forgot your password? It happens 😅'), __('Just tell me the email you signed up with and I\'ll send you a code.')]])

  <h1>Forgot password?</h1>
  <p class="fp-lead">No worries. Enter the email you registered with and we'll send you a 6-digit code.</p>

  @if ($errors->any())
    <div class="fp-msg err">{{ $errors->first() }}</div>
  @endif

  <form method="POST" action="{{ route('password.email') }}" id="fpForm">
    @csrf
    <div class="field">
      <label>Email</label>
      <div class="fp-input">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2.5"/><path d="m4 7 8 6 8-6"/></svg>
        <input type="email" name="email" placeholder="Enter your email" value="{{ old('email', session('pw_reset_email')) }}" required autofocus autocomplete="email">
      </div>
    </div>
    <button type="submit" class="btn-signin" id="fpBtn"><span>Send code</span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></button>
  </form>

  <div class="fp-chips">
    <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>Code works {{ $minutes ?? 5 }} min</span>
    <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V8a4 4 0 0 1 8 0v3"/></svg>Never share your code</span>
  </div>

  <div class="fp-foot"><a class="fp-back" href="{{ route('login') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M11 18l-6-6 6-6"/></svg> Back to login</a></div>
@include('partials.forgot-foot')

<script>
  document.getElementById('fpForm').addEventListener('submit', function () {
    var b = document.getElementById('fpBtn');
    setTimeout(function () { b.disabled = true; b.querySelector('span').textContent = 'Sending…'; }, 0);
  });
</script>
@endsection
