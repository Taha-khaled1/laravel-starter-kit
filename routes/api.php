<?php

use App\Http\Controllers\Api\AttributeController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\EmailVerificationController;
use App\Http\Controllers\Api\Auth\ResetPasswordController;
use App\Http\Controllers\Api\Auth\UserController;
use App\Http\Controllers\Api\ContactUsController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\MassgeController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\RatingController;
use Illuminate\Support\Facades\Route;


Route::group(
    ['middleware' => ['changeLanguage']],
    function () {
        Route::post('verification-notification', [EmailVerificationController::class, 'verificationNotification']);
        Route::post('verify-code', [EmailVerificationController::class, 'verifyCode']);
        Route::post('reset-password', [ResetPasswordController::class, 'resetPassword'])->middleware('sanctum');

        Route::controller(AuthController::class)->group(function () {
            Route::post('/login', 'login');
            Route::get('/login/invitation', 'useInvitationCode');
            Route::get('getOtpForUser',  'getOtpForUser');
            Route::post('/social/register', 'socialRegister');
            Route::post('/register', 'register');
            Route::post('/logout', 'logout')->middleware('sanctum');
            Route::delete('delete-account', 'deleteAccount')->middleware('sanctum');
        });

        Route::controller(UserController::class)->group(function () {
            Route::post('profile/update', [UserController::class, 'updateProfile'])->middleware('sanctum');
            Route::post('/profile/change-password', [UserController::class, 'changePassword'])->middleware('sanctum');
            Route::get('/getUserInfo', [UserController::class, 'getUserInfo'])->middleware('sanctum');
        });
        Route::controller(MassgeController::class)->group(function () {
            Route::get('/chats', 'index')->middleware('sanctum');
            // Show a Chat and Its Messages
            Route::get('/chats/show', 'show')->middleware('sanctum');
            // Create a New Chat
            Route::get('/chats/create', 'createChat')->middleware('sanctum');
            // Send a Message to a Chat
            Route::post('/chats/send', 'sendMessage')->middleware('sanctum');
            Route::post('/chats/mark-as-read', 'markAsRead')->middleware('sanctum');
            Route::get('/chats/unread-count',  'unreadChatsCount')->middleware('sanctum');
            Route::post('/chat/delete',  'deleteChat')->middleware('sanctum');
        });

        Route::controller(AttributeController::class)->group(function () {
            Route::get('/countries',  'getCountry');
            Route::get('/cities',  'getCity');
            Route::get('/postions',  'postions');
        });
      
        Route::controller(ContactUsController::class)->group(function () {
            Route::post('/contact-us', 'store')->middleware('sanctum');
        });
        Route::controller(HomeController::class)->group(function () {
            Route::get('/home', 'index');
           
        });
        Route::controller(NotificationController::class)->group(function () {
            Route::get('/test-notification', 'sendNotficationTest')->middleware('sanctum');
            Route::get('/notifications', 'getUserNotifications')->middleware('sanctum');
            Route::get('/notifications/unread', 'getUnReadNotifications')->middleware('sanctum');
            Route::post('/notifications/{notification}/read', 'markAsRead')->middleware('sanctum');
            Route::post('/notifications/mark-all-read', 'markAllAsRead')->middleware('sanctum');
            Route::delete('/notifications/{notificationId}/delete',  'deleteNotification')->middleware('sanctum');
            Route::delete('/notifications/delete-all', 'deleteAllNotifications')->middleware('sanctum');
        });
    },
);
