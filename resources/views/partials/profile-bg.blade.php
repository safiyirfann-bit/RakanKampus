{{-- Profile section pages: a fixed background, the same gradient as Reminders, instead of
     the slowly shifting animated one (phone). PC keeps its light / dark plain background. --}}
<style>
  @media (max-width: 860px) {
    html body { background: linear-gradient(160deg, #14213d, #1b3a5c 55%, #2ec4c6) fixed !important; animation: none !important; }
    html[data-theme="dark"] body { background: linear-gradient(160deg, #0c1320, #112031 55%, #1d6869) fixed !important; }
  }
</style>
