@extends('layouts.app')

@section('content')

@include('partials.auth-style')
@include('partials.forgot-style')

<div class="blob blob-1"></div>
<div class="blob blob-2"></div>
<div class="blob blob-3"></div>

<div class="card fp-card">
  @include('partials.forgot-head', ['step' => 2, 'labels' => [1 => 'Details', 2 => 'Verify email', 3 => 'Done']])

  <h1>Verify your email</h1>
  <p class="fp-lead">We sent a 6-digit code to <b>{{ $maskedEmail }}</b>. Type it below to finish creating your account. It works for {{ $minutes }} minutes — check your spam folder too.</p>

  @if (session('otp_error'))
    <div class="fp-msg err">{{ session('otp_error') }}</div>
  @elseif (session('otp_status'))
    <div class="fp-msg ok">{{ session('otp_status') }}</div>
  @endif

  <form method="POST" action="{{ route('register.verify.post') }}" id="otpForm">
    @csrf
    <input type="hidden" name="code" id="otpCode">
    <div class="otp {{ session('otp_error') ? 'shake' : '' }}" id="otpBoxes">
      @for ($i = 0; $i < 6; $i++)
        @if ($i === 3)<span class="gap"></span>@endif
        <input type="text" inputmode="numeric" maxlength="1" aria-label="Digit {{ $i + 1 }}" {{ $i === 0 ? 'autocomplete=one-time-code autofocus' : 'autocomplete=off' }}>
      @endfor
    </div>
    <button type="submit" class="btn-signin" id="otpBtn" disabled><span>Verify &amp; create account</span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></button>
  </form>

  <form method="POST" action="{{ route('register.resend') }}" class="fp-resend">
    @csrf
    Didn't get it?
    <button type="submit" id="resendBtn" {{ $resendIn > 0 ? 'disabled' : '' }} data-wait="{{ $resendIn }}">
      {{ $resendIn > 0 ? "Resend in {$resendIn}s" : 'Resend code' }}
    </button>
  </form>

  <div class="fp-foot"><a class="fp-back" href="{{ route('register') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M11 18l-6-6 6-6"/></svg> Change my details</a></div>
</div>

<footer>© 2026 RakanKampus · Politeknik Ungku Omar</footer>

<script>
(function () {
  var boxes = Array.prototype.slice.call(document.querySelectorAll('#otpBoxes input'));
  var form = document.getElementById('otpForm');
  var hidden = document.getElementById('otpCode');
  var btn = document.getElementById('otpBtn');

  function sync() {
    var code = boxes.map(function (b) { return b.value; }).join('');
    hidden.value = code;
    boxes.forEach(function (b) { b.classList.toggle('filled', !!b.value); });
    btn.disabled = code.length !== 6;
    return code;
  }
  function fill(from, digits) {
    for (var i = 0; i < digits.length && from + i < boxes.length; i++) boxes[from + i].value = digits[i];
    var next = Math.min(from + digits.length, boxes.length - 1);
    boxes[next].focus();
    if (sync().length === 6) { btn.disabled = true; btn.querySelector('span').textContent = 'Checking…'; form.submit(); }
  }

  boxes.forEach(function (box, i) {
    box.addEventListener('input', function () {
      var d = box.value.replace(/\D/g, '');
      box.value = '';
      if (d) fill(i, d); else sync();
    });
    box.addEventListener('keydown', function (e) {
      if (e.key === 'Backspace' && !box.value && i > 0) { boxes[i - 1].value = ''; boxes[i - 1].focus(); sync(); e.preventDefault(); }
      if (e.key === 'ArrowLeft' && i > 0) boxes[i - 1].focus();
      if (e.key === 'ArrowRight' && i < 5) boxes[i + 1].focus();
    });
    box.addEventListener('paste', function (e) {
      var d = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '');
      if (d) { e.preventDefault(); fill(i, d.slice(0, 6 - i)); }
    });
    box.addEventListener('focus', function () { box.select(); });
  });
  form.addEventListener('submit', function () { sync(); });

  // resend countdown
  var rb = document.getElementById('resendBtn');
  var left = parseInt(rb.getAttribute('data-wait'), 10) || 0;
  if (left > 0) {
    var t = setInterval(function () {
      left--;
      if (left <= 0) { clearInterval(t); rb.disabled = false; rb.textContent = 'Resend code'; }
      else rb.textContent = 'Resend in ' + left + 's';
    }, 1000);
  }
})();
</script>
@endsection
