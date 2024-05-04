<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookUserController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/book-user/most-read-books', [BookUserController::class, 'mostReadBooks']);
Route::resource('/book-user', BookUserController::class);
