<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;

// Public : Not protected by token, just cookie
Route::post('/login',[UserController::class,'login'])->name('user.login');
Route::post('/register',[UserController::class,'register'])->name('user.register');
Route::post('/refresh',[UserController::class,'refresh'])->name('user.refresh');

//Private(Protected)
Route::middleware('auth:sanctum')->group(function() {
    Route::get('/me', function(Request $request){
        return $request->user()->load('roles','permissions');
    });

    Route::post('/logout',[UserController::class,'logout'])->name('user.logout');

    // Route::post('/logoutAll',[UserController::class,'logoutAll'])->name('all.logout');
});