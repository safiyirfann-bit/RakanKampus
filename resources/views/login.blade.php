<!DOCTYPE html>
<html lang="ms">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>RakanKampus - Sign In</title>
<style>
  :root {
    --blue-deep: #1d3fd6;
    --blue-mid: #2f5bdb;
    --blue-light: #6f8ff0;
    --card-bg: rgba(255,255,255,0.08);
    --field-bg: rgba(255,255,255,0.14);
    --field-border: rgba(255,255,255,0.25);
    --text-white: #ffffff;
    --text-muted: #c9d5fb;
    --text-placeholder: rgba(255,255,255,0.55);
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
    background: radial-gradient(circle at 30% 20%, var(--blue-light) 0%, var(--blue-mid) 40%, var(--blue-deep) 100%);
  }

  .card {
    width: 100%;
    max-width: 560px;
    background: var(--card-bg);
    border: 1px solid rgba(255,255,255,0.15);
    border-radius: 24px;
    padding: 48px 56px;
    backdrop-filter: blur(6px);
    box-shadow: 0 20px 60px rgba(0,0,0,0.25);
  }

  .logo-wrap {
    display: flex;
    justify-content: center;
    margin-bottom: 24px;
  }

  .logo {
    width: 96px;
    height: 96px;
    border-radius: 22px;
    background: linear-gradient(145deg, rgba(255,255,255,0.25), rgba(255,255,255,0.05));
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: inset 0 0 0 1px rgba(255,255,255,0.2);
  }

  .logo-hex {
    width: 56px;
    height: 56px;
    background: linear-gradient(145deg, #4f6fe8, #3455d8);
    clip-path: polygon(25% 5%, 75% 5%, 100% 50%, 75% 95%, 25% 95%, 0% 50%);
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: inset 0 0 0 2px rgba(255,255,255,0.6);
  }

  .logo-hex span {
    color: #fff;
    font-weight: 700;
    font-size: 15px;
    letter-spacing: 0.5px;
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
    margin: 0 0 32px;
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
    border-color: rgba(255,255,255,0.55);
    background: rgba(255,255,255,0.18);
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
    background: #ffffff;
    color: var(--blue-deep);
    font-size: 16px;
    font-weight: 700;
    cursor: pointer;
    transition: transform 0.1s ease, box-shadow 0.15s ease;
  }

  .btn-signin:hover {
    box-shadow: 0 6px 18px rgba(0,0,0,0.2);
  }

  .btn-signin:active {
    transform: translateY(1px);
  }

  footer {
    margin-top: 24px;
    color: var(--text-muted);
    font-size: 13px;
    text-align: center;
  }

  @media (max-width: 480px) {
    .card {
      padding: 36px 28px;
    }
    h1 { font-size: 26px; }
  }
</style>
</head>
<body>

  <div class="card">
    <div class="logo-wrap">
      <div class="logo">
        <div class="logo-hex"><span>RK</span></div>
      </div>
    </div>

    <h1>RakanKampus</h1>
    <p class="subtitle">Your Polytechnic AI Assistant</p>
    <form method="POST" action="/login">
    @csrf

    <div class="field">
        <label>Email</label>
        <input type="email"
               name="email"
               placeholder="Enter your email"
               required>
    </div>

    <div class="field">
        <label>Password</label>
        <input type="password"
               name="password"
               placeholder="Enter your password"
               required>
    </div>

    <button type="submit" class="btn-signin">
        Sign In
    </button>

    <div style="text-align:center; margin-top:18px; color: var(--text-muted); font-size:14px;">
        Don’t have an account?
        <a href="{{ url('/register') }}"
           style="color: var(--text-white); font-weight:700; text-decoration:underline; margin-left:4px;">
            Create account
        </a>
    </div>

</form>
  </div>

  <footer>© 2026 RakanKampus · Politeknik Ungku Omar</footer>

</body>
</html>