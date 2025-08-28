<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ForgotPasswordController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])
    ->name('password.email'); 

Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword'])
    ->name('password.update'); 
Route::get('/email/verify/{id}/{hash}', [RegisterController::class,'emailVerification'])->middleware(['signed'])->name('verification.verify');

Route::post('/email/verification-notifications',[RegisterController::class,'sendVerification'])->middleware(['auth:sanctum'])->name('verification.send');


Route::get('/users',[RegisterController::class,'index']);
Route::post('/register',[RegisterController::class,'store']);
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::get('/test-posts/{id}', [PostController::class, 'show']);

Route::middleware(['auth:sanctum','verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::resource('categories',CategoryController::class)->except(['create','edit']);
    Route::resource('posts',PostController::class)->except(['create','edit']);
    Route::get('/bookmarks', [BookmarkController::class, 'index']);
    Route::post('/bookmarks/{post}', [BookmarkController::class, 'store']);
    Route::delete('/bookmarks/{post}', [BookmarkController::class, 'destroy']);
});