<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RecommendedProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\OrderController;

// Get authenticated user
Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

// Authentication
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware('guest');
Route::post('/register', [RegisteredUserController::class, 'store'])->middleware('guest');
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth');

// Email verification
Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
    ->middleware(['auth:sanctum', 'throttle:6,1']);

// Public product catalog
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/category/{category}', [ProductController::class, 'byCategory']);
Route::get('/products/recommended', [RecommendedProductController::class, 'recommended']);
Route::get('/products/sale', [ProductController::class, 'sale']);
Route::get('/products/search', [ProductController::class, 'search']);
Route::get('/products/{id}', [ProductController::class, 'show']);

// Contact form
Route::post('/contact', [ContactController::class, 'send']);

// Orders - create (public, for guests and users)
Route::post('/orders', [OrderController::class, 'store']);

// User profile management
Route::middleware('auth:sanctum')->group(function () {
    Route::put('/user/profile', [ProfileController::class, 'updateProfile']);
    Route::put('/user/password', [ProfileController::class, 'updatePassword']);
    Route::delete('/user', [ProfileController::class, 'deleteAccount']);
    
    // Favorites API
    Route::get('/favorites', [\App\Http\Controllers\FavoriteController::class, 'index']);
    Route::post('/favorites', [\App\Http\Controllers\FavoriteController::class, 'store']);
    Route::delete('/favorites/{product}', [\App\Http\Controllers\FavoriteController::class, 'destroy']);
    Route::post('/favorites/sync', [\App\Http\Controllers\FavoriteController::class, 'sync']);
    
    // Cart API
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart', [CartController::class, 'add']);
    Route::put('/cart/{productId}', [CartController::class, 'update']);
    Route::delete('/cart/{productId}', [CartController::class, 'remove']);
    Route::delete('/cart', [CartController::class, 'clear']);
    
    // Orders API - get (protected, only own orders)
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);
    Route::post('/orders/{order}/send-confirmation', [OrderController::class, 'sendConfirmationEmail']);
    
});

// Password reset (Breeze Auth)
Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->middleware('guest');
Route::post('/reset-password', [NewPasswordController::class, 'store'])->middleware('guest');

// Admin routes
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/admin/users', [AdminController::class, 'getUsers']);
    Route::get('/admin/users/{id}', [AdminController::class, 'getUser']);
    Route::put('/admin/users/{id}', [AdminController::class, 'updateUser']);
    Route::delete('/admin/users/{id}', [AdminController::class, 'deleteUser']);
    Route::post('/admin/users/{id}/reset-password', [AdminController::class, 'resetUserPassword']);
    Route::post('/admin/users/{id}/set-password', [AdminController::class, 'setUserPassword']);
    Route::get('/admin/stats', [AdminController::class, 'getStats']);
    
    // Orders
    Route::get('/admin/orders', [AdminController::class, 'getOrders']);
    Route::put('/admin/orders/{id}', [AdminController::class, 'updateOrder']);
    Route::delete('/admin/orders/{id}', [AdminController::class, 'deleteOrder']);
    
    // Product management
    Route::get('/admin/products', [AdminController::class, 'getProducts']);
    Route::post('/admin/products', [AdminController::class, 'createProduct']);
    Route::put('/admin/products/{id}', [AdminController::class, 'updateProduct']);
    Route::delete('/admin/products/{id}', [AdminController::class, 'deleteProduct']);
});
