<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RecommendedProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegisterController;


Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/category/{category}', [ProductController::class, 'byCategory']);
Route::get('/products/recommended', [RecommendedProductController::class, 'recommended']);

Route::get('/me', [AuthController::class, 'me']);

/*
Route::post('/login', [AuthController::class, 'login']);


Route::post('/logout', [AuthController::class, 'logout']);
Route::get('/me', [AuthController::class, 'me']);

Route::get('/register', [AuthController::class, 'register']);
Route::post('/resend', [RegisterController::class, 'resend']);
Route::post('/startregister', [RegisterController::class, 'startRegistration']);
Route::post('/verify-register', [RegisterController::class, 'verifyCode']);
Route::post('/pending-users/remove', [RegisterController::class, 'removePending']);

*/
