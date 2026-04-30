<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ComponentController;
use App\Http\Controllers\Api\AnnouncementController;
use App\Http\Controllers\Api\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Публичные маршруты (доступны гостям)
Route::prefix('v1')->group(function () {
    // Каталог компонентов
    Route::get('/components', [ComponentController::class, 'index']);
    Route::get('/components/{component}', [ComponentController::class, 'show']);

    // Доска объявлений
    Route::get('/announcements', [AnnouncementController::class, 'index']);
    Route::get('/announcements/{announcement}', [AnnouncementController::class, 'show']);
});

// Маршруты для авторизованных пользователей
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    // Аутентификация
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'me']);
    Route::put('/user/profile', [AuthController::class, 'updateProfile']);
    Route::post('/user/change-password', [AuthController::class, 'changePassword']);

    // Объявления (CRUD для своих объявлений)
    Route::post('/announcements', [AnnouncementController::class, 'store']);
    Route::put('/announcements/{announcement}', [AnnouncementController::class, 'update']);
    Route::post('/announcements/{announcement}/archive', [AnnouncementController::class, 'archive']);
    Route::delete('/announcements/{announcement}', [AnnouncementController::class, 'destroy']);

    // Прайс-листы
    // Route::apiResource('price-lists', PriceListController::class);

    // Чат
    // Route::apiResource('chat/rooms', ChatRoomController::class);
    // Route::apiResource('chat/messages', ChatMessageController::class);
});

// Маршруты только для гостей (регистрация, вход)
Route::prefix('v1')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});
