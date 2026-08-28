<?php

namespace Database\Seeders;

use App\Models\Information;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FaqDatabaseSeeder extends Seeder
{
    /**
     * Seed the `information` and `knowledge_bases` tables from the
     * bundled FAQ data (originally exported as faqs.sql).
     *
     * The source SQL held everything in one flat `faqs` table. Here it is
     * split into topics (-> information) each with its own set of
     * question/answer entries (-> knowledge_bases), matching the
     * `information` 1-to-many `knowledge_bases` schema.
     */
    public function run(): void
    {
        $topics = require database_path('seeders/data/faq_data.php');

        foreach ($topics as $topic) {
            $information = Information::create([
                'main_topic' => $topic['main_topic'],
                'description' => $topic['description'],
            ]);

            $now = now();

            $rows = collect($topic['entries'])->map(function (array $entry) use ($information, $now) {
                return [
                    'information_id' => $information->id,
                    'intent' => $entry['intent'],
                    'question' => $entry['question'],
                    'answer' => $entry['answer'],
                    'category' => $entry['category'] !== '' ? $entry['category'] : null,
                    'keywords' => $entry['keywords'] !== '' ? $entry['keywords'] : null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            })->all();

            // Bulk insert in chunks to stay well under the DB's max
            // placeholder/packet limits for topics with many entries.
            foreach (array_chunk($rows, 100) as $chunk) {
                DB::table('knowledge_bases')->insert($chunk);
            }

            $this->command?->info(sprintf(
                'Seeded "%s" with %d knowledge base entries.',
                $information->main_topic,
                count($rows)
            ));
        }
    }
}
