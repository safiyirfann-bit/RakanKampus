{{-- Top of each forgot-password step (and the sign-up email check): the robot chats on the
     gradient, then the white sheet opens with a labelled stepper. The view closes the
     sheet with @include('partials.forgot-foot').
     Pass $step (1..3), $lines (what the robot says, in order) and optionally $labels.
     Page scripts can add a message later with fpSay('text') or fpSay('text', true) for a warning. --}}
@php
  $labels = $labels ?? [1 => __('Email'), 2 => __('Code'), 3 => __('New password')];
  $lines = array_values(array_filter($lines ?? []));
@endphp

<div class="fp-blob fp-b1"></div>
<div class="fp-blob fp-b2"></div>

<div class="fp">
 <div class="fp-shell">

  <div class="fp-hero">
    <div class="fp-chat" id="fpChat" aria-live="polite">
      <div class="fp-hdr">
        <div class="fp-av"><x-brand-logo size="48" /></div>
        <div><b>RakanKampus</b><span>{{ __('Here to help') }}</span></div>
      </div>
    </div>
  </div>

  <div class="fp-sheet">
    <ol class="fp-stepper" aria-label="{{ __('Step :n of 3', ['n' => $step]) }}">
      @foreach ($labels as $n => $label)
        <li class="{{ $n < $step ? 'done' : ($n === $step ? 'on' : '') }}">
          <span class="dot">
            @if ($n < $step)
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 5 5 9-10"/></svg>
            @else
              {{ $n }}
            @endif
          </span>
          <span class="lbl">{{ $label }}</span>
        </li>
        @if ($n < 3)<li class="bar {{ $n < $step ? 'done' : '' }}" aria-hidden="true"></li>@endif
      @endforeach
    </ol>

<script>
(function () {
  var chat = document.getElementById('fpChat');
  var still = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var queue = Promise.resolve();
  function bubble(text, warn) {
    var b = chat.querySelectorAll('.fp-bub');
    if (b.length >= (window.innerHeight < 720 ? 2 : 3)) b[0].remove();
    var d = document.createElement('div');
    d.className = 'fp-bub' + (warn ? ' warn' : '');
    d.textContent = text;
    chat.appendChild(d);
  }
  // Show "typing…" for a moment, then the message. Messages wait their turn.
  window.fpSay = function (text, warn) {
    queue = queue.then(function () {
      if (still) { bubble(text, warn); return; }
      var t = document.createElement('div');
      t.className = 'fp-typing';
      t.innerHTML = '<i></i><i></i><i></i>';
      chat.appendChild(t);
      return new Promise(function (r) {
        setTimeout(function () { t.remove(); bubble(text, warn); setTimeout(r, 250); }, 750);
      });
    });
  };
  var L = @json($lines);
  L.forEach(function (l) {
    if (typeof l === 'string') fpSay(l); else fpSay(l[0], !!l[1]);
  });
})();
</script>
