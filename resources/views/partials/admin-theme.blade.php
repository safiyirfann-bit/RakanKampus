{{--
  Shared look for every admin page ("Green Hero"): soft green page, a green hero
  banner with glass stat tiles (x-admin-hero), white rounded cards, green buttons.
  Included by partials/admin-nav. Admin is PC-only.
--}}
@once
@include('partials.rk-dialog')
<style>
  body { background: #f4f8f5 !important; min-width: 1100px; }
  .adm-wrap { padding: 22px 28px 40px; max-width: 1400px; }

  /* hero banner */
  .adm-hero {
    position: relative; overflow: hidden; border-radius: 26px; padding: 24px 24px 22px; margin-bottom: 20px; color: #fff;
    background: linear-gradient(135deg, #2f4f3a, #4a7856 55%, #6fa37e);
    box-shadow: 0 18px 40px rgba(47, 79, 58, .22);
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
  }
  .adm-hero::before { content: ''; position: absolute; width: 440px; height: 440px; border-radius: 50%; background: #f5c563; opacity: .22; filter: blur(70px); right: -120px; top: -200px; pointer-events: none; }
  .adm-hero::after { content: ''; position: absolute; width: 320px; height: 320px; border-radius: 50%; background: #a7f3d0; opacity: .16; filter: blur(70px); left: 20%; bottom: -220px; pointer-events: none; }
  .adm-hero > * { position: relative; z-index: 1; }
  .adm-hero-top { display: flex; align-items: center; gap: 16px; }
  .adm-hero-title { min-width: 0; }
  .adm-hero-title h1 { margin: 0; font-size: 25px; font-weight: 800; color: #fff; display: flex; align-items: center; gap: 10px; }
  .adm-hero-title p { margin: 4px 0 0; font-size: 13.5px; color: #d7e8da; }
  .adm-hero-back { width: 36px; height: 36px; border-radius: 12px; background: rgba(255,255,255,.16); border: 1px solid rgba(255,255,255,.3); color: #fff; display: grid; place-items: center; text-decoration: none; flex-shrink: 0; }
  .adm-hero-back:hover { background: rgba(255,255,255,.26); }
  .adm-hero-actions { margin-left: auto; display: flex; align-items: center; gap: 10px; flex-shrink: 0; }
  .adm-search { display: flex; align-items: center; gap: 8px; width: 300px; padding: 11px 14px; border-radius: 14px;
    background: rgba(255,255,255,.16); border: 1px solid rgba(255,255,255,.35); backdrop-filter: blur(14px); -webkit-backdrop-filter: blur(14px); color: #eef7f0; }
  .adm-search svg { width: 16px; height: 16px; flex-shrink: 0; }
  .adm-search input { border: 0; outline: 0; background: transparent; color: #fff; font-size: 13.5px; width: 100%; font-family: inherit; }
  .adm-search input::placeholder { color: rgba(238,247,240,.8); }
  .adm-btn-light { display: inline-flex; align-items: center; gap: 8px; padding: 11px 16px; border-radius: 14px; border: 0; background: #fff; color: #2f4f3a; font-weight: 800; font-size: 14px; cursor: pointer; text-decoration: none; white-space: nowrap; box-shadow: 0 8px 18px rgba(0,0,0,.12); font-family: inherit; }
  .adm-btn-light:hover { background: #ecfdf5; }
  .adm-btn-glass { display: inline-flex; align-items: center; gap: 6px; padding: 10px 14px; border-radius: 14px; border: 1px solid rgba(255,255,255,.35); background: rgba(255,255,255,.16); color: #fff; font-weight: 700; font-size: 13px; text-decoration: none; cursor: pointer; font-family: inherit; }
  .adm-btn-glass:hover { background: rgba(255,255,255,.26); }
  .adm-hero select { padding: 10px 12px; border-radius: 14px; border: 1px solid rgba(255,255,255,.35); background: rgba(255,255,255,.16); color: #fff; font-weight: 700; font-size: 13px; font-family: inherit; outline: 0; }
  .adm-hero select option { color: #1f2937; }

  /* glass stat tiles on the banner */
  .adm-kpis { display: grid; grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); gap: 14px; margin-top: 22px; }
  .adm-kpi { position: relative; overflow: hidden; border-radius: 18px; padding: 14px 16px; text-align: left; font-family: inherit;
    background: rgba(255,255,255,.14); border: 1px solid rgba(255,255,255,.3); backdrop-filter: blur(16px) saturate(140%); -webkit-backdrop-filter: blur(16px) saturate(140%); color: #fff; }
  a.adm-kpi, button.adm-kpi { cursor: pointer; text-decoration: none; transition: transform .15s, background .15s; }
  a.adm-kpi:hover, button.adm-kpi:hover { transform: translateY(-2px); background: rgba(255,255,255,.22); }
  .adm-kpi .ic { width: 36px; height: 36px; border-radius: 12px; background: rgba(255,255,255,.2); display: grid; place-items: center; margin-bottom: 10px; }
  .adm-kpi .ic svg { width: 18px; height: 18px; }
  .adm-kpi b { display: block; font-size: 26px; line-height: 1.05; font-weight: 800; }
  .adm-kpi span.lbl { display: block; font-size: 12.5px; font-weight: 600; color: #e6f2e8; margin-top: 3px; }
  .adm-kpi .tag { position: absolute; right: 12px; top: 14px; font-size: 11px; font-weight: 800; padding: 2px 8px; border-radius: 99px; background: #fff; color: #3f7a52; }
  .adm-kpi .tag.red { color: #dc2626; }
  .adm-kpi .tag.amber { color: #b45309; }
  .adm-kpi svg.spark { position: absolute; right: 10px; bottom: 10px; width: 84px; height: 28px; }

  /* cards */
  .adm-card { background: #fff; border: 1px solid #e3ece5; border-radius: 22px; padding: 18px 20px; box-shadow: 0 6px 18px rgba(47, 79, 58, .05); }
  .adm-card-h { display: flex; align-items: center; gap: 8px; margin: 0 0 14px; font-size: 15.5px; font-weight: 800; color: #1f2937; }
  .adm-card-h a { margin-left: auto; font-size: 12.5px; font-weight: 700; color: #3f7a52; text-decoration: none; }
  .adm-card-h a:hover { text-decoration: underline; }
  .adm-empty { text-align: center; color: #64748b; font-size: 13.5px; padding: 18px 8px; }
  .adm-empty b { display: block; color: #1f2937; margin-bottom: 4px; font-size: 14px; }

  /* page content right under the hero: line it up with the banner */
  .adm-wrap + .page, .adm-wrap + .container, .adm-wrap + .content {
    max-width: 1400px !important; margin: 0 !important; padding-top: 0 !important; padding-left: 28px !important; padding-right: 28px !important;
  }
  /* status flash */
  .adm-flash { background: #ecfdf5; border: 1px solid #bbf7d0; color: #166534; border-radius: 14px; padding: 11px 16px; font-size: 14px; font-weight: 600; margin-bottom: 16px; }
</style>
@endonce
