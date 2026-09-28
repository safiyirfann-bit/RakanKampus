<link rel="manifest" href="/manifest.json">
<meta name="theme-color" content="#0f2747">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-title" content="RakanKampus">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<link rel="apple-touch-icon" href="/images/icons/apple-touch-icon.png">
{{-- UI translations for JavaScript. Same JSON files as Blade's __() (lang/ms.json, zh.json, ta.json);
     English is the key itself. Use t('Text') or t('Deleted :count items', {count: 3}) in page scripts. --}}
@php($__uiLocale = app()->getLocale())
<script>
window.APP_LOCALE = @json($__uiLocale);
window.I18N = @json(($__uiLocale !== 'en' && is_file(lang_path($__uiLocale . '.json'))) ? json_decode(file_get_contents(lang_path($__uiLocale . '.json')), true) : (object) []);
function t(key, params) {
  let s = (window.I18N && window.I18N[key]) || key;
  if (params) for (const k in params) s = s.split(':' + k).join(params[k]);
  return s;
}
</script>
{{-- Colour theme. Students choose Light / Dark / Use device setting in Profile → Appearance
     (users.theme, default "system"). Set before the page paints so there's no white flash.
     Guests and admins always get light — only student pages have dark styles. --}}
@php($__themePref = (auth()->check() && ! auth()->user()->isAdmin()) ? (auth()->user()->theme ?? 'system') : 'light')
<script>
(function () {
  var pref = @json($__themePref);
  var mq = window.matchMedia ? window.matchMedia('(prefers-color-scheme: dark)') : null;
  function apply(p) {
    if (p) pref = p;
    var dark = pref === 'dark' || (pref === 'system' && mq && mq.matches);
    document.documentElement.setAttribute('data-theme', dark ? 'dark' : 'light');
    var meta = document.querySelector('meta[name="theme-color"]');
    if (meta) meta.setAttribute('content', dark ? '#070d16' : '#0f2747');
  }
  apply();
  if (mq && mq.addEventListener) mq.addEventListener('change', function () { apply(); });
  window.rkApplyTheme = apply;
})();
</script>
@include('partials.dark-tailwind')
@include('partials.dark-fixes')
