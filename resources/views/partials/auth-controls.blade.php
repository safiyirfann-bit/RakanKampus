{{--
  Language menu + dark mode button for the guest pages (Sign Up, Forgot password steps,
  sign-up email check), same as on the Login page. Put it first inside the form sheet:
  top-right of the sheet on PC, top-left of the screen on phones.
  The language goes to the session (/lang/{locale}); the theme is remembered in this
  browser (rk_guest_theme) and applied as html[data-theme], which each page styles.
--}}
@php
  $acLocale = app()->getLocale();
  $acLangs = ['en' => 'English', 'ms' => 'Bahasa Melayu', 'zh' => '中文', 'ta' => 'தமிழ்'];
  $acShort = ['en' => 'EN', 'ms' => 'BM', 'zh' => '中文', 'ta' => 'தமிழ்'];
@endphp
@once
<style>
  .ac-ctl{position:absolute;top:18px;right:20px;z-index:5;display:flex;gap:8px}
  .ac-cb{height:36px;min-width:36px;padding:0 12px;border-radius:12px;border:1.5px solid #dbeeee;background:#f4fbfb;color:#14213d;
    display:inline-flex;align-items:center;justify-content:center;gap:6px;font:700 12.5px 'Plus Jakarta Sans',sans-serif;cursor:pointer;transition:border-color .15s}
  .ac-cb:hover{border-color:#2ec4c6}
  .ac-cb svg{width:16px;height:16px;flex:none}
  .ac-cb svg[hidden]{display:none}
  .ac-cb.ic{padding:0;width:36px}
  .ac-lang{position:relative}
  .ac-menu{display:none;position:absolute;top:42px;right:0;width:168px;padding:6px;border-radius:14px;background:#fff;box-shadow:0 16px 34px rgba(15,39,71,.2);z-index:6}
  .ac-menu.open{display:block;animation:acMenu .15s ease}
  @keyframes acMenu{from{opacity:0;transform:translateY(-4px)}}
  .ac-menu a{display:flex;justify-content:space-between;align-items:center;padding:9px 11px;border-radius:9px;font-size:13px;font-weight:600;color:#14213d;text-decoration:none}
  .ac-menu a:hover{background:#f1f5f9}
  .ac-menu a.on{background:#ecfdf9;color:#0f766e}
  @media (max-width:860px){
    .ac-ctl{position:fixed;top:max(12px,env(safe-area-inset-top));left:14px;right:auto}
    .ac-cb{background:rgba(255,255,255,.16);border-color:rgba(255,255,255,.35);color:#fff;-webkit-backdrop-filter:blur(8px);backdrop-filter:blur(8px)}
    .ac-menu{left:0;right:auto}
  }
  html[data-theme="dark"] .ac-cb{background:#0f1724;border-color:#24324a;color:#e2e8f0}
  html[data-theme="dark"] .ac-menu{background:#17202d;box-shadow:0 16px 34px rgba(0,0,0,.5)}
  html[data-theme="dark"] .ac-menu a{color:#dee1e9}
  html[data-theme="dark"] .ac-menu a:hover{background:#1f2a3a}
  html[data-theme="dark"] .ac-menu a.on{background:#1d3d3b;color:#41eedf}
  @media (max-width:860px){ html[data-theme="dark"] .ac-cb{background:rgba(255,255,255,.1);border-color:rgba(255,255,255,.25);color:#fff} }
  html[data-theme="dark"] input:-webkit-autofill{-webkit-text-fill-color:#e2e8f0;-webkit-box-shadow:0 0 0 40px #0f1724 inset}
</style>
@endonce
<div class="ac-ctl">
  <div class="ac-lang">
    <button type="button" class="ac-cb" id="acLangBtn" aria-haspopup="true" aria-expanded="false" aria-label="{{ __('Language') }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M3 12h18"/><path d="M12 3a14 14 0 0 1 0 18a14 14 0 0 1 0-18"/></svg>
      {{ $acShort[$acLocale] ?? 'EN' }}
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:12px;height:12px"><path d="m6 9 6 6 6-6"/></svg>
    </button>
    <div class="ac-menu" id="acLangMenu" role="menu">
      @foreach($acLangs as $code => $name)
        <a href="{{ route('guest.lang', $code) }}" lang="{{ $code }}" class="{{ $acLocale === $code ? 'on' : '' }}"><span>{{ $name }}</span> @if($acLocale === $code)<span>✓</span>@endif</a>
      @endforeach
    </div>
  </div>
  <button type="button" class="ac-cb ic" id="acTheme" aria-label="{{ __('Dark mode') }}">
    <svg class="moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8z"/></svg>
    <svg class="sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" hidden><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/></svg>
  </button>
</div>
<script>
(function () {
  var root = document.documentElement, btn = document.getElementById('acTheme');
  function apply(t) {
    root.setAttribute('data-theme', t);
    btn.querySelector('.moon').toggleAttribute('hidden', t === 'dark');
    btn.querySelector('.sun').toggleAttribute('hidden', t !== 'dark');
  }
  var saved = null; try { saved = localStorage.getItem('rk_guest_theme'); } catch (e) {}
  apply(saved === 'dark' ? 'dark' : 'light');
  btn.addEventListener('click', function () {
    var t = root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
    apply(t);
    try { localStorage.setItem('rk_guest_theme', t); } catch (e) {}
  });
  var lb = document.getElementById('acLangBtn'), menu = document.getElementById('acLangMenu');
  function setOpen(o) { menu.classList.toggle('open', o); lb.setAttribute('aria-expanded', o ? 'true' : 'false'); }
  lb.addEventListener('click', function (e) { e.stopPropagation(); setOpen(!menu.classList.contains('open')); });
  document.addEventListener('click', function (e) { if (!menu.contains(e.target)) setOpen(false); });
  document.addEventListener('keydown', function (e) { if (e.key === 'Escape') setOpen(false); });
})();
</script>
