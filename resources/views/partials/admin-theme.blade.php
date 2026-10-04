{{--
  Shared look for every admin page (PC only), "Bento Premium": Plus Jakarta Sans, warm
  off-white page, big rounded white tiles, one deep-green hero, near-black + mint accents,
  pill buttons. The base rules come first; the "Bento Premium" block at the end refines them.
  Included once by partials/admin-nav.
--}}
@once
@include('partials.font')
@include('partials.rk-dialog')
<style>
  :root {
    --a-g900: #1f3a2b; --a-g800: #2f4f3a; --a-g700: #3f6e4f; --a-g600: #4a7856; --a-g500: #5f9370; --a-g100: #e8f2eb; --a-g50: #f3f8f4;
    --a-ink: #16241c; --a-ink2: #46574d; --a-mute: #7b8b81; --a-line: #e4ebe6; --a-bg: #f4f7f5; --a-amber: #c7851e; --a-red: #d64545; --a-blue: #3c6fb0; --a-violet: #7a55c7;
    --a-font: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
  }
  body { background: var(--a-bg) !important; color: var(--a-ink); font-family: var(--a-font) !important; min-width: 1180px; -webkit-font-smoothing: antialiased; }
  body button, body input, body select, body textarea { font-family: var(--a-font); }
  .adm-wrap { padding: 26px 32px 44px; max-width: 1480px; }

  /* ---------- hero banner ---------- */
  .adm-hero { position: relative; overflow: hidden; border-radius: 24px; padding: 26px 28px; margin-bottom: 22px; color: #fff;
    background: linear-gradient(120deg, #23412f 0%, #35603f 45%, #5b8f68 100%); display: flex; gap: 24px; align-items: center; flex-wrap: wrap; }
  .adm-hero::before { content: ''; position: absolute; inset: 0; pointer-events: none;
    background: radial-gradient(600px 220px at 85% -20%, rgba(245,197,99,.28), transparent 60%), radial-gradient(500px 240px at 10% 130%, rgba(167,243,208,.22), transparent 60%); }
  .adm-hero::after { content: ''; position: absolute; inset: 0; pointer-events: none;
    background-image: radial-gradient(rgba(255,255,255,.08) 1px, transparent 1px); background-size: 18px 18px;
    -webkit-mask-image: linear-gradient(90deg, transparent, #000 60%); mask-image: linear-gradient(90deg, transparent, #000 60%); }
  .adm-hero > * { position: relative; z-index: 1; }
  .adm-hero-text { min-width: 0; flex: 1 1 360px; }
  .adm-hero-text small { font-size: 12.5px; font-weight: 600; color: #cfe3d4; letter-spacing: .02em; display: flex; align-items: center; gap: 8px; }
  .adm-hero-text small a { color: #cfe3d4; text-decoration: none; } .adm-hero-text small a:hover { color: #fff; text-decoration: underline; }
  .adm-hero-text h1 { margin: 4px 0 6px; font-size: 26px; font-weight: 800; letter-spacing: -.02em; line-height: 1.2; color: #fff; }
  .adm-hero-text p { margin: 0; font-size: 13.5px; color: #d6e7da; max-width: 560px; line-height: 1.55; }
  .adm-hero-acts { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 16px; align-items: center; }
  .adm-hstats { display: grid; grid-template-columns: repeat(var(--n, 4), 152px); gap: 12px; margin-left: auto; }
  .adm-hero.wide .adm-hstats { grid-template-columns: repeat(var(--n, 5), minmax(0, 1fr)); width: 100%; margin-left: 0; }
  .adm-hs { position: relative; overflow: hidden; border-radius: 18px; padding: 14px 16px; text-align: left; color: #fff; text-decoration: none; font-family: inherit;
    background: rgba(255,255,255,.12); border: 1px solid rgba(255,255,255,.22); backdrop-filter: blur(14px); -webkit-backdrop-filter: blur(14px); }
  a.adm-hs, button.adm-hs { cursor: pointer; transition: background .15s, transform .15s; }
  a.adm-hs:hover, button.adm-hs:hover { background: rgba(255,255,255,.2); transform: translateY(-2px); }
  .adm-hs span { font-size: 12px; color: #d6e7da; font-weight: 600; display: block; }
  .adm-hs b.sm { font-size: 20px; margin: 9px 0 5px; }
  .adm-hs b { font-size: 26px; font-weight: 800; display: block; margin: 6px 0 2px; letter-spacing: -.02em; line-height: 1.1; }
  .adm-hs em { font-style: normal; font-size: 11.5px; font-weight: 700; color: #bff0cd; }
  .adm-hs em.red { color: #ffd0d0; }
  .adm-hs svg.spark { position: absolute; right: 10px; bottom: 10px; width: 70px; height: 24px; opacity: .9; }

  /* ---------- buttons ---------- */
  .adm-btn { display: inline-flex; align-items: center; gap: 8px; height: 40px; padding: 0 16px; border-radius: 12px; border: 0; cursor: pointer;
    font-weight: 700; font-size: 13.5px; text-decoration: none; white-space: nowrap; transition: background .15s, box-shadow .15s, transform .1s; font-family: inherit; }
  .adm-btn:active { transform: translateY(1px); }
  .adm-btn svg { width: 16px; height: 16px; }
  .adm-btn.w { background: #fff; color: var(--a-g800); box-shadow: 0 6px 16px rgba(0,0,0,.12); } .adm-btn.w:hover { background: #f0f7f2; }
  .adm-btn.gl { background: rgba(255,255,255,.14); border: 1px solid rgba(255,255,255,.28); color: #fff; backdrop-filter: blur(10px); } .adm-btn.gl:hover { background: rgba(255,255,255,.24); }
  .adm-btn.g { background: var(--a-g700); color: #fff; box-shadow: 0 6px 14px rgba(63,110,79,.22); } .adm-btn.g:hover { background: var(--a-g800); }
  .adm-btn.o { background: #fff; color: var(--a-ink2); border: 1px solid var(--a-line); } .adm-btn.o:hover { background: var(--a-g50); }
  .adm-btn.danger { background: #fff; color: var(--a-red); border: 1px solid #f3d0d0; } .adm-btn.danger:hover { background: #fdecec; }
  .adm-btn.sm { height: 34px; padding: 0 12px; font-size: 12.5px; border-radius: 10px; }
  .adm-ab { width: 32px; height: 32px; border-radius: 9px; border: 1px solid var(--a-line); background: #fff; display: inline-grid; place-items: center; color: #8a9a90; cursor: pointer; text-decoration: none; transition: all .15s; }
  .adm-ab svg { width: 15px; height: 15px; }
  .adm-ab:hover { color: var(--a-g700); border-color: #cfe0d4; background: var(--a-g50); }
  .adm-ab.del:hover { color: var(--a-red); border-color: #f3d0d0; background: #fdecec; }

  /* ---------- page header (pages without a hero) ---------- */
  .adm-ph { display: flex; align-items: flex-end; gap: 16px; margin-bottom: 20px; }
  .adm-ph h1 { margin: 0; font-size: 24px; font-weight: 800; letter-spacing: -.02em; color: var(--a-ink); }
  .adm-ph p { margin: 4px 0 0; color: var(--a-mute); font-size: 13.5px; }
  .adm-ph .r { margin-left: auto; display: flex; gap: 10px; align-items: center; }

  /* ---------- cards ---------- */
  .adm-card { background: #fff; border: 1px solid var(--a-line); border-radius: 20px; box-shadow: 0 1px 2px rgba(22,36,28,.04), 0 8px 24px rgba(22,36,28,.04); padding: 18px 20px; }
  .adm-card.flush { padding: 0; overflow: hidden; }
  .adm-card-h { display: flex; align-items: center; gap: 8px; margin: 0 0 14px; font-size: 15px; font-weight: 700; color: var(--a-ink); }
  .adm-card-h a { margin-left: auto; font-size: 12.5px; font-weight: 700; color: var(--a-g700); text-decoration: none; }
  .adm-card-h a:hover { text-decoration: underline; }
  .adm-ch { display: flex; align-items: center; gap: 12px; padding: 18px 20px 14px; }
  .adm-ch h3 { margin: 0; font-size: 15px; font-weight: 700; letter-spacing: -.01em; color: var(--a-ink); }
  .adm-ch p { margin: 1px 0 0; font-size: 12.5px; color: var(--a-mute); }
  .adm-ch .r { margin-left: auto; display: flex; gap: 8px; align-items: center; }
  .adm-ci { width: 34px; height: 34px; border-radius: 10px; display: grid; place-items: center; flex-shrink: 0; }
  .adm-ci svg { width: 18px; height: 18px; }
  .t-green { background: var(--a-g100); color: var(--a-g700); } .t-amber { background: #fdf1dc; color: var(--a-amber); } .t-red { background: #fdecec; color: var(--a-red); }
  .t-blue { background: #e7f0fb; color: var(--a-blue); } .t-violet { background: #f1ecfb; color: var(--a-violet); }

  .adm-kpis { display: grid; grid-template-columns: repeat(var(--n, 4), minmax(0, 1fr)); gap: 16px; margin-bottom: 20px; }
  .adm-k { position: relative; padding: 16px 18px; display: flex; gap: 14px; align-items: center; }
  .adm-k .adm-ci { width: 44px; height: 44px; border-radius: 14px; } .adm-k .adm-ci svg { width: 21px; height: 21px; }
  .adm-k b { font-size: 22px; font-weight: 800; display: block; letter-spacing: -.02em; color: var(--a-ink); line-height: 1.15; }
  .adm-k span { font-size: 12.5px; color: var(--a-mute); font-weight: 600; }
  .adm-k em { position: absolute; right: 14px; top: 12px; font-style: normal; font-size: 11.5px; font-weight: 700; padding: 3px 9px; border-radius: 99px; white-space: nowrap; }

  /* search, segmented control, tabs, chips */
  .adm-search { height: 40px; border-radius: 12px; border: 1px solid var(--a-line); background: #fff; display: flex; align-items: center; gap: 9px; padding: 0 12px; color: #9aa8a0; width: 280px; }
  .adm-search svg { width: 15px; height: 15px; flex-shrink: 0; }
  .adm-search input { border: 0; outline: 0; background: transparent; font-size: 13px; color: var(--a-ink); width: 100%; font-family: inherit; }
  .adm-search:focus-within { border-color: #b9d4c1; box-shadow: 0 0 0 3px rgba(95,147,112,.12); }
  .adm-seg { display: flex; background: #f1f5f2; border-radius: 10px; padding: 3px; gap: 2px; flex-wrap: wrap; }
  .adm-seg button { border: 0; background: transparent; font-size: 12px; font-weight: 700; padding: 6px 10px; border-radius: 8px; color: var(--a-mute); cursor: pointer; font-family: inherit; white-space: nowrap; }
  .adm-seg button.on { background: #fff; color: var(--a-ink); box-shadow: 0 1px 3px rgba(0,0,0,.08); }
  .adm-tabs { display: flex; gap: 22px; border-bottom: 1px solid var(--a-line); padding: 0 20px; }
  .adm-tabs button { border: 0; background: none; padding: 14px 2px 12px; font-weight: 700; font-size: 13.5px; color: var(--a-mute); border-bottom: 2px solid transparent; margin-bottom: -1px; display: flex; gap: 8px; align-items: center; cursor: pointer; font-family: inherit; }
  .adm-tabs button i { font-style: normal; font-size: 11px; background: #eef2ef; color: var(--a-ink2); padding: 1px 7px; border-radius: 99px; }
  .adm-tabs button.on { color: var(--a-g800); border-color: var(--a-g600); }
  .adm-tabs button.on i { background: var(--a-g100); color: var(--a-g800); }
  .adm-chip { display: inline-block; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 6px; background: var(--a-g50); color: var(--a-g700); border: 1px solid #dcebdf; white-space: nowrap; }
  .adm-pill { display: inline-flex; align-items: center; gap: 5px; font-size: 11px; font-weight: 700; padding: 2px 9px; border-radius: 99px; white-space: nowrap; }
  .adm-code { font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-size: 12px; background: #f1f5f2; color: var(--a-g800); padding: 3px 7px; border-radius: 6px; white-space: nowrap; }

  /* tables */
  .adm-table { width: 100%; border-collapse: collapse; }
  .adm-table th { font-size: 11.5px; font-weight: 700; color: #9aa8a0; text-transform: uppercase; letter-spacing: .05em; text-align: left; padding: 10px 16px; background: #fafcfb; border-top: 1px solid #eef3ef; border-bottom: 1px solid #eef3ef; }
  .adm-table td { padding: 13px 16px; border-bottom: 1px solid #eef3ef; font-size: 13.5px; color: var(--a-ink2); vertical-align: top; }
  .adm-table tr:last-child td { border-bottom: 0; }
  .adm-table tbody tr { transition: background .12s; }
  .adm-table tbody tr:hover { background: #fbfdfb; }
  .adm-table td b { color: var(--a-ink); font-weight: 600; }
  .adm-table .acts { display: flex; gap: 6px; justify-content: flex-end; }
  .adm-foot { padding: 12px 20px; font-size: 12.5px; color: var(--a-mute); border-top: 1px solid #eef3ef; }

  .adm-empty { text-align: center; color: var(--a-mute); font-size: 13.5px; padding: 38px 16px; }
  .adm-empty b { display: block; color: var(--a-ink); margin-bottom: 4px; font-size: 15px; }
  .adm-empty .ic { width: 52px; height: 52px; border-radius: 16px; margin: 0 auto 12px; display: grid; place-items: center; }
  .adm-empty .ic svg { width: 24px; height: 24px; }
  .adm-flash { background: #ecfdf5; border: 1px solid #bbf7d0; color: #166534; border-radius: 14px; padding: 11px 16px; font-size: 13.5px; font-weight: 600; margin-bottom: 16px; }
  .adm-check { width: 17px; height: 17px; accent-color: var(--a-g600); cursor: pointer; }

  /* older markup kept working (dashboard / analytics / database) */
  .adm-btn-light { display: inline-flex; align-items: center; gap: 8px; height: 40px; padding: 0 16px; border-radius: 12px; border: 0; background: #fff; color: var(--a-g800); font-weight: 700; font-size: 13.5px; cursor: pointer; text-decoration: none; white-space: nowrap; box-shadow: 0 6px 16px rgba(0,0,0,.12); font-family: inherit; }
  .adm-btn-light:hover { background: #f0f7f2; }
  .adm-btn-glass { display: inline-flex; align-items: center; gap: 6px; height: 40px; padding: 0 14px; border-radius: 12px; border: 1px solid rgba(255,255,255,.28); background: rgba(255,255,255,.14); color: #fff; font-weight: 700; font-size: 13px; text-decoration: none; cursor: pointer; font-family: inherit; backdrop-filter: blur(10px); }
  .adm-btn-glass:hover { background: rgba(255,255,255,.24); }
  .adm-hero .adm-search { background: rgba(255,255,255,.14); border-color: rgba(255,255,255,.3); color: #eef7f0; backdrop-filter: blur(12px); }
  .adm-hero .adm-search input { color: #fff; } .adm-hero .adm-search input::placeholder { color: rgba(238,247,240,.8); }
  .adm-hero select { height: 40px; padding: 0 12px; border-radius: 12px; border: 1px solid rgba(255,255,255,.3); background: rgba(255,255,255,.14); color: #fff; font-weight: 700; font-size: 13px; font-family: inherit; outline: 0; }
  .adm-hero select option { color: var(--a-ink); }
  .adm-wrap + .page, .adm-wrap + .container, .adm-wrap + .content {
    max-width: 1480px !important; margin: 0 !important; padding-top: 0 !important; padding-left: 32px !important; padding-right: 32px !important;
  }

  /* modals (shared look for the pages' add / edit / view forms) */
  .modal { display: none; position: fixed; inset: 0; z-index: 90; background: rgba(15, 30, 22, .45); backdrop-filter: blur(3px); align-items: center; justify-content: center; padding: 20px; }
  .modal .modal-content { background: #fff; border-radius: 22px; width: 100%; max-width: 560px; padding: 24px 26px; box-shadow: 0 30px 60px rgba(0,0,0,.25); border: 0; max-height: 90vh; overflow-y: auto; }
  .modal .modal-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 18px; }
  .modal .modal-header h2 { margin: 0; font-size: 19px; font-weight: 800; letter-spacing: -.01em; color: var(--a-ink); }
  .modal .close-btn { width: 34px; height: 34px; border-radius: 10px; border: 1px solid var(--a-line); background: #fff; font-size: 20px; color: var(--a-mute); cursor: pointer; line-height: 1; }
  .modal .form-group { margin-bottom: 14px; }
  .modal .form-group label { display: block; font-size: 12.5px; font-weight: 700; color: var(--a-ink2); margin-bottom: 6px; text-transform: none; letter-spacing: 0; }
  .modal .form-group input, .modal .form-group textarea, .modal .form-group select { width: 100%; box-sizing: border-box; border: 1px solid var(--a-line); border-radius: 12px; padding: 11px 13px; font-size: 14px; color: var(--a-ink); background: #fff; outline: 0; font-family: inherit; }
  .modal .form-group input:focus, .modal .form-group textarea:focus, .modal .form-group select:focus { border-color: #b9d4c1; box-shadow: 0 0 0 3px rgba(95,147,112,.14); }
  .modal .view-field { background: var(--a-g50); border: 1px solid var(--a-line); border-radius: 12px; padding: 11px 13px; font-size: 14px; color: var(--a-ink); line-height: 1.55; white-space: pre-wrap; }
  .modal .modal-actions { display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px; }
  .modal .cancel-btn, .modal .submit-btn { height: 42px; padding: 0 18px; border-radius: 12px; font-weight: 700; font-size: 14px; cursor: pointer; border: 0; font-family: inherit; }
  .modal .cancel-btn { background: #fff; color: var(--a-ink2); border: 1px solid var(--a-line); }
  .modal .submit-btn { background: var(--a-g700); color: #fff; }
  .modal .submit-btn:hover { background: var(--a-g800); }

  /* =====================  Bento Premium  ===================== */
  :root {
    --a-bg: #f6f7f5; --a-ink: #111c15; --a-ink2: #3d4a42; --a-mute: #7a847d; --a-line: #eceee9;
    --a-g900: #0f2a1c; --a-g800: #15502f; --a-g700: #1f6b43; --a-g600: #2f7a4f; --a-g500: #3fb070; --a-g100: #e7f5ec; --a-g50: #f2f9f4;
    --a-dark: #111c15; --a-mint: #7ee0b0;
  }
  .adm-wrap { padding: 8px 34px 48px; max-width: 1520px; }
  .adm-ph { align-items: flex-end; margin-bottom: 22px; }
  .adm-ph h1 { font-size: 30px; letter-spacing: -.03em; }
  .adm-ph p { font-size: 14px; }

  /* tiles */
  .adm-card { border-radius: 28px; border: 1px solid var(--a-line); box-shadow: none; padding: 22px 24px; }
  .adm-card.flush { padding: 0; }
  .adm-ch { padding: 22px 24px 14px; }
  .adm-ch h3 { font-size: 17px; font-weight: 800; letter-spacing: -.01em; }
  .adm-ci { width: 40px; height: 40px; border-radius: 13px; }
  .adm-ci svg { width: 19px; height: 19px; }
  .adm-card-h { font-size: 17px; font-weight: 800; }

  /* metric tiles: icon + chip on top, big number at the bottom */
  .adm-kpis { gap: 18px; margin-bottom: 22px; }
  .adm-k { display: grid; grid-template-columns: auto 1fr; grid-template-rows: auto 1fr; align-items: start; gap: 0; min-height: 148px; padding: 20px 22px; }
  .adm-k .adm-ci { width: 42px; height: 42px; border-radius: 14px; grid-column: 1; grid-row: 1; }
  .adm-k > div:not(.adm-ci) { grid-column: 1 / -1; grid-row: 2; align-self: end; padding-top: 16px; }
  .adm-k b { font-size: 34px; letter-spacing: -.04em; line-height: 1.05; }
  .adm-k span { font-size: 13px; }
  .adm-k em { position: static; grid-column: 2; grid-row: 1; justify-self: end; align-self: start; font-size: 11.5px; font-weight: 800; padding: 4px 10px; }
  .adm-k.dark { background: var(--a-dark); border-color: var(--a-dark); }
  .adm-k.dark b { color: #fff; } .adm-k.dark span { color: #9fb3a6; }
  .adm-k.dark .adm-ci { background: rgba(255,255,255,.1); color: var(--a-mint); }
  .adm-k.dark em { background: rgba(126,224,176,.16); color: var(--a-mint); }

  /* buttons: pills */
  .adm-btn { height: 42px; padding: 0 18px; border-radius: 99px; }
  .adm-btn.sm { height: 34px; padding: 0 14px; border-radius: 99px; }
  .adm-btn.g { background: var(--a-dark); color: #fff; box-shadow: none; } .adm-btn.g:hover { background: #24322a; }
  .adm-btn.o { border-color: var(--a-line); color: var(--a-ink); } .adm-btn.o:hover { background: #f3f5f2; }
  .adm-btn.w { background: var(--a-mint); color: var(--a-g900); box-shadow: none; } .adm-btn.w:hover { background: #9aeac3; }
  .adm-btn.gl { border-radius: 99px; }
  .adm-ab { width: 34px; height: 34px; border-radius: 12px; border-color: var(--a-line); }

  /* search, segmented control, tabs */
  .adm-search { height: 42px; border-radius: 99px; padding: 0 16px; border-color: var(--a-line); }
  .adm-search:focus-within { border-color: #bfe3cc; box-shadow: 0 0 0 4px rgba(63,176,112,.12); }
  .adm-seg { background: #f1f3f0; border-radius: 99px; padding: 4px; }
  .adm-seg button { border-radius: 99px; padding: 6px 13px; }
  .adm-seg button.on { background: var(--a-dark); color: #fff; box-shadow: none; }
  .adm-tabs { padding: 0 24px; gap: 8px; border-bottom: 1px solid var(--a-line); }
  .adm-tabs button { padding: 16px 12px 14px; }
  .adm-tabs button.on { color: var(--a-ink); border-color: var(--a-dark); }
  .adm-tabs button.on i { background: var(--a-dark); color: #fff; }
  .adm-chip { border-radius: 99px; padding: 3px 10px; background: var(--a-g100); border-color: transparent; color: var(--a-g800); }
  .adm-code { background: #f1f3f0; color: var(--a-ink2); border-radius: 8px; }

  /* tables */
  .adm-table th { background: #fafbf9; color: #9aa39c; border-color: var(--a-line); padding: 12px 18px; }
  .adm-table td { padding: 15px 18px; border-color: #f1f2ef; }
  .adm-table tbody tr:hover { background: #fafbf9; }
  .adm-foot { padding: 14px 24px; border-color: #f1f2ef; }
  .adm-flash { border-radius: 99px; padding: 11px 20px; }

  /* hero: deep green tile with a soft mint glow */
  .adm-hero { border-radius: 28px; padding: 30px 32px; background: linear-gradient(135deg, #0f2a1c 0%, #1d4d33 60%, #2f7a4f 100%); box-shadow: none; }
  .adm-hero::before { background: radial-gradient(460px 320px at 88% -10%, rgba(126,224,176,.35), transparent 65%), radial-gradient(420px 260px at 0% 120%, rgba(126,224,176,.14), transparent 60%); }
  .adm-hero::after { opacity: .5; }
  .adm-hero-text h1 { font-size: 30px; letter-spacing: -.03em; }
  .adm-hero-text p { color: #bfe0cb; font-size: 14px; }
  .adm-hero-text small, .adm-hero-text small a { color: #9fd6b6; }
  .adm-hs { border-radius: 22px; background: rgba(255,255,255,.08); border-color: rgba(255,255,255,.14); }
  .adm-hs em { color: var(--a-mint); }

  /* modals */
  .modal .modal-content { border-radius: 28px; padding: 26px 28px; }
  .modal .close-btn { border-radius: 99px; }
  .modal .cancel-btn, .modal .submit-btn { border-radius: 99px; }
  .modal .submit-btn { background: var(--a-dark); } .modal .submit-btn:hover { background: #24322a; }
  .modal .form-group input, .modal .form-group textarea, .modal .form-group select { border-radius: 14px; }
</style>
@endonce
