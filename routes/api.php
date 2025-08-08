




<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::prefix('v1')->group(function(){
        // Route::prefix('auth')->group(function(){
        //     Route::post('login', '');
        //     Route::middleware('auth.token')->post('logout', '');
        // });
    
        // Route::prefix('admin')
        //     ->middleware(['auth.token', 'role:admin'])
        //     ->group(function () {
        //         //Route::get('teachers', []);
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