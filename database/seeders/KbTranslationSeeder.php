<?php

namespace Database\Seeders;

use App\Models\Information;
use App\Models\KnowledgeBase;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Fills in the English / Chinese / Tamil (and Malay, when needed) versions of the
 * questions that come from faq_data.php, using database/seeders/data/translations/*.csv.
 *
 * CSV columns: main_topic, intent, question, question_ms, question_en, question_zh, question_ta
 * An entry is matched by its topic + intent + original question. Runs on every deploy;
 * safe to run again and again.
 *
 *   php artisan db:seed --class=KbTranslationSeeder --force
 */
class KbTranslationSeeder extends Seeder
{
    public function run(): void
    {
        $topics = Information::pluck('id', 'main_topic');
        $updated = 0;

        foreach (glob(database_path('seeders/data/translations/*.csv')) ?: [] as $file) {
            $handle = fopen($file, 'r');
            $header = array_map(fn ($h) => strtolower(trim(preg_replace('/^\xEF\xBB\xBF/', '', $h))), fgetcsv($handle) ?: []);

            while (($data = fgetcsv($handle)) !== false) {
                if (count($data) < count($header)) {
                    continue;
                }
                $row = array_combine($header, array_slice($data, 0, count($header)));
                $topicId = $topics[trim($row['main_topic'])] ?? null;
                if (! $topicId) {
                    continue;
                }

                $values = [];
                foreach (KnowledgeBase::LOCALES as $locale) {
                    $text = trim($row["question_{$locale}"] ?? '');
                    $values["question_{$locale}"] = $text !== '' ? Str::limit($text, 255, '') : null;
                }

                $updated += KnowledgeBase::where('information_id', $topicId)
                    ->where('intent', trim($row['intent']))
                    ->where('question', trim($row['question']))
                    ->update($values);
            }
            fclose($handle);
        }

        $this->command?->info("Question translations: {$updated} entries updated.");
    }
}
