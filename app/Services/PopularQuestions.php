<?php

namespace App\Services;

use App\Models\ChatMessage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * "Frequently asked" quick-question chips for the Home page, worked out from
 * what students actually type into the chatbot.
 *
 * - Looks at student messages from the last 60 days.
 * - Groups questions that are the same after normalising (case, punctuation,
 *   extra spaces).
 * - Privacy: a question is only shown if at least MIN_STUDENTS different
 *   students asked it, it's short, and it has no long digit runs (IC / phone /
 *   matric numbers) — so one student's personal question never appears.
 * - Result is cached for an hour; empty slots are filled with default questions.
 */
class PopularQuestions
{
    public const MIN_STUDENTS = 3;

    private const BLOCKED_WORDS = [
        'posisi69', 'seks', 'seksual', 'lucah', 'bogel', 'ngentot',
        'jimak', 'senggama', 'porno', 'sex', 'bodoh', 'babi', 'sial',
    ];

    public const DEFAULTS = [
        'How do I register for courses?',
        'When is the fee payment deadline?',
        'How do I access library resources?',
        "What's the exam timetable?",
    ];

    /**
     * @return array<int, array{text: string, popular: bool}>
     */
    public static function top(int $limit = 4): array
    {
        $popular = Cache::remember('home.popular_questions', now()->addHour(), fn () => self::compute(8));

        $chips = [];
        $seen = [];
        foreach ($popular as $q) {
            if (count($chips) >= $limit) {
                break;
            }
            $chips[] = ['text' => $q, 'popular' => true];
            $seen[self::normalise($q)] = true;
        }

        // Top up with the default questions (translated in the view).
        foreach (self::DEFAULTS as $q) {
            if (count($chips) >= $limit) {
                break;
            }
            if (! isset($seen[self::normalise($q)])) {
                $chips[] = ['text' => $q, 'popular' => false];
            }
        }

        return $chips;
    }

    /**
     * Questions for the sliding rows on the new-chat screen: what students ask most,
     * topped up with short knowledge-base questions from many different topics
     * (so every chip is something the bot can answer). Cached for an hour.
     *
     * @return array<int, array{text: string, topic: ?string}>
     */
    public static function marquee(int $limit = 12): array
    {
        return Cache::remember('chat.marquee_questions.' . $limit, now()->addHour(), function () use ($limit) {
            $out = [];
            $seen = [];
            foreach (self::compute(6) as $q) {
                $out[] = ['text' => $q, 'topic' => null];
                $seen[self::normalise($q)] = true;
            }

            $kb = \App\Models\KnowledgeBase::query()
                ->whereRaw('LENGTH(question) BETWEEN 10 AND 48')
                ->inRandomOrder()->limit(200)->get(['question', 'category']);
            $perTopic = [];
            foreach ($kb as $row) {
                if (count($out) >= $limit) {
                    break;
                }
                $topic = (string) $row->category;
                if (($perTopic[$topic] ?? 0) >= 2) {
                    continue; // keep the rows varied
                }
                $key = self::normalise($row->question);
                if (isset($seen[$key])) {
                    continue;
                }
                $seen[$key] = true;
                $perTopic[$topic] = ($perTopic[$topic] ?? 0) + 1;
                $out[] = ['text' => trim($row->question), 'topic' => $topic ?: null];
            }

            foreach (self::DEFAULTS as $q) {
                if (count($out) >= $limit) {
                    break;
                }
                if (! isset($seen[self::normalise($q)])) {
                    $out[] = ['text' => __($q), 'topic' => null];
                }
            }

            return $out;
        });
    }

    /** @return array<int, string> */
    private static function compute(int $limit): array
    {
        $rows = ChatMessage::query()
            ->join('chat_conversations', 'chat_conversations.id', '=', 'chat_messages.chat_conversation_id')
            ->where('chat_messages.sender', 'user')
            ->where('chat_messages.created_at', '>=', now()->subDays(60))
            ->latest('chat_messages.id')
            ->limit(3000)
            ->get(['chat_messages.message', 'chat_conversations.user_id']);

        $groups = [];
        foreach ($rows as $row) {
            $text = trim(preg_replace('/\s+/u', ' ', (string) $row->message));
            $key = self::normalise($text);

            if (! self::allowed($text, $key)) {
                continue;
            }

            $groups[$key]['users'][$row->user_id] = true;
            $groups[$key]['count'] = ($groups[$key]['count'] ?? 0) + 1;
            $groups[$key]['variants'][$text] = ($groups[$key]['variants'][$text] ?? 0) + 1;
        }

        $groups = array_filter($groups, fn ($g) => count($g['users']) >= self::MIN_STUDENTS);

        uasort($groups, fn ($a, $b) => [count($b['users']), $b['count']] <=> [count($a['users']), $a['count']]);

        $out = [];
        foreach (array_slice($groups, 0, $limit, true) as $g) {
            arsort($g['variants']);
            $best = array_key_first($g['variants']); // the most common way it was typed
            $best = Str::ucfirst($best);
            if (! preg_match('/[?？]$/u', $best)) {
                $best .= '?';
            }
            $out[] = $best;
        }

        return $out;
    }

    private static function allowed(string $text, string $key): bool
    {
        $len = mb_strlen($text);
        if ($len < 8 || $len > 80 || mb_strlen($key) < 6) {
            return false;
        }
        if (preg_match('/\d{5,}/', $text) || preg_match('/[\w.+-]+@[\w-]+\.\w+/', $text) || preg_match('#https?://#i', $text)) {
            return false; // IC / phone / matric numbers, emails, links
        }
        foreach (self::BLOCKED_WORDS as $w) {
            if (preg_match('/\b' . preg_quote($w, '/') . '\b/iu', $text)) {
                return false;
            }
        }

        return true;
    }

    private static function normalise(string $text): string
    {
        $t = mb_strtolower($text);
        $t = preg_replace('/[^\p{L}\p{N}\s]/u', '', $t);

        return trim(preg_replace('/\s+/u', ' ', $t));
    }
}
