{{--
  Faster page changes. When a finger touches a link (phone / app) or the mouse rests on it
  (PC), the next page starts downloading right away instead of only after the tap is let go,
  so by the time the browser navigates the page is usually already here.
  Only same-site page links; skips logout, downloads, new tabs, # links and forms.
  Included by partials/pwa-head (student + guest pages) and partials/admin-nav (admin).
--}}
@once
<script>
(function () {
  var link = document.createElement('link');
  if (!(link.relList && link.relList.supports && link.relList.supports('prefetch'))) return;
  var done = {}, timer = null;
  function target(el) {
    var a = el && el.closest ? el.closest('a[href]') : null;
    if (!a || a.target === '_blank' || a.hasAttribute('download') || a.dataset.noPrefetch !== undefined) return null;
    var u;
    try { u = new URL(a.href, location.href); } catch (e) { return null; }
    if (u.origin !== location.origin || !/^https?:$/.test(u.protocol)) return null;
    if (u.pathname === location.pathname && u.search === location.search) return null;
    if (/logout|\/lang\/|\.(pdf|png|jpe?g|zip|csv|xlsx?)$/i.test(u.pathname)) return null;
    u.hash = '';
    return u.href;
  }
  function prefetch(href) {
    if (!href || done[href]) return;
    done[href] = 1;
    var l = document.createElement('link');
    l.rel = 'prefetch'; l.href = href; l.as = 'document';
    document.head.appendChild(l);
  }
  document.addEventListener('touchstart', function (e) { prefetch(target(e.target)); }, { passive: true, capture: true });
  document.addEventListener('mousedown', function (e) { prefetch(target(e.target)); }, true);
  document.addEventListener('mouseover', function (e) {
    var h = target(e.target);
    clearTimeout(timer);
    if (h) timer = setTimeout(function () { prefetch(h); }, 65);
  }, true);
  document.addEventListener('mouseout', function () { clearTimeout(timer); }, true);
})();
</script>
@endonce
