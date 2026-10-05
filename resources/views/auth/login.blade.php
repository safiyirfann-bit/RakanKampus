@extends('layouts.app')

@section('content')

{{-- Intro animation (once a day, skippable) — not after a failed login or a redirect with a message --}}
@if (! $errors->any() && ! session('success') && ! session('status'))
    @include('partials.intro-splash')
@endif

{{--
  Student login. Top: the theme gradient with a small live chat demo (a question is
  asked, the robot "types", the answer appears, then the next example). Bottom: the
  form as a white glass sheet. Phones get it full screen; wider screens get it as a
  split card (demo on the left, form on the right).
--}}
<style>
  :root{
    --lg-purple:#0d9488; --lg-pink:#2ec4c6; --lg-blue:#2ec4c6; --lg-amber:#14213d;
    --lg-ink:#14213d; --lg-muted:#64748b; --lg-soft:#94a3b8;
    --lg-sheet:rgba(255,255,255,.9); --lg-field:#f0fafa; --lg-field-focus:#ffffff; --lg-link:#0d9488; --lg-link2:#0f766e;
  }
  *{box-sizing:border-box}
  html,body{height:100%;margin:0}
  body{font-family:'Plus Jakarta Sans', -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Helvetica,Arial,sans-serif;color:var(--lg-ink);
    background:linear-gradient(120deg,var(--lg-purple),var(--lg-pink),var(--lg-blue),var(--lg-purple));background-size:300% 300%;
    animation:lgShift 15s ease infinite;min-height:100vh;overflow-x:hidden}
  @keyframes lgShift{0%,100%{background-position:0% 50%}50%{background-position:100% 50%}}

  .lg-blob{position:fixed;border-radius:50%;filter:blur(50px);opacity:.55;pointer-events:none;z-index:0}
  .lg-b1{width:300px;height:300px;background:var(--lg-amber);top:-80px;left:-90px;animation:lgFloat 12s ease-in-out infinite alternate}
  .lg-b2{width:280px;height:280px;background:var(--lg-blue);top:30%;right:-110px;animation:lgFloat 15s ease-in-out infinite alternate-reverse}
  @media (max-width: 860px){
    .lg-blob{filter:blur(38px)}
    .lg-b1{width:62vw;height:62vw;top:-40px;left:-50px}
    .lg-b2{width:64vw;height:64vw;top:170px;right:-80px}
  }
  @keyframes lgFloat{to{transform:translate(40px,50px) scale(1.15)}}

  .lg{position:relative;z-index:1;min-height:100vh;display:flex;flex-direction:column}
  .lg-hero{flex:1;min-height:300px;display:flex;align-items:stretch;justify-content:center;padding:56px 22px 26px}

  /* ---- chat demo ---- */
  .lg-win{position:relative;width:100%;max-width:360px;align-self:stretch;display:flex;flex-direction:column;min-height:0;
    background:rgba(255,255,255,.16);border:1px solid rgba(255,255,255,.45);border-radius:24px;padding:12px 12px 12px;
    -webkit-backdrop-filter:blur(12px);backdrop-filter:blur(12px);box-shadow:0 18px 40px rgba(15,39,71,.2)}
  .lg-peek{position:absolute;right:-10px;top:-38px;width:64px;height:64px;filter:drop-shadow(0 8px 12px rgba(15,39,71,.35));animation:lgPeek 3s ease-in-out infinite;transform-origin:50% 100%}
  @keyframes lgPeek{0%,100%{transform:rotate(8deg)}50%{transform:rotate(-4deg) translateY(-5px)}}
  .lg-wh{display:flex;align-items:center;gap:6px;padding:2px 4px 10px;border-bottom:1px solid rgba(255,255,255,.3);margin-bottom:8px}
  .lg-wh i{width:9px;height:9px;border-radius:50%;background:rgba(255,255,255,.7)}
  .lg-wh b{color:#fff;font-size:14px;margin-left:6px}
  .lg-wh span{display:flex;align-items:center;gap:5px;color:rgba(255,255,255,.9);font-size:11.5px}
  .lg-wh span:before{content:"";width:7px;height:7px;border-radius:50%;background:#4ade80;box-shadow:0 0 0 3px rgba(74,222,128,.3)}
  .lg-msgs{flex:1;min-height:0;display:flex;flex-direction:column;justify-content:flex-end;gap:8px;overflow:hidden;padding:0 2px;
    -webkit-mask-image:linear-gradient(transparent,#000 18%);mask-image:linear-gradient(transparent,#000 18%)}
  .lg-bar{margin-top:10px;height:44px;border-radius:22px;background:rgba(255,255,255,.92);display:flex;align-items:center;padding:0 6px 0 16px;gap:8px;font-size:13.5px;color:#0f766e}
  .lg-t{flex:1;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
  .lg-t .ph{color:#94a3b8}
  .lg-t .caret{display:inline-block;width:1.5px;height:15px;background:#0d9488;vertical-align:middle;margin-left:1px;animation:lgBlink 1s steps(1) infinite}
  @keyframes lgBlink{50%{opacity:0}}
  .lg-send{width:34px;height:34px;border-radius:50%;display:grid;place-items:center;color:#fff;background:linear-gradient(135deg,var(--lg-purple),var(--lg-pink));transition:transform .15s}
  .lg-send.go{transform:scale(.85)}
  .lg-send svg{width:16px;height:16px}
  .lg-bub{flex:none;max-width:84%;padding:9px 13px;border-radius:18px;font-size:13.5px;line-height:1.4;opacity:0;transform:translateY(10px) scale(.96)}
  .lg-bub.u{align-self:flex-end;background:#fff;color:#0f766e;border-bottom-right-radius:6px;font-weight:600;box-shadow:0 6px 16px rgba(15,39,71,.15)}
  .lg-bub.b{align-self:flex-start;background:rgba(255,255,255,.22);color:#fff;border:1px solid rgba(255,255,255,.38);
    -webkit-backdrop-filter:blur(8px);backdrop-filter:blur(8px);border-bottom-left-radius:6px}
  .lg-typing{flex:none;align-self:flex-start;display:flex;gap:5px;padding:12px 14px;border-radius:18px;background:rgba(255,255,255,.22);opacity:0}
  .lg-typing i{width:7px;height:7px;border-radius:50%;background:#fff;animation:lgDot 1s infinite}
  .lg-typing i:nth-child(2){animation-delay:.15s}.lg-typing i:nth-child(3){animation-delay:.3s}
  .lg-in{animation:lgPop .35s ease forwards}
  .lg-out{transition:opacity .3s;opacity:0 !important}
  @keyframes lgDot{50%{transform:translateY(-4px);opacity:.5}}
  @keyframes lgPop{to{opacity:1;transform:none}}

  /* ---- form sheet ---- */
  .lg-sheet{position:relative;background:var(--lg-sheet);-webkit-backdrop-filter:blur(18px);backdrop-filter:blur(18px);
    border-radius:30px 30px 0 0;padding:28px 24px 22px;box-shadow:0 -12px 34px rgba(15,39,71,.18)}
  .lg-sheet h1{margin:0;font-weight:800;font-size:26px;letter-spacing:-.5px;color:var(--lg-ink)}
  .lg-sub{margin:4px 0 20px;font-size:14px;color:var(--lg-muted)}
  .lg-alert{padding:11px 14px;border-radius:14px;font-size:13.5px;margin-bottom:14px;line-height:1.45}
  .lg-alert.err{background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.35);color:#b91c1c}
  .lg-alert.ok{background:rgba(34,197,94,.1);border:1px solid rgba(34,197,94,.35);color:#15803d}
  .lg-alert.info{background:rgba(46,196,198,.14);border:1px solid rgba(46,196,198,.4);color:var(--lg-ink)}
  .lg-field{position:relative;display:flex;align-items:center;height:52px;border-radius:26px;background:var(--lg-field);
    border:1.5px solid transparent;padding:0 18px;gap:11px;margin-bottom:12px;transition:border-color .2s,box-shadow .2s,background .2s}
  .lg-field:focus-within{background:var(--lg-field-focus);border-color:var(--lg-purple);box-shadow:0 0 0 4px rgba(46,196,198,.2)}
  .lg-field svg{width:19px;height:19px;flex:none;color:var(--lg-soft)}
  .lg-field:focus-within svg{color:var(--lg-purple)}
  .lg-field input{flex:1;min-width:0;border:0;outline:0;background:transparent;font-size:15px;color:var(--lg-ink);font-family:inherit}
  .lg-field input::placeholder{color:var(--lg-soft)}
  .lg-eye{border:0;background:none;padding:4px;cursor:pointer;display:grid;place-items:center;color:var(--lg-soft)}
  .lg-row{display:flex;justify-content:flex-end;margin:2px 4px 16px}
  .lg-row a{font-size:13px;font-weight:700;color:var(--lg-link);text-decoration:none}
  .lg-btn{width:100%;height:52px;border:0;border-radius:26px;cursor:pointer;font-size:16px;font-weight:800;color:#fff;font-family:inherit;
    background:linear-gradient(90deg,var(--lg-purple),var(--lg-pink));box-shadow:0 10px 24px rgba(13,148,136,.35);transition:transform .15s,box-shadow .15s}
  .lg-btn:hover{transform:translateY(-1px);box-shadow:0 14px 28px rgba(13,148,136,.42)}
  .lg-btn:active{transform:translateY(1px)}
  .lg-foot{text-align:center;font-size:14px;color:var(--lg-muted);margin-top:18px}
  .lg-foot a{color:var(--lg-link2);font-weight:800;text-decoration:none}
  .lg-admin{display:flex;justify-content:center;align-items:center;gap:6px;margin-top:12px;font-size:13px;color:var(--lg-soft);text-decoration:none}
  .lg-admin:hover{color:var(--lg-link)}
  .lg-copy{text-align:center;font-size:11.5px;color:var(--lg-soft);margin-top:14px}

  /* ---- wider screens: split card ---- */
  @media (min-width: 861px){
    .lg{align-items:center;justify-content:center;padding:32px}
    .lg-card{display:flex;width:100%;max-width:960px;min-height:580px;border-radius:32px;overflow:hidden;
      background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.3);box-shadow:0 30px 70px rgba(15,39,71,.3)}
    .lg-hero{flex:1.05;padding:72px 48px 48px;align-items:center}
    .lg-win{max-width:400px;height:min(440px,100%)}
    .lg-sheet{flex:1;border-radius:0;padding:56px 52px 36px;display:flex;flex-direction:column;justify-content:center;box-shadow:none}
    .lg-sheet h1{font-size:30px}
  }
  /* big screens: a big card, not a small box in the middle */
  @media (min-width: 1100px){
    .lg-card{max-width:1080px;min-height:min(640px,86vh)}
    .lg-hero{padding:76px 56px 56px}
    .lg-win{max-width:420px;height:min(470px,100%)}
    .lg-bub{font-size:15px;padding:11px 15px}
    .lg-sheet{padding:56px 64px 36px}
    .lg-sheet h1{font-size:32px;letter-spacing:-.6px}
    .lg-sub{font-size:15px;margin:4px 0 24px}
    .lg-field{height:52px}
    .lg-field input{font-size:15px}
    .lg-btn{height:52px;font-size:16px}
  }
  @media (max-width: 860px){
    .lg-card{display:flex;flex-direction:column;min-height:100vh;min-height:100dvh}
    .lg-hero{flex:1 1 0;min-height:190px;padding:max(48px,env(safe-area-inset-top)) 20px 18px}
    .lg-win{max-width:440px;margin:0 auto}
    .lg-sheet{flex:none;padding:24px 22px 16px}
  }
  @media (max-width: 860px) and (max-height: 700px){
    .lg-sub{display:none}.lg-sheet h1{margin-bottom:14px;font-size:23px}
    .lg-copy{display:none}.lg-field{height:48px}.lg-btn{height:48px}
    .lg-foot{margin-top:12px}.lg-row{margin-bottom:12px}
    .lg-wh{padding-bottom:7px;margin-bottom:6px}.lg-bar{height:38px;margin-top:7px}.lg-send{width:30px;height:30px}
    .lg-bub{font-size:12.5px;padding:7px 11px}
  }
  @media (prefers-reduced-motion: reduce){ body,.lg-blob{animation:none} }

  /* ---- student theme (navy-teal), layout "B": phone = still navy→teal gradient like Reminders;
     PC = light mint page, white card, navy-teal left panel ---- */
  body{background:linear-gradient(160deg,#14213d,#1b3a5c 55%,#2ec4c6) fixed !important;animation:none !important}
  .lg-b1{background:rgba(127,245,236,.45) !important;opacity:.35 !important}
  .lg-b2{background:rgba(255,255,255,.5) !important;opacity:.25 !important}
  @media (min-width:861px){
    body{background:#eefafa !important}
    .lg-b1{background:#2ec4c6 !important;opacity:.28 !important;filter:blur(80px) !important}
    .lg-b2{background:#14213d !important;opacity:.14 !important;filter:blur(80px) !important}
    .lg-card{background:#fff !important;border:0 !important;box-shadow:0 30px 70px rgba(15,39,71,.16) !important}
    .lg-hero{background:linear-gradient(160deg,#14213d 0%,#1b3a5c 45%,#21768a 75%,#2ec4c6 100%) !important}
    .lg-sheet{background:#fff !important;-webkit-backdrop-filter:none !important;backdrop-filter:none !important}
  }

  /* ---- language + dark mode buttons (top-right of the form on PC, top-left of the page on phones) ---- */
  .lg-ctl{position:absolute;top:18px;right:20px;z-index:5;display:flex;gap:8px}
  .lg-cb{height:36px;min-width:36px;padding:0 12px;border-radius:12px;border:1.5px solid #dbeeee;background:#f4fbfb;color:var(--lg-ink);
    display:inline-flex;align-items:center;justify-content:center;gap:6px;font:700 12.5px 'Plus Jakarta Sans',sans-serif;cursor:pointer;transition:background .15s,border-color .15s}
  .lg-cb:hover{border-color:#2ec4c6}
  .lg-cb svg{width:16px;height:16px;flex:none}
  .lg-cb.ic{padding:0;width:36px}
  .lg-cb svg[hidden]{display:none}
  .lg-lang{position:relative}
  .lg-menu{display:none;position:absolute;top:42px;right:0;width:168px;padding:6px;border-radius:14px;background:#fff;box-shadow:0 16px 34px rgba(15,39,71,.2);z-index:6}
  .lg-menu.open{display:block;animation:lgMenu .15s ease}
  @keyframes lgMenu{from{opacity:0;transform:translateY(-4px)}}
  .lg-menu a{display:flex;justify-content:space-between;align-items:center;padding:9px 11px;border-radius:9px;font-size:13px;font-weight:600;color:#14213d;text-decoration:none}
  .lg-menu a:hover{background:#f1f5f9}
  .lg-menu a.on{background:#ecfdf9;color:#0f766e}
  .lg-sheet{position:relative}
  @media (max-width:860px){
    .lg-sheet{-webkit-backdrop-filter:none !important;backdrop-filter:none !important}
    .lg-ctl{position:fixed;top:max(12px,env(safe-area-inset-top));left:14px;right:auto}
    .lg-cb{background:rgba(255,255,255,.16);border-color:rgba(255,255,255,.35);color:#fff;-webkit-backdrop-filter:blur(8px);backdrop-filter:blur(8px)}
    .lg-menu{left:0;right:auto}
  }

  /* ---- dark mode (picked with the moon button; remembered in this browser) ---- */
  html[data-theme="dark"]{--lg-ink:#e2e8f0;--lg-muted:#94a3b8;--lg-soft:#64748b;--lg-field:#0f1724;--lg-field-focus:#0b1220;--lg-link:#41eedf;--lg-link2:#41eedf}
  html[data-theme="dark"] body{background:linear-gradient(160deg,#0b1424,#112031 55%,#1d6869) fixed !important}
  html[data-theme="dark"] .lg-sheet{background:#131c2b !important;box-shadow:0 -12px 34px rgba(0,0,0,.4)}
  html[data-theme="dark"] .lg-field{border-color:#24324a}
  html[data-theme="dark"] .lg-field:focus-within{border-color:#2ec4c6}
  html[data-theme="dark"] .lg-alert.err{background:rgba(239,68,68,.12);color:#fca5a5}
  html[data-theme="dark"] .lg-alert.ok{color:#86efac}
  html[data-theme="dark"] .lg-cb{background:#0f1724;border-color:#24324a;color:#e2e8f0}
  html[data-theme="dark"] .lg-menu{background:#17202d;box-shadow:0 16px 34px rgba(0,0,0,.5)}
  html[data-theme="dark"] .lg-menu a{color:#dee1e9}
  html[data-theme="dark"] .lg-menu a:hover{background:#1f2a3a}
  html[data-theme="dark"] .lg-menu a.on{background:#1d3d3b;color:#41eedf}
  html[data-theme="dark"] input:-webkit-autofill{-webkit-text-fill-color:#e2e8f0;-webkit-box-shadow:0 0 0 40px #0f1724 inset}
  @media (min-width:861px){
    html[data-theme="dark"] body{background:#0b1424 !important}
    html[data-theme="dark"] .lg-b1{opacity:.18 !important}
    html[data-theme="dark"] .lg-b2{background:#2ec4c6 !important;opacity:.08 !important}
    html[data-theme="dark"] .lg-card{background:#131c2b !important;box-shadow:0 30px 70px rgba(0,0,0,.5) !important}
    html[data-theme="dark"] .lg-hero{background:linear-gradient(160deg,#0b1424 0%,#112031 45%,#17505a 80%,#1d6869 100%) !important}
  }
  @media (max-width:860px){ html[data-theme="dark"] .lg-cb{background:rgba(255,255,255,.1);border-color:rgba(255,255,255,.25);color:#fff} }
</style>

<div class="lg-blob lg-b1"></div>
<div class="lg-blob lg-b2"></div>

<div class="lg">
 <div class="lg-card">

  <div class="lg-hero">
    <div class="lg-win" aria-hidden="true">
      <x-brand-logo size="64" class="lg-peek" />
      <div class="lg-wh"><i></i><i></i><i></i><b>RakanKampus</b><span>{{ __('Online') }}</span></div>
      <div class="lg-msgs" id="lgDemo"></div>
      <div class="lg-bar"><span class="lg-t" id="lgType"></span><span class="lg-send"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span></div>
    </div>
  </div>

  <div class="lg-sheet">
@php
  $lgLocale = app()->getLocale();
@endphp
  <div class="lg-ctl">
    <div class="lg-lang">
      <button type="button" class="lg-cb" id="lgLangBtn" aria-haspopup="true" aria-expanded="false" aria-label="{{ __('Language') }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M3 12h18"/><path d="M12 3a14 14 0 0 1 0 18a14 14 0 0 1 0-18"/></svg>
        {{ ['en' => 'EN', 'ms' => 'BM', 'zh' => '中文', 'ta' => 'தமிழ்'][$lgLocale] ?? 'EN' }}
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:12px;height:12px"><path d="m6 9 6 6 6-6"/></svg>
      </button>
      <div class="lg-menu" id="lgLangMenu" role="menu">
        <a href="{{ route('guest.lang', 'en') }}" class="{{ $lgLocale === 'en' ? 'on' : '' }}" lang="en"><span>English</span> @if($lgLocale === 'en')<span>✓</span>@endif</a>
        <a href="{{ route('guest.lang', 'ms') }}" class="{{ $lgLocale === 'ms' ? 'on' : '' }}" lang="ms"><span>Bahasa Melayu</span> @if($lgLocale === 'ms')<span>✓</span>@endif</a>
        <a href="{{ route('guest.lang', 'zh') }}" class="{{ $lgLocale === 'zh' ? 'on' : '' }}" lang="zh"><span>中文</span> @if($lgLocale === 'zh')<span>✓</span>@endif</a>
        <a href="{{ route('guest.lang', 'ta') }}" class="{{ $lgLocale === 'ta' ? 'on' : '' }}" lang="ta"><span>தமிழ்</span> @if($lgLocale === 'ta')<span>✓</span>@endif</a>
      </div>
    </div>
    <button type="button" class="lg-cb ic" id="lgTheme" aria-label="{{ __('Dark mode') }}">
      <svg class="moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8z"/></svg>
      <svg class="sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" hidden><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/></svg>
    </button>
  </div>
    <h1>{{ __('Welcome Back') }}</h1>
    <p class="lg-sub">{{ __('Log in to chat with your campus assistant.') }}</p>

    @if (session('success'))
      <div class="lg-alert ok">{{ session('success') }}</div>
    @endif
    @if (session('status'))
      <div class="lg-alert info">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
      <div class="lg-alert err">@foreach ($errors->all() as $error){{ $error }}<br>@endforeach</div>
    @endif

    <form method="POST" action="/login">
      @csrf
      <input type="hidden" name="theme_pick" id="lgThemePick" value="">
      <label class="lg-field">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="5" width="18" height="14" rx="3"/><path d="m3 7 9 6 9-6"/></svg>
        <input type="email" name="email" placeholder="{{ __('Email') }}" value="{{ old('email') }}" autocomplete="email" required>
      </label>
      <label class="lg-field">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="10" width="16" height="11" rx="3"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>
        <input type="password" name="password" id="lgPass" placeholder="{{ __('Password') }}" autocomplete="current-password" required>
        <button type="button" class="lg-eye" id="lgEye" aria-label="{{ __('Show password') }}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>
        </button>
      </label>

      <div class="lg-row"><a href="{{ route('password.request') }}">{{ __('Forgot password?') }}</a></div>

      <button type="submit" class="lg-btn">{{ __('Log In') }}</button>
    </form>

    <p class="lg-foot">{{ __("Don't have an account?") }} <a href="{{ route('register') }}">{{ __('Sign Up') }}</a></p>

    <a class="lg-admin" href="{{ route('admin.login') }}">
      <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2h-1V9a5 5 0 00-10 0v2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
      {{ __('Administrator login') }}
    </a>

    <p class="lg-copy">© {{ date('Y') }} RakanKampus · Politeknik Ungku Omar</p>
  </div>

 </div>
</div>

<script>
(function () {
  // dark mode: set before paint isn't possible from here, so apply right away; remembered in this browser
  var root = document.documentElement, btn = document.getElementById('lgTheme'), pick = document.getElementById('lgThemePick');
  function apply(t) {
    root.setAttribute('data-theme', t);
    btn.querySelector('.moon').toggleAttribute('hidden', t === 'dark');
    btn.querySelector('.sun').toggleAttribute('hidden', t !== 'dark');
  }
  var saved = null; try { saved = localStorage.getItem('rk_guest_theme'); } catch (e) {}
  apply(saved === 'dark' ? 'dark' : 'light');
  if (saved) pick.value = saved;
  btn.addEventListener('click', function () {
    var t = root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
    apply(t); pick.value = t;
    try { localStorage.setItem('rk_guest_theme', t); } catch (e) {}
  });
  // language menu
  var lb = document.getElementById('lgLangBtn'), menu = document.getElementById('lgLangMenu');
  function setOpen(o) { menu.classList.toggle('open', o); lb.setAttribute('aria-expanded', o ? 'true' : 'false'); }
  lb.addEventListener('click', function (e) { e.stopPropagation(); setOpen(!menu.classList.contains('open')); });
  document.addEventListener('click', function (e) { if (!menu.contains(e.target)) setOpen(false); });
  document.addEventListener('keydown', function (e) { if (e.key === 'Escape') setOpen(false); });
})();
</script>
@php
  $lgDemo = [
    [__('When is my DFP50463 class tomorrow?'), __('Tomorrow at 10:00 am in Lab 3, JTMK 📍')],
    [__('How do I pay my fees?'), __('You can pay online through iPayment 💳')],
    [__('Where is the surau?'), __('Next to the main hall — about a 3 minute walk 🕌')],
    [__('Remind me to submit my PBA'), __('Done! I\'ll remind you tomorrow at 8:00 am 🔔')],
  ];
@endphp
<script>
(function () {
  // show / hide password
  var eye = document.getElementById('lgEye'), pass = document.getElementById('lgPass');
  eye.addEventListener('click', function () {
    pass.type = pass.type === 'password' ? 'text' : 'password';
    eye.style.color = pass.type === 'text' ? 'var(--lg-purple)' : '';
  });

  // live chat demo
  var convo = @json($lgDemo);
  var box = document.getElementById('lgDemo'), bar = document.getElementById('lgType');
  var send = document.querySelector('.lg-send'), PH = @json(__('Ask anything…'));
  var still = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var wait = function (ms) { return new Promise(function (r) { setTimeout(r, ms); }); };
  function add(cls, text) {
    var d = document.createElement('div');
    d.className = cls;
    if (text) d.textContent = text; else d.innerHTML = '<i></i><i></i><i></i>';
    box.appendChild(d);
    while (box.children.length > 8) box.firstChild.remove();
    requestAnimationFrame(function () { d.classList.add('lg-in'); });
    return d;
  }
  function placeholder() { bar.innerHTML = '<span class="ph"></span>'; bar.firstChild.textContent = PH; }
  // start with two finished exchanges so the window already looks lived-in
  var seed = window.innerWidth > 860 ? 3 : 2;
  for (var k = 0; k < seed; k++) { add('lg-bub u', convo[k][0]); add('lg-bub b', convo[k][1]); }
  placeholder();
  if (still) return;
  (async function loop() {
    var i = seed;
    while (true) {
      var qa = convo[i++ % convo.length];
      await wait(1400);
      for (var n = 1; n <= qa[0].length; n++) {
        bar.textContent = qa[0].slice(0, n);
        bar.insertAdjacentHTML('beforeend', '<i class="caret"></i>');
        await wait(45);
      }
      await wait(350);
      send.classList.add('go'); setTimeout(function () { send.classList.remove('go'); }, 160);
      placeholder();
      add('lg-bub u', qa[0]);
      await wait(500);
      var t = add('lg-typing');
      await wait(1300);
      t.remove();
      add('lg-bub b', qa[1]);
      await wait(2400);
    }
  })();
})();
</script>

@endsection
