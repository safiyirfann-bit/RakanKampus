<?php

namespace App\Http\Controllers;

use App\Models\ChatConversation;
use App\Models\KnowledgeBase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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
            'conversation_id' => 'nullable|integer',
        ]);

        $user = $request->user();
        $message = $request->input('message');

        $conversation = $request->filled('conversation_id')
            ? ChatConversation::where('user_id', $user->id)->find($request->input('conversation_id'))
            : null;

        if (! $conversation) {
            $conversation = ChatConversation::create([
                'user_id' => $user->id,
                'title' => Str::limit($message, 40),
            ]);
        }

        $conversation->messages()->create([
            'sender' => 'user',
            'message' => $message,
        ]);

        $entries = $this->searchKnowledgeBase($message);

$context = $entries->isEmpty() ? null : $entries->map(function ($entry) {
    return "Soalan: {$entry->question}\nJawapan: {$entry->answer}";
})->implode("\n\n");

$systemPrompt = "Anda ialah RakanKampus AI, pembantu mesra untuk pelajar kampus (politeknik). "
    . "Anda faham Bahasa Melayu formal, santai, dan slanga (contoh: 'hai', 'wsup', 'apa cerita', 'ko', 'awak') — balas dengan mesra dan natural macam kawan, bukan robot kaku. "
    . "Untuk sapaan/borak ringan (hai, hello, apa khabar), balas mesra dan tanya macam mana boleh bantu — TAK PERLU rujuk pangkalan data untuk ni. "
    . "JANGAN mulakan SETIAP jawapan dengan 'Hai!' atau sapaan lain — guna sapaan tu HANYA pada mesej PERTAMA dalam conversation, atau bila user memang menyapa (cth 'hai', 'hello', 'apa khabar'). Untuk soalan susulan (follow-up) dalam conversation yang sama, terus jawab soalan tu tanpa ulang sapaan setiap kali. "
    . "Untuk soalan berkaitan kampus (kursus, yuran, perpustakaan, exam, dll), jawab HANYA berdasarkan 'Maklumat rujukan' di bawah jika ada. "
    . "Jika soalan berkaitan kampus tapi TIADA dalam maklumat rujukan, beritahu dengan jujur & mesra yang tiada maklumat tu buat masa ini, cadangkan hubungi pihak berkaitan — jangan reka jawapan. "
    . "PENTING: Jangan sekali-kali guna format Markdown (jangan guna simbol seperti **, |, #, -, atau table). Tulis dalam ayat/perenggan biasa sahaja, macam berbual terus. "
    . "Kalau jawapan ada beberapa perkara/langkah, susun dalam bentuk senarai bernombor (1. 2. 3.) dengan SETIAP nombor pada baris baru — jangan tulis semua bersambung dalam satu ayat panjang. Untuk jawapan biasa yang bukan senarai, boleh guna beberapa perenggan pendek supaya senang dibaca, bukan satu blok teks panjang. "
    . "Jika pelajar secara EKSPLISIT minta jawapan dalam bahasa tertentu dalam mesej mereka (contoh ada perkataan 'in english', 'dalam bahasa inggeris', 'speak english', 'in bahasa melayu', 'reply in malay'), WAJIB ikut arahan bahasa tu untuk jawapan — ni diutamakan berbanding bahasa perkataan/topik lain dalam mesej yang sama. "
    . "Jawab dalam BAHASA YANG SAMA seperti bahasa yang digunakan pelajar dalam mesej mereka — kalau pelajar tanya dalam Bahasa Melayu, jawab dalam Bahasa Melayu; kalau tanya dalam Bahasa Inggeris, jawab dalam Bahasa Inggeris; kalau bahasa lain (cth Mandarin, Tamil), cuba jawab dalam bahasa yang sama jika anda mampu. Jangan tukar bahasa sendiri melainkan pelajar mula guna bahasa lain dalam mesej tu. Jawab ringkas dan jelas."
    . ($context ? "\n\nMaklumat rujukan:\n{$context}" : '');

// Ambil sejarah mesej dalam conversation ni (supaya AI ingat konteks & bahasa)
$history = $conversation->messages()
    ->orderBy('created_at')
    ->latest('created_at')
    ->take(20)
    ->get(['sender', 'message'])
    ->sortBy('created_at')
    ->map(function ($m) {
        return [
            'role' => $m->sender === 'user' ? 'user' : 'assistant',
            'content' => $m->message,
        ];
    })
    ->values()
    ->toArray();

$response = Http::withToken(config('services.groq.key'))
    ->post('https://api.groq.com/openai/v1/chat/completions', [
        'model' => 'openai/gpt-oss-20b',
        'messages' => array_merge(
            [['role' => 'system', 'content' => $systemPrompt]],
            $history
        ),
        'temperature' => 0.5,
    ]);

if ($response->failed()) {
    \Log::error('Groq API error', [
        'status' => $response->status(),
        'body' => $response->body(),
    ]);

    return response()->json([
        'error' => 'Groq API error',
        'details' => $response->json(),
    ], $response->status());
}

$reply = $response->json('choices.0.message.content');

// Buang sebarang format Markdown yang AI masih guna (jaring keselamatan tambahan)
$reply = preg_replace('/\*\*(.*?)\*\*/s', '$1', $reply);   // buang **bold**
$reply = preg_replace('/\*(.*?)\*/s', '$1', $reply);        // buang *italic*
$reply = preg_replace('/^#{1,6}\s*/m', '', $reply);         // buang # heading
$reply = preg_replace('/^[-*+]\s+/m', '', $reply);          // buang bullet - / *
$reply = str_replace('|', '', $reply);                      // buang simbol table |
$reply = trim($reply);;


        $conversation->messages()->create([
            'sender' => 'bot',
            'message' => $reply,
        ]);

        $conversation->touch();

        return response()->json([
            'reply' => $reply,
            'conversation_id' => $conversation->id,
        ]);
    }

    public function history(Request $request)
    {
        $conversations = $request->user()
            ->chatConversations()
            ->latest('updated_at')
            ->take(15)
            ->get()
            ->map(function ($conversation) {
                $lastMessage = $conversation->messages()->latest()->first();

                return [
                    'id' => $conversation->id,
                    'title' => $conversation->title ?: 'New Conversation',
                    'preview' => $lastMessage ? Str::limit($lastMessage->message, 45) : '',
                ];
            });

        return response()->json($conversations);
    }

    public function show(Request $request, ChatConversation $conversation)
    {
        abort_unless($conversation->user_id === $request->user()->id, 403);

        $messages = $conversation->messages()
            ->orderBy('created_at')
            ->get(['sender', 'message']);

        return response()->json($messages);
    }
   
    public function rename(Request $request, ChatConversation $conversation)
{
    abort_unless($conversation->user_id === $request->user()->id, 403);

    $request->validate([
        'title' => 'required|string|max:100',
    ]);

    $conversation->update(['title' => $request->input('title')]);

    return response()->json(['success' => true, 'title' => $conversation->title]);
}

public function destroy(Request $request, ChatConversation $conversation)
{
    abort_unless($conversation->user_id === $request->user()->id, 403);

    $conversation->messages()->delete();
    $conversation->delete();

    return response()->json(['success' => true]);
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