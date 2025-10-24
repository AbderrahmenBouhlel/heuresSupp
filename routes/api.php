<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\auth\AuthController;


Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('login', [AuthController::class, 'login']);
        Route::middleware('auth.token')->get('logout', [AuthController::class, 'logout']);
        Route::middleware('auth.token')->get('me', [AuthController::class, 'me']);
    });
    Route::prefix('shared')->group(function () {
        // AcademicYear routes
        require base_path('app/Modules/AcademicYear/V1/api.php');
        require base_path('app/Modules/Notifications/V1/api.php');
        
    });

    // admin api endpoints
    require base_path('app/Modules/Admin/V1/api.php');


    // teacher api endpoints
    require base_path('app/Modules/Teacher/V1/api.php');

    
});
