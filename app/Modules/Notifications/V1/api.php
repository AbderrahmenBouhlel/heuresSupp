<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Notifications\V1\Http\Controllers\NotificationController;
use App\Modules\Domain\User\V1\VOs\enums\UserRoleEnum;

Route::prefix('notifications')
    ->middleware(['auth.token', 'role:'. UserRoleEnum::ADMIN->value .','.UserRoleEnum::TEACHER->value])
    ->group(function () {
        Route::get('/', [NotificationController::class, 'getAllNotifications']);
        Route::patch('/read', [NotificationController::class, 'markAsRead']);
        Route::patch('/readAll', [NotificationController::class, 'markAllAsRead']);
    });