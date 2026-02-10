<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AccessRequestController;

// Public : Not protected by token, just cookie
Route::post('/login',[UserController::class,'login'])->name('user.login');
Route::post('/register',[UserController::class,'register'])->name('user.register');
Route::post('/refresh',[UserController::class,'refresh'])->name('user.refresh');

//Private(Protected)
Route::middleware('auth:sanctum')->group(function() {
    Route::get('/me',[UserController::class,'me'])->name('user.me');
    Route::post('/logout',[UserController::class,'logout'])->name('user.logout');
    Route::post('/up_req',[UserController::class,'requestUpdate'])->name('user.requestUpdate');

    // Route::post('/logoutAll',[UserController::class,'logoutAll'])->name('all.logout');
});

//Admin Routes

Route::middleware(['auth:sanctum','can:manage_users'])
    ->prefix('admin')
    ->group(function() {
        Route::get('/users', [AdminController::class, 'index'])->name('admin.index');
        Route::patch('/users/{user}/role', [AdminController::class, 'updateRole'])->name('admin.updateRole');
        Route::patch('/users/{user}/permissions', [AdminController::class, 'updatePermissions'])->name('admin.updatePermissions');
    });


// Request roles and permissions
Route::middleware(['auth:sanctum'])->group(function(){
    Route::post('/access-request',[AccessRequestController::class,'requestUpdate'])->name('requestUpdate');
    
    Route::middleware('can:manage_users')->group(function() {
        Route::post('/access-request/{accessRequest}/approve', [AccessRequestController::class,'approve'])->name('approve');
        Route::post('/access-request/{accessRequest}/reject', [AccessRequestController::class,'reject'])->name('reject');
    });
});