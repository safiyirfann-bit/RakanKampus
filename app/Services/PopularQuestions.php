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

    /**
     * Small talk that isn't a real question (thanks, greetings, ok...). A message made
     * up only of these words is never shown as a "frequently asked" question.
     */
    private const SMALL_TALK = [
        'terima', 'kasih', 'thank', 'thanks', 'thankyou', 'tq', 'ty', 'tqvm', 'tenkiu', 'thx',
        'hi', 'hai', 'hello', 'helo', 'hey', 'assalamualaikum', 'salam', 'waalaikumsalam',
        'selamat', 'pagi', 'petang', 'malam', 'tengahari', 'good', 'morning', 'afternoon', 'evening', 'night',
        'ok', 'okay', 'okey', 'oke', 'baik', 'faham', 'noted', 'alright', 'sure', 'yes', 'no', 'ya', 'ye', 'tak', 'tidak',
        'bye', 'goodbye', 'jumpa', 'lagi', 'banyak', 'very', 'much', 'so', 'you', 'awak', 'bot', 'sis', 'bro',
        'nice', 'great', 'cool', 'mantap', 'best', 'wow', 'haha', 'hehe', 'lol', 'test', 'testing',
        'apa', 'khabar', 'how', 'are', 'what', 'up', 'sup',
    ];

    /** Only questions the knowledge base can answer (no fee deadline / exam timetable: that data isn't in it). */
    public const DEFAULTS = [
        'How do I register for courses?',
        'How do I pay my tuition fees online?',
        'What clubs can I join at PUO?',
        'Who is the director of PUO?',
    ];

    /**
     * @return array<int, array{text: string, popular: bool}>
     */
    public static function top(int $limit = 4): array
    {
        $popular = Cache::remember('home.popular_questions.v3', now()->addHour(), fn () => self::compute(8));

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
     * @return array<int, array{text: string, topic: ?string, kb_id: ?int}>
     */
    public static function marquee(int $limit = 12): array
    {
        return Cache::remember('chat.marquee_questions.v6.' . $limit, now()->addHour(), function () use ($limit) {
            $out = [];
            $seen = [];
            foreach (self::compute(6) as $q) {
                $out[] = ['text' => $q, 'topic' => null, 'kb_id' => null];
                $seen[self::normalise($q)] = true;
            }

            $kb = \App\Models\KnowledgeBase::query()->suggestable()
                ->whereRaw('LENGTH(question) BETWEEN 10 AND 48')
                ->inRandomOrder()->limit(200)->get(['id', 'question', 'question_ms', 'question_en', 'question_zh', 'question_ta', 'category']);
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
                $out[] = ['text' => trim($row->question), 'topic' => $topic ?: null, 'kb_id' => $row->id];
            }

            foreach (self::DEFAULTS as $q) {
                if (count($out) >= $limit) {
                    break;
                }
                if (! isset($seen[self::normalise($q)])) {
                    $out[] = ['text' => $q, 'topic' => null, 'kb_id' => null];
                }
            }

            return $out;
        });
    }

    /** Short English questions the knowledge base can answer (used if translating fails). */
    public const FALLBACK_EN = [
        'How do I pay my tuition fees online?',
        'What is SPMP and how do I use it?',
        'Where is the surau on campus?',
        'What clubs can I join at PUO?',
        'What programmes does PUO offer?',
        'Who is the director of PUO?',
        'What does JTMK stand for?',
        'Where are the lecture halls?',
    ];

    /**
     * The marquee questions in the student's language. The knowledge base is written
     * in Malay, so for English / Chinese / Tamil the list is translated once with the
     * AI and cached for a day; if that fails, a built-in English list is used.
     *
     * @return array<int, array{text: string, topic: ?string}>
     */
    public static function marqueeFor(string $locale, int $limit = 12): array
    {
        $items = self::marquee($limit);

        // Knowledge-base chips: use the stored translation of the question (no AI needed).
        $kb = \App\Models\KnowledgeBase::whereIn('id', array_filter(array_column($items, 'kb_id')))->get()->keyBy('id');
        $todo = [];
        foreach ($items as $i => $q) {
            $entry = $q['kb_id'] ? ($kb[$q['kb_id']] ?? null) : null;
            $hasOwn = $entry && ($locale === 'ms' ? true : filled($entry->{"question_{$locale}"}));
            if ($hasOwn) {
                $items[$i]['text'] = $entry->questionFor($locale);
            } elseif ($locale === 'ms') {
                $items[$i]['text'] = __($q['text']);
            } else {
                $todo[$i] = $q['text']; // a student's own question, or an entry not translated yet
            }
        }
        if (! $todo) {
            return $items;
        }

        // The rest are translated once with the AI and cached for a day.
        $key = 'chat.marquee_tr.' . $locale . '.' . md5(json_encode(array_values($todo)));
        $translated = Cache::get($key);
        if (! is_array($translated)) {
            $translated = self::translate(array_values($todo), $locale);
            if ($translated) {
                Cache::put($key, $translated, now()->addDay());
            }
        }

        $translated = is_array($translated) && count($translated) === count($todo) ? array_values($translated) : null;
        foreach (array_keys($todo) as $n => $i) {
            if ($translated) {
                $items[$i]['text'] = $translated[$n];
            } else {
                unset($items[$i]); // can't show it in this language: leave it out
            }
        }

        return array_values($items) ?: array_map(fn ($q) => ['text' => __($q), 'topic' => null, 'kb_id' => null], array_slice(self::FALLBACK_EN, 0, $limit));
    }

    /** @return array<int, string>|null */
    private static function translate(array $texts, string $locale): ?array
    {
        $key = (string) config('services.groq.key');
        if ($key === '' || ! $texts) {
            return null;
        }
        $language = ['en' => 'English', 'zh' => 'Simplified Chinese', 'ta' => 'Tamil'][$locale] ?? 'English';

        try {
            $res = \Illuminate\Support\Facades\Http::withToken($key)->timeout(8)
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => 'openai/gpt-oss-20b',
                    'temperature' => 0.2,
                    'messages' => [
                        ['role' => 'system', 'content' => "Translate each student question about Politeknik Ungku Omar (PUO) into natural, short {$language}. Keep names, acronyms and codes (PUO, SPMP, JTMK, JKM, iPayment) unchanged. Reply with ONLY a JSON array of strings, same order and same count as the input."],
                        ['role' => 'user', 'content' => json_encode(array_values($texts), JSON_UNESCAPED_UNICODE)],
                    ],
                ]);
            if ($res->failed()) {
                return null;
            }
            $content = (string) $res->json('choices.0.message.content');
            if (preg_match('/\[.*\]/s', $content, $m)) {
                $arr = json_decode($m[0], true);
                if (is_array($arr) && count($arr) === count($texts)) {
                    return array_map(fn ($x) => Str::limit(trim((string) $x), 70, '…'), array_values($arr));
                }
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Marquee translate failed: ' . $e->getMessage());
        }

        return null;
    }

    /** @return array<int, string> */
    private static function compute(int $limit): array
    {
        $rows = ChatMessage::query()
            ->join('chat_conversations', 'chat_conversations.id', '=', 'chat_messages.chat_conversation_id')
            ->where('chat_messages.sender', 'user')
            // only questions that matched a knowledge-base entry, so every chip is something the bot can answer
            ->whereNotNull('chat_messages.knowledge_base_id')
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
        $words = preg_split('/\s+/u', $key, -1, PREG_SPLIT_NO_EMPTY);
        if (count($words) < 3 || ! array_diff($words, self::SMALL_TALK)) {
            return false; // "Terima kasih", "Hi bot", "Ok thanks" etc. aren't real questions
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
