@extends('layouts.app')

@section('content')

{{-- Intro animation (once a day, skippable) — not after a failed login or a redirect with a message --}}
@if (! $errors->any() && ! session('success') && ! session('status'))
    @include('partials.intro-splash')
@endif

@include('partials.auth-style')

<div class="blob blob-1"></div>
<div class="blob blob-2"></div>
<div class="blob blob-3"></div>

<div class="card">

  <div class="mascot-wrap">
    <x-brand-logo size="110" class="mascot" />
    <div class="mascot-bubble">
      <span></span><span></span><span></span>
    </div>
  </div>

  <h1>RakanKampus</h1>
  <p class="subtitle">Your Politeknik AI Assistant</p>

  @if(session('success'))
  <div style="background: rgba(34,197,94,0.15); border: 1px solid rgba(34,197,94,0.4); color: #fff; padding: 14px 18px; border-radius: 14px; margin-bottom: 20px; text-align: center; font-size: 14px;">
        {{ session('success') }}
  </div>
  @endif

  @if(session('status'))
  <div style="background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.3); color: #fff; padding: 14px 18px; border-radius: 14px; margin-bottom: 20px; text-align: center; font-size: 14px;">
        {{ session('status') }}
  </div>
  @endif

  @if ($errors->any())
  <div style="background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.4); color: #fff; padding: 14px 18px; border-radius: 14px; margin-bottom: 20px; text-align: center; font-size: 14px;">
    @foreach ($errors->all() as $error)
      {{ $error }}
    @endforeach
  </div>
  @endif

  <form method="POST" action="/login">
      @csrf

      <div class="field">
          <label>Email</label>
          <input
              type="email"
              name="email"
              placeholder="Enter your email"
              value="{{ old('email') }}"
              required
          >
      </div>

      <div class="field">
          <label>Password</label>
          <input
              type="password"
              name="password"
              placeholder="Enter your password"
              required
          >
      </div>

      <div class="forgot-row">
          <a href="{{ route('password.request') }}">Forgot password?</a>
      </div>

      <button type="submit" class="btn-signin">
          Sign In
      </button>
  </form>

  <!-- Register + Admin Link -->
  <div style="text-align:center; margin-top:22px;">

      <p style="color: var(--text-muted); font-size:14px; margin-bottom:16px;">
          Don't have an account?
          <a href="{{ route('register') }}"
             style="color: var(--text-white); font-weight:700; text-decoration:underline;">
              Register here
          </a>
      </p>

      <a href="{{ route('admin.login') }}"
         style="color: var(--text-muted); font-size:14px; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">

          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2h-1V9a5 5 0 00-10 0v2H6a2 2 0 00-2 2v6a2 2 0 002 2z" />
          </svg>

          Administrator login
      </a>

  </div>

</div>

<footer>© 2026 RakanKampus · Politeknik Ungku Omar</footer>

@endsection

