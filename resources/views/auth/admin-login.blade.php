{{-- resources/views/auth/admin-login.blade.php
     Admin sign-in, same look as the admin dashboard (green + mint): a split card with a
     soft mint "control room" panel on the left and the form on the right. The numbers and
     bars on the left are decoration only — nothing real is shown before signing in. --}}
<!DOCTYPE html>
<html lang="en">
<head>
@include('partials.font')
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>RakanKampus Admin - Sign In</title>
<style>
  :root {
    --ink: #111c15; --ink2: #3d4a42; --mute: #7a847d; --line: #e4ebe6; --bg: #f4f7f5;
    --g900: #0f2a1c; --g800: #15502f; --g700: #1f6b43; --g600: #2f7a4f; --g500: #3fb070; --g100: #e7f5ec; --g50: #f2f9f4;
    --mint: #7ee0b0; --amber: #c7851e; --red: #d64545;
  }
  * { box-sizing: border-box; }
  html, body { height: 100%; margin: 0; }
  body {
    min-height: 100vh; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 32px;
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    color: var(--ink); background: var(--bg); position: relative; overflow-x: hidden; -webkit-font-smoothing: antialiased;
  }
  body::before, body::after { content: ""; position: fixed; border-radius: 50%; filter: blur(90px); pointer-events: none; z-index: 0; }
  body::before { width: 460px; height: 460px; top: -140px; left: -120px; background: rgba(126,224,176,.35); }
  body::after { width: 420px; height: 420px; bottom: -160px; right: -120px; background: rgba(63,176,112,.18); }

  .card {
    position: relative; z-index: 1; width: 100%; max-width: 1040px; min-height: min(600px, 86vh);
    display: grid; grid-template-columns: 1.1fr 1fr; border-radius: 30px; overflow: hidden;
    background: #fff; box-shadow: 0 30px 70px rgba(17,28,21,.12), 0 0 0 1px rgba(17,28,21,.04);
  }

  /* ---- left: mint control room ---- */
  .room { position: relative; overflow: hidden; padding: 44px 44px 40px; background: var(--g100); display: flex; flex-direction: column; justify-content: center; }
  .room::before { content: ""; position: absolute; inset: 0; pointer-events: none;
    background-image: linear-gradient(rgba(31,107,67,.07) 1px, transparent 1px), linear-gradient(90deg, rgba(31,107,67,.07) 1px, transparent 1px);
    background-size: 28px 28px; }
  .room > * { position: relative; }
  .tag { align-self: flex-start; display: inline-flex; align-items: center; gap: 8px; padding: 6px 12px; border-radius: 999px;
    border: 1px solid #9fd6b6; color: var(--g700); font-size: 11px; font-weight: 800; letter-spacing: .1em; background: rgba(255,255,255,.6); }
  .tag::before { content: ""; width: 7px; height: 7px; border-radius: 50%; background: var(--g500); box-shadow: 0 0 0 3px rgba(63,176,112,.2); animation: pulse 2s ease-in-out infinite; }
  @keyframes pulse { 50% { box-shadow: 0 0 0 6px rgba(63,176,112,0); } }
  .brand { display: flex; align-items: center; gap: 14px; margin: 22px 0 26px; }
  .brand h1 { margin: 0; font-size: 26px; font-weight: 800; letter-spacing: -.02em; color: var(--g900); }
  .brand p { margin: 2px 0 0; font-size: 13.5px; color: #5b7a66; }
  .stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-bottom: 14px; }
  .stat { background: #fff; border: 1px solid #d5e9dc; border-radius: 16px; padding: 14px 16px; }
  .stat b { display: block; font-size: 24px; font-weight: 800; letter-spacing: -.02em; color: var(--g900); }
  .stat span { font-size: 12px; color: #5b7a66; }
  .stat.warn b { color: var(--amber); }
  .chart { background: #fff; border: 1px solid #d5e9dc; border-radius: 16px; padding: 16px 16px 12px; height: 150px; display: flex; align-items: flex-end; gap: 10px; }
  .chart i { flex: 1; border-radius: 6px 6px 3px 3px; background: linear-gradient(180deg, var(--g500), rgba(126,224,176,.55)); transform-origin: bottom; animation: grow .8s cubic-bezier(.3,1.4,.5,1) both; }
  @keyframes grow { from { transform: scaleY(0); } }
  .note { margin: 14px 2px 0; font-size: 12px; color: #5b7a66; display: flex; align-items: center; gap: 6px; }
  .note svg { width: 14px; height: 14px; }

  /* ---- right: form ---- */
  .form { padding: 56px 56px 40px; display: flex; flex-direction: column; justify-content: center; }
  .form h2 { margin: 0; font-size: 28px; font-weight: 800; letter-spacing: -.02em; }
  .form .sub { margin: 6px 0 28px; font-size: 14px; color: var(--mute); }
  label { display: block; font-size: 11.5px; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; color: var(--g700); margin: 0 0 8px 2px; }
  .field { margin-bottom: 18px; }
  .input { display: flex; align-items: center; gap: 10px; height: 52px; padding: 0 16px; border-radius: 14px; background: var(--g50);
    border: 1.5px solid var(--line); transition: border-color .15s, box-shadow .15s, background .15s; }
  .input:focus-within { background: #fff; border-color: var(--g500); box-shadow: 0 0 0 4px rgba(63,176,112,.15); }
  .input.is-invalid { border-color: var(--red); }
  .input svg { width: 18px; height: 18px; flex: none; color: #9aa39c; }
  .input:focus-within svg { color: var(--g600); }
  .input input { flex: 1; min-width: 0; border: 0; outline: 0; background: transparent; font: 600 15px 'Plus Jakarta Sans', sans-serif; color: var(--ink); }
  .input input::placeholder { color: #a3ada6; font-weight: 500; }
  .eye { border: 0; background: none; padding: 4px; cursor: pointer; color: #9aa39c; display: grid; place-items: center; }
  .eye:hover { color: var(--g700); }
  .error-text { color: var(--red); font-size: 12.5px; margin: 6px 2px 0; }
  .alert-error { background: #fdecec; border: 1px solid #f5c2c2; color: #a12f2f; border-radius: 12px; padding: 12px 16px; font-size: 13.5px; margin-bottom: 20px; }
  .btn-signin { width: 100%; height: 52px; margin-top: 6px; border: 0; border-radius: 14px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px;
    background: var(--mint); color: var(--g900); font: 800 15.5px 'Plus Jakarta Sans', sans-serif; box-shadow: 0 10px 22px rgba(63,176,112,.25); transition: transform .12s, box-shadow .15s, filter .15s; }
  .btn-signin svg { width: 16px; height: 16px; }
  .btn-signin:hover { filter: brightness(1.04); box-shadow: 0 12px 28px rgba(63,176,112,.35); }
  .btn-signin:active { transform: translateY(1px); }
  .back-link { display: inline-flex; align-self: center; align-items: center; gap: 6px; margin-top: 22px; color: var(--g700); font-size: 13.5px; font-weight: 700; text-decoration: none; }
  .back-link:hover { text-decoration: underline; }
  footer { position: relative; z-index: 1; margin-top: 22px; color: var(--mute); font-size: 12.5px; text-align: center; }

  @media (max-width: 900px) {
    .card { grid-template-columns: 1fr; max-width: 480px; min-height: 0; }
    .room { display: none; }
    .form { padding: 40px 30px 32px; }
  }
  @media (prefers-reduced-motion: reduce) { .chart i, .tag::before { animation: none; } }
</style>
</head>
<body>

  <div class="card">
    <aside class="room" aria-hidden="true">
      <span class="tag">ADMINISTRATOR PORTAL</span>
      <div class="brand">
        <x-brand-logo size="54" />
        <div>
          <h1>RakanKampus</h1>
          <p>Admin control room</p>
        </div>
      </div>
      <div class="stats">
        <div class="stat"><b>1,284</b><span>Questions</span></div>
        <div class="stat warn"><b>12</b><span>Unanswered</span></div>
        <div class="stat"><b>356</b><span>Students</span></div>
      </div>
      <div class="chart">
        @foreach([38, 55, 46, 70, 62, 84, 78, 96, 68, 88] as $i => $h)
          <i style="height: {{ $h }}%; animation-delay: {{ $i * 0.05 }}s"></i>
        @endforeach
      </div>
      <p class="note">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-3.5 8-10V5l-8-3-8 3v7c0 6.5 8 10 8 10z"/></svg>
        Manage the knowledge base, inbox and analytics.
      </p>
    </aside>

    <main class="form">
      <h2>Admin sign in</h2>
      <p class="sub">Restricted access · staff only</p>

      @if ($errors->any() && ! $errors->has('email') && ! $errors->has('password'))
        <div class="alert-error">{{ $errors->first() }}</div>
      @endif

      <form method="POST" action="{{ route('admin.login.post') }}">
        @csrf

        <div class="field">
          <label for="email">Email</label>
          <div class="input @error('email') is-invalid @enderror">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>
            <input type="email" id="email" name="email" placeholder="admin@rakankampus.com" value="{{ old('email') }}" autocomplete="email" autofocus>
          </div>
          @error('email')
            <p class="error-text">{{ $message }}</p>
          @enderror
        </div>

        <div class="field">
          <label for="password">Password</label>
          <div class="input @error('password') is-invalid @enderror">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="11" width="16" height="10" rx="2.5"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg>
            <input type="password" id="password" name="password" placeholder="••••••••" autocomplete="current-password">
            <button type="button" class="eye" id="pwEye" aria-label="Show password">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>
            </button>
          </div>
          @error('password')
            <p class="error-text">{{ $message }}</p>
          @enderror
        </div>

        <button type="submit" class="btn-signin">
          <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 1a5 5 0 0 0-5 5v3H6a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-9a2 2 0 0 0-2-2h-1V6a5 5 0 0 0-5-5zm0 2a3 3 0 0 1 3 3v3H9V6a3 3 0 0 1 3-3z"/></svg>
          Sign In
        </button>
      </form>

      <a href="{{ route('login') }}" class="back-link">&larr; Back to student portal</a>
    </main>
  </div>

  <footer>&copy; {{ date('Y') }} RakanKampus &middot; Restricted Access</footer>

<script>
  (function () {
    var eye = document.getElementById('pwEye'), pw = document.getElementById('password');
    if (!eye || !pw) return;
    eye.addEventListener('click', function () {
      var show = pw.type === 'password';
      pw.type = show ? 'text' : 'password';
      eye.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
    });
  })();
</script>
</body>
</html>
