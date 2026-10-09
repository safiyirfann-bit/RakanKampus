<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClassSchedule;
use App\Services\PopularQuestions;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/** Everything the app's Home screen needs, in one request. */
class HomeController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();
        $today = now()->startOfDay();

        $classes = $user->classSchedules()->get()
            ->sortBy(fn ($s) => array_search($s->day_of_week, ClassSchedule::DAYS) * 1440 + (int) str_replace(':', '', (string) $s->start_time))
            ->values()
            ->map(fn ($s) => [
                'id' => $s->id,
                'subject' => $s->subject,
                'day_of_week' => $s->day_of_week,
                'start_time' => $s->start_time,
                'end_time' => $s->end_time,
                'room' => $s->room,
                'lecturer' => $s->lecturer,
            ]);

        $reminders = $user->reminders()->where('due_at', '>=', now())->orderBy('due_at')->take(3)->get()
            ->map(function ($r) use ($today) {
                $days = (int) $today->diffInDays($r->due_at->copy()->startOfDay());

                return [
                    'id' => $r->id,
                    'subject' => $r->subject,
                    'type' => $r->type ?: 'Other',
                    'due_at' => $r->due_at->toIso8601String(),
                    'when' => $r->due_at->translatedFormat('D, j M · g:i A'),
                    'left' => $days === 0 ? __('Today') : ($days === 1 ? __('Tomorrow') : __(':n days', ['n' => $days])),
                    'days_left' => $days,
                ];
            });

        $conversations = $user->chatConversations()->latest('updated_at')->take(4)->get()
            ->map(function ($c) {
                $last = $c->messages()->latest('id')->first();

                return [
                    'id' => $c->id,
                    'title' => $c->title ?: __('New Conversation'),
                    'preview' => $last ? Str::limit($last->message, 60) : '',
                    'updated_at' => $c->updated_at->toIso8601String(),
                    'time' => $c->updated_at->diffForHumans(),
                ];
            });

        return response()->json([
            'user' => AuthController::userJson($user),
            'today' => now()->format('l'),
            'classes' => $classes,
            'reminders' => $reminders,
            'conversations' => $conversations,
            // Popular ones are students' own words; the defaults are translated (same as the website).
            'quick_questions' => array_map(fn ($q) => $q + ['label' => $q['popular'] ? $q['text'] : __($q['text'])], PopularQuestions::top(4)),
        ]);
    }
}
