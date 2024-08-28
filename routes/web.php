<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TagController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CategoryController;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');


Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');

// routes/web.php
Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
// Route::resource('posts', PostController::class);
Route::get('/post/{slug}', [PostController::class, 'show'])->name('posts.show');
Route::get('/category/{slug}', [CategoryController::class, 'show'])->name('category.show');
Route::get('/tag/{slug}', [TagController::class, 'show'])->name('tag.show');

require __DIR__ . '/auth.php';
