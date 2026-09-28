<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * One row per user per hour they were active. Powers the admin
     * Analytics page ("users online by hour" + day×hour heatmap).
     */
    public function up(): void
    {
        Schema::create('user_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->dateTime('active_at')->index(); // truncated to the hour
            $table->unique(['user_id', 'active_at']);
        });

        $this->backfill();
    }

    /**
     * Seed history from data that already exists, so the charts aren't
     * empty on day one: every chat message a user sent, and every
     * session's last activity.
     */
    private function backfill(): void
    {
        $rows = [];
        $add = function ($userId, $at) use (&$rows) {
            if (! $userId || ! $at) {
                return;
            }
            $hour = Carbon::parse($at)->startOfHour()->format('Y-m-d H:00:00');
            $rows[$userId.'|'.$hour] = ['user_id' => $userId, 'active_at' => $hour];
        };

        if (Schema::hasTable('chat_messages') && Schema::hasTable('chat_conversations')) {
            DB::table('chat_messages')
                ->join('chat_conversations', 'chat_conversations.id', '=', 'chat_messages.chat_conversation_id')
                ->when(Schema::hasColumn('chat_messages', 'sender'), fn ($q) => $q->where('chat_messages.sender', 'user'))
                ->select('chat_conversations.user_id', 'chat_messages.created_at')
                ->orderBy('chat_messages.id')
                ->chunk(1000, function ($chunk) use ($add) {
                    foreach ($chunk as $r) {
                        $add($r->user_id, $r->created_at);
                    }
                });
        }

        if (Schema::hasTable('sessions')) {
            foreach (DB::table('sessions')->whereNotNull('user_id')->get(['user_id', 'last_activity']) as $s) {
                $add($s->user_id, Carbon::createFromTimestamp($s->last_activity, config('app.timezone')));
            }
        }

        $existing = DB::table('users')->pluck('id')->flip();
        $rows = array_values(array_filter($rows, fn ($r) => isset($existing[$r['user_id']])));

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('user_activity_logs')->insertOrIgnore($chunk);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('user_activity_logs');
    }
};
