<?php

namespace App\Support;

use App\Models\ChatMessage;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Icon + colours for a knowledge base topic (picked from words in its title),
 * its tags (from "Title — tag1, tag2" descriptions) and how often the bot used it.
 */
class TopicLook
{
    private const ICONS = [
        'users' => '<circle cx="9" cy="8" r="4"/><path d="M2 21a7 7 0 0 1 14 0M17 11a3 3 0 1 0 0-6M22 21a6 6 0 0 0-5-6"/>',
        'pin' => '<path d="M12 22s7-7.5 7-12a7 7 0 0 0-14 0c0 4.5 7 12 7 12z"/><circle cx="12" cy="10" r="2.5"/>',
        'cap' => '<path d="m2 9 10-5 10 5-10 5z"/><path d="M6 11v5c3 2 9 2 12 0v-5"/>',
        'card' => '<rect x="2" y="5" width="20" height="14" rx="3"/><path d="M2 10h20"/>',
        'history' => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
        'font' => '<path d="M4 20 10 4h4l6 16M7 14h10"/>',
        'chip' => '<rect x="5" y="5" width="14" height="14" rx="2"/><path d="M9 1v4M15 1v4M9 19v4M15 19v4M1 9h4M1 15h4M19 9h4M19 15h4"/>',
        'cal' => '<rect x="3" y="4" width="18" height="17" rx="3"/><path d="M3 9h18M8 2v4M16 2v4"/>',
        'home' => '<path d="m3 11 9-7 9 7v9a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1z"/>',
        'ball' => '<circle cx="12" cy="12" r="9"/><path d="M12 3v18M3 12h18"/>',
        'book' => '<path d="M4 19.5V5a2 2 0 0 1 2-2h13v16H6.5A2.5 2.5 0 0 0 4 21.5"/><path d="M8 7h7M8 11h5"/>',
    ];

    private const TONES = [
        'green' => ['#e8f2eb', '#3f6e4f'], 'amber' => ['#fdf1dc', '#c7851e'], 'red' => ['#fdecec', '#d64545'],
        'blue' => ['#e7f0fb', '#3c6fb0'], 'violet' => ['#f1ecfb', '#7a55c7'], 'grey' => ['#eef2ef', '#46574d'],
    ];

    private const RULES = [
        ['spmp|sistem|portal|cidos', 'chip', 'blue'],
        ['kelab|persatuan|club', 'users', 'amber'], ['kantin|surau|makan', 'pin', 'green'], ['program|pengajian|diploma|kursus', 'cap', 'red'],
        ['dewan|kuliah|bilik|lokasi', 'pin', 'green'], ['payment|bayar|yuran|fee', 'card', 'blue'], ['pengurusan|ketua|staff|pensyarah', 'users', 'violet'],
        ['sejarah|pengenalan|visi|misi', 'history', 'amber'], ['singkatan|akronim', 'font', 'grey'],
        ['peperiksaan|exam|jadual', 'cal', 'amber'], ['asrama|kolej|hostel', 'home', 'green'], ['sukan|sport', 'ball', 'green'],
    ];

    /** @return array{svg: string, bg: string, fg: string} */
    public static function for(string $title): array
    {
        $t = mb_strtolower($title);
        [$icon, $tone] = ['book', 'green'];
        foreach (self::RULES as [$re, $i, $c]) {
            if (preg_match('/' . $re . '/u', $t)) {
                [$icon, $tone] = [$i, $c];
                break;
            }
        }

        return [
            'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' . self::ICONS[$icon] . '</svg>',
            'bg' => self::TONES[$tone][0],
            'fg' => self::TONES[$tone][1],
        ];
    }

    /** "Title — tag1, tag2" → ['tag1', 'tag2'] */
    public static function tags(?string $description, int $max = 2): array
    {
        $parts = preg_split('/\s+—\s+/u', (string) $description);
        if (count($parts) < 2) {
            return [];
        }

        return collect(explode(',', end($parts)))->map(fn ($x) => trim($x))->filter()->take($max)->values()->all();
    }

    /** information_id => times the bot answered from that topic between $from and $to */
    public static function usage($from, $to = null): Collection
    {
        try {
            return ChatMessage::query()
                ->join('knowledge_bases', 'knowledge_bases.id', '=', 'chat_messages.knowledge_base_id')
                ->where('chat_messages.created_at', '>=', $from)
                ->when($to, fn ($q) => $q->where('chat_messages.created_at', '<', $to))
                ->groupBy('knowledge_bases.information_id')
                ->select('knowledge_bases.information_id', DB::raw('COUNT(*) as n'))
                ->pluck('n', 'information_id')
                ->map(fn ($n) => (int) $n);
        } catch (\Throwable $e) {
            return collect();
        }
    }
}
