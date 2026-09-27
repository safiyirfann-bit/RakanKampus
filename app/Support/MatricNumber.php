<?php

namespace App\Support;

/**
 * Reads a PUO matric number (e.g. 01DIT24F1128) and works out the programme.
 * Codes live in config/programs.php.
 */
class MatricNumber
{
    /** 2-digit institution, 3-letter programme, 2-digit year, 1 letter session, 3-5 digit number */
    public const PATTERN = '/^(\d{2})([A-Z]{3})(\d{2})([A-Z])(\d{3,5})$/';

    public static function normalise(?string $matric): string
    {
        return strtoupper(preg_replace('/[\s-]+/', '', (string) $matric));
    }

    /** @return array{institution: string, code: string, year: string, session: string, number: string}|null */
    public static function parse(?string $matric): ?array
    {
        if (! preg_match(self::PATTERN, self::normalise($matric), $m)) {
            return null;
        }

        return ['institution' => $m[1], 'code' => $m[2], 'year' => $m[3], 'session' => $m[4], 'number' => $m[5]];
    }

    /** True when it looks like a matric number from this institution (01 = PUO). */
    public static function isValid(?string $matric): bool
    {
        $p = self::parse($matric);

        return $p !== null && $p['institution'] === config('programs.institution_code', '01');
    }

    /** Full programme name for the matric number, or null if the code isn't in the list. */
    public static function programme(?string $matric): ?string
    {
        $p = self::parse($matric);

        return $p ? (config('programs.codes')[$p['code']] ?? null) : null;
    }
}
