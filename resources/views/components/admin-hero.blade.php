@props(['title', 'sub' => null, 'back' => null, 'kpis' => []])
{{--
  Green hero banner used at the top of every admin page.
  <x-admin-hero title="Inbox" sub="…" :kpis="[['icon' => 'mail', 'value' => 3, 'label' => 'Unread', 'tag' => 'new', 'tone' => 'red', 'href' => '…']]">
      <x-slot:actions> …search / buttons… </x-slot:actions>
  </x-admin-hero>
--}}
@php
  $icons = [
    'users' => '<circle cx="9" cy="8" r="4"/><path d="M2 21a7 7 0 0 1 14 0M17 11a3 3 0 1 0 0-6M22 21a6 6 0 0 0-5-6"/>',
    'chat' => '<path d="M4 5h16v11H8l-4 4z"/>',
    'book' => '<path d="M4 19.5V5a2 2 0 0 1 2-2h13v16H6.5A2.5 2.5 0 0 0 4 21.5"/><path d="M8 7h7M8 11h5"/>',
    'alert' => '<circle cx="12" cy="12" r="9"/><path d="M12 8v5M12 16h.01"/>',
    'check' => '<path d="M20 6 9 17l-5-5"/>',
    'mail' => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>',
    'star' => '<path d="m12 3 2.7 5.6 6.1.9-4.4 4.3 1 6.1L12 17l-5.4 2.9 1-6.1L3.2 9.5l6.1-.9z"/>',
    'bulb' => '<path d="M9 18h6M10 21h4M12 3a6 6 0 0 0-4 10.5c.7.7 1 1.5 1 2.5h6c0-1 .3-1.8 1-2.5A6 6 0 0 0 12 3z"/>',
    'warn' => '<path d="M12 3 2 20h20L12 3z"/><path d="M12 10v4M12 17h.01"/>',
    'clock' => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/>',
    'history' => '<path d="M3 12a9 9 0 1 0 3-6.7L3 8"/><path d="M3 3v5h5M12 7v5l3 2"/>',
    'repeat' => '<path d="M17 2l4 4-4 4"/><path d="M3 11v-1a4 4 0 0 1 4-4h14M7 22l-4-4 4-4"/><path d="M21 13v1a4 4 0 0 1-4 4H3"/>',
    'db' => '<ellipse cx="12" cy="5" rx="8" ry="3"/><path d="M4 5v6c0 1.66 3.58 3 8 3s8-1.34 8-3V5M4 11v6c0 1.66 3.58 3 8 3s8-1.34 8-3v-6"/>',
    'online' => '<circle cx="12" cy="12" r="4"/><path d="M4.9 4.9a10 10 0 0 0 0 14.2M19.1 4.9a10 10 0 0 1 0 14.2"/>',
    'tag' => '<path d="M20.6 13.4 13.4 20.6a2 2 0 0 1-2.8 0L3 13V3h10l7.6 7.6a2 2 0 0 1 0 2.8z"/><circle cx="7.5" cy="7.5" r="1.5"/>',
    'peak' => '<path d="M3 20h18M6 16V9M11 16V5M16 16v-5M21 16v-8"/>',
  ];
@endphp
<section {{ $attributes->merge(['class' => 'adm-hero']) }}>
  <div class="adm-hero-top">
    @if($back)
      <a href="{{ $back }}" class="adm-hero-back" aria-label="Back"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg></a>
    @endif
    <div class="adm-hero-title">
      <h1>{!! $title !!}</h1>
      @if($sub)<p>{{ $sub }}</p>@endif
    </div>
    @isset($actions)
      <div class="adm-hero-actions">{{ $actions }}</div>
    @endisset
  </div>
  @if(count($kpis))
    <div class="adm-kpis">
      @foreach($kpis as $k)
        @php $tagName = isset($k['href']) ? 'a' : (isset($k['onclick']) ? 'button' : 'div'); @endphp
        <{{ $tagName }} class="adm-kpi" @if(isset($k['href'])) href="{{ $k['href'] }}" @endif @if(isset($k['onclick'])) type="button" onclick="{{ $k['onclick'] }}" @endif>
          <span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{!! $icons[$k['icon'] ?? 'check'] ?? $icons['check'] !!}</svg></span>
          @if(!empty($k['tag']))<span class="tag {{ $k['tone'] ?? '' }}">{{ $k['tag'] }}</span>@endif
          <b>{{ $k['value'] }}</b>
          <span class="lbl">{{ $k['label'] }}</span>
          @if(!empty($k['spark']))
            @php $pts = $k['spark']; $mx = max(1, max($pts)); $n = max(1, count($pts) - 1);
                 $poly = collect($pts)->map(fn($v, $i) => round($i * 84 / $n, 1) . ',' . round(26 - ($v / $mx) * 22, 1))->implode(' '); @endphp
            <svg class="spark" viewBox="0 0 84 28" aria-hidden="true"><polyline points="{{ $poly }}" fill="none" stroke="#f5e6a8" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
          @endif
        </{{ $tagName }}>
      @endforeach
    </div>
  @endif
  {{ $slot }}
</section>
