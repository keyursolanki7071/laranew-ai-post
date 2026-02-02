<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::get('/', [\App\Http\Controllers\BlogController::class, 'index'])->name('blogs.index');
Route::post('/blogs/fetch', [\App\Http\Controllers\BlogController::class, 'fetch'])->name('blogs.fetch');
Route::get('/blogs/proxy-image', [\App\Http\Controllers\BlogController::class, 'proxyImage'])->name('blogs.proxy-image');
Route::get('/blogs/{blog}', [\App\Http\Controllers\BlogController::class, 'show'])->name('blogs.show');
