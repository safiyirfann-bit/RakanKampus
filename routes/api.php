<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\AppNotificationController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\ClassScheduleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\ReminderController;
use App\Http\Middleware\ApiLocale;
use Illuminate\Support\Facades\Route;

/*
| RakanKampus mobile app (Flutter) — all URLs start with /api.
| Sign in with POST /api/login, then send "Authorization: Bearer <token>" and
| "Accept: application/json" on every request. Same data and rules as the website.
*/

Route::middleware(ApiLocale::class)->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');

    Route::middleware(['auth:api', ApiLocale::class, \App\Http\Middleware\LogUserActivity::class])->group(function () {
        // Account
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::put('/me', [AuthController::class, 'updateProfile']);
        Route::put('/me/password', [AuthController::class, 'updatePassword']);
        Route::put('/me/settings', [AuthController::class, 'updateSettings']);
        Route::post('/me/photo', [ProfileController::class, 'uploadPhoto']);
        Route::post('/me/cover', [ProfileController::class, 'uploadCover']);
        Route::delete('/me/cover', [ProfileController::class, 'removeCover']);

        // Home
        Route::get('/home', HomeController::class);

        // Chat ("stream": true → one JSON object per line while the AI writes)
        Route::post('/chat', [ChatbotController::class, 'chat'])->middleware('throttle:20,1');
        Route::get('/conversations', [ChatbotController::class, 'history']);
        Route::get('/conversations/{conversation}', [ChatbotController::class, 'show']);
        Route::put('/conversations/{conversation}', [ChatbotController::class, 'rename']);
        Route::delete('/conversations/{conversation}', [ChatbotController::class, 'destroy']);
        Route::post('/messages/{message}/rate', [ChatbotController::class, 'rate'])->middleware('throttle:60,1');

        // Reminders
        Route::get('/reminders', [ReminderController::class, 'index']);
        Route::get('/reminders/history', [ReminderController::class, 'history']);
        Route::post('/reminders', [ReminderController::class, 'store']);
        Route::put('/reminders/{reminder}', [ReminderController::class, 'update']);
        Route::delete('/reminders/{reminder}', [ReminderController::class, 'destroy']);
        Route::post('/reminders/ai-capture', [ReminderController::class, 'aiCapture'])->middleware('throttle:10,1');
        Route::post('/reminders/bulk-store', [ReminderController::class, 'bulkStore']);
        Route::post('/reminders/bulk-delete', [ReminderController::class, 'bulkDestroy']);
        Route::post('/reminders/restore', [ReminderController::class, 'restore']);

        // Timetable
        Route::get('/timetable', [ClassScheduleController::class, 'index']);
        Route::post('/timetable', [ClassScheduleController::class, 'store']);
        Route::put('/timetable/{schedule}', [ClassScheduleController::class, 'update']);
        Route::delete('/timetable/{schedule}', [ClassScheduleController::class, 'destroy']);
        Route::post('/timetable/ai-capture', [ClassScheduleController::class, 'aiCapture'])->middleware('throttle:10,1');
        Route::post('/timetable/bulk-store', [ClassScheduleController::class, 'bulkStore']);
        Route::post('/timetable/bulk-delete', [ClassScheduleController::class, 'bulkDestroy']);
        Route::post('/timetable/delete-all', [ClassScheduleController::class, 'destroyAll']);
        Route::post('/programs', [ProgramController::class, 'store']);
        Route::put('/programs/{program}', [ProgramController::class, 'update']);
        Route::delete('/programs/{program}', [ProgramController::class, 'destroy']);

        // Phone notifications the app schedules itself (reminders + classes, next 8 days)
        Route::get('/notifications/upcoming', [AppNotificationController::class, 'index']);
    });
});
