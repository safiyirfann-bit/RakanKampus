{{-- Profile section pages: a fixed background, the same gradient as Reminders, instead of
     the slowly shifting animated one (phone). PC keeps its light / dark plain background.
     On PC the page header has no white bar: the title sits on the page like Reminders / Timetable. --}}
<style>
  @media (max-width: 860px) {
    html body { background: linear-gradient(160deg, #14213d, #1b3a5c 55%, #2ec4c6) fixed !important; animation: none !important; }
    html[data-theme="dark"] body { background: linear-gradient(160deg, #0c1320, #112031 55%, #1d6869) fixed !important; }
  }
  @media (min-width: 861px) {
    html body .page-header, html body .profile-header {
      background: transparent !important; border: 0 !important; box-shadow: none !important;
      -webkit-backdrop-filter: none !important; backdrop-filter: none !important;
      padding: 32px 40px 16px !important; align-items: center;
    }
    html body .page-header h1, html body .profile-header h1 { color: #14213d !important; font-size: 22px !important; font-weight: 800; }
    html body .page-header p, html body .profile-header p { color: #0d9488 !important; font-size: 13px !important; }
    html body .page-header .back-btn {
      width: 40px !important; height: 40px !important; min-width: 40px; border-radius: 12px !important;
      background: #fff !important; color: #0d9488 !important; border: 1px solid #dbeeee; box-shadow: 0 4px 12px rgba(15,39,71,.06);
    }
    html body .page-header .back-btn:hover { background: #ecfdf9 !important; border-color: #99f6e4; }
    html[data-theme="dark"] body .page-header h1, html[data-theme="dark"] body .profile-header h1 { color: #dee1e9 !important; }
    html[data-theme="dark"] body .page-header p, html[data-theme="dark"] body .profile-header p { color: #41eedf !important; }
    html[data-theme="dark"] body .page-header .back-btn { background: #17202d !important; color: #41eedf !important; border-color: #283648; box-shadow: none; }
    html[data-theme="dark"] body .page-header .back-btn:hover { background: #1c3b39 !important; }
  }
</style>
