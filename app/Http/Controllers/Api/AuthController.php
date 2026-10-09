<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\SetLocale;
use App\Models\ApiToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Mobile app account: sign in / out, the student's profile and settings.
 * Every other endpoint needs "Authorization: Bearer <token>" from login().
 */
class AuthController extends Controller
{
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:100'],
        ]);

        $user = User::where('email', $data['email'])->first();
        if (! $user || ! Hash::check($data['password'], $user->password)) {
            return response()->json(['message' => __('Incorrect email or password.')], 422);
        }
        if ($user->isAdmin()) {
            return response()->json(['message' => __('This is an admin account. The admin panel works on a PC only.')], 403);
        }

        $user->forceFill(['last_login_at' => now()])->saveQuietly();
        try {
            DB::table('login_activities')->insert([
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'user_agent' => substr('RakanKampus app · ' . ($data['device_name'] ?? '') . ' · ' . $request->userAgent(), 0, 1000),
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
            report($e);
        }

        return response()->json([
            'token' => ApiToken::issue($user, $data['device_name'] ?? null),
            'user' => self::userJson($user),
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentApiToken?->delete();

        return response()->json(['success' => true]);
    }

    public function me(Request $request)
    {
        return response()->json(['user' => self::userJson($request->user())]);
    }

    /** Edit Profile (same rules as the website). */
    public function updateProfile(Request $request, ProfileController $profile)
    {
        return response()->json(['success' => true, 'user' => self::userJson($profile->saveProfile($request))]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);
        $user = $request->user();
        if (! Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => __('Current password is incorrect.'), 'errors' => ['current_password' => [__('Current password is incorrect.')]]], 422);
        }
        $user->password = $request->password;
        $user->save();

        // Sign out the student's other phones, keep this one.
        if ($request->boolean('logout_other_devices')) {
            $user->apiTokens()->where('id', '!=', $user->currentApiToken?->id)->delete();
        }

        return response()->json(['success' => true]);
    }

    /** Language, theme and notification settings — send only what changed. */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'language' => 'sometimes|string|in:' . implode(',', SetLocale::SUPPORTED),
            'theme' => 'sometimes|string|in:' . implode(',', ProfileController::THEMES),
            'reminder_notifications' => 'sometimes|boolean',
            'class_notifications' => 'sometimes|boolean',
            'dnd_until' => 'sometimes|nullable|date',
        ]);

        $user = $request->user();
        if ($request->has('language')) {
            $user->language = $request->language;
        }
        if ($request->has('theme')) {
            $user->theme = $request->theme;
        }
        $notify = array_merge(
            ['reminder_notifications' => true, 'class_notifications' => true, 'dnd_until' => null],
            $user->notification_settings ?? []
        );
        foreach (['reminder_notifications', 'class_notifications'] as $key) {
            if ($request->has($key)) {
                $notify[$key] = $request->boolean($key);
            }
        }
        if ($request->has('dnd_until')) {
            $notify['dnd_until'] = $request->dnd_until;
        }
        $user->notification_settings = $notify;
        $user->save();

        return response()->json(['success' => true, 'user' => self::userJson($user)]);
    }

    public static function userJson(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'student_id' => $user->student_id,
            'email' => $user->email,
            'phone' => $user->phone,
            'programme' => $user->programme,
            'photo' => $user->photo_data,      // data: URI or null
            'cover' => $user->cover_data,      // data: URI or null
            'language' => $user->language ?? 'en',
            'theme' => $user->theme ?? 'system',
            'notification_settings' => array_merge(
                ['reminder_notifications' => true, 'class_notifications' => true, 'dnd_until' => null],
                $user->notification_settings ?? []
            ),
        ];
    }
}
