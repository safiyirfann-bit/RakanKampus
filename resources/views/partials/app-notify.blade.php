{{--
  RakanKampus Android app only (window.RKAppNotify comes from the app).
  WebView can't receive Web Push, so the app schedules reminder / class alerts on the
  phone itself. This keeps the app's list in step with the website:
  - every page load and whenever the app comes back to the front
  - shortly after anything is saved (any POST/PUT/DELETE made with fetch)
  - on guest pages (logged out) the list is cleared, so nobody else gets your alerts.
  Included by partials/pwa-head.
--}}
@once
<script>
(function () {
  var app = window.RKAppNotify;
  if (!app) return;
  @guest
    try { app.sync('[]'); } catch (e) {}
  @else
    var url = @json(route('app.notifications', [], false)), timer = null, busy = false;
    function sync() {
      if (busy) return;
      busy = true;
      origFetch(url, { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' })
        .then(function (r) { return r.ok && /json/.test(r.headers.get('Content-Type') || '') ? r.json() : null; })
        .then(function (d) { if (d && d.items) app.sync(JSON.stringify(d.items)); })
        .catch(function () {})
        .then(function () { busy = false; });
    }
    function later() { clearTimeout(timer); timer = setTimeout(sync, 900); }
    var origFetch = window.fetch.bind(window);
    window.fetch = function (input, init) {
      var p = origFetch(input, init);
      var m = ((init && init.method) || (input && input.method) || 'GET').toUpperCase();
      if (m !== 'GET' && m !== 'HEAD') p.then(later, function () {});
      return p;
    };
    document.addEventListener('visibilitychange', function () { if (document.visibilityState === 'visible') later(); });
    if (document.readyState === 'complete') later(); else window.addEventListener('load', later);
  @endguest
})();
</script>
@endonce
