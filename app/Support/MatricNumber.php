<?php

namespace App\Support;

/**
 * Reads a PUO matric number (e.g. 01DIT24F1128) and works out the programme.
 * Codes live in config/programs.php.
 */
class MatricNumber
{
    /** Exactly: 2-digit institution, 3-letter programme, 2-digit year, 1-letter session, 4-digit number (12 characters) */
    public const PATTERN = '/^(\d{2})([A-Z]{3})(\d{2})([A-Z])(\d{4})$/';

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

    /**
     * True when it's a matric number from this institution (01 = PUO) AND its
     * programme code is one of the official codes in config/programs.php.
     */
    public static function isValid(?string $matric): bool
    {
        $p = self::parse($matric);

        return $p !== null
            && $p['institution'] === config('programs.institution_code', '01')
            && array_key_exists($p['code'], config('programs.codes', []));
    }

    /** Full programme name for the matric number, or null if the code isn't in the list. */
    public static function programme(?string $matric): ?string
    {
        $p = self::parse($matric);

        return $p ? (config('programs.codes')[$p['code']] ?? null) : null;
    }
}
