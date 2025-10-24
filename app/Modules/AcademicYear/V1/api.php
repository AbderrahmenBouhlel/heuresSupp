<?php

use Illuminate\Support\Facades\Route;
use App\Modules\AcademicYear\V1\Http\controller\AcademicYearController;
use App\Modules\Domain\User\V1\VOs\enums\UserRoleEnum;



Route::prefix('academic-years')
    ->middleware(['auth.token', 'role:'. UserRoleEnum::ADMIN->value .','.UserRoleEnum::TEACHER->value])
    ->group(function () {
        Route::get('last-academic-years', [AcademicYearController::class, 'getLastNAcademicYears']);
        
    });
