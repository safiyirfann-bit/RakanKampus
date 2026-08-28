<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\InformationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\KnowledgeBaseController;
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

    return view('homepage', [
        'user' => $user,
        'conversations' => $conversations,
    ]);
})->name('student.home');

    // Chat
    Route::get('/student/chat', function (Illuminate\Http\Request $request) {
        return view('chat', [
            'user' => $request->user(),
        ]);
    })->name('student.chat');

    // Chatbot API
    Route::post('/chatbot', [ChatbotController::class, 'chat'])->name('chatbot.send');
    Route::get('/chatbot/history', [ChatbotController::class, 'history'])->name('chatbot.history');
    Route::get('/chatbot/{conversation}', [ChatbotController::class, 'show'])->name('chatbot.show');
    Route::put('/chatbot/{conversation}/rename', [ChatbotController::class, 'rename'])->name('chatbot.rename');
Route::delete('/chatbot/{conversation}', [ChatbotController::class, 'destroy'])->name('chatbot.destroy');

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

        // Category pages
        Route::view('/category/mpp', 'admin.category-detail')
            ->name('category.mpp');

        Route::view('/category/peraturan', 'admin.category-detail')
            ->name('category.peraturan');

        Route::view('/category/am', 'admin.category-detail')
            ->name('category.am');
    });