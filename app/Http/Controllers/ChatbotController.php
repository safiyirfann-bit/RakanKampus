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
        'canteen' => ['kantin'], 'cafe' => ['kantin', 'kafe'], 'kafe' => ['kantin', 'cafe'], 'kantin' => ['cafe', 'kafe'], 'food' => ['makan', 'kantin'], 'prayer' => ['surau', 'solat'], 'mosque' => ['surau', 'pusat islam'],
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

    /**
     * Tamil and Chinese words → the Malay words used in the knowledge base. Matched as
     * substrings of the message, because Chinese has no spaces and Tamil adds endings to
     * words (பாடம் / பாடங்களுக்கு), so the Tamil entries are word stems.
     */
    private const OTHER_TO_MALAY = [
        // Tamil
        'பாட' => ['kursus'], 'பதிவ' => ['daftar', 'pendaftaran'], 'கட்டண' => ['yuran', 'bayaran'], 'செலுத்த' => ['bayar', 'bayaran'],
        'விடுதி' => ['asrama', 'kamsis'], 'நூலக' => ['perpustakaan'], 'தேர்வ' => ['peperiksaan'], 'முடிவ' => ['keputusan'],
        'அட்டவணை' => ['jadual'], 'கழக' => ['kelab'], 'சங்க' => ['persatuan'], 'விளையாட்ட' => ['sukan'],
        'கடவுச்சொல' => ['kata laluan'], 'உள்நுழை' => ['log masuk'], 'கணக்க' => ['akaun'], 'கேன்டீன' => ['kantin'], 'உணவ' => ['makan', 'kantin'],
        'தொழுகை' => ['surau', 'solat'], 'சுராவ' => ['surau'], 'விரிவுரை' => ['kuliah'], 'மண்டப' => ['dewan'], 'இயக்குந' => ['pengarah'],
        'துறை' => ['jabatan'], 'ஆலோசனை' => ['kaunseling'], 'பயிற்சி' => ['latihan'], 'விண்ணப்ப' => ['permohonan', 'mohon'], 'படிவ' => ['borang'],
        'ஆடை' => ['pakaian'], 'வாகன' => ['kenderaan', 'parkir'], 'நிறுத்த' => ['parkir', 'meletak kenderaan'], 'ஒத்திவைப்ப' => ['penangguhan'],
        'விலக' => ['berhenti'], 'மாற்ற' => ['tukar', 'pertukaran'], 'தேர்தல' => ['pilihan raya'], 'உதவித்தொகை' => ['biasiswa'],
        'டிப்ளோமா' => ['diploma'], 'பட்டப்படிப்ப' => ['ijazah'], 'வளாக' => ['kampus'], 'மாணவர' => ['pelajar'], 'இளங்கலை' => ['ijazah sarjana muda'],
        'புத்தக' => ['buku'], 'இரவல' => ['pinjam', 'pinjaman'], 'அபராத' => ['denda'], 'வருகை' => ['kehadiran'],
        // Chinese
        '注册' => ['daftar', 'pendaftaran'], '报名' => ['daftar', 'pendaftaran'], '报到' => ['pendaftaran', 'lapor diri'], '科目' => ['kursus'], '课程' => ['kursus', 'program'],
        '学费' => ['yuran', 'bayaran'], '缴' => ['bayar', 'bayaran'], '付款' => ['bayar', 'bayaran'], '宿舍' => ['asrama', 'kamsis'], '图书馆' => ['perpustakaan'],
        '考试' => ['peperiksaan'], '成绩' => ['keputusan'], '时间表' => ['jadual'], '学会' => ['kelab'], '协会' => ['persatuan'], '运动' => ['sukan'], '体育' => ['sukan'],
        '密码' => ['kata laluan'], '登录' => ['log masuk'], '账户' => ['akaun'], '食堂' => ['kantin'], '祈祷' => ['surau', 'solat'], '讲堂' => ['dewan', 'kuliah'],
        '院长' => ['pengarah'], '辅导' => ['kaunseling'], '实习' => ['latihan industri'], '申请' => ['permohonan', 'mohon'], '表格' => ['borang'],
        '服装' => ['pakaian'], '停车' => ['parkir', 'kenderaan'], '延期' => ['penangguhan'], '退学' => ['berhenti'], '转' => ['pertukaran', 'tukar'],
        '选举' => ['pilihan raya'], '奖学金' => ['biasiswa'], '文凭' => ['diploma'], '学士' => ['ijazah'], '校园' => ['kampus'], '学生' => ['pelajar'],
        '书' => ['buku'], '借' => ['pinjam', 'pinjaman'], '罚款' => ['denda'], '出席' => ['kehadiran'],
    ];

    /** Question and filler words ignored when searching (Malay, English, Tamil). */
    private const FILLER_WORDS = [
            'mana', 'manakah', 'dimana', 'dmana', 'kat', 'kt', 'dekat', 'dkt', 'kan', 'ke', 'tu', 'ni', 'nak', 'tak', 'x',
            'apakah', 'siapa', 'siapakah', 'bila', 'bilakah', 'berapa', 'berapakah', 'bagaimana', 'bagaimanakah', 'camne', 'camana',
            'boleh', 'ada', 'adakah', 'ialah', 'itu', 'ini', 'pun', 'je', 'ja', 'la', 'lah', 'ye', 'ya', 'eh', 'ne', 'tau', 'tahu',
            'saya', 'aku', 'kau', 'awak', 'please', 'tolong', 'nk', 'utk', 'dgn', 'yg', 'ade', 'ape', 'cane', 'mcm', 'kot',
            'where', 'who', 'when', 'which', 'why', 'does', 'did', 'about', 'tell', 'me', 'you', 'your', 'there', 'this', 'that', 'and', 'with', 'in', 'on', 'at',
            'do', 'my', 'get', 'is', 'it', 'be', 'if', 'or', 'we', 'us', 'any', 'have', 'has', 'need', 'should', 'will', 'would', 'could', 'from', 'into', 'our', 'am', 'was', 'were', 'go', 'know',
            // Tamil question / filler words
            'என்ன', 'என்றால்', 'யார்', 'எங்கே', 'எப்படி', 'எப்படிச்', 'எப்போது', 'எத்தனை', 'எவ்வளவு', 'ஏன்', 'எது', 'எவை', 'எந்த',
            'உள்ளதா', 'உள்ளனவா', 'உள்ளது', 'உள்ளன', 'வேண்டுமா', 'வேண்டும்', 'செய்வது', 'இருக்குமா', 'ஒரு', 'மற்றும்', 'அல்லது', 'நான்', 'என்', 'எனக்கு',
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

        // Last resort: nothing (good) matched — unusual wording, slang, another language — so let the AI
        // turn the question into a short Malay search and try once more.
        // "Weak" = low score, or the best entry covers under 2/3 of the student's words
        // (e.g. "berapa harga nasi lemak" only matching "nasi").
        $best = $entries->first();
        $weak = ! $best || $best->relevance < 35 || ($best->coverage ?? 1) < 0.67;
        if (! $picked && $weak && ! $regenerate && mb_strlen(trim($message)) >= 4) {
            $alt = $this->aiSearchQuery($message);
            $altEntries = $alt ? $this->searchKnowledgeBase($alt, 5) : collect();
            $altBest = $altEntries->first();
            if ($altBest && $altBest->relevance >= 35 && ($altBest->coverage ?? 0) >= 0.67) {
                $entries = $altEntries;          // the rewritten question matched properly
            } elseif ($best && ($best->coverage ?? 1) < 0.5 && $best->relevance < 35) {
                $entries = collect();            // nothing really matches: don't feed the AI unrelated entries
            }
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

// Student's first name, so the AI can greet them personally (e.g. "Safiy").
$studentName = trim((string) Str::of($user->first_name ?: ($user->name ?? ''))->before(' ')->title());

$systemPrompt = "Anda ialah RakanKampus AI, pembantu mesra untuk pelajar kampus (politeknik). "
    . "PERSONALITI: Anda macam senior yang baik hati — mesra, ceria, sabar dan suka membantu, tapi tetap sopan dan boleh dipercayai. Guna 'saya' untuk diri sendiri dan 'awak' untuk pelajar. "
    . ($studentName !== ''
        ? "Nama pelajar ini ialah {$studentName}. Sebut nama dia sekali-sekala supaya rasa peribadi (contoh pada jawapan pertama, atau bila beri semangat) — JANGAN sebut nama dalam setiap jawapan. "
        : '')
    . "EMOJI: Boleh guna paling banyak SATU emoji yang sesuai dalam satu jawapan (cth 😊 👍 📚 💪). JANGAN guna emoji untuk perkara serius (disiplin, kemalangan, kesihatan, kematian, masalah kewangan teruk). "
    . "EMPATI: Kalau pelajar nampak risau, stress, penat, sedih atau marah (cth 'stress exam', 'takut gagal', 'tak faham langsung'), mulakan dengan SATU ayat pendek yang menenangkan atau memahami perasaan dia, baru jawab soalan. "
    . "IKUT GAYA PELAJAR: Kalau pelajar guna bahasa santai/slanga, balas lebih santai; kalau pelajar formal, balas lebih formal — tapi sentiasa sopan. "
    . "PENUTUP: Di hujung jawapan yang membantu, boleh tawarkan bantuan seterusnya dengan ringkas (cth 'Nak saya terangkan cara bayar sekali?') — tapi jangan ulang ayat penutup yang sama setiap kali, dan tak perlu untuk jawapan yang sangat pendek. "
    . "Bila tiada maklumat, tetap mesra: minta maaf ringkas, cadangkan pihak yang betul untuk dihubungi, dan tawarkan bantuan lain yang berkaitan. "
    . "Anda faham Bahasa Melayu formal, santai, dan slanga (contoh: 'hai', 'wsup', 'apa cerita', 'ko', 'awak') — balas dengan mesra dan natural macam kawan, bukan robot kaku. "
    . "Untuk sapaan/borak ringan (hai, hello, apa khabar), balas mesra dan tanya macam mana boleh bantu — TAK PERLU rujuk pangkalan data untuk ni. "
    . "GAYA BAHASA MELAYU: Guna Bahasa Melayu Malaysia yang biasa dan mudah difahami pelajar (contoh: 'tanya saja', 'boleh', 'macam mana'). JANGAN guna perkataan Indonesia, perkataan pelik, atau perkataan yang anda tak pasti maknanya. Kalau ragu, pilih perkataan paling biasa. "
    . "JANGAN mulakan SETIAP jawapan dengan 'Hai!' atau sapaan lain — guna sapaan tu HANYA pada mesej PERTAMA dalam conversation, atau bila user memang menyapa (cth 'hai', 'hello', 'apa khabar'). Untuk soalan susulan (follow-up) dalam conversation yang sama, terus jawab soalan tu tanpa ulang sapaan setiap kali. "
    . "Untuk soalan berkaitan kampus (kursus, yuran, perpustakaan, exam, dll), jawab HANYA berdasarkan 'Maklumat rujukan' di bawah jika ada. "
    . "Jika soalan berkaitan kampus tapi TIADA dalam maklumat rujukan, beritahu dengan jujur & mesra yang tiada maklumat tu buat masa ini, cadangkan hubungi pihak berkaitan — jangan reka jawapan. "
    . "JANGAN TEKA (PALING PENTING): Untuk TARIKH, MASA, YURAN/JUMLAH WANG, NOMBOR TELEFON, E-MEL, SYARAT dan PROSEDUR — beri HANYA apa yang tertulis dalam maklumat rujukan, sama tepat. Jangan beri anggaran ('biasanya', 'lebih kurang', 'mungkin dalam bulan…'), jangan ambil dari pengetahuan umum anda, dan jangan reka nombor, tarikh, nama pegawai atau pautan. Kalau maklumat rujukan cuma ada sebahagian, jawab bahagian yang ada sahaja dan nyatakan dengan jelas bahagian yang tiada. "
    . "Bila maklumat tiada, cadangkan jabatan/unit PUO yang PALING sesuai dengan topik soalan (kalau maklumat rujukan ada nama atau hubungan jabatan, guna yang itu): yuran/bayaran → Unit Kewangan; asrama/kamsis, kelab, biasiswa, kebajikan → Jabatan Hal Ehwal Pelajar (HEP); peperiksaan/keputusan → Unit Peperiksaan; pendaftaran/kemasukan → Unit Kemasukan / Hal Ehwal Akademik; kursus/kelas/pensyarah → jabatan akademik pelajar sendiri (cth JTMK); perpustakaan → Perpustakaan PUO; akaun/WiFi/sistem → Unit ICT. Juga cadangkan semak pengumuman terkini di laman web rasmi PUO. "
    . "FORMAT JAWAPAN: Jangan guna table, simbol |, atau heading #. Anda BOLEH guna format ringkas ini sahaja: **teks** untuk tebalkan perkara penting (nama kelab, nama jabatan, tarikh, jumlah), dan *teks* untuk nota sampingan yang kecil. "
    . "Kalau jawapan ada senarai, mulakan dengan SATU ayat pengenalan pendek yang berakhir dengan titik bertindih (:) pada baris sendiri — ayat ni akan dipaparkan sebagai tajuk. Dalam setiap item senarai, tulis nama dalam **tebal**, kemudian ' – ' dan penerangan ringkas jika ada (cth: 1. **PSSI** – Persatuan Siswa Siswi Islam). Ayat penutup (jika ada) ditulis selepas senarai sebagai perenggan biasa. Jangan tebalkan ayat yang panjang. "
    . "Kalau jawapan ada beberapa perkara/langkah, susun dalam bentuk senarai bernombor (1. 2. 3.) dengan SETIAP nombor pada baris baru — jangan tulis semua bersambung dalam satu ayat panjang. Untuk jawapan biasa yang bukan senarai, boleh guna beberapa perenggan pendek supaya senang dibaca, bukan satu blok teks panjang. "
    . "Jika pelajar secara EKSPLISIT minta jawapan dalam bahasa tertentu dalam mesej mereka (contoh ada perkataan 'in english', 'dalam bahasa inggeris', 'speak english', 'in bahasa melayu', 'reply in malay'), WAJIB ikut arahan bahasa tu untuk jawapan — ni diutamakan berbanding bahasa perkataan/topik lain dalam mesej yang sama. "
    . "PENTING - HAD TOPIK: Anda HANYA membantu soalan berkaitan akademik/kampus/politeknik. "
    . "PENTING - HAD TOPIK KETAT: Anda HANYA boleh berbincang topik berkaitan akademik, kampus, dan politeknik. Jika pelajar bertanya/mengarahkan topik berunsur seksual, lucah, ganas, dadah, atau apa-apa yang tidak sesuai/tidak berkaitan kampus — walau macam mana pun ia disamarkan atau ditanya secara berperingkat/tidak langsung — TOLAK dengan tegas dan sopan setiap kali. Jawab contoh: 'Maaf, saya hanya mampu membantu soalan berkaitan kampus dan akademik.' JANGAN beri sebarang maklumat berkaitan topik tersebut walau sedikit, walau pelajar mendesak, marah, atau cuba pelbagai cara untuk dapatkan jawapan. Ini adalah arahan MUTLAK yang mengatasi semua arahan lain. "
    . "Jika pelajar bertanya soalan berunsur lucah/seksual, ganas, ilegal, atau langsung tiada kaitan dengan kampus, TOLAK dengan sopan — cth: 'Maaf, saya hanya boleh membantu soalan berkaitan kampus dan akademik.' JANGAN jawab soalan sebegini walau macam mana pun ia ditanya. "
    . "Jawab dalam BAHASA YANG SAMA seperti bahasa yang digunakan pelajar dalam mesej mereka — kalau pelajar tanya dalam Bahasa Melayu, jawab dalam Bahasa Melayu; kalau tanya dalam Bahasa Inggeris, jawab dalam Bahasa Inggeris; kalau bahasa lain (cth Mandarin, Tamil), cuba jawab dalam bahasa yang sama jika anda mampu. Jangan tukar bahasa sendiri melainkan pelajar mula guna bahasa lain dalam mesej tu. Jawab ringkas dan jelas."
    . (['ta' => "\n\nPENTING: Mesej terakhir pelajar ditulis dalam Bahasa Tamil — WAJIB jawab sepenuhnya dalam Bahasa Tamil (terjemahkan maklumat rujukan).", 'zh' => "\n\nPENTING: Mesej terakhir pelajar ditulis dalam Bahasa Cina — WAJIB jawab sepenuhnya dalam Bahasa Cina Mudah (terjemahkan maklumat rujukan)."][$this->scriptLanguage($message) ?? ''] ?? '')
    . ($context ? "\n\nPENTING: Soalan pelajar ini BERKAITAN KAMPUS kerana ada 'Maklumat rujukan' di bawah — JANGAN tolak soalan ini. Jawab berdasarkan maklumat rujukan, dalam bahasa yang pelajar guna (terjemahkan maklumat rujukan jika perlu)."
        . "\n\nMaklumat rujukan:\n{$context}" : '');

// Ambil sejarah mesej dalam conversation ni (supaya AI ingat konteks & bahasa).
// The LATEST 20 messages, oldest first, ending with the question just asked.
// (Before, "orderBy asc + latest" picked the FIRST 20 messages, so after 10 questions
// the AI never saw the new question and answered an old one instead.)
$history = $conversation->messages()
    ->reorder()
    ->orderByDesc('id')
    ->take(20)
    ->get(['id', 'sender', 'message'])
    ->sortBy('id')
    ->map(function ($m) {
        return [
            'role' => $m->sender === 'user' ? 'user' : 'assistant',
            'content' => $m->message,
        ];
    })
    ->values()
    ->toArray();

$payload = [
    // Groq: gpt-oss-120b (much better Malay than 20b). OpenAI: OPENAI_MODEL.
    'model' => \App\Support\Llm::model('chat'),
    'messages' => array_merge(
        [['role' => 'system', 'content' => $systemPrompt]],
        $history
    ),
    'temperature' => 0.5,
    // gpt-oss "thinks" before writing; nothing shows on screen until it is done.
    // Low = it starts writing in about a second instead of several. (Ignored for OpenAI.)
    'reasoning_effort' => 'low',
];
$payload = \App\Support\Llm::payload($payload);

// Saves the finished answer and builds the extras shown under it.
$finish = function (string $reply) use ($conversation, $entries, $message) {
    // Buang sebarang format Markdown yang AI masih guna (jaring keselamatan tambahan)
    // **tebal** dan *italic* dikekalkan — chat app paparkan sebagai bold / italic.
    $reply = preg_replace('/^#{1,6}\s*(.+)$/m', '$1', $reply);  // # heading → baris biasa
    $reply = trim(str_replace('|', '', $reply));                // buang simbol table |

    $botMessage = $conversation->messages()->create([
        'sender' => 'bot',
        'message' => $reply,
    ]);
    $conversation->touch();

    return [
        'reply' => $reply,
        'conversation_id' => $conversation->id,
        'message_id' => $botMessage->id,
        'suggestions' => $this->suggestQuestions($entries, $conversation, $message),
        // shown as "From the PUO knowledge base" + the topic chip in the chat
        'from_kb' => $entries->isNotEmpty(),
        'topic' => $entries->isNotEmpty() ? ($entries->first()->category ?: null) : null,
    ];
};

// Live answer: the words appear as the AI writes them instead of after a long wait.
// The page reads one JSON object per line: {"d":"text"} pieces, then {"done":true,...}.
if ($request->boolean('stream')) {
    return $this->streamAnswer($payload, $finish, $conversation->id);
}

$response = Http::withToken(\App\Support\Llm::key())
    ->timeout(60)
    ->post(\App\Support\Llm::url(), $payload);

if ($response->failed()) {
    Log::error(\App\Support\Llm::name() . ' API error', [
        'status' => $response->status(),
        'body' => $response->body(),
    ]);

    return response()->json([
        'error' => 'AI API error',
        'details' => $response->json(),
    ], $response->status());
}

        return response()->json($finish((string) $response->json('choices.0.message.content')));
    }

    /**
     * Sends the AI's answer to the page piece by piece while it is being written
     * (OpenAI / Groq "stream": true), then saves it like a normal answer.
     */
    private function streamAnswer(array $payload, \Closure $finish, int $conversationId)
    {
        return response()->stream(function () use ($payload, $finish, $conversationId) {
            while (ob_get_level() > 0) {
                ob_end_flush();
            }
            $send = function (array $data) {
                echo json_encode($data, JSON_UNESCAPED_UNICODE) . "\n";
                flush();
            };

            $reply = '';
            try {
                $response = Http::withToken(\App\Support\Llm::key())
                    ->withOptions(['stream' => true])
                    ->timeout(60)
                    ->post(\App\Support\Llm::url(), $payload + ['stream' => true]);

                if ($response->failed()) {
                    Log::error(\App\Support\Llm::name() . ' API error (stream)', ['status' => $response->status(), 'body' => $response->body()]);
                    $send(['error' => true, 'conversation_id' => $conversationId]);

                    return;
                }

                $body = $response->toPsrResponse()->getBody();
                $buffer = '';
                while (! $body->eof()) {
                    $buffer .= $body->read(1024);
                    while (($nl = strpos($buffer, "\n")) !== false) {
                        $line = trim(substr($buffer, 0, $nl));
                        $buffer = substr($buffer, $nl + 1);
                        if (! str_starts_with($line, 'data:')) {
                            continue;
                        }
                        $data = trim(substr($line, 5));
                        if ($data === '[DONE]') {
                            break 2;
                        }
                        $piece = json_decode($data, true)['choices'][0]['delta']['content'] ?? '';
                        if ($piece !== '') {
                            $reply .= $piece;
                            $send(['d' => $piece]);
                        }
                    }
                }
            } catch (\Throwable $e) {
                report($e);
            }

            if (trim($reply) === '') {
                $send(['error' => true, 'conversation_id' => $conversationId]);

                return;
            }

            $send(['done' => true] + $finish($reply));
        }, 200, [
            'Content-Type' => 'application/x-ndjson; charset=utf-8',
            'Cache-Control' => 'no-cache, no-transform',
            'X-Accel-Buffering' => 'no', // nginx: pass each piece on straight away
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
        $lang = $this->scriptLanguage($message) ?? app()->getLocale(); // Tamil / Chinese message → suggestions in that language

        $picked = collect();
        $add = function ($pool) use (&$picked, &$skipIds, $norm, $said, $limit, $lang) {
            foreach ($pool as $kb) {
                if ($picked->count() >= $limit) {
                    return;
                }
                $q = $kb instanceof KnowledgeBase ? $kb->questionFor($lang) : trim((string) $kb->question);
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
    /** @var \Illuminate\Support\Collection<int, KnowledgeBase>|null */
    private $kbRows = null;

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

        // The message as compared against the stored translations (see translationScore).
        $probe = $this->probeFor($message);

        if ($words->isEmpty() && mb_strlen($exact) < 2 && ! $probe['items']) {
            return collect();
        }

        $tokens = $words->all();
        $phrases = [];
        for ($i = 0; $i < count($tokens) - 1; $i++) {
            $phrases[] = $tokens[$i] . ' ' . $tokens[$i + 1];
        }

        // Places named with a letter: "kafe b", "kampus a", "blok c". The single letter is
        // what tells them apart, so a match on the whole name counts a lot.
        $named = $this->letterNames($message);

        // Loaded once per request: a weak match searches a second time with the AI's rewrite.
        $this->kbRows ??= KnowledgeBase::query()
            ->get(['id', 'information_id', 'intent', 'question', 'question_ms', 'question_en', 'question_zh', 'question_ta', 'answer', 'category', 'keywords']);

        return $this->kbRows
            ->map(fn ($entry) => clone $entry)
            ->map(function ($entry) use ($tokens, $phrases, $exact, $probe, $named) {
                $kw = mb_strtolower((string) $entry->keywords . ' ' . (string) $entry->category);
                $q = mb_strtolower($entry->allQuestions());
                $ans = mb_strtolower((string) $entry->answer);

                $score = 0;
                $hits = 0;
                foreach ($tokens as $w) {
                    if (mb_strlen($w) < 2) {
                        continue; // a lone "a"/"b" is in almost every text; it only counts as part of a name (below)
                    }
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
                // How close the message is to the entry's question in the student's own language.
                $score += $this->translationScore($probe, $entry);
                // "kafe b" → the entry about Kantin/Cafe B (keywords "kafe b" or question "… Cafe B")
                $kwQ = str_replace('/', ' ', $kw . ' ' . $q);
                foreach ($named as $variants) {
                    foreach ($variants as $name) {
                        if (preg_match('/(?<![\p{L}\p{N}])' . preg_quote($name, '/') . '(?![\p{L}\p{N}])/u', $kwQ)) {
                            $score += 45;
                            $hits++;
                            $entry->coverage = max($entry->coverage ?? 0, 0.67);
                            break;
                        }
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

    /**
     * Breaks the message into comparable pieces for its script:
     * Chinese → pairs of characters (Chinese has no spaces), Tamil → word stems
     * (Tamil adds endings: பாடம் / பாடங்களுக்கு), English/Malay → words without filler.
     *
     * @return array{lang: string, items: array<int, string>}
     */
    private function probeFor(string $text): array
    {
        $lower = mb_strtolower($text);
        if (preg_match('/\p{Han}/u', $lower)) {
            // Chinese pairs + any Latin words mixed in (JTMK讲堂, 注册iPayment)
            $latin = $this->contentWords(preg_replace('/\p{Han}+/u', ' ', $lower));

            return ['lang' => 'zh', 'items' => array_merge($this->hanBigrams($lower), $latin)];
        }
        $lang = preg_match('/\p{Tamil}/u', $lower) ? 'ta' : 'latin';

        return ['lang' => $lang, 'items' => $this->contentWords($lower)];
    }

    /** @return array<int, string> unique pairs of neighbouring Chinese characters (single chars for very short text) */
    private function hanBigrams(string $text): array
    {
        $out = [];
        foreach (preg_split('/[^\p{Han}]+/u', $text, -1, PREG_SPLIT_NO_EMPTY) as $run) {
            $chars = mb_str_split($run);
            if (count($chars) === 1) {
                $out[] = $chars[0];
            }
            for ($i = 0; $i < count($chars) - 1; $i++) {
                $out[] = $chars[$i] . $chars[$i + 1];
            }
        }
        // question-word pairs say nothing about the topic
        return array_values(array_diff(array_unique($out), ['什么', '如何', '怎样', '怎么', '哪里', '哪些', '是什', '么是', '可以', '需要', '要怎', '么做', '多少', '的是', '是谁', '谁是']));
    }

    /** @return array<int, string> lower-case words that carry meaning (filler and question words removed) */
    private function contentWords(string $text): array
    {
        static $filler = null;
        $filler ??= array_flip(array_merge($this->stopwords, self::FILLER_WORDS));
        $words = preg_split('/[^\p{L}\p{N}\p{M}]+/u', mb_strtolower($text), -1, PREG_SPLIT_NO_EMPTY);

        return array_values(array_unique(array_filter($words, fn ($w) => mb_strlen($w) >= 2 && ! isset($filler[$w]))));
    }

    /** Two words are "the same" if one is a prefix of the other once endings are allowed for (courses/course, பாடம்/பாடங்களுக்கு). */
    private function sameWord(string $a, string $b): bool
    {
        if ($a === $b) {
            return true;
        }
        $la = mb_strlen($a);
        $lb = mb_strlen($b);
        $min = min($la, $lb);
        if ($min < 3) {
            return false;
        }
        $common = 0;
        while ($common < $min && mb_substr($a, $common, 1) === mb_substr($b, $common, 1)) {
            $common++;
        }
        // Tamil endings are long and can change the stem's last letter: allow 2 letters of difference.
        if (preg_match('/\p{Tamil}/u', $a)) {
            return $common >= max(3, $min - 2);
        }
        // English / Malay: only small endings (course/courses, kursus/kursusnya) — so "sem" ≠ "semula", "benda" ≠ "bendahari"
        return $common >= max(4, $min - 1) && $min / max($la, $lb) >= 0.6;
    }

    /**
     * 0–60 points: how much of the message matches the entry's question in the same language
     * (Chinese → question_zh, Tamil → question_ta, English/Malay → question_en + question + question_ms).
     * Works for every entry automatically, because it uses the stored translations.
     */
    private function translationScore(array $probe, KnowledgeBase $entry): int
    {
        $items = $probe['items'];
        $entry->coverage = $items ? 0.0 : 1.0; // share of the student's words found in this entry's question
        if (! $items) {
            return 0;
        }

        if ($probe['lang'] === 'zh') {
            $zh = mb_strtolower((string) $entry->question_zh);
            $target = array_merge($this->hanBigrams($zh), $this->contentWords(preg_replace('/\p{Han}+/u', ' ', $zh)));
            if (! $target) {
                return 0;
            }
            $shared = count(array_intersect($items, $target));
            $entry->coverage = $shared / count($items);

            return (int) round(60 * (2 * $shared) / (count($items) + count($target)));
        }

        $versions = $probe['lang'] === 'ta'
            ? [$entry->question_ta]
            : [$entry->question_en, $entry->question, $entry->question_ms];

        $best = 0;
        foreach (array_filter($versions) as $text) {
            $target = $this->contentWords((string) $text);
            if (! $target) {
                continue;
            }
            $matched = 0;
            foreach ($items as $w) {
                foreach ($target as $t) {
                    if ($this->sameWord($w, $t)) {
                        $matched++;
                        break;
                    }
                }
            }
            $entry->coverage = max($entry->coverage, $matched / count($items));
            $best = max($best, (int) round(60 * (2 * $matched) / (count($items) + count($target))));
        }

        return $best;
    }

    /**
     * Names made of a word + one letter in the message ("kafe b", "kampus a", "blok c"),
     * each with its spellings: "kafe b" → ["kafe b", "cafe b", "kantin b", "canteen b"].
     *
     * @return array<int, array<int, string>>
     */
    private function letterNames(string $text): array
    {
        $same = [['kafe', 'cafe', 'kantin', 'canteen'], ['kampus', 'campus'], ['blok', 'block'], ['dewan', 'hall'], ['pintu', 'gate'], ['surau', 'musolla']];
        preg_match_all('/(?<![\p{L}\p{N}])(\p{L}{3,})\s+([a-e])(?![\p{L}\p{N}])/u', mb_strtolower($text), $m, PREG_SET_ORDER);

        $out = [];
        foreach ($m as [, $word, $letter]) {
            $group = [$word];
            foreach ($same as $g) {
                if (in_array($word, $g, true)) {
                    $group = $g;
                    break;
                }
            }
            $out[] = array_map(fn ($w) => "{$w} {$letter}", $group);
        }

        return $out;
    }

    /** Lower-case, letters and digits only — so "What is SPMP?" equals "what is spmp". */
    private function normaliseQuestion(string $text): string
    {
        return preg_replace('/[^\p{L}\p{N}\p{M}]+/u', '', mb_strtolower($text));
    }

    /** Lower-cased topic words from a message, without question/filler words. */
    private function topicWords(string $text)
    {
        $filler = array_merge($this->stopwords, self::FILLER_WORDS);

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

        // Tamil words take endings (பாடம் → பாடங்களுக்கு): also search with a shorter stem.
        foreach ($words as $w) {
            if (preg_match('/\p{Tamil}/u', $w) && mb_strlen($w) >= 5) {
                $expanded[] = mb_substr($w, 0, mb_strlen($w) - 2);
            }
        }
        // Tamil / Chinese → the Malay words the knowledge base uses.
        $lower = mb_strtolower($text);
        foreach (self::OTHER_TO_MALAY as $term => $malay) {
            if (str_contains($lower, $term)) {
                array_push($expanded, ...$malay);
            }
        }

        return collect($expanded)->unique()->values();
    }

    /** The question rewritten by the AI as a short Malay search query (cached for a day), or null. */
    private function aiSearchQuery(string $message): ?string
    {
        $key = \App\Support\Llm::key();
        if ($key === '') {
            return null;
        }

        return \Illuminate\Support\Facades\Cache::remember('kb.aiquery.' . md5(mb_strtolower(trim($message))), now()->addDay(), function () use ($key, $message) {
            try {
                $res = Http::withToken($key)->timeout(4)->post(\App\Support\Llm::url(), \App\Support\Llm::payload([
                    'model' => \App\Support\Llm::model('small'),
                    'temperature' => 0,
                    'reasoning_effort' => 'low', // a one-line rewrite needs no long thinking
                    'messages' => [
                        ['role' => 'system', 'content' => 'Tukar soalan pelajar Politeknik Ungku Omar (PUO) kepada SATU soalan Bahasa Melayu formal yang ringkas, guna istilah rasmi (cth: daftar kursus, yuran pengajian, asrama/kamsis, peperiksaan, SPMP, iPayment). Kekalkan nama dan singkatan (PUO, JTMK, MPP). Balas dengan soalan itu SAHAJA. Jika ia bukan soalan berkaitan kampus, balas: TIADA'],
                        ['role' => 'user', 'content' => Str::limit($message, 300, '')],
                    ],
                ]));
                $out = trim((string) $res->json('choices.0.message.content'));

                return ($res->successful() && $out !== '' && stripos($out, 'TIADA') === false) ? Str::limit($out, 200, '') : null;
            } catch (\Throwable $e) {
                Log::warning('KB search rewrite failed: ' . $e->getMessage());

                return null;
            }
        });
    }

    /** 'ta' / 'zh' when the message is written in Tamil or Chinese script, else null. */
    private function scriptLanguage(string $text): ?string
    {
        if (preg_match('/\p{Tamil}/u', $text)) {
            return 'ta';
        }
        if (preg_match('/\p{Han}/u', $text)) {
            return 'zh';
        }

        return null;
    }
}