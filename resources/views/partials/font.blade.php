{{-- App-wide font: Plus Jakarta Sans, served from /public/fonts (no Google request, works on weak
     internet). Included in the <head> of every student, admin and auth page. Characters the font
@include('partials.font')
     doesn't have (Chinese, Tamil, emoji) fall back to the device's own fonts automatically. --}}
@once
<link rel="preload" href="/fonts/plus-jakarta-sans/plus-jakarta-sans-latin-400-normal.woff2" as="font" type="font/woff2" crossorigin>
<link rel="preload" href="/fonts/plus-jakarta-sans/plus-jakarta-sans-latin-700-normal.woff2" as="font" type="font/woff2" crossorigin>
<style>
  @font-face{font-family:'Plus Jakarta Sans';font-style:normal;font-weight:400;font-display:swap;src:url('/fonts/plus-jakarta-sans/plus-jakarta-sans-latin-400-normal.woff2') format('woff2');unicode-range:U+0000-00FF,U+0131,U+0152-0153,U+02BB-02BC,U+02C6,U+02DA,U+02DC,U+0304,U+0308,U+0329,U+2000-206F,U+20AC,U+2122,U+2191,U+2193,U+2212,U+2215,U+FEFF,U+FFFD}
  @font-face{font-family:'Plus Jakarta Sans';font-style:normal;font-weight:400;font-display:swap;src:url('/fonts/plus-jakarta-sans/plus-jakarta-sans-latin-ext-400-normal.woff2') format('woff2');unicode-range:U+0100-02BA,U+02BD-02C5,U+02C7-02CC,U+02CE-02D7,U+02DD-02FF,U+0304,U+0308,U+0329,U+1D00-1DBF,U+1E00-1E9F,U+1EF2-1EFF,U+2020,U+20A0-20AB,U+20AD-20C0,U+2113,U+2C60-2C7F,U+A720-A7FF}
  @font-face{font-family:'Plus Jakarta Sans';font-style:normal;font-weight:500;font-display:swap;src:url('/fonts/plus-jakarta-sans/plus-jakarta-sans-latin-500-normal.woff2') format('woff2');unicode-range:U+0000-00FF,U+0131,U+0152-0153,U+02BB-02BC,U+02C6,U+02DA,U+02DC,U+0304,U+0308,U+0329,U+2000-206F,U+20AC,U+2122,U+2191,U+2193,U+2212,U+2215,U+FEFF,U+FFFD}
  @font-face{font-family:'Plus Jakarta Sans';font-style:normal;font-weight:500;font-display:swap;src:url('/fonts/plus-jakarta-sans/plus-jakarta-sans-latin-ext-500-normal.woff2') format('woff2');unicode-range:U+0100-02BA,U+02BD-02C5,U+02C7-02CC,U+02CE-02D7,U+02DD-02FF,U+0304,U+0308,U+0329,U+1D00-1DBF,U+1E00-1E9F,U+1EF2-1EFF,U+2020,U+20A0-20AB,U+20AD-20C0,U+2113,U+2C60-2C7F,U+A720-A7FF}
  @font-face{font-family:'Plus Jakarta Sans';font-style:normal;font-weight:600;font-display:swap;src:url('/fonts/plus-jakarta-sans/plus-jakarta-sans-latin-600-normal.woff2') format('woff2');unicode-range:U+0000-00FF,U+0131,U+0152-0153,U+02BB-02BC,U+02C6,U+02DA,U+02DC,U+0304,U+0308,U+0329,U+2000-206F,U+20AC,U+2122,U+2191,U+2193,U+2212,U+2215,U+FEFF,U+FFFD}
  @font-face{font-family:'Plus Jakarta Sans';font-style:normal;font-weight:600;font-display:swap;src:url('/fonts/plus-jakarta-sans/plus-jakarta-sans-latin-ext-600-normal.woff2') format('woff2');unicode-range:U+0100-02BA,U+02BD-02C5,U+02C7-02CC,U+02CE-02D7,U+02DD-02FF,U+0304,U+0308,U+0329,U+1D00-1DBF,U+1E00-1E9F,U+1EF2-1EFF,U+2020,U+20A0-20AB,U+20AD-20C0,U+2113,U+2C60-2C7F,U+A720-A7FF}
  @font-face{font-family:'Plus Jakarta Sans';font-style:normal;font-weight:700;font-display:swap;src:url('/fonts/plus-jakarta-sans/plus-jakarta-sans-latin-700-normal.woff2') format('woff2');unicode-range:U+0000-00FF,U+0131,U+0152-0153,U+02BB-02BC,U+02C6,U+02DA,U+02DC,U+0304,U+0308,U+0329,U+2000-206F,U+20AC,U+2122,U+2191,U+2193,U+2212,U+2215,U+FEFF,U+FFFD}
  @font-face{font-family:'Plus Jakarta Sans';font-style:normal;font-weight:700;font-display:swap;src:url('/fonts/plus-jakarta-sans/plus-jakarta-sans-latin-ext-700-normal.woff2') format('woff2');unicode-range:U+0100-02BA,U+02BD-02C5,U+02C7-02CC,U+02CE-02D7,U+02DD-02FF,U+0304,U+0308,U+0329,U+1D00-1DBF,U+1E00-1E9F,U+1EF2-1EFF,U+2020,U+20A0-20AB,U+20AD-20C0,U+2113,U+2C60-2C7F,U+A720-A7FF}
  @font-face{font-family:'Plus Jakarta Sans';font-style:normal;font-weight:800;font-display:swap;src:url('/fonts/plus-jakarta-sans/plus-jakarta-sans-latin-800-normal.woff2') format('woff2');unicode-range:U+0000-00FF,U+0131,U+0152-0153,U+02BB-02BC,U+02C6,U+02DA,U+02DC,U+0304,U+0308,U+0329,U+2000-206F,U+20AC,U+2122,U+2191,U+2193,U+2212,U+2215,U+FEFF,U+FFFD}
  @font-face{font-family:'Plus Jakarta Sans';font-style:normal;font-weight:800;font-display:swap;src:url('/fonts/plus-jakarta-sans/plus-jakarta-sans-latin-ext-800-normal.woff2') format('woff2');unicode-range:U+0100-02BA,U+02BD-02C5,U+02C7-02CC,U+02CE-02D7,U+02DD-02FF,U+0304,U+0308,U+0329,U+1D00-1DBF,U+1E00-1E9F,U+1EF2-1EFF,U+2020,U+20A0-20AB,U+20AD-20C0,U+2113,U+2C60-2C7F,U+A720-A7FF}
  :root{--rk-font:'Plus Jakarta Sans',-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Helvetica,Arial,"Noto Sans SC","Noto Sans Tamil",sans-serif}
  html body{font-family:var(--rk-font) !important;-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale}
  html body button,html body input,html body select,html body textarea,html body .font-sans{font-family:var(--rk-font)}
  /* Chrome autofill paints fields light blue — keep the soft field colour on the sign-in pages */
  .lg-field input:-webkit-autofill,.box input:-webkit-autofill,.fp-sheet input:-webkit-autofill{
    -webkit-box-shadow:0 0 0 1000px #f5f0ff inset !important;-webkit-text-fill-color:#2e1065 !important;caret-color:#2e1065;transition:background-color 99999s}
  .lg-field:focus-within input:-webkit-autofill,.box:focus-within input:-webkit-autofill,.fp-sheet input:focus:-webkit-autofill{-webkit-box-shadow:0 0 0 1000px #fff inset !important}
</style>
@endonce
