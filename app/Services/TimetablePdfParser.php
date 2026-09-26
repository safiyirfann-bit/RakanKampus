<?php

namespace App\Services;

use RuntimeException;

/**
 * Reads the official Politeknik "JADUAL WAKTU KELAS" PDF (Borang PK-02) straight
 * from its text layer — no AI involved, so codes, lecturers, rooms and times come
 * out exactly as printed.
 *
 * Uses poppler's `pdftotext -bbox` to get every word with its position, then:
 *  - the two rows of time headers (e.g. 8:00 / 9:00) give the column boundaries,
 *  - the day labels (ISNIN ... JUMAAT) give the row bands,
 *  - each (day, column) cell is read as up to 3 lines: CODE(TYPE) / lecturer / room,
 *  - the KOD / NAMA KURSUS table under the grid gives code -> course name.
 *
 * Output uses the same shape as the AI path in ClassScheduleController:
 *   ['legend' => [CODE => NAME], 'cells' => [[day, start, end, code, type, lecturer, room], ...]]
 */
class TimetablePdfParser
{
    private const DAY_LABELS = [
        'ISNIN' => 'MON', 'SELASA' => 'TUE', 'RABU' => 'WED', 'KHAMIS' => 'THU',
        'JUMAAT' => 'FRI', 'SABTU' => 'SAT', 'AHAD' => 'SUN',
        'MONDAY' => 'MON', 'TUESDAY' => 'TUE', 'WEDNESDAY' => 'WED', 'THURSDAY' => 'THU',
        'FRIDAY' => 'FRI', 'SATURDAY' => 'SAT', 'SUNDAY' => 'SUN',
    ];

    private const TIME_RE = '/^\d{1,2}[:.]\d{2}$/';

    public static function isAvailable(): bool
    {
        $out = @shell_exec('pdftotext -v 2>&1');

        return is_string($out) && stripos($out, 'pdftotext') !== false;
    }

    /**
     * @return array{legend: array<string, string>, cells: array<int, array<int, string|null>>}
     */
    public function parse(string $pdfPath): array
    {
        $words = $this->words($pdfPath);
        if (count($words) === 0) {
            throw new RuntimeException('PDF ni tak ada teks (mungkin hasil scan). Upload gambar/screenshot jadual sebagai ganti.');
        }

        // --- Rows: day labels in the left column ---------------------------------
        $days = [];
        foreach ($words as $w) {
            $label = strtoupper($w['text']);
            if (isset(self::DAY_LABELS[$label])) {
                $days[] = ['day' => self::DAY_LABELS[$label], 'y' => $w['cy'], 'xMax' => $w['xMax']];
            }
        }
        usort($days, fn ($a, $b) => $a['y'] <=> $b['y']);
        if (count($days) === 0) {
            throw new RuntimeException('Tak jumpa label hari (ISNIN, SELASA, ...) dalam PDF ni.');
        }
        $gridTop = $days[0]['y'];
        $labelRight = max(array_column($days, 'xMax'));

        // --- Columns: the two header rows of times above the first day ----------
        $times = array_values(array_filter($words, fn ($w) => preg_match(self::TIME_RE, $w['text']) && $w['cy'] < $gridTop && $w['xMin'] > $labelRight));
        $timeRows = $this->clusterByY($times, 4);
        if (count($timeRows) < 1) {
            throw new RuntimeException('Tak jumpa baris masa (8:00, 9:00, ...) dalam PDF ni.');
        }
        $starts = $timeRows[0];
        $ends = $timeRows[1] ?? [];
        usort($starts, fn ($a, $b) => $a['xMin'] <=> $b['xMin']);
        usort($ends, fn ($a, $b) => $a['xMin'] <=> $b['xMin']);

        $cols = [];
        foreach ($starts as $i => $s) {
            $nextStart = $starts[$i + 1]['xMin'] ?? PHP_FLOAT_MAX;
            $end = $ends[$i] ?? null;
            $cols[] = [
                'x0' => $s['xMin'] - 6,
                'x1' => $end ? max($end['xMax'] + 6, $s['xMax']) : $nextStart - 6,
                'start' => $s['text'],
                'end' => $end['text'] ?? ($starts[$i + 1]['text'] ?? null),
            ];
        }

        // --- Legend (KOD / NAMA KURSUS) and bottom of the grid ------------------
        $kod = null;
        foreach ($words as $w) {
            if (strtoupper($w['text']) === 'KOD' && $w['cy'] > end($days)['y']) {
                $kod = $w;
                break;
            }
        }
        $lastGap = count($days) > 1 ? $days[count($days) - 1]['y'] - $days[count($days) - 2]['y'] : 40;
        $gridBottom = $kod ? min($kod['yMin'] - 2, end($days)['y'] + $lastGap / 2) : end($days)['y'] + $lastGap / 2;

        // Row bands: halfway between neighbouring day labels.
        foreach ($days as $i => &$d) {
            $prev = $days[$i - 1]['y'] ?? null;
            $next = $days[$i + 1]['y'] ?? null;
            $d['y0'] = $prev !== null ? ($prev + $d['y']) / 2 : $d['y'] - $lastGap / 2;
            $d['y1'] = $next !== null ? ($next + $d['y']) / 2 : $gridBottom;
        }
        unset($d);

        // --- Bucket every grid word into its (day, column) cell -----------------
        $buckets = [];
        foreach ($words as $w) {
            if ($w['xMin'] <= $labelRight || $w['cy'] <= $gridTop - $lastGap / 2 || $w['cy'] >= $gridBottom) {
                continue;
            }
            $ci = null;
            foreach ($cols as $i => $c) {
                if ($w['cx'] >= $c['x0'] && $w['cx'] <= $c['x1']) {
                    $ci = $i;
                    break;
                }
            }
            $di = null;
            foreach ($days as $i => $d) {
                if ($w['cy'] >= $d['y0'] && $w['cy'] < $d['y1']) {
                    $di = $i;
                    break;
                }
            }
            if ($ci !== null && $di !== null) {
                $buckets[$di][$ci][] = $w;
            }
        }

        $cells = [];
        foreach ($buckets as $di => $byCol) {
            foreach ($byCol as $ci => $cellWords) {
                $lines = array_map(
                    fn ($line) => implode(' ', array_column($this->sortByX($line), 'text')),
                    $this->clusterByY($cellWords, 3)
                );
                if (! preg_match('/^([A-Z0-9]+)\s*(?:\(([A-Z]+)\))?/i', $lines[0] ?? '', $m)) {
                    continue;
                }
                $cells[] = [
                    $days[$di]['day'],
                    $cols[$ci]['start'],
                    $cols[$ci]['end'],
                    $m[1],
                    $m[2] ?? '',
                    $lines[1] ?? null,
                    $lines[2] ?? null,
                ];
            }
        }

        return ['legend' => $kod ? $this->legend($words, $kod) : [], 'cells' => $cells];
    }

    /** @return array<string, string> */
    private function legend(array $words, array $kod): array
    {
        $below = array_filter($words, fn ($w) => $w['yMin'] > $kod['yMax'] - 1 && $w['xMin'] >= $kod['xMin'] - 20);
        $legend = [];

        foreach ($this->clusterByY(array_values($below), 3) as $line) {
            $line = $this->sortByX($line);
            $first = strtoupper($line[0]['text']);
            if (in_array($first, ['JUMLAH', 'TANDATANGAN', 'NAMA:', 'TARIKH:'], true)) {
                break;
            }
            if (! preg_match('/^[A-Z]{2,4}\d{0,6}[A-Z]?$/', $first) || count($line) < 2) {
                continue;
            }
            $rest = array_slice($line, 1);
            if (preg_match('/^\d+(\.\d+)?$/', end($rest)['text'])) {
                array_pop($rest); // JAM KREDIT
            }
            // Stop at a big horizontal gap (e.g. the right-hand signature block on the same line).
            $nameWords = [];
            $prevX = $line[0]['xMax'];
            foreach ($rest as $w) {
                if ($w['xMin'] - $prevX > 250) {
                    break;
                }
                $nameWords[] = $w['text'];
                $prevX = $w['xMax'];
            }
            if ($nameWords) {
                $legend[$first] = implode(' ', $nameWords);
            }
        }

        return $legend;
    }

    /** @return array<int, array{text: string, xMin: float, xMax: float, yMin: float, yMax: float, cx: float, cy: float}> */
    private function words(string $pdfPath): array
    {
        $cmd = 'pdftotext -f 1 -l 1 -bbox ' . escapeshellarg($pdfPath) . ' - 2>/dev/null';
        $html = shell_exec($cmd);
        if (! is_string($html) || $html === '') {
            throw new RuntimeException('Tak dapat baca PDF tu.');
        }

        preg_match_all('/<word xMin="([\d.]+)" yMin="([\d.]+)" xMax="([\d.]+)" yMax="([\d.]+)">(.*?)<\/word>/s', $html, $m, PREG_SET_ORDER);

        return array_map(fn ($r) => [
            'text' => html_entity_decode($r[5], ENT_QUOTES | ENT_XML1, 'UTF-8'),
            'xMin' => (float) $r[1], 'yMin' => (float) $r[2], 'xMax' => (float) $r[3], 'yMax' => (float) $r[4],
            'cx' => ((float) $r[1] + (float) $r[3]) / 2, 'cy' => ((float) $r[2] + (float) $r[4]) / 2,
        ], $m);
    }

    /** Group words into lines by vertical position, top to bottom. */
    private function clusterByY(array $words, float $tol): array
    {
        usort($words, fn ($a, $b) => $a['cy'] <=> $b['cy']);
        $lines = [];
        foreach ($words as $w) {
            $last = count($lines) - 1;
            if ($last >= 0 && abs($lines[$last][0]['cy'] - $w['cy']) <= $tol) {
                $lines[$last][] = $w;
            } else {
                $lines[] = [$w];
            }
        }

        return $lines;
    }

    private function sortByX(array $words): array
    {
        usort($words, fn ($a, $b) => $a['xMin'] <=> $b['xMin']);

        return $words;
    }
}
