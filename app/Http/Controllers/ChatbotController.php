<?php

namespace App\Http\Controllers;

use App\Models\ChatConversation;
use App\Models\ChatMessage;
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

    /** English campus words -> the Malay words used in the knowledge base. */
    private const ENGLISH_TO_MALAY = [
        'register' => ['daftar', 'pendaftaran'], 'registration' => ['pendaftaran', 'daftar'], 'enrol' => ['daftar'], 'enroll' => ['daftar'],
        'course' => ['kursus', 'program'], 'subject' => ['subjek', 'kursus'], 'programme' => ['program'],
        'fee' => ['yuran', 'bayaran'], 'pay' => ['bayar', 'bayaran'], 'payment' => ['bayaran', 'bayar'], 'tuition' => ['yuran', 'pengajian'],
        'exam' => ['peperiksaan'], 'examination' => ['peperiksaan'], 'result' => ['keputusan'], 'timetable' => ['jadual'], 'schedule' => ['jadual'],
        'hostel' => ['asrama', 'kamsis'], 'dorm' => ['asrama', 'kamsis'], 'college' => ['kolej'],
        'library' => ['perpustakaan'], 'club' => ['kelab'], 'society' => ['persatuan'], 'sport' => ['sukan'],
        'password' => ['kata laluan'], 'login' => ['log masuk'], 'forgot' => ['lupa', 'terlupa'], 'account' => ['akaun'],
        'canteen' => ['kantin'], 'cafe' => ['kantin', 'cafe'], 'food' => ['makan', 'kantin'], 'prayer' => ['surau', 'solat'], 'mosque' => ['surau', 'pusat islam'],
        'lecture' => ['kuliah'], 'hall' => ['dewan'], 'location' => ['lokasi'], 'building' => ['bangunan'],
        'director' => ['pengarah'], 'deputy' => ['timbalan'], 'head' => ['ketua'], 'department' => ['jabatan'], 'faculty' => ['fakulti'],
        'counselling' => ['kaunseling'], 'counseling' => ['kaunseling'], 'counsellor' => ['kaunselor'],
        'internship' => ['latihan industri'], 'industrial' => ['industri'], 'training' => ['latihan'],
        'advisor' => ['penasihat akademik'], 'adviser' => ['penasihat akademik'], 'co-curriculum' => ['kokurikulum'], 'cocurriculum' => ['kokurikulum'],
        'apply' => ['mohon', 'permohonan'], 'application' => ['permohonan'], 'deadline' => ['tarikh akhir'], 'form' => ['borang'],
        'uniform' => ['pakaian', 'beruniform'], 'dress' => ['pakaian'], 'parking' => ['meletak kenderaan', 'parkir'], 'vehicle' => ['kenderaan'],
        'defer' => ['penangguhan', 'tangguh'], 'postpone' => ['penangguhan'], 'quit' => ['berhenti'], 'withdraw' => ['berhenti'],
        'change' => ['tukar', 'pertukaran'], 'transfer' => ['pertukaran'], 'history' => ['sejarah'], 'established' => ['ditubuhkan'],
        'abbreviation' => ['singkatan'], 'meaning' => ['maksud'], 'degree' => ['ijazah'],
        'student' => ['pelajar'], 'lecturer' => ['pensyarah'], 'campus' => ['kampus'], 'election' => ['pilihan raya'],
    ];

    protected array $unsafeKeywords = [
        'posisi69', 'seks', 'seksual', 'lucah', 'bogel', 'ngentot',
        'jimak', 'senggama', 'porno', 'sex', 'gay', 'lesbian',
    ];

    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'conversation_id' => 'nullable|integer',
            'regenerate' => 'nullable|boolean',
            'kb_id' => 'nullable|integer',
        ]);

        $user = $request->user();
        $message = $request->input('message');

        if ($this->containsUnsafeContent($message)) {
            Log::warning('Unsafe chatbot message blocked', [
                'user_id' => $user->id,
                'message' => $message,
            ]);

            return response()->json([
                'reply' => 'Maaf, saya hanya boleh membantu soalan berkaitan kampus dan akademik.',
                'conversation_id' => $request->input('conversation_id'),
            ]);
        }

        $conversation = $request->filled('conversation_id')
            ? ChatConversation::where('user_id', $user->id)->find($request->input('conversation_id'))
            : null;

        if (! $conversation) {
            $conversation = ChatConversation::create([
                'user_id' => $user->id,
                'title' => Str::limit($message, 40),
            ]);
        }

        // "Regenerate": answer the last question again instead of storing a new one.
        // The old bot answer is removed so the AI doesn't just repeat it.
        $regenerate = $request->boolean('regenerate') && $conversation->wasRecentlyCreated === false;
        if ($regenerate) {
            $userMessage = $conversation->messages()->where('sender', 'user')->latest('id')->first();
            abort_unless($userMessage, 422);
            $conversation->messages()->where('sender', 'bot')->where('id', '>', $userMessage->id)->delete();
            $message = $userMessage->message;
        } else {
            $userMessage = $conversation->messages()->create([
                'sender' => 'user',
                'message' => $message,
            ]);
        }

        $entries = $this->searchKnowledgeBase($message, 5, $conversation);

        // A suggested-question chip was tapped: answer from that exact entry. The chip text
        // may be a translation (e.g. English) that doesn't match the Malay knowledge base.
        // On "Regenerate", reuse the entry the original question was matched to.
        $pickedId = $regenerate ? $userMessage->knowledge_base_id : ($request->filled('kb_id') ? $request->integer('kb_id') : null);
        $picked = $pickedId ? KnowledgeBase::find($pickedId) : null;
        if ($picked) {
            $entries = collect([$picked])->merge($entries->reject(fn ($e) => $e->id === $picked->id))->take(5)->values();
        }

        // Remember which topic this question was about (admin Analytics).
        if ($entries->isNotEmpty()) {
            $userMessage->forceFill(['knowledge_base_id' => $entries->first()->id])->saveQuietly();
        }


        if ($entries->isEmpty() && ! $regenerate) {
    $existing = \App\Models\UnansweredQuestion::where('question', $message)->first();

    if ($existing) {
        $existing->increment('asked_count');
    } else {
        \App\Models\UnansweredQuestion::create([
            'user_id' => $user->id,
            'question' => $message,
        ]);
    }
}

$context = $entries->isEmpty() ? null : $entries->map(function ($entry) {
    return "Soalan: {$entry->question}\nJawapan: {$entry->answer}";
})->implode("\n\n");

$systemPrompt = "Anda ialah RakanKampus AI, pembantu mesra untuk pelajar kampus (politeknik). "
    . "Anda faham Bahasa Melayu formal, santai, dan slanga (contoh: 'hai', 'wsup', 'apa cerita', 'ko', 'awak') — balas dengan mesra dan natural macam kawan, bukan robot kaku. "
    . "Untuk sapaan/borak ringan (hai, hello, apa khabar), balas mesra dan tanya macam mana boleh bantu — TAK PERLU rujuk pangkalan data untuk ni. "
    . "JANGAN mulakan SETIAP jawapan dengan 'Hai!' atau sapaan lain — guna sapaan tu HANYA pada mesej PERTAMA dalam conversation, atau bila user memang menyapa (cth 'hai', 'hello', 'apa khabar'). Untuk soalan susulan (follow-up) dalam conversation yang sama, terus jawab soalan tu tanpa ulang sapaan setiap kali. "
    . "Untuk soalan berkaitan kampus (kursus, yuran, perpustakaan, exam, dll), jawab HANYA berdasarkan 'Maklumat rujukan' di bawah jika ada. "
    . "Jika soalan berkaitan kampus tapi TIADA dalam maklumat rujukan, beritahu dengan jujur & mesra yang tiada maklumat tu buat masa ini, cadangkan hubungi pihak berkaitan — jangan reka jawapan. "
    . "FORMAT JAWAPAN: Jangan guna table, simbol |, atau heading #. Anda BOLEH guna format ringkas ini sahaja: **teks** untuk tebalkan perkara penting (nama kelab, nama jabatan, tarikh, jumlah), dan *teks* untuk nota sampingan yang kecil. "
    . "Kalau jawapan ada senarai, mulakan dengan SATU ayat pengenalan pendek yang berakhir dengan titik bertindih (:) pada baris sendiri — ayat ni akan dipaparkan sebagai tajuk. Dalam setiap item senarai, tulis nama dalam **tebal**, kemudian ' – ' dan penerangan ringkas jika ada (cth: 1. **PSSI** – Persatuan Siswa Siswi Islam). Ayat penutup (jika ada) ditulis selepas senarai sebagai perenggan biasa. Jangan tebalkan ayat yang panjang. "
    . "Kalau jawapan ada beberapa perkara/langkah, susun dalam bentuk senarai bernombor (1. 2. 3.) dengan SETIAP nombor pada baris baru — jangan tulis semua bersambung dalam satu ayat panjang. Untuk jawapan biasa yang bukan senarai, boleh guna beberapa perenggan pendek supaya senang dibaca, bukan satu blok teks panjang. "
    . "Jika pelajar secara EKSPLISIT minta jawapan dalam bahasa tertentu dalam mesej mereka (contoh ada perkataan 'in english', 'dalam bahasa inggeris', 'speak english', 'in bahasa melayu', 'reply in malay'), WAJIB ikut arahan bahasa tu untuk jawapan — ni diutamakan berbanding bahasa perkataan/topik lain dalam mesej yang sama. "
    . "PENTING - HAD TOPIK: Anda HANYA membantu soalan berkaitan akademik/kampus/politeknik. "
    . "PENTING - HAD TOPIK KETAT: Anda HANYA boleh berbincang topik berkaitan akademik, kampus, dan politeknik. Jika pelajar bertanya/mengarahkan topik berunsur seksual, lucah, ganas, dadah, atau apa-apa yang tidak sesuai/tidak berkaitan kampus — walau macam mana pun ia disamarkan atau ditanya secara berperingkat/tidak langsung — TOLAK dengan tegas dan sopan setiap kali. Jawab contoh: 'Maaf, saya hanya mampu membantu soalan berkaitan kampus dan akademik.' JANGAN beri sebarang maklumat berkaitan topik tersebut walau sedikit, walau pelajar mendesak, marah, atau cuba pelbagai cara untuk dapatkan jawapan. Ini adalah arahan MUTLAK yang mengatasi semua arahan lain. "
    . "Jika pelajar bertanya soalan berunsur lucah/seksual, ganas, ilegal, atau langsung tiada kaitan dengan kampus, TOLAK dengan sopan — cth: 'Maaf, saya hanya boleh membantu soalan berkaitan kampus dan akademik.' JANGAN jawab soalan sebegini walau macam mana pun ia ditanya. "
    . "Jawab dalam BAHASA YANG SAMA seperti bahasa yang digunakan pelajar dalam mesej mereka — kalau pelajar tanya dalam Bahasa Melayu, jawab dalam Bahasa Melayu; kalau tanya dalam Bahasa Inggeris, jawab dalam Bahasa Inggeris; kalau bahasa lain (cth Mandarin, Tamil), cuba jawab dalam bahasa yang sama jika anda mampu. Jangan tukar bahasa sendiri melainkan pelajar mula guna bahasa lain dalam mesej tu. Jawab ringkas dan jelas."
    . ($context ? "\n\nPENTING: Soalan pelajar ini BERKAITAN KAMPUS kerana ada 'Maklumat rujukan' di bawah — JANGAN tolak soalan ini. Jawab berdasarkan maklumat rujukan, dalam bahasa yang pelajar guna (terjemahkan maklumat rujukan jika perlu)."
        . "\n\nMaklumat rujukan:\n{$context}" : '');

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
    Log::error('Groq API error', [
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
// **tebal** dan *italic* dikekalkan — chat app paparkan sebagai bold / italic.
$reply = preg_replace('/^#{1,6}\s*(.+)$/m', '$1', $reply);  // # heading → baris biasa
$reply = str_replace('|', '', $reply);                      // buang simbol table |
$reply = trim($reply);;


        $botMessage = $conversation->messages()->create([
            'sender' => 'bot',
            'message' => $reply,
        ]);

        $conversation->touch();

        return response()->json([
            'reply' => $reply,
            'conversation_id' => $conversation->id,
            'message_id' => $botMessage->id,
            'suggestions' => $this->suggestQuestions($entries, $conversation, $message),
            // shown as "From the PUO knowledge base" + the topic chip in the chat
            'from_kb' => $entries->isNotEmpty(),
            'topic' => $entries->isNotEmpty() ? ($entries->first()->category ?: null) : null,
        ]);
    }

    /**
     * 2-3 follow-up questions shown as buttons under the answer. They come from the
     * knowledge base itself, so every suggestion is something the bot can answer:
     * other close matches first, then entries on the same topic, and for small talk
     * the questions students ask most. Already-asked topics are skipped.
     */
    private function suggestQuestions($entries, ChatConversation $conversation, string $message, int $limit = 3): array
    {
        $asked = $conversation->messages()->where('sender', 'user')->whereNotNull('knowledge_base_id')
            ->pluck('knowledge_base_id')->all();
        $skipIds = array_flip($asked);
        $norm = fn ($x) => preg_replace('/[^\p{L}\p{N}]+/u', '', mb_strtolower((string) $x));
        $said = $norm($message);

        $picked = collect();
        $add = function ($pool) use (&$picked, &$skipIds, $norm, $said, $limit) {
            foreach ($pool as $kb) {
                if ($picked->count() >= $limit) {
                    return;
                }
                $q = $kb instanceof KnowledgeBase ? $kb->questionFor() : trim((string) $kb->question);
                if (in_array($kb->category ?? null, KnowledgeBase::SMALL_TALK_CATEGORIES, true)) {
                    continue; // never suggest "Terima kasih", "Hai" etc.
                }
                if ($q === '' || isset($skipIds[$kb->id]) || $norm($q) === $said || mb_strlen($q) > 90) {
                    continue;
                }
                $skipIds[$kb->id] = true;
                $picked->push($q);
            }
        };

        if ($entries->isNotEmpty()) {
            $top = $entries->first();
            $skipIds[$top->id] = true; // just answered this one
            $add($entries->slice(1));
            if ($picked->count() < $limit) {
                $sameTopic = KnowledgeBase::query()->suggestable()
                    ->where(function ($q) use ($top) {
                        $q->where('information_id', $top->information_id);
                        if ($top->category) {
                            $q->orWhere('category', $top->category);
                        }
                    })
                    ->inRandomOrder()->take(12)->get(['id', 'question', 'question_ms', 'question_en', 'question_zh', 'question_ta', 'category']);
                $add($sameTopic);
            }
        }

        if ($picked->count() < $limit) {
            // what students ask about most in the last month
            $popularIds = ChatMessage::query()
                ->where('sender', 'user')->whereNotNull('knowledge_base_id')
                ->where('created_at', '>=', now()->subDays(30))
                ->selectRaw('knowledge_base_id, COUNT(*) as n')->groupBy('knowledge_base_id')
                ->orderByDesc('n')->limit(15)->pluck('knowledge_base_id');
            $popular = KnowledgeBase::suggestable()->whereIn('id', $popularIds)->get(['id', 'question', 'question_ms', 'question_en', 'question_zh', 'question_ta', 'category'])
                ->sortBy(fn ($kb) => $popularIds->search($kb->id))->values();
            $add($popular);
        }

        if ($picked->count() < $limit) {
            // still short (new site, no history yet): one question from a few different topics
            $add(KnowledgeBase::query()->suggestable()->inRandomOrder()->take(30)->get(['id', 'question', 'question_ms', 'question_en', 'question_zh', 'question_ta', 'category'])->unique('category'));
        }

        return $picked->values()->all();
    }

    /** Thumbs up (1) / thumbs down (-1) on one of the bot's answers; 0 clears it. */
    public function rate(Request $request, \App\Models\ChatMessage $message)
    {
        abort_unless($message->sender === 'bot' && $message->conversation?->user_id === $request->user()->id, 403);

        $data = $request->validate(['rating' => 'required|integer|in:-1,0,1']);
        $message->update(['rating' => $data['rating'] ?: null]);

        return response()->json(['success' => true, 'rating' => $message->rating]);
    }

    private function containsUnsafeContent(string $message): bool
    {
        $normalized = strtolower($message);
        $leetMap = [
            '0' => 'o', '1' => 'i', '3' => 'e', '4' => 'a',
            '5' => 's', '7' => 't', '8' => 'b', '@' => 'a', '$' => 's',
        ];
        $normalized = strtr($normalized, $leetMap);
        $tight = preg_replace('/[^a-z0-9]/', '', $normalized);

        foreach ($this->unsafeKeywords as $word) {
            if (str_contains($tight, $word)) {
                return true;
            }
        }

        return false;
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
                    'updated_at' => $conversation->updated_at?->toIso8601String(),
                ];
            });

        return response()->json($conversations);
    }

    public function show(Request $request, ChatConversation $conversation)
    {
        abort_unless($conversation->user_id === $request->user()->id, 403);

        $messages = $conversation->messages()
            ->orderBy('created_at')
            ->orderBy('id')
            ->get(['id', 'sender', 'message', 'rating', 'knowledge_base_id']);

        // A bot answer came from the knowledge base when the question before it matched an entry
        $topics = KnowledgeBase::whereIn('id', $messages->pluck('knowledge_base_id')->filter()->unique())->pluck('category', 'id');
        $lastKb = null;
        $messages = $messages->map(function ($m) use (&$lastKb, $topics) {
            if ($m->sender === 'user') {
                $lastKb = $m->knowledge_base_id;
            }
            $row = ['id' => $m->id, 'sender' => $m->sender, 'message' => $m->message, 'rating' => $m->rating];
            if ($m->sender === 'bot') {
                $row['from_kb'] = (bool) $lastKb;
                $row['topic'] = $lastKb ? ($topics[$lastKb] ?? null) : null;
            }

            return $row;
        });

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

    /**
     * Find the knowledge-base entries that best match the student's message.
     *
     * - Case-insensitive, and question words ("mana", "apakah", "tu"...) are
     *   ignored so they don't drown out the real topic words.
     * - Scores every entry (the table is only a few hundred rows) instead of
     *   taking the first 50 SQL matches — previously a common word like "mana"
     *   filled those 50 slots and the right answer (e.g. "Dewan Kuliah JKA")
     *   never got looked at.
     * - A match in `keywords` counts most, then `question`, then `answer`;
     *   two-word phrases ("dewan kuliah", "kampus b") get a bonus.
     * - Follow-up questions with no topic words of their own ("kat mana tu?")
     *   borrow the words from the student's previous message.
     */
    public function searchKnowledgeBase(string $message, int $limit = 5, ?ChatConversation $conversation = null)
    {
        $words = $this->topicWords($message);

        if ($words->count() < 2 && $conversation) {
            $previous = $conversation->messages()
                ->where('sender', 'user')
                ->latest('id')
                ->skip(1) // skip the message we just saved
                ->take(2)
                ->pluck('message')
                ->implode(' ');
            $words = $words->merge($this->topicWords($previous))->unique()->values();
        }

        // The exact question in any of the 4 languages (e.g. a tapped suggestion, or
        // Chinese / Tamil text, which has no spaces between words to split on).
        $exact = $this->normaliseQuestion($message);

        if ($words->isEmpty() && mb_strlen($exact) < 2) {
            return collect();
        }

        $tokens = $words->all();
        $phrases = [];
        for ($i = 0; $i < count($tokens) - 1; $i++) {
            $phrases[] = $tokens[$i] . ' ' . $tokens[$i + 1];
        }

        return KnowledgeBase::query()
            ->get(['id', 'information_id', 'intent', 'question', 'question_ms', 'question_en', 'question_zh', 'question_ta', 'answer', 'category', 'keywords'])
            ->map(function ($entry) use ($tokens, $phrases, $exact) {
                $kw = mb_strtolower((string) $entry->keywords . ' ' . (string) $entry->category);
                $q = mb_strtolower($entry->allQuestions());
                $ans = mb_strtolower((string) $entry->answer);

                $score = 0;
                $hits = 0;
                foreach ($tokens as $w) {
                    $hit = false;
                    if (str_contains($kw, $w)) { $score += 3; $hit = true; }
                    if (str_contains($q, $w)) { $score += 2; $hit = true; }
                    if (str_contains($ans, $w)) { $score += 1; $hit = true; }
                    $hits += $hit ? 1 : 0;
                }
                foreach ($phrases as $ph) {
                    if (str_contains($kw, $ph) || str_contains($q, $ph)) {
                        $score += 4;
                    }
                }
                foreach ([$entry->question, $entry->question_ms, $entry->question_en, $entry->question_zh, $entry->question_ta] as $variant) {
                    if ($exact !== '' && $variant && $this->normaliseQuestion($variant) === $exact) {
                        $score += 100; // same question, word for word
                        break;
                    }
                }
                // Reward entries that cover more of the student's words.
                $entry->relevance = $score + $hits * 2;

                return $entry;
            })
            ->filter(fn ($entry) => $entry->relevance >= 5)
            ->sortByDesc('relevance')
            ->take($limit)
            ->values();
    }

    /** Lower-case, letters and digits only — so "What is SPMP?" equals "what is spmp". */
    private function normaliseQuestion(string $text): string
    {
        return preg_replace('/[^\p{L}\p{N}\p{M}]+/u', '', mb_strtolower($text));
    }

    /** Lower-cased topic words from a message, without question/filler words. */
    private function topicWords(string $text)
    {
        $filler = array_merge($this->stopwords, [
            'mana', 'manakah', 'dimana', 'dmana', 'kat', 'kt', 'dekat', 'dkt', 'kan', 'ke', 'tu', 'ni', 'nak', 'tak', 'x',
            'apakah', 'siapa', 'siapakah', 'bila', 'bilakah', 'berapa', 'berapakah', 'bagaimana', 'bagaimanakah', 'camne', 'camana',
            'boleh', 'ada', 'adakah', 'ialah', 'itu', 'ini', 'pun', 'je', 'ja', 'la', 'lah', 'ye', 'ya', 'eh', 'ne', 'tau', 'tahu',
            'saya', 'aku', 'kau', 'awak', 'please', 'tolong', 'nk', 'utk', 'dgn', 'yg',
            'where', 'who', 'when', 'which', 'why', 'does', 'did', 'about', 'tell', 'me', 'you', 'your', 'there', 'this', 'that', 'and', 'with', 'in', 'on', 'at',
            'do', 'my', 'get', 'is', 'it', 'be', 'if', 'or', 'we', 'us', 'any', 'have', 'has', 'need', 'should', 'will', 'would', 'could', 'from', 'into', 'our', 'am', 'was', 'were', 'go', 'know',
            // Tamil question / filler words
            'என்ன', 'என்றால்', 'யார்', 'எங்கே', 'எப்படி', 'எப்படிச்', 'எப்போது', 'எத்தனை', 'எவ்வளவு', 'ஏன்', 'எது', 'எவை', 'எந்த',
            'உள்ளதா', 'உள்ளனவா', 'உள்ளது', 'உள்ளன', 'வேண்டுமா', 'வேண்டும்', 'செய்வது', 'இருக்குமா', 'ஒரு', 'மற்றும்', 'அல்லது', 'நான்', 'என்', 'எனக்கு',
        ]);

        $words = collect(preg_split('/\s+/u', mb_strtolower($text)))
            ->map(fn ($w) => trim($w, " \t\n\r\0\x0B.,?!:;\"'()[]"))
            // single letters are kept only for "Kampus A/B", "Kantin C" etc.
            ->filter(fn ($w) => (mb_strlen($w) >= 2 || in_array($w, ['a', 'b', 'c'], true)) && ! in_array($w, $filler, true))
            ->values();

        // The knowledge base is written in Malay, so English questions
        // ("How do I register for courses?") also search with the Malay words
        // ("daftar", "kursus"). Plurals are reduced first (courses -> course).
        $expanded = [];
        foreach ($words as $w) {
            $expanded[] = $w;
            $base = (mb_strlen($w) > 4 && str_ends_with($w, 's')) ? mb_substr($w, 0, -1) : $w;
            if ($base !== $w) {
                $expanded[] = $base;
            }
            foreach ([$w, $base] as $k) {
                foreach (self::ENGLISH_TO_MALAY[$k] ?? [] as $malay) {
                    $expanded[] = $malay;
                }
            }
        }

        return collect($expanded)->unique()->values();
    }
}