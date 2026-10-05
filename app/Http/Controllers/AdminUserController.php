<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Admin → Users: every registered student, when they last logged in, and a way to
 * remove an account. Deleting a student also removes their chats, reminders,
 * classes, programmes and login history (database cascades), their open sessions
 * and any pending password-reset codes for their email.
 */
class AdminUserController extends Controller
{
    public function index()
    {
        $students = User::where(fn ($q) => $q->where('role', '!=', 'admin')->orWhereNull('role'))
            ->withCount(['chatConversations', 'reminders', 'classSchedules'])
            ->orderByDesc('created_at')
            ->get();

        $weekAgo = now()->subDays(7);

        return view('admin.users', [
            'students' => $students,
            'stats' => [
                'total' => $students->count(),
                'activeWeek' => $students->filter(fn ($u) => $u->last_login_at && $u->last_login_at->gte($weekAgo))->count(),
                'newWeek' => $students->filter(fn ($u) => $u->created_at && $u->created_at->gte($weekAgo))->count(),
                'never' => $students->whereNull('last_login_at')->count(),
            ],
        ]);
    }

    public function destroy(Request $request, User $user)
    {
        abort_if($user->isAdmin(), 403, 'Admin accounts cannot be deleted here.');
        abort_if($user->id === $request->user()->id, 403);

        DB::transaction(function () use ($user) {
            DB::table('sessions')->where('user_id', $user->id)->delete();
            DB::table('password_reset_tokens')->where('email', $user->email)->delete();
            if (\Illuminate\Support\Facades\Schema::hasTable('password_otps')) {
                DB::table('password_otps')->where('email', $user->email)->delete();
            }
            $user->delete();
        });

        return response()->json(['success' => true]);
    }
}
