<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;

Route::post('/login',[UserController::class])->name('user.login');
Route::post('/register',[UserController::class])->name('user.register');

Route::middleware('auth:sanctum')->group( function () {
    Route::get('me', function (Request $request){
        return $request->user();
    });
});