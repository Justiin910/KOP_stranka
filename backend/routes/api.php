<?php

use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RecommendedProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegisterController;





Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/category/{category}', [ProductController::class, 'byCategory']);
Route::get('/products/recommended', [RecommendedProductController::class, 'recommended']);




Route::post('/login', [AuthController::class, 'login']);


Route::post('/logout', [AuthController::class, 'logout']);

Route::get('/register', [AuthController::class, 'register']);
Route::post('/resend', [RegisterController::class, 'resend']);
Route::post('/startregister', [RegisterController::class, 'startRegistration']);
Route::post('/verify-register', [RegisterController::class, 'verifyCode']);
Route::post('/pending-users/remove', [RegisterController::class, 'removePending']);
