@props(['size' => 48])

<svg
    {{ $attributes->merge(['class' => '']) }}
    width="{{ $size }}"
    height="{{ $size }}"
    viewBox="0 0 200 220"
    fill="none"
    xmlns="http://www.w3.org/2000/svg"
>
    <!-- antenna -->
    <line x1="100" y1="14" x2="100" y2="30" stroke="#14213d" stroke-width="5" stroke-linecap="round"/>
    <circle cx="100" cy="12" r="6" fill="#2ec4c6"/>

    <!-- head -->
    <rect x="56" y="30" width="88" height="64" rx="24" fill="#14213d"/>

    <!-- headphone cups -->
    <circle cx="54" cy="58" r="14" fill="#2ec4c6"/>
    <circle cx="146" cy="58" r="14" fill="#2ec4c6"/>

    <!-- face plate -->
    <rect x="72" y="44" width="56" height="38" rx="14" fill="#ffffff"/>

    <!-- eyes -->
    <circle cx="90" cy="62" r="6" fill="#14213d"/>
    <circle cx="110" cy="62" r="6" fill="#14213d"/>

    <!-- smile -->
    <path d="M90 74 Q100 82 110 74" stroke="#14213d" stroke-width="4" fill="none" stroke-linecap="round"/>

    <!-- body -->
    <rect x="52" y="96" width="96" height="86" rx="26" fill="#ffffff" stroke="#14213d" stroke-width="4"/>

    <!-- chest light -->
    <circle cx="100" cy="128" r="7" fill="#2ec4c6"/>

    <!-- waving arm -->
    <path d="M58 118 Q26 108 22 76" stroke="#14213d" stroke-width="20" stroke-linecap="round" fill="none"/>
    <circle cx="24" cy="80" r="10" fill="#2ec4c6"/>
    <circle cx="22" cy="64" r="15" fill="#ffffff" stroke="#14213d" stroke-width="4"/>
    <line x1="22" y1="50" x2="14" y2="38" stroke="#14213d" stroke-width="4" stroke-linecap="round"/>
    <line x1="22" y1="49" x2="22" y2="36" stroke="#14213d" stroke-width="4" stroke-linecap="round"/>
    <line x1="22" y1="50" x2="30" y2="38" stroke="#14213d" stroke-width="4" stroke-linecap="round"/>

    <!-- other arm (tucked) -->
    <path d="M142 118 Q160 130 158 152" stroke="#14213d" stroke-width="18" stroke-linecap="round" fill="none"/>
    <circle cx="158" cy="158" r="12" fill="#14213d"/>

    <!-- legs -->
    <rect x="68" y="176" width="20" height="32" rx="9" fill="#14213d"/>
    <rect x="112" y="176" width="20" height="32" rx="9" fill="#14213d"/>

    <!-- feet -->
    <ellipse cx="78" cy="214" rx="17" ry="9" fill="#14213d"/>
    <ellipse cx="122" cy="214" rx="17" ry="9" fill="#14213d"/>
</svg>
