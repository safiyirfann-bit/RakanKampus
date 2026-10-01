{{-- Top of each forgot-password step: robot with a status badge + labelled stepper.
     Pass $step (1..3). --}}
@php
  $badges = [
    1 => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="15" r="4"/><path d="m10.8 12.2 8.7-8.7M17 6l2.5 2.5M14.5 8.5 16.5 10.5"/></svg>',
    2 => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2.5"/><path d="m4 7 8 6 8-6"/></svg>',
    3 => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3 5 6v5c0 4.5 3 8.3 7 10 4-1.7 7-5.5 7-10V6l-7-3Z"/><path d="m9 12 2 2 4-4"/></svg>',
  ];
  $labels = [1 => 'Email', 2 => 'Code', 3 => 'New password'];
@endphp

<div class="fp-hero">
  <span class="fp-ring"></span>
  <x-brand-logo size="92" class="fp-bot" />
  <span class="fp-badge fp-badge-{{ $step }}">{!! $badges[$step] !!}</span>
</div>

<ol class="fp-stepper" aria-label="Step {{ $step }} of 3">
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
