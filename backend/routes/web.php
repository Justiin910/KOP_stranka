<?php

use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;

Route::middleware('api')->group(function () {
    Route::get('/categories', [CategoryController::class, 'index']);
});

