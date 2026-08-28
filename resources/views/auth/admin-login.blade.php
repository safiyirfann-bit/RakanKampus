{{-- resources/views/auth/admin-login.blade.php --}}
<!DOCTYPE html>
<html lang="ms">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>RakanKampus Admin - Sign In</title>
<style>
  :root {
    --purple: #a78bfa;
    --pink: #f472b6;
    --blue: #60a5fa;
    --teal: #5eead4;
    --card-bg: rgba(255,255,255,0.10);
    --card-border: rgba(255,255,255,0.18);
    --field-bg: rgba(0,0,0,0.22);
    --field-border: rgba(255,255,255,0.32);
    --text-white: #ffffff;
    --text-muted: #e6d9ff;
    --text-dim: #c9b8ee;
    --text-placeholder: rgba(255,255,255,0.75);
    --error: #ff8a8a;
  }

  * { box-sizing: border-box; }

  html, body {
    height: 100%;
    margin: 0;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
  }

  body {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 24px;
    background: linear-gradient(120deg, #4c3d7a, #a78bfa, #60a5fa, #4c3d7a);
    background-size: 300% 300%;
    animation: gradientShift 15s ease infinite;
  }

  .card {
    width: 100%;
    max-width: 480px;
    background: var(--card-bg);
    border: 1px solid var(--card-border);
    border-radius: 20px;
    padding: 40px 44px;
    backdrop-filter: blur(10px);
    box-shadow: 0 20px 60px rgba(0,0,0,0.35);
  }

  .badge-wrap { display: flex; justify-content: center; margin-bottom: 24px; }

  .badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(46, 196, 198, 0.22);
    border: 1px solid rgba(94, 234, 212, 0.55);
    color: var(--teal);
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.06em;
    padding: 8px 16px;
    border-radius: 999px;
    text-shadow: 0 1px 3px rgba(0,0,0,0.4);
  }

  .badge .dot {
    width: 6px; height: 6px; border-radius: 50%; background: var(--teal);
  }

  .brand-row {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    margin-bottom: 6px;
  }

  .brand-icon {
    width: 44px; height: 44px;
    display: flex; align-items: center; justify-content: center;
  }

  h1 { color: var(--text-white); font-size: 22px; font-weight: 800; margin: 0; }

  .subtitle { text-align: center; color: var(--text-muted); font-size: 14px; margin: 0 0 28px; }

  label {
    display: block; color: var(--text-white); font-size: 12px; font-weight: 700;
    letter-spacing: 0.06em; text-transform: uppercase; margin-bottom: 8px;
  }

  .field { margin-bottom: 20px; }

  input {
    width: 100%; padding: 14px 16px; border-radius: 10px;
    border: 1px solid var(--field-border); background: var(--field-bg);
    color: var(--text-white); font-size: 15px; outline: none;
    transition: border-color 0.15s ease;
  }

  input.is-invalid { border-color: var(--error); }

  input::placeholder { color: var(--text-placeholder); }

  input:focus { border-color: var(--teal); }

  .error-text { color: var(--error); font-size: 12.5px; margin: 6px 2px 0; }

  .alert-error {
    background: rgba(255, 90, 90, 0.15);
    border: 1px solid rgba(255, 90, 90, 0.4);
    color: #ffb3b3;
    border-radius: 10px;
    padding: 12px 16px;
    font-size: 13.5px;
    margin-bottom: 20px;
  }

  .btn-signin {
    width: 100%;
    padding: 14px;
    border: none;
    border-radius: 10px;
    background: linear-gradient(120deg, var(--purple), var(--pink));
    color: #ffffff;
    font-size: 15px;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: filter 0.15s ease, transform 0.1s ease;
  }

  .btn-signin svg { width: 16px; height: 16px; }

  .btn-signin:hover { filter: brightness(1.08); }
  .btn-signin:active { transform: translateY(1px); }

  .divider {
    border-top: 1px solid var(--card-border);
    margin: 28px 0 20px;
  }

  .back-link {
    display: block;
    text-align: center;
    color: var(--teal);
    font-size: 13.5px;
    font-weight: 600;
    text-decoration: none;
    text-shadow: 0 1px 3px rgba(0,0,0,0.4);
  }

  .back-link:hover { text-decoration: underline; }

  footer {
    margin-top: 24px;
    color: var(--text-dim);
    font-size: 12.5px;
    text-align: center;
  }

  @media (max-width: 480px) {
    .card { padding: 32px 26px; }
  }

  @keyframes gradientShift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
  }
</style>
</head>
<body>

  <div class="card">
    <div class="badge-wrap">
      <span class="badge"><span class="dot"></span> ADMINISTRATOR PORTAL</span>
    </div>

    <div class="brand-row">
      <div class="brand-icon">
        <x-brand-logo size="44" />
      </div>
      <h1>RakanKampus</h1>
    </div>
    <p class="subtitle">Administrator Dashboard</p>

    @if ($errors->any())
      <div class="alert-error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('admin.login.post') }}">
      @csrf

      <div class="field">
  <label for="email">Email</label>
  <input
    type="email"
    id="email"
    name="email"
    class="@error('email') is-invalid @enderror"
    placeholder="admin@rakankampus.com"
    value="{{ old('email') }}"
    autocomplete="email"
    autofocus
  >
  @error('email')
    <p class="error-text">{{ $message }}</p>
  @enderror
</div>

      <div class="field">
        <label for="password">Password</label>
        <input
          type="password"
          id="password"
          name="password"
          class="@error('password') is-invalid @enderror"
          placeholder="••••••••"
          autocomplete="current-password"
        >
        @error('password')
          <p class="error-text">{{ $message }}</p>
        @enderror
      </div>

      <button type="submit" class="btn-signin">
        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 1a5 5 0 0 0-5 5v3H6a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-9a2 2 0 0 0-2-2h-1V6a5 5 0 0 0-5-5zm0 2a3 3 0 0 1 3 3v3H9V6a3 3 0 0 1 3-3z"/></svg>
        Sign In
      </button>
    </form>

    <div class="divider"></div>

    <a href="{{ route('login') }}" class="back-link">&larr; Back to student portal</a>
  </div>

  <footer>&copy; {{ date('Y') }} RakanKampus &middot; Restricted Access</footer>

</body>
</html>