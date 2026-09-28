<?php

namespace App\Http\Controllers;

use App\Support\DeviceName;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Profile → Privacy & Security: signed-in devices, sign-in history,
 * clearing chat history and downloading your own data.
 */
class PrivacySecurityController extends Controller
{
    public function show(Request $request): View
    {
        $user = $request->user();
        $currentId = $request->session()->getId();

        $devices = DB::table('sessions')
            ->where('user_id', $user->id)
            ->orderByDesc('last_activity')
            ->get(['id', 'ip_address', 'user_agent', 'last_activity'])
            ->map(fn ($s) => (object) array_merge(DeviceName::from($s->user_agent), [
                'id' => $s->id,
                'ip' => $s->ip_address,
                'current' => $s->id === $currentId,
                'last_active' => Carbon::createFromTimestamp($s->last_activity, config('app.timezone')),
            ]))
            ->sortByDesc('current')
            ->values();

        $logins = Schema::hasTable('login_activities')
            ? DB::table('login_activities')
                ->where('user_id', $user->id)
                ->orderByDesc('created_at')
                ->limit(8)
                ->get()
                ->map(fn ($l) => (object) array_merge(DeviceName::from($l->user_agent), [
                    'ip' => $l->ip_address,
                    'at' => Carbon::parse($l->created_at),
                ]))
            : collect();

        return view('student.privacy-security', [
            'devices' => $devices,
            'logins' => $logins,
            'chatCount' => $user->chatConversations()->count(),
        ]);
    }

    /** Sign out one other device. */
    public function logoutDevice(Request $request, string $session): RedirectResponse
    {
        abort_if($session === $request->session()->getId(), 422);

        DB::table('sessions')
            ->where('user_id', $request->user()->id)
            ->where('id', $session)
            ->delete();

        return back()->with('success', __('That device has been signed out.'));
    }

    /** Sign out every device except this one. */
    public function logoutOthers(Request $request): RedirectResponse
    {
        $count = DB::table('sessions')
            ->where('user_id', $request->user()->id)
            ->where('id', '!=', $request->session()->getId())
            ->delete();

        return back()->with('success', $count === 0
            ? __('No other devices were signed in.')
            : __('Signed out :count other device(s).', ['count' => $count]));
    }

    /** Delete every chatbot conversation (their messages go with them). */
    public function clearChats(Request $request): RedirectResponse
    {
        $request->user()->chatConversations()->delete();

        return back()->with('success', __('Your chat history has been cleared.'));
    }

    /** Download everything RakanKampus stores about the student, as a JSON file. */
    public function download(Request $request): StreamedResponse
    {
        $user = $request->user();

        $data = [
            'exported_at' => now()->toIso8601String(),
            'profile' => collect($user->toArray())
                ->except(['password', 'remember_token', 'photo_data', 'role', 'is_admin'])
                ->all(),
            'chat_conversations' => $user->chatConversations()
                ->with(['messages' => fn ($q) => $q->orderBy('created_at')
                    ->select('id', 'chat_conversation_id', 'sender', 'message', 'created_at')])
                ->orderBy('created_at')
                ->get(['id', 'title', 'created_at', 'updated_at'])
                ->toArray(),
            'reminders' => $user->reminders()->orderBy('due_at')->get()->makeHidden(['user_id'])->toArray(),
            'class_schedules' => $user->classSchedules()->get()->makeHidden(['user_id'])->toArray(),
            'sign_in_history' => Schema::hasTable('login_activities')
                ? DB::table('login_activities')->where('user_id', $user->id)->orderByDesc('created_at')
                    ->get(['ip_address', 'user_agent', 'created_at'])->toArray()
                : [],
        ];

        $filename = 'rakankampus-my-data-' . now()->format('Y-m-d') . '.json';

        return response()->streamDownload(function () use ($data) {
            echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }, $filename, ['Content-Type' => 'application/json']);
    }
}
