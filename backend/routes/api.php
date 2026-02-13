<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AccessRequestController;
use App\Http\Controllers\ContentController;

/*
*---------------------------------------------------------|
*               Auth Controllers                          |
*---------------------------------------------------------|
*/


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
    Route::get('/permissions',function () {
        return \Spatie\Permission\Models\Permission::select('id','name')->get();
    });
        
    Route::get('/roles',function () {
        return \Spatie\Permission\Models\Role::select('id','name')->get();
    });
            
});
            
            
// Request roles and permissions
Route::middleware(['auth:sanctum'])->group(function(){
    Route::post('/access-request',[AccessRequestController::class,'requestUpdate'])->name('requestUpdate');
    
    Route::middleware('can:manage_users')->group(function() {
        Route::post('/access-request/{accessRequest}/approve', [AccessRequestController::class,'approve'])->name('approve');
        Route::post('/access-request/{accessRequest}/reject', [AccessRequestController::class,'reject'])->name('reject');
    });
});
    
    
/*
*---------------------------------------------------------|
*               Content Controllers                       |
*---------------------------------------------------------|
*/

// Public published articles (no authentication required)
Route::get('/articles',[ContentController::class,'showAll']);
Route::get('/articles/{content}',[ContentController::class,'show']);
Route::get('/articles_type',function(){
    return App\Models\ArticleType::all();
});
Route::get('/articles_type/{type}', function ($type){
    return App\Models\ArticleType::where('id',$type)->first();
});
// Protected routes (authentication required)
Route::middleware('auth:sanctum')->group(function () {
    
    // Editor/Admin personal articles
    Route::get('/my-articles',[ContentController::class,'index'])
        ->middleware('can:create_articles');

    // Create
    Route::post('/articles',[ContentController::class,'store'])
        ->middleware('can:create_articles');

    // Update
    Route::put('/articles/{content}',[ContentController::class,'update'])
        ->middleware('can:edit_articles');

    // Delete
    Route::delete('/articles/{content}',[ContentController::class,'destroy'])
        ->middleware('can:delete_articles');

    // Submit for review
    Route::post('/articles/{content}/submit',[ContentController::class,'submit']);

    // Publish (Admin only)
    Route::post('/articles/{content}/publish',[ContentController::class,'publish'])
        ->middleware('can:publish_articles');
});