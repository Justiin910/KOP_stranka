<?php

use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RecommendedProductController;

Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/category/{category}', [ProductController::class, 'byCategory']);
Route::get('/products/recommended', [RecommendedProductController::class, 'recommended']);