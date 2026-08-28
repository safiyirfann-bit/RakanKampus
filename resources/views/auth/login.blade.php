@extends('layouts.app')

@section('content')

<style>
  :root {
    --purple: #a78bfa;
    --pink: #f472b6;
    --blue: #60a5fa;
    --amber: #f59e0b;
    --card-bg: rgba(255,255,255,0.10);
    --field-bg: rgba(255,255,255,0.14);
    --field-border: rgba(255,255,255,0.28);
    --text-white: #ffffff;
    --text-muted: #e6d9ff;
    --text-placeholder: rgba(255,255,255,0.55);
  }

  * { box-sizing: border-box; }

  html, body {
    height: 100%;
    margin: 0;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
  }

  body {
    position: relative;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 24px;
    overflow: hidden;
    background: linear-gradient(120deg, #a78bfa, #f472b6, #60a5fa, #a78bfa);
    background-size: 300% 300%;
    animation: gradientShift 15s ease infinite;
  }

  /* Floating glow blobs */
  .blob {
    position: absolute;
    border-radius: 50%;
    filter: blur(50px);
    opacity: 0.45;
    pointer-events: none;
    z-index: 0;
  }

  .blob-1 {
    width: 320px; height: 320px;
    background: var(--amber);
    top: -60px; left: -80px;
    animation: floatA 14s ease-in-out infinite;
  }

  .blob-2 {
    width: 260px; height: 260px;
    background: var(--blue);
    bottom: -60px; right: -60px;
    animation: floatB 18s ease-in-out infinite;
  }

  .blob-3 {
    width: 200px; height: 200px;
    background: #ffffff;
    top: 40%; right: 8%;
    opacity: 0.18;
    animation: floatC 12s ease-in-out infinite;
  }

  @keyframes floatA {
    0%, 100% { transform: translate(0, 0) scale(1); }
    50%      { transform: translate(40px, 60px) scale(1.15); }
  }

  @keyframes floatB {
    0%, 100% { transform: translate(0, 0) scale(1); }
    50%      { transform: translate(-30px, -40px) scale(1.1); }
  }

  @keyframes floatC {
    0%, 100% { transform: translate(0, 0); }
    50%      { transform: translate(-20px, 30px); }
  }

  /* AI chatbot mascot */
  .mascot-wrap {
    position: relative;
    z-index: 1;
    display: flex;
    justify-content: center;
    margin-bottom: 8px;
    animation: bob 4s ease-in-out infinite;
  }

  @keyframes bob {
    0%, 100% { transform: translateY(0); }
    50%      { transform: translateY(-10px); }
  }

  .mascot {
    width: 110px;
    height: 110px;
    filter: drop-shadow(0 10px 20px rgba(0,0,0,0.25));
  }

  .mascot-bubble {
    position: absolute;
    top: -6px;
    right: calc(50% - 76px);
    background: #ffffff;
    border-radius: 14px;
    padding: 6px 10px;
    display: flex;
    align-items: center;
    gap: 4px;
    box-shadow: 0 6px 16px rgba(0,0,0,0.2);
  }

  .mascot-bubble span {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: var(--purple);
    display: inline-block;
    animation: typingDots 1.2s infinite ease-in-out;
  }

  .mascot-bubble span:nth-child(2) { animation-delay: 0.15s; }
  .mascot-bubble span:nth-child(3) { animation-delay: 0.3s; }

  @keyframes typingDots {
    0%, 60%, 100% { transform: translateY(0); opacity: 0.6; }
    30%           { transform: translateY(-4px); opacity: 1; }
  }

  .card {
    position: relative;
    z-index: 1;
    width: 100%;
    max-width: 560px;
    background: var(--card-bg);
    border: 1px solid rgba(255,255,255,0.18);
    border-radius: 24px;
    padding: 28px 56px 48px;
    backdrop-filter: blur(10px);
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
  }

  h1 {
    text-align: center;
    color: var(--text-white);
    font-size: 30px;
    font-weight: 800;
    margin: 0 0 8px;
  }

  .subtitle {
    text-align: center;
    color: var(--text-muted);
    font-size: 15px;
    margin: 0 0 28px;
  }

  label {
    display: block;
    color: var(--text-white);
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    margin-bottom: 8px;
  }

  .field {
    margin-bottom: 20px;
  }

  input {
    width: 100%;
    padding: 14px 16px;
    border-radius: 12px;
    border: 1px solid var(--field-border);
    background: var(--field-bg);
    color: var(--text-white);
    font-size: 15px;
    outline: none;
    transition: border-color 0.15s ease, background 0.15s ease;
  }

  input::placeholder {
    color: var(--text-placeholder);
  }

  input:focus {
    border-color: rgba(255,255,255,0.6);
    background: rgba(255,255,255,0.2);
  }

  .forgot-row {
    text-align: right;
    margin-bottom: 24px;
  }

  .forgot-row a {
    color: var(--text-white);
    font-size: 13px;
    text-decoration: none;
  }

  .forgot-row a:hover {
    text-decoration: underline;
  }

  .btn-signin {
    width: 100%;
    padding: 15px;
    border: none;
    border-radius: 12px;
    background: linear-gradient(120deg, var(--purple), var(--pink));
    color: #ffffff;
    font-size: 16px;
    font-weight: 700;
    cursor: pointer;
    transition: transform 0.1s ease, box-shadow 0.15s ease;
  }

  .btn-signin:hover {
    box-shadow: 0 6px 18px rgba(0,0,0,0.3);
  }

  .btn-signin:active {
    transform: translateY(1px);
  }

  footer {
    position: relative;
    z-index: 1;
    margin-top: 24px;
    color: var(--text-muted);
    font-size: 13px;
    text-align: center;
  }

  @media (max-width: 480px) {
    .card {
      padding: 32px 28px 40px;
    }
    h1 { font-size: 26px; }
    .mascot { width: 90px; height: 90px; }
  }

  @keyframes gradientShift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
  }
</style>

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
  <p class="subtitle">Your University AI Assistant</p>

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

