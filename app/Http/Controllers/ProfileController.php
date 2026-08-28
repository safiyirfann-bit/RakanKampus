<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('student.edit-profile', [
            'user' => $request->user(),
        ]);
    }

    public function studentProfile(Request $request)
    {
        return view('student.profile', [
            'user' => $request->user(),
        ]);
    }

    public function uploadPhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $user = $request->user();

        if ($user->photo) {
            Storage::disk('public')->delete($user->photo);
        }

        $path = $request->file('photo')->store('avatars', 'public');

        $user->update(['photo' => $path]);

        return response()->json([
            'success' => true,
            'photoUrl' => Storage::url($path),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'first_name'  => 'required|string|max:255',
            'last_name'   => 'required|string|max:255',
            'student_id'  => 'nullable|string|max:255|unique:users,student_id,' . $request->user()->id,
            'email'       => 'required|email|max:255',
            'faculty'     => 'nullable|string|max:255',
            'phone'       => 'nullable|string|max:20',
        ]);

        $user = $request->user();
        $user->first_name  = $request->first_name;
        $user->last_name   = $request->last_name;
        $user->name        = $request->first_name . ' ' . $request->last_name;
        $user->student_id  = $request->student_id;
        $user->email       = $request->email;
        $user->faculty     = $request->faculty;
        $user->phone       = $request->phone;
        $user->save();

        return redirect()->route('student.profile.edit')->with('success', 'Profile updated successfully!');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = $request->user();

        if (! Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'Current password is incorrect.',
            ]);
        }

        if ($request->logout_other_devices) {
            Auth::logoutOtherDevices($request->current_password);
        }

        $user->password = $request->password;
        $user->save();

        return redirect()->route('student.profile.password')->with('success', 'Password updated successfully!');
    }

    public function notificationSettings(Request $request): View
    {
        $defaults = [
            'course_announcements' => true,
            'exam_alerts' => true,
            'fee_reminders' => true,
            'chatbot_replies' => true,
            'system_updates' => false,
            'events_promotions' => false,
            'preferred_method' => 'Push notifications',
            'dnd_until' => null,
        ];

        $settings = array_merge($defaults, $request->user()->notification_settings ?? []);

        return view('student.notification-settings', [
            'settings' => $settings,
        ]);
    }

    public function updateNotificationSettings(Request $request): RedirectResponse
    {
        $request->validate([
            'preferred_method' => 'required|string',
            'dnd_until' => 'nullable|date',
        ]);

        $settings = [
            'course_announcements' => $request->boolean('course_announcements'),
            'exam_alerts' => $request->boolean('exam_alerts'),
            'fee_reminders' => $request->boolean('fee_reminders'),
            'chatbot_replies' => $request->boolean('chatbot_replies'),
            'system_updates' => $request->boolean('system_updates'),
            'events_promotions' => $request->boolean('events_promotions'),
            'preferred_method' => $request->preferred_method,
            'dnd_until' => $request->dnd_until,
        ];

        $request->user()->update(['notification_settings' => $settings]);

        return redirect()->route('student.profile.notifications')->with('success', 'Notification settings updated!');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->user()->delete();

        Auth::logout();

        return redirect('/');
    }
}