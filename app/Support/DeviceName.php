<?php

namespace App\Support;

/**
 * Turns a browser user-agent string into something a student recognises,
 * e.g. "Chrome · Windows" or "RakanKampus app · Android".
 */
class DeviceName
{
    /** @return array{name: string, mobile: bool} */
    public static function from(?string $ua): array
    {
        $ua = (string) $ua;

        $os = match (true) {
            str_contains($ua, 'iPhone') => 'iPhone',
            str_contains($ua, 'iPad') => 'iPad',
            str_contains($ua, 'Android') => 'Android',
            str_contains($ua, 'Windows') => 'Windows',
            str_contains($ua, 'Mac OS X'), str_contains($ua, 'Macintosh') => 'Mac',
            str_contains($ua, 'CrOS') => 'Chromebook',
            str_contains($ua, 'Linux') => 'Linux',
            default => null,
        };

        $browser = match (true) {
            str_contains($ua, '; wv)'), str_contains($ua, 'RakanKampus') => 'RakanKampus app',
            str_contains($ua, 'Edg/') => 'Edge',
            str_contains($ua, 'OPR/'), str_contains($ua, 'Opera') => 'Opera',
            str_contains($ua, 'SamsungBrowser') => 'Samsung Internet',
            str_contains($ua, 'Firefox/'), str_contains($ua, 'FxiOS') => 'Firefox',
            str_contains($ua, 'CriOS'), str_contains($ua, 'Chrome/') => 'Chrome',
            str_contains($ua, 'Safari/') => 'Safari',
            default => null,
        };

        $name = match (true) {
            $browser !== null && $os !== null => "{$browser} · {$os}",
            $browser !== null => $browser,
            $os !== null => $os,
            default => __('Unknown device'),
        };

        return ['name' => $name, 'mobile' => in_array($os, ['iPhone', 'Android'], true)];
    }
}
