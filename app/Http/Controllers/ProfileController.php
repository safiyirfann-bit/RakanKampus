<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            'photo' => 'required|image|mimes:jpg,jpeg,png,webp|max:8192',
        ]);

        // Profile photos are stored as a base64 data URI on the user's own
        // row instead of as a file on disk — Render's free plan has no
        // persistent disk, so anything written to local storage disappears
        // the moment the app redeploys or the free instance spins down.
        // The database row doesn't have that problem.
        $dataUri = $this->resizeToDataUri($request->file('photo')->getRealPath());

        $user = $request->user();
        $user->update([
            'photo' => null,
            'photo_data' => $dataUri,
        ]);

        return response()->json([
            'success' => true,
            'photoUrl' => $dataUri,
        ]);
    }

    /**
     * Downscale an uploaded image to a sensible avatar size and re-encode
     * it as a compact JPEG, returned as a data: URI ready to store and to
     * drop straight into an <img src="">. Keeping this small matters here
     * since it's going into a database column, not a file.
     */
    private function resizeToDataUri(string $path, int $maxDimension = 480, int $quality = 82): string
    {
        $source = @imagecreatefromstring(file_get_contents($path));

        if ($source === false) {
            abort(422, 'Could not process the uploaded image.');
        }

        $source = $this->applyExifOrientation($source, $path);

        $width = imagesx($source);
        $height = imagesy($source);
        $scale = min(1, $maxDimension / max($width, $height));
        $targetWidth = max(1, (int) round($width * $scale));
        $targetHeight = max(1, (int) round($height * $scale));

        // Flatten onto a white background so transparent PNGs don't turn
        // black once re-encoded as JPEG (JPEG has no alpha channel).
        $resized = imagecreatetruecolor($targetWidth, $targetHeight);
        $white = imagecolorallocate($resized, 255, 255, 255);
        imagefill($resized, 0, 0, $white);
        imagecopyresampled($resized, $source, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);
        imagedestroy($source);

        ob_start();
        imagejpeg($resized, null, $quality);
        $bytes = ob_get_clean();
        imagedestroy($resized);

        return 'data:image/jpeg;base64,' . base64_encode($bytes);
    }

    /**
     * Phone camera photos are very often saved "sideways" with an EXIF
     * orientation tag telling viewers how to rotate them for display. GD
     * ignores that tag, so without this a portrait selfie can come out
     * rotated 90°/upside-down once re-encoded.
     */
    private function applyExifOrientation(\GdImage $image, string $path): \GdImage
    {
        if (! function_exists('exif_read_data')) {
            return $image;
        }

        $exif = @exif_read_data($path);
        $orientation = $exif['Orientation'] ?? 1;

        return match ($orientation) {
            3 => imagerotate($image, 180, 0),
            6 => imagerotate($image, -90, 0),
            8 => imagerotate($image, 90, 0),
            default => $image,
        };
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

    public function language(Request $request): View
    {
        return view('student.language', [
            'currentLanguage' => $request->user()->language ?? 'en',
        ]);
    }

    public function updateLanguage(Request $request): RedirectResponse
    {
        $request->validate([
            'language' => 'required|string|in:' . implode(',', \App\Http\Middleware\SetLocale::SUPPORTED),
        ]);

        $request->user()->update(['language' => $request->language]);

        return redirect()->route('student.profile.language')->with('success', __('Language updated!'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->user()->delete();

        Auth::logout();

        return redirect('/');
    }
}