<?php

namespace App\Console\Commands;

use App\Http\Controllers\ChatbotController;
use App\Models\ChatMessage;
use Illuminate\Console\Command;

/**
 * Links past student questions to the knowledge-base entry that best
 * matches them (same search the chatbot uses), so the admin Analytics
 * page can show the most asked topics for older messages too.
 * Safe to re-run: only untagged messages are checked.
 */
class TagChatTopics extends Command
{
    protected $signature = 'chat:tag-topics';

    protected $description = 'Match untagged student chat messages to their knowledge-base topic';

    public function handle(ChatbotController $chatbot): int
    {
        $tagged = 0;

        ChatMessage::where('sender', 'user')
            ->whereNull('knowledge_base_id')
            ->orderBy('id')
            ->chunkById(200, function ($messages) use ($chatbot, &$tagged) {
                foreach ($messages as $m) {
                    $best = $chatbot->searchKnowledgeBase((string) $m->message, 1)->first();
                    if ($best) {
                        $m->forceFill(['knowledge_base_id' => $best->id])->saveQuietly();
                        $tagged++;
                    }
                }
            });

        $this->info("Tagged {$tagged} message(s) with a knowledge-base topic.");

        return self::SUCCESS;
    }
}
