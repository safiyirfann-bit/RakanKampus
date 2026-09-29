{{-- Phones and the Android app: a decoration (blob, glow, off-screen menu) must never make the page
     wider than the screen, or the whole page slides left/right and fixed layers (intro, dialogs)
     stop being centred. "clip" cuts the overflow without breaking position: sticky headers. --}}
<style>
  html, body { max-width: 100%; overflow-x: clip; }
  @supports not (overflow: clip) { html { overflow-x: hidden; } }
</style>
