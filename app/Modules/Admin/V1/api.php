<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Admin\V1\Http\Controllers\AdminController;
use App\Modules\Domain\User\V1\VOs\enums\UserRoleEnum;


Route::prefix('admin')
    ->middleware(['auth.token', 'role:'. UserRoleEnum::ADMIN->value])
    ->group(function () {
        Route::get('active-teachers-for-year', [AdminController::class, 'getActiveTeachersForAcademicYear']);
        Route::get('teacher-full-profile', [AdminController::class, 'getTeacherFullProfile']);
        Route::get('teacher-annual-report', [AdminController::class, 'getTeacherAnnualReport']);
        Route::post('reject-reclamation', [AdminController::class, 'rejectReclamation']);
        Route::post('accept-reclamation', [AdminController::class, 'acceptReclamation']);
        Route::get('teacher-history', [AdminController::class, 'getTeacherHistory']);


        // download assignment template
        Route::get('download-assignment-template', [AdminController::class, 'downloadAssignmentTemplate']);
        Route::get('get-excel-validation-rules', [AdminController::class, 'getExcelValidationRules']);
    });
