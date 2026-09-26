<?php

namespace App\Http\Controllers;

use App\Models\ClassSchedule;
use App\Services\TimetablePdfParser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ClassScheduleController extends Controller
{
    /** Longest believable single class block (real timetables max out around 3h). */
    private const MAX_PLAUSIBLE_CLASS_MINUTES = 240;

    public function index(Request $request)
    {
        $schedules = $request->user()->classSchedules()
            ->get()
            ->sortBy(fn (ClassSchedule $s) => array_search($s->day_of_week, ClassSchedule::DAYS) * 1440 + $this->toMinutes($s->start_time))
            ->values()
            ->map(fn (ClassSchedule $s) => $this->toRaw($s));

        return view('timetable', [
            'user' => $request->user(),
            'schedules' => $schedules,
            'days' => ClassSchedule::DAYS,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateSchedule($request);

        $schedule = $request->user()->classSchedules()->create($data);

        return response()->json(['success' => true, 'schedule' => $this->toRaw($schedule)]);
    }

    public function update(Request $request, ClassSchedule $schedule)
    {
        abort_unless($schedule->user_id === $request->user()->id, 403);

        $data = $this->validateSchedule($request);

        $schedule->update($data);

        return response()->json(['success' => true, 'schedule' => $this->toRaw($schedule)]);
    }

    public function destroy(Request $request, ClassSchedule $schedule)
    {
        abort_unless($schedule->user_id === $request->user()->id, 403);

        $schedule->delete();

        return response()->json(['success' => true]);
    }

    public function destroyAll(Request $request)
    {
        $request->user()->classSchedules()->delete();

        return response()->json(['success' => true]);
    }

    public function bulkDestroy(Request $request)
    {
        $data = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);

        $request->user()->classSchedules()
            ->whereIn('id', $data['ids'])
            ->delete();

        return response()->json(['success' => true]);
    }

    /**
     * AI timetable capture — step 1 of 2: read the photo and return a PREVIEW.
     * Nothing is saved here. The student reviews/edits the result in the modal
     * and then confirms, which calls bulkStore().
     *
     * Design: the model only TRANSCRIBES (one entry per occupied grid cell +
     * the KOD/NAMA KURSUS legend). Everything that needs reasoning — merging
     * adjacent cells into one class, working out start/end times, resolving a
     * course code to its name, sanity checks — is done in plain PHP below, so
     * it's deterministic instead of depending on a small vision model getting
     * it right. Earlier versions asked the model to do all of that at once and
     * it kept shifting times and inventing course names.
     */
    public function aiCapture(Request $request)
    {
        $request->validate([
            'photo' => 'required|file|mimes:jpg,jpeg,png,webp,gif,bmp,heic,pdf|max:6144', // 6MB
        ], [
            'photo.mimes' => 'Fail mesti gambar (JPG/PNG) atau PDF jadual.',
            'photo.max' => 'Fail terlalu besar (maksimum 6MB).',
        ]);

        $file = $request->file('photo');
        $isPdf = strtolower($file->getClientOriginalExtension()) === 'pdf' || $file->getMimeType() === 'application/pdf';

        if ($isPdf) {
            // Official PDF timetable: read its text layer directly — exact, no AI.
            if (! TimetablePdfParser::isAvailable()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Server belum boleh baca PDF (pdftotext tiada). Upload gambar/screenshot jadual buat masa ni.',
                ], 422);
            }

            try {
                $parsed = (new TimetablePdfParser())->parse($file->getRealPath());
            } catch (\RuntimeException $e) {
                return response()->json(['success' => false, 'error' => $e->getMessage()], 422);
            }
        } elseif (config('services.gemini.key')) {
            // Photo + Gemini key set: Gemini reads dense tables far better than the
            // small Groq vision model (which kept misreading codes and names).
            [$rawContent, $geminiError] = $this->geminiTranscribe($file->getRealPath(), $file->getMimeType());

            if ($rawContent === null) {
                return response()->json(['success' => false, 'error' => $geminiError], 422);
            }

            $parsed = $this->extractJson($rawContent) ?? [];
        } else {
            $dataUri = 'data:' . $file->getMimeType() . ';base64,' . base64_encode(file_get_contents($file->getRealPath()));

            $response = Http::withToken(config('services.groq.key'))
                ->timeout(110)
                ->post('https://api.groq.com/openai/v1/chat/completions', $this->visionPayload($dataUri));

            if (! $response || $response->failed()) {
                Log::error('Groq vision API error (timetable)', [
                    'status' => $response?->status(),
                    'body' => $response?->body(),
                ]);

                return response()->json([
                    'success' => false,
                    'error' => 'AI tak dapat proses gambar tu sekarang. Cuba lagi sekejap.',
                ], 422);
            }

            $rawContent = $response->json('choices.0.message.content');
            $finishReason = $response->json('choices.0.finish_reason');

            Log::info('Groq vision raw response (timetable)', [
                'finish_reason' => $finishReason,
                'raw' => Str::limit((string) $rawContent, 4000),
            ]);

            if ($finishReason === 'length') {
                Log::warning('Groq vision response for timetable was cut off by max_completion_tokens');
            }

            $parsed = $this->extractJson($rawContent) ?? [];
        }

        // Legend: course code -> full course name, straight from the KOD/NAMA KURSUS table.
        $legend = [];
        foreach ((array) ($parsed['legend'] ?? []) as $k => $row) {
            // Accept whatever shape the model used: ["CODE","NAME"], {"code":..,"name":..}, or {"CODE":"NAME"}.
            if (is_string($row) && is_string($k)) {
                [$rawCode, $rawName] = [$k, $row];
            } elseif (is_array($row) && array_is_list($row)) {
                [$rawCode, $rawName] = [$row[0] ?? '', $row[1] ?? ''];
            } elseif (is_array($row)) {
                $rawCode = $row['code'] ?? $row['kod'] ?? $row['CODE'] ?? $row['KOD'] ?? '';
                $rawName = $row['name'] ?? $row['nama'] ?? $row['course'] ?? $row['NAME'] ?? $row['NAMA KURSUS'] ?? '';
            } else {
                continue;
            }
            $code = $this->normalizeCode($rawCode);
            $name = trim((string) $rawName);
            if ($code !== '' && $name !== '' && ! in_array(strtoupper($name), ['JUMLAH', 'TOTAL'], true)) {
                $legend[$code] = $name;
            }
        }

        // The AI now returns the grid position-by-position ("rows"): every day as
        // an array with one entry per time column, null for empty. Enumerating
        // every column stops it from blurring neighbouring cells together.
        // Convert that to the flat cell list the rest of this method uses.
        $shakyDays = [];
        if (isset($parsed['rows']) && is_array($parsed['rows'])) {
            [$parsed['cells'], $shakyDays] = $this->gridToCells($parsed['rows'], (array) ($parsed['headers'] ?? []));
        }

        // Cells: one per occupied grid cell -> [day, start, end, code, type, lecturer, room].
        $cells = [];
        foreach ((array) ($parsed['cells'] ?? []) as $row) {
            if (! is_array($row) || count($row) < 4) {
                continue;
            }

            $day = $this->parseDay($row[0] ?? null);
            $start = $this->parseHour($row[1] ?? null);
            $end = $this->parseHour($row[2] ?? null);
            $code = $this->normalizeCode($row[3] ?? '');
            $type = strtoupper(trim((string) ($row[4] ?? '')));
            if (($type === '' || $type === 'NULL') && preg_match('/\(([A-Z]{1,3})\)/i', (string) ($row[3] ?? ''), $tm)) {
                $type = strtoupper($tm[1]); // model left "(L)" inside the code instead
            }
            $type = $type === 'NULL' ? '' : preg_replace('/[^A-Z]/', '', $type);

            if (! $day || ! $start || ! $code) {
                continue;
            }

            if (! $end || $this->toMinutes($end) <= $this->toMinutes($start)) {
                $end = sprintf('%02d:%s', ((int) substr($start, 0, 2)) + 1, substr($start, 3, 2)); // assume a 1-hour column
            }

            $cells[] = [
                'day_of_week' => $day,
                'start_time' => $start,
                'end_time' => $end,
                'code' => $code,
                'type' => $type,
                'lecturer' => $this->cleanText($row[5] ?? null),
                'room' => $this->cleanText($row[6] ?? null),
            ];
        }

        if (count($cells) === 0) {
            return response()->json([
                'success' => false,
                'error' => 'Tak dapat kesan jadual kelas dalam gambar tu. Cuba gambar yang lebih jelas, atau isi manual.',
            ], 422);
        }

        // The model often misreads one or two characters of a code (DFK50463 for
        // DFP50463). Snap each code to the closest legend code before merging, so
        // the course name can still be found.
        foreach ($cells as &$cell) {
            [$cell['code'], $cell['code_fixed']] = $this->matchLegendCode($cell['code'], array_keys($legend));
        }
        unset($cell);

        $classes = $this->mergeCells($cells);

        // Turn code + legend into the subject, and flag anything that looks off
        // so the preview highlights it for the student to double-check.
        foreach ($classes as &$c) {
            $name = $legend[$c['code']] ?? null;
            // Keep the type exactly as printed in the timetable: (L), (P), (TU), (O)...
            $typeLabel = $c['type'] !== '' ? " ({$c['type']})" : '';
            // Subject shown to the student = the course NAME, not the code.
            $c['subject'] = Str::limit(
                $name ? Str::title(mb_strtolower($name)) . $typeLabel : $c['code'] . $typeLabel,
                150,
                ''
            );

            $warnings = [];
            if (! $name) {
                $warnings[] = "Nama kursus untuk kod {$c['code']} tak jumpa — sila taip nama subjek.";
            } elseif (! empty($c['code_fixed'])) {
                $warnings[] = "AI baca kod kurang jelas, dipadankan ke {$c['code']} — pastikan subjek betul.";
            }
            $c['warnings'] = $warnings;
            unset($c['code'], $c['type'], $c['code_fixed']);
        }
        unset($c);

        // A real polytechnic class is never more than ~4 consecutive hours. A longer
        // merged block usually means the model repeated one cell's text across
        // columns that held something else (idea from commit 516750f — flagged for
        // review here instead of dropping the whole day, since the student checks
        // the preview anyway).
        foreach ($classes as &$c) {
            if ($this->toMinutes($c['end_time']) - $this->toMinutes($c['start_time']) > self::MAX_PLAUSIBLE_CLASS_MINUTES) {
                $c['warnings'][] = 'Kelas ni panjang luar biasa (lebih 4 jam) — semak masa tamat.';
            }
        }
        unset($c);

        foreach ($classes as &$c) {
            if (in_array($c['day_of_week'], $shakyDays, true)) {
                $c['warnings'][] = 'Bilangan lajur baris hari ni tak sama dengan jadual — semak masa.';
            }
        }
        unset($c);

        // Overlapping classes on the same day = the model misread the grid somewhere.
        // Flag both sides instead of rejecting everything, since the student reviews anyway.
        foreach ($this->overlappingIndexes($classes) as $i) {
            $classes[$i]['warnings'][] = 'Bertindih masa dengan kelas lain — semak masa.';
        }

        return response()->json(['success' => true, 'classes' => array_values($classes)]);
    }

    /**
     * AI timetable capture — step 2 of 2: save the classes the student confirmed.
     */
    public function bulkStore(Request $request)
    {
        $data = $request->validate([
            'classes' => 'required|array|min:1|max:60',
            'classes.*.subject' => 'required|string|max:150',
            'classes.*.day_of_week' => 'required|string|in:' . implode(',', ClassSchedule::DAYS),
            'classes.*.start_time' => 'required|date_format:H:i',
            'classes.*.end_time' => 'required|date_format:H:i',
            'classes.*.room' => 'nullable|string|max:100',
            'classes.*.lecturer' => 'nullable|string|max:150',
        ]);

        $created = [];

        foreach ($data['classes'] as $c) {
            $schedule = $request->user()->classSchedules()->create([
                'subject' => $c['subject'],
                'day_of_week' => $c['day_of_week'],
                'start_time' => $c['start_time'],
                'end_time' => $c['end_time'],
                'room' => $c['room'] ?? null,
                'lecturer' => $c['lecturer'] ?? null,
            ]);

            $created[] = $this->toRaw($schedule);
        }

        return response()->json(['success' => true, 'schedules' => $created]);
    }

    /**
     * The Groq request payload. The model is asked to transcribe only — no
     * merging, no time maths, no guessing course names.
     */
    private function visionPrompt(): string
    {
        return 'You are an exact OCR transcriber for a Malaysian polytechnic class timetable (JADUAL WAKTU KELAS). Copy text only; never guess, merge or invent. '
            . 'LAYOUT: the main table is a grid. The header row lists one-hour time columns; each column header shows a start time and an end time (e.g. "8:00" and "9:00", "2.00" and "3:00"). '
            . 'Each following row is one day, labelled in the first column (ISNIN, SELASA, RABU, KHAMIS, JUMAAT, maybe SABTU, AHAD). '
            . 'Each non-empty cell has up to 3 lines: line 1 = course code with its type in brackets exactly as printed (e.g. "DFK50083(L)", "MPU21072(TU)", "PA(O)"), line 2 = lecturer(s), line 3 = room. '
            . 'STEPS: (1) Read the header row and list EVERY time column left to right as [start, end]. Count them. '
            . '(2) For EACH day row, go column by column from left to right and output EXACTLY one entry per time column - the same count as the headers. Use null for an empty cell. '
            . 'Adjacent cells in the same row often look almost identical but can differ ONLY in the bracketed type, e.g. "MPU21072(L)" in one column then "MPU21072(TU)" in the next. Read the brackets of EVERY cell separately; never copy them from the neighbour. '
            . '(3) Transcribe the separate KOD / NAMA KURSUS table under the grid. '
            . 'Copy characters exactly (codes are usually 3 letters + 5 digits like DFP50463; rooms look like APDV1, CNW3, DK-JTMK, M204-JTMK). '
            . 'Reply with ONLY this JSON, no markdown: '
            . '{"headers":[["8:00","9:00"],["9:00","10:00"],...],'
            . '"rows":{"ISNIN":["CODE(TYPE) | LECTURER | ROOM" or null, ...one per header...],"SELASA":[...],...},'
            . '"legend":[["CODE","COURSE NAME"],...]} '
            . 'Example row entry: "DFK50083(L) | AINIE HAYATI,AFIFAH | APDV1". '
            . 'If the image is not a class timetable, return {"headers":[],"rows":{},"legend":[]}.';
    }

    /**
     * Small screenshots make the tiny cell text hard to read. Upscale anything
     * narrower than 2400px (GD, sharp-ish resampling) and send it as PNG.
     * Falls back to the original bytes if GD can't open the file (e.g. HEIC).
     *
     * @return array{mime_type: string, data: string}
     */
    private function prepareImage(string $path, string $mime): array
    {
        $original = ['mime_type' => $mime, 'data' => base64_encode(file_get_contents($path))];

        if (! function_exists('imagecreatefromstring')) {
            return $original;
        }

        $img = @imagecreatefromstring(file_get_contents($path));
        if (! $img) {
            return $original;
        }

        $w = imagesx($img);
        $h = imagesy($img);
        $target = 2400;

        if ($w >= $target) {
            imagedestroy($img);

            return $original;
        }

        $scale = $target / $w;
        $big = imagescale($img, $target, (int) round($h * $scale), IMG_BICUBIC);
        imagedestroy($img);
        if (! $big) {
            return $original;
        }

        ob_start();
        imagepng($big, null, 6);
        $png = ob_get_clean();
        imagedestroy($big);

        return ['mime_type' => 'image/png', 'data' => base64_encode($png)];
    }

    /**
     * Convert the AI's position-by-position grid into flat cells.
     *
     * @return array{0: array<int, array<int, ?string>>, 1: array<int, string>} [cells, days whose column count looked wrong]
     */
    private function gridToCells(array $rows, array $headers): array
    {
        $cells = [];
        $shaky = [];
        $n = count($headers);

        foreach ($rows as $dayLabel => $entries) {
            $day = $this->parseDay($dayLabel);
            if (! $day || ! is_array($entries)) {
                continue;
            }
            if ($n > 0 && count($entries) !== $n) {
                $shaky[] = $day;
                Log::warning('Timetable AI grid: column count mismatch', ['day' => $day, 'expected' => $n, 'got' => count($entries)]);
            }

            foreach (array_values($entries) as $i => $entry) {
                if ($entry === null || trim((string) (is_array($entry) ? implode('|', $entry) : $entry)) === '' || ! isset($headers[$i])) {
                    continue;
                }
                $parts = is_array($entry)
                    ? array_values($entry)
                    : array_map('trim', explode('|', (string) $entry));

                $header = (array) $headers[$i];
                $cells[] = [
                    $dayLabel,
                    $header[0] ?? null,
                    $header[1] ?? null,
                    $parts[0] ?? '',  // "CODE(TYPE)" - type is split out by the caller
                    '',
                    $parts[1] ?? null,
                    $parts[2] ?? null,
                ];
            }
        }

        return [$cells, array_values(array_unique($shaky))];
    }

    /**
     * Send the timetable photo to Google Gemini and return its raw JSON text,
     * or null on failure. Key: GEMINI_API_KEY (free from aistudio.google.com).
     * Model: GEMINI_MODEL (optional; defaults to gemini-flash-latest).
     *
     * @return array{0: ?string, 1: ?string} [raw JSON text, error message for the user]
     */
    private function geminiTranscribe(string $path, string $mime): array
    {
        // Google retires dated model ids regularly (gemini-2.5-flash is already
        // deprecated), so default to the "-latest" alias and fall back through a
        // couple of others if one returns 404.
        $models = array_values(array_unique(array_filter([
            config('services.gemini.model'),
            'gemini-flash-latest',
            'gemini-flash-lite-latest',
        ])));

        $body = [
            'systemInstruction' => ['parts' => [['text' => $this->visionPrompt()]]],
            'contents' => [[
                'role' => 'user',
                'parts' => [
                    ['inline_data' => $this->prepareImage($path, $mime)],
                    ['text' => 'Transcribe this timetable following the steps exactly.'],
                ],
            ]],
            'generationConfig' => [
                'temperature' => 0,
                'responseMimeType' => 'application/json',
                'maxOutputTokens' => 8192,
            ],
        ];

        // 404 = model id retired -> try the next model.
        // 500/503 = Google's side is overloaded (common on the free tier) ->
        // retry once after a short pause, then move on to the next (lighter) model.
        $response = null;
        foreach ($models as $model) {
            for ($attempt = 1; $attempt <= 2; $attempt++) {
                $response = Http::timeout(60)
                    ->withHeaders(['x-goog-api-key' => config('services.gemini.key')])
                    ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent", $body);

                if (! in_array($response->status(), [500, 503], true)) {
                    break;
                }
                Log::warning("Gemini {$model} busy ({$response->status()}), attempt {$attempt}");
                sleep(2);
            }

            if (! in_array($response->status(), [404, 500, 503], true)) {
                break;
            }
        }

        if (! $response || $response->failed()) {
            $status = $response?->status();
            Log::error('Gemini vision API error (timetable)', [
                'status' => $status,
                'body' => Str::limit((string) $response?->body(), 2000),
            ]);

            $reason = match (true) {
                $status === 400 || $status === 403 => 'API key Gemini tak sah — semak GEMINI_API_KEY kat Render.',
                $status === 429 => 'Had penggunaan AI percuma dah penuh. Cuba lagi selepas seminit.',
                $status === 404 => 'Model Gemini tak dijumpai — set GEMINI_MODEL kat Render.',
                $status === 500 || $status === 503 => 'Server AI Google tengah sibuk. Cuba lagi dalam seminit.',
                default => 'AI tak dapat proses gambar tu sekarang (ralat ' . ($status ?? '?') . ').',
            };

            return [null, $reason . ' Atau upload PDF jadual.'];
        }

        $text = collect($response->json('candidates.0.content.parts') ?? [])
            ->reject(fn ($p) => ! empty($p['thought']))
            ->pluck('text')->filter()->implode('');

        Log::info('Gemini vision raw response (timetable)', [
            'finish_reason' => $response->json('candidates.0.finishReason'),
            'raw' => Str::limit($text, 4000),
        ]);

        return $text !== ''
            ? [$text, null]
            : [null, 'AI tak pulangkan apa-apa untuk gambar tu. Cuba gambar lain atau upload PDF jadual.'];
    }

    /**
     * The Groq request payload (fallback when no Gemini key is configured).
     */
    private function visionPayload(string $dataUri): array
    {
        $systemPrompt = $this->visionPrompt();

        return [
            'model' => 'qwen/qwen3.8-27b',
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => [
                    ['type' => 'text', 'text' => 'Transcribe this timetable following the steps exactly.'],
                    ['type' => 'image_url', 'image_url' => ['url' => $dataUri]],
                ]],
            ],
            // See git history for why these values: low temperature without being
            // fully greedy (which repeat-locked on one name), low reasoning effort to
            // stay under this Groq plan's 1000 output tokens/minute pre-flight check,
            // and enough completion budget that hidden reasoning doesn't eat the answer.
            // The compact array-per-cell format keeps the visible answer small.
            'temperature' => 0.15,
            'response_format' => ['type' => 'json_object'],
            'reasoning_effort' => 'low',
            'reasoning_format' => 'hidden',
            'max_completion_tokens' => 8000,
        ];
    }

    /**
     * Merge back-to-back cells of the same class (same day, code, type, room,
     * lecturer, and the next cell starting exactly where the previous ended)
     * into one class spanning the full time range.
     *
     * @param array<int, array<string, mixed>> $cells
     * @return array<int, array<string, mixed>>
     */
    private function mergeCells(array $cells): array
    {
        usort($cells, function ($a, $b) {
            return array_search($a['day_of_week'], ClassSchedule::DAYS, true) <=> array_search($b['day_of_week'], ClassSchedule::DAYS, true)
                ?: $this->toMinutes($a['start_time']) <=> $this->toMinutes($b['start_time']);
        });

        // Lecturer names are compared order-insensitively ("PUTERI,JAMALIAH" == "JAMALIAH,PUTERI").
        $lect = function (?string $l) {
            $names = array_map('trim', explode(',', strtoupper((string) $l)));
            sort($names);

            return implode(',', $names);
        };
        $key = fn (array $c) => implode('|', [$c['day_of_week'], $c['code'], $c['type'], $c['room'], $lect($c['lecturer'])]);
        // code_fixed must survive a merge if any merged cell was corrected.
        $merged = [];

        foreach ($cells as $cell) {
            $lastIndex = count($merged) - 1;
            $last = $merged[$lastIndex] ?? null;

            if ($last && $key($last) === $key($cell)) {
                if ($last['end_time'] === $cell['start_time']) {
                    $merged[$lastIndex]['end_time'] = $cell['end_time'];
                    $merged[$lastIndex]['code_fixed'] = ! empty($last['code_fixed']) || ! empty($cell['code_fixed']);
                    continue;
                }
                if ($last['start_time'] === $cell['start_time']) {
                    continue; // exact duplicate cell from the model
                }
            }

            $merged[] = $cell;
        }

        return $merged;
    }

    /**
     * @param array<int, array<string, mixed>> $classes
     * @return array<int, int>
     */
    private function overlappingIndexes(array $classes): array
    {
        $bad = [];
        $n = count($classes);

        for ($i = 0; $i < $n; $i++) {
            for ($j = $i + 1; $j < $n; $j++) {
                if ($classes[$i]['day_of_week'] !== $classes[$j]['day_of_week']) {
                    continue;
                }
                if ($this->toMinutes($classes[$i]['start_time']) < $this->toMinutes($classes[$j]['end_time'])
                    && $this->toMinutes($classes[$j]['start_time']) < $this->toMinutes($classes[$i]['end_time'])) {
                    $bad[$i] = $i;
                    $bad[$j] = $j;
                }
            }
        }

        return array_values($bad);
    }

    /**
     * Snap a possibly-misread code to the nearest legend code (edit distance <= 2,
     * and a unique best match). Returns [code, wasCorrected].
     *
     * @param array<int, string> $legendCodes
     * @return array{0: string, 1: bool}
     */
    private function matchLegendCode(string $code, array $legendCodes): array
    {
        if ($code === '' || in_array($code, $legendCodes, true) || count($legendCodes) === 0) {
            return [$code, false];
        }

        $best = null;
        $bestDist = PHP_INT_MAX;
        $tie = false;

        foreach ($legendCodes as $candidate) {
            $d = levenshtein($code, $candidate);
            if ($d < $bestDist) {
                [$best, $bestDist, $tie] = [$candidate, $d, false];
            } elseif ($d === $bestDist) {
                $tie = true;
            }
        }

        return ($best !== null && $bestDist <= 2 && ! $tie) ? [$best, true] : [$code, false];
    }

    /** "JUMAAT" / "Fri" / "friday" -> "Friday" */
    private function parseDay(mixed $label): ?string
    {
        $l = mb_strtoupper(trim((string) $label));

        return match (true) {
            in_array($l, ['MON', 'MONDAY', 'ISNIN'], true) => 'Monday',
            in_array($l, ['TUE', 'TUESDAY', 'SELASA'], true) => 'Tuesday',
            in_array($l, ['WED', 'WEDNESDAY', 'RABU'], true) => 'Wednesday',
            in_array($l, ['THU', 'THURSDAY', 'KHAMIS'], true) => 'Thursday',
            in_array($l, ['FRI', 'FRIDAY', 'JUMAAT'], true) => 'Friday',
            in_array($l, ['SAT', 'SATURDAY', 'SABTU'], true) => 'Saturday',
            in_array($l, ['SUN', 'SUNDAY', 'AHAD'], true) => 'Sunday',
            default => null,
        };
    }

    /**
     * Column-header time as printed ("8:00", "2.00", "12:00", "3 PM") -> "HH:MM" 24h.
     * Timetable headers are 12-hour without AM/PM, and classes run ~8am-6pm,
     * so 1-7 are treated as afternoon.
     */
    private function parseHour(mixed $raw): ?string
    {
        if (! preg_match('/(\d{1,2})(?:[:.](\d{2}))?\s*(am|pm)?/i', trim((string) $raw), $m)) {
            return null;
        }

        $h = (int) $m[1];
        $min = isset($m[2]) && $m[2] !== '' ? (int) $m[2] : 0;
        $ampm = strtolower($m[3] ?? '');

        if ($ampm === 'pm' && $h < 12) {
            $h += 12;
        } elseif ($ampm === '' && $h >= 1 && $h <= 7) {
            $h += 12;
        }

        if ($h > 23 || $min > 59) {
            return null;
        }

        return sprintf('%02d:%02d', $h, $min);
    }

    /** "dfk 50083(L)" -> "DFK50083" (type in brackets is read separately). */
    private function normalizeCode(mixed $raw): string
    {
        $code = strtoupper((string) $raw);
        $code = preg_replace('/\(.*$/', '', $code);

        return preg_replace('/[^A-Z0-9]/', '', $code);
    }

    private function cleanText(mixed $raw): ?string
    {
        $t = trim(preg_replace('/\s+/', ' ', (string) $raw));

        return ($t === '' || strtolower($t) === 'null') ? null : Str::limit($t, 100, '');
    }

    private function validateSchedule(Request $request): array
    {
        $data = $request->validate([
            'subject' => 'required|string|max:150',
            'day_of_week' => 'required|string|in:' . implode(',', ClassSchedule::DAYS),
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'room' => 'nullable|string|max:100',
            'lecturer' => 'nullable|string|max:150',
        ]);

        return $data;
    }

    private function toRaw(ClassSchedule $schedule): array
    {
        return [
            'id' => $schedule->id,
            'subject' => $schedule->subject,
            'day_of_week' => $schedule->day_of_week,
            'start_time' => $schedule->start_time,
            'end_time' => $schedule->end_time,
            'room' => $schedule->room,
            'lecturer' => $schedule->lecturer,
        ];
    }

    private function toMinutes(?string $time): int
    {
        if (! $this->isValidTime($time)) {
            return 0;
        }

        [$h, $m] = explode(':', $time);

        return ((int) $h) * 60 + (int) $m;
    }

    private function isValidTime(?string $time): bool
    {
        return is_string($time) && preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $time) === 1;
    }

    private function extractJson(?string $raw): ?array
    {
        if (! $raw) {
            return null;
        }

        $decoded = json_decode($raw, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        if (preg_match('/\{.*\}/s', $raw, $m)) {
            $decoded = json_decode($m[0], true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return null;
    }
}
