<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Teacher\V1\Http\Controllers\TeacherController;
use App\Modules\Domain\User\V1\VOs\enums\UserRoleEnum;

Route::prefix('teacher')->group(function () {
    Route::middleware(['auth.token', 'role:'. UserRoleEnum::TEACHER->value ])
    ->get('teacher-data-for-year', [TeacherController::class, 'getTeacherAssignment']);
    Route::middleware(['auth.token', 'role:'. UserRoleEnum::TEACHER->value ])
    ->post('verify-teacher-overtime-process', [TeacherController::class, 'verifyTeacherOvertimeProcess']);
    Route::middleware(['auth.token', 'role:'. UserRoleEnum::TEACHER->value ])
    ->post('reclaim-overtime-process', [TeacherController::class, 'reclaimOvertimeProcess']);
});