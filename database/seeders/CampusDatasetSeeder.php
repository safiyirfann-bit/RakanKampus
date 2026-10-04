<?php

namespace Database\Seeders;

use App\Models\Information;
use App\Models\KnowledgeBase;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Imports the RakanKampus campus datasets (database/seeders/data/campus/*.csv)
 * into `information` (one row per main_topic) and `knowledge_bases`
 * (one row per question/answer).
 *
 * CSV columns: id, main_topic, intent, question, answer, category, keywords,
 *              question_ms, question_en, question_zh, question_ta (optional translations),
 *              location_name, latitude, longitude (optional map pin shown in the chat)
 *
 * Safe to run again and again (it runs on every deploy): a topic is matched by
 * its main_topic and an entry by topic + intent, so re-running updates the
 * existing rows instead of creating duplicates. To change the chatbot's data,
 * edit the CSV and redeploy — or add a new CSV to the folder.
 *
 *   php artisan db:seed --class=CampusDatasetSeeder --force
 */
class CampusDatasetSeeder extends Seeder
{
    public function run(): void
    {
        $files = glob(database_path('seeders/data/campus/*.csv')) ?: [];
        sort($files);

        foreach ($files as $file) {
            $rows = $this->readCsv($file);
            if (count($rows) === 0) {
                continue;
            }

            $topics = [];
            $created = 0;
            $updated = 0;

            foreach ($rows as $row) {
                $mainTopic = trim($row['main_topic'] ?? '');
                $intent = trim($row['intent'] ?? '');
                $question = trim($row['question'] ?? '');
                $answer = trim($row['answer'] ?? '');

                if ($mainTopic === '' || $intent === '' || $question === '' || $answer === '') {
                    continue;
                }

                $topics[$mainTopic] ??= Information::firstOrCreate(
                    ['main_topic' => $mainTopic],
                    ['description' => $this->describe($mainTopic, $rows)]
                );

                $translations = [];
                if (array_key_exists('question_en', $row)) {
                    foreach (KnowledgeBase::LOCALES as $locale) {
                        $text = trim($row["question_{$locale}"] ?? '');
                        $translations["question_{$locale}"] = $text !== '' ? Str::limit($text, 255, '') : null;
                    }
                }

                // Optional map pin (location_name, latitude, longitude columns). Only set when the CSV
                // has a value, so a pin added by the admin is never wiped by a re-run.
                $location = [];
                if (trim($row['latitude'] ?? '') !== '' && trim($row['longitude'] ?? '') !== '') {
                    $location = [
                        'location_name' => trim($row['location_name'] ?? '') ?: null,
                        'latitude' => (float) $row['latitude'],
                        'longitude' => (float) $row['longitude'],
                    ];
                }

                $entry = KnowledgeBase::updateOrCreate(
                    ['information_id' => $topics[$mainTopic]->id, 'intent' => $intent],
                    $location + $translations + [
                        'question' => Str::limit($question, 255, ''),
                        'answer' => $answer,
                        'category' => trim($row['category'] ?? '') ?: null,
                        'keywords' => Str::limit(trim($row['keywords'] ?? ''), 255, '') ?: null,
                    ]
                );

                $entry->wasRecentlyCreated ? $created++ : $updated++;
            }

            $this->command?->info(sprintf('%s: %d new, %d updated.', basename($file), $created, $updated));
        }
    }

    /** @return array<int, array<string, string>> */
    private function readCsv(string $file): array
    {
        $handle = fopen($file, 'r');
        if (! $handle) {
            return [];
        }

        $header = fgetcsv($handle);
        if (! $header) {
            fclose($handle);

            return [];
        }
        $header = array_map(fn ($h) => strtolower(trim(preg_replace('/^\xEF\xBB\xBF/', '', $h))), $header);

        $rows = [];
        while (($data = fgetcsv($handle)) !== false) {
            if (count($data) === 1 && trim((string) $data[0]) === '') {
                continue; // blank line
            }
            $rows[] = array_combine($header, array_pad(array_slice($data, 0, count($header)), count($header), ''));
        }
        fclose($handle);

        return $rows;
    }

    /** Short description for a new topic: its categories, e.g. "Asasi, Diploma, Ijazah Sarjana Muda". */
    private function describe(string $mainTopic, array $rows): string
    {
        $categories = collect($rows)
            ->where('main_topic', $mainTopic)
            ->pluck('category')
            ->filter()
            ->unique()
            ->implode(', ');

        return $categories !== '' ? "{$mainTopic} — {$categories}" : $mainTopic;
    }
}
