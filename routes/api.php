




<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// localhost//api/v1/auth
// localhost//api/v1/admin

Route::prefix('v1')->group(function(){
        Route::prefix('auth')->group(function(){
                Route::post('login', fn() => 'temp');
                Route::middleware('auth.token')->post('logout', fn() => 'temp');
        });
    
        // Route::prefix('admin')
        //     ->middleware(['auth.token', 'role:admin'])
        //     ->group(function () {

        //         Route::get('teachers', )
        //     });
    
    
        // Route::prefix('teacher')
        //     ->middleware(['auth.token', 'role:teacher'])
        //     ->group(function () {
    
        //         Route::get('profile', function (Request $request){
        //             return response()->json([
        //                 'message' => 'Teacher profile retrieved successfully',
        //             ]);
        //         });
        //     });


});