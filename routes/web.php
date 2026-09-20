<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\InformationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReminderController;
use App\Http\Controllers\ClassScheduleController;
use App\Http\Controllers\Admin\KnowledgeBaseController;
use App\Http\Controllers\Admin\UnansweredQuestionController;
use App\Http\Controllers\FeedbackController;

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

// Student Login
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::get('/login', [AuthController::class, 'showLogin']);
Route::post('/login', [AuthController::class, 'login']);

// Student Register
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// Admin Login
Route::get('/admin/login', [AuthController::class, 'showAdminLogin'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'adminLogin'])->name('admin.login.post');

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// External cron ping (token protected, see CronController)
Route::get('/cron/reminders', [\App\Http\Controllers\CronController::class, 'reminders'])
    ->middleware('throttle:30,1')
    ->name('cron.reminders');

/*
|--------------------------------------------------------------------------
| Student Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::put('/student/profile/update', [ProfileController::class, 'update'])
        ->name('student.profile.update');

    Route::post('/profile/photo', [ProfileController::class, 'uploadPhoto'])->name('profile.photo.upload');

   Route::get('/student/home', function (Illuminate\Http\Request $request) {
    $user = $request->user();

    $conversations = $user->chatConversations()
        ->latest('updated_at')
        ->take(4)
        ->get()
        ->map(function ($conversation) {
            $lastMessage = $conversation->messages()->latest()->first();

            return [
                'id' => $conversation->id,
                'title' => $conversation->title ?: 'New Conversation',
                'preview' => $lastMessage ? \Illuminate\Support\Str::limit($lastMessage->message, 50) : 'Belum ada mesej',
                'time' => $conversation->updated_at->diffForHumans(),
            ];
        });

    $now = now();
    $todayClasses = $user->classSchedules()
        ->where('day_of_week', $now->format('l'))
        ->get()
        ->sortBy(fn ($s) => $s->start_time)
        ->values()
        ->map(function ($schedule) use ($now) {
            $start = $now->copy()->setTimeFromTimeString($schedule->start_time . ':00');
            $end = $now->copy()->setTimeFromTimeString($schedule->end_time . ':00');

            $status = $now->lt($start) ? 'upcoming' : ($now->lt($end) ? 'ongoing' : 'past');

            return [
                'id' => $schedule->id,
                'subject' => $schedule->subject,
                'start_time' => $schedule->start_time,
                'end_time' => $schedule->end_time,
                'room' => $schedule->room,
                'lecturer' => $schedule->lecturer,
                'status' => $status,
            ];
        });

    return view('homepage', [
        'user' => $user,
        'conversations' => $conversations,
        'todayClasses' => $todayClasses,
    ]);
})->name('student.home');

    // Chat
    Route::get('/student/chat', function (Illuminate\Http\Request $request) {
        return view('chat', [
            'user' => $request->user(),
        ]);
    })->name('student.chat');

    // Chatbot API
    Route::post('/chatbot', [ChatbotController::class, 'chat'])->middleware('throttle:20,1')->name('chatbot.send');
    Route::get('/chatbot/history', [ChatbotController::class, 'history'])->name('chatbot.history');
    Route::get('/chatbot/{conversation}', [ChatbotController::class, 'show'])->name('chatbot.show');
    Route::put('/chatbot/{conversation}/rename', [ChatbotController::class, 'rename'])->name('chatbot.rename');
Route::delete('/chatbot/{conversation}', [ChatbotController::class, 'destroy'])->name('chatbot.destroy');

    // Reminders
    Route::get('/student/reminders', [ReminderController::class, 'index'])->name('student.reminders');
    Route::get('/student/reminders/history', [ReminderController::class, 'history'])->name('student.reminders.history');
    Route::post('/reminders', [ReminderController::class, 'store'])->name('reminders.store');
    Route::post('/reminders/ai-capture', [ReminderController::class, 'aiCapture'])->middleware('throttle:10,1')->name('reminders.aiCapture');
    Route::put('/reminders/{reminder}', [ReminderController::class, 'update'])->name('reminders.update');
    Route::delete('/reminders/{reminder}', [ReminderController::class, 'destroy'])->name('reminders.destroy');
    Route::post('/reminders/bulk-delete', [ReminderController::class, 'bulkDestroy'])->name('reminders.bulkDestroy');
    Route::post('/reminders/history/bulk-delete', [ReminderController::class, 'historyBulkDestroy'])->name('reminders.history.bulkDestroy');

    // Timetable
    Route::get('/student/timetable', [ClassScheduleController::class, 'index'])->name('student.timetable');
    Route::post('/timetable', [ClassScheduleController::class, 'store'])->name('timetable.store');
    Route::post('/timetable/ai-capture', [ClassScheduleController::class, 'aiCapture'])->middleware('throttle:10,1')->name('timetable.aiCapture');
    Route::put('/timetable/{schedule}', [ClassScheduleController::class, 'update'])->name('timetable.update');
    Route::delete('/timetable/{schedule}', [ClassScheduleController::class, 'destroy'])->name('timetable.destroy');

    // Web push subscriptions
    Route::post('/push/subscribe', [\App\Http\Controllers\PushSubscriptionController::class, 'store'])->name('push.subscribe');
    Route::post('/push/unsubscribe', [\App\Http\Controllers\PushSubscriptionController::class, 'destroy'])->name('push.unsubscribe');

    // Profile
    Route::get('/student/profile', [ProfileController::class, 'studentProfile'])
        ->name('student.profile');

    // Edit Profile
    Route::get('/student/profile/edit', [ProfileController::class, 'edit'])
        ->name('student.profile.edit');

    // Change Password
    Route::view('/student/profile/password', 'student.change-password')
        ->name('student.profile.password');

    Route::put('/student/profile/password', [ProfileController::class, 'updatePassword'])
        ->name('student.profile.password.update');


    // Privacy Settings
    Route::view('/student/profile/privacy', 'student.privacy-settings')
        ->name('student.profile.privacy');

    // Notification Settings
    Route::get('/student/profile/notifications', [ProfileController::class, 'notificationSettings'])
        ->name('student.profile.notifications');

    Route::put('/student/profile/notifications', [ProfileController::class, 'updateNotificationSettings'])
        ->name('student.profile.notifications.update');

    // Security Settings
    Route::view('/student/profile/security', 'student.security-settings')
        ->name('student.profile.security');

    // Help & Support page
    Route::view('/student/help-support', 'student.help-support')
        ->name('student.help-support');

    // About RakanKampus
    Route::view('/student/about', 'student.about')
        ->name('student.about');

    // Feedback submit route
    Route::post('/student/feedback', [FeedbackController::class, 'store'])
        ->name('student.feedback.store');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [InformationController::class, 'index'])
            ->name('dashboard');

        // Store new information
        Route::post('/information', [InformationController::class, 'store'])
            ->name('information.store');

        // Update information
        Route::put('/information/{information}', [InformationController::class, 'update'])
            ->name('information.update');

        // Delete information
        Route::delete('/information/{information}', [InformationController::class, 'destroy'])
            ->name('information.destroy');

        // Knowledge base — detail page for one Information
        Route::get('/information/{information}', [KnowledgeBaseController::class, 'show'])
            ->name('information.show');

        Route::post('/information/{information}/entries', [KnowledgeBaseController::class, 'store'])
            ->name('information.entries.store');

        Route::put('/information/{information}/entries/{entry}', [KnowledgeBaseController::class, 'update'])
            ->name('information.entries.update');

        Route::delete('/information/{information}/entries/{entry}', [KnowledgeBaseController::class, 'destroy'])
            ->name('information.entries.destroy');

        // Inbox
        Route::get('/inbox', [FeedbackController::class, 'inbox'])
            ->name('inbox');

        // Unanswered questions
Route::get('/unanswered', [UnansweredQuestionController::class, 'index'])
    ->name('unanswered.index');

Route::post('/unanswered/{unansweredQuestion}/answer', [UnansweredQuestionController::class, 'storeAndResolve'])
    ->name('unanswered.answer');

Route::put('/unanswered/{unansweredQuestion}/resolve', [UnansweredQuestionController::class, 'resolve'])
    ->name('unanswered.resolve');

        // Category pages
        Route::view('/category/mpp', 'admin.category-detail')
            ->name('category.mpp');

        Route::view('/category/peraturan', 'admin.category-detail')
            ->name('category.peraturan');

        Route::view('/category/am', 'admin.category-detail')
            ->name('category.am');
    });