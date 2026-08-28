<?php

namespace App\Http\Controllers;

use App\Models\KnowledgeBase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatbotController extends Controller
{
    protected array $stopwords = [
        'yang', 'untuk', 'dengan', 'adalah', 'saya', 'boleh', 'tak', 'tidak',
        'apa', 'macam', 'ada', 'di', 'ke', 'dan', 'atau', 'ini', 'itu',
        'the', 'is', 'are', 'a', 'an', 'to', 'for', 'of', 'what', 'how', 'can', 'i',
    ];

    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $message = $request->input('message');

        $entries = $this->searchKnowledgeBase($message);

        if ($entries->isEmpty()) {
            return response()->json([
                'reply' => 'Maaf, saya tiada maklumat berkaitan soalan tersebut dalam pangkalan data kami buat masa ini. Sila hubungi pihak berkaitan untuk maklumat lanjut.',
            ]);
        }

        $context = $entries->map(function ($entry) {
            return "Soalan: {$entry->question}\nJawapan: {$entry->answer}";
        })->implode("\n\n");

        $systemPrompt = "Anda adalah pembantu AI untuk pelajar kampus. Jawab soalan pelajar HANYA berdasarkan maklumat rujukan yang diberikan di bawah. "
            . "Jika maklumat yang ditanya tiada dalam rujukan, beritahu pelajar dengan jujur bahawa anda tiada maklumat tentang itu — jangan reka jawapan. "
            . "Jawab dalam Bahasa Melayu, ringkas dan jelas.\n\n"
            . "Maklumat rujukan:\n{$context}";

        $response = Http::withToken(config('services.groq.key'))
            ->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => 'llama-3.3-70b-versatile',
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $message],
                ],
                'temperature' => 0.3,
            ]);

        if ($response->failed()) {
            return response()->json([
                'error' => 'Groq API error',
                'details' => $response->json(),
            ], $response->status());
        }

        return response()->json([
            'reply' => $response->json('choices.0.message.content'),
        ]);
    }

    private function searchKnowledgeBase(string $message, int $limit = 5)
    {
        $keywords = collect(preg_split('/\s+/', strtolower($message)))
            ->map(fn ($w) => trim($w, ".,?!"))
            ->filter(fn ($w) => strlen($w) > 2 && ! in_array($w, $this->stopwords))
            ->unique()
            ->values();

        if ($keywords->isEmpty()) {
            return collect();
        }

        $candidates = KnowledgeBase::query()
            ->where(function ($q) use ($keywords) {
                foreach ($keywords as $word) {
                    $q->orWhere('question', 'like', "%{$word}%")
                      ->orWhere('answer', 'like', "%{$word}%")
                      ->orWhere('keywords', 'like', "%{$word}%")
                      ->orWhere('category', 'like', "%{$word}%");
                }
            })
            ->limit(50)
            ->get();

        return $candidates
            ->map(function ($entry) use ($keywords) {
                $haystack = strtolower($entry->question . ' ' . $entry->answer . ' ' . $entry->keywords . ' ' . $entry->category);
                $entry->relevance = $keywords->filter(fn ($word) => str_contains($haystack, $word))->count();
                return $entry;
            })
            ->filter(fn ($entry) => $entry->relevance > 0)
            ->sortByDesc('relevance')
            ->take($limit)
            ->values();
    }
}