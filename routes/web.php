<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderAdminController;
use App\Http\Controllers\Admin\ProductAdminController;
use App\Http\Controllers\Admin\UserAdminController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\YooKassaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::post('/email/verify', function (\Illuminate\Http\Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Ссылка отправлена!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
    ->middleware(['auth', 'signed'])
    ->name('verification.verify');

Route::middleware('guest')->group(function () {

    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register.form');
    Route::post('/register', [AuthController::class, 'register'])->name('register');

    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
});
Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profile', [AuthController::class, 'showProfile'])->name('profile.form');
    Route::patch('/profile/{id}', [AuthController::class, 'updateProfile'])->name('profile.update');

    Route::get('/change-password', [AuthController::class, 'showChangePasswordForm'])->name('password.form');
    Route::post('/change-password', [AuthController::class, 'updatePassword'])->name('password.update');

    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/{product}', [ProductController::class, 'show'])
        ->name('products.show');
    Route::get('/categories', [CategoryController::class, 'index'])
        ->name('categories.index');

    Route::get('/categories/{category:slug}', [CategoryController::class, 'show'])
        ->name('categories.show');

    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/items/{product}', [CartController::class, 'store'])->name('cart.items.store');
    Route::patch('/cart/items/{product}', [CartController::class, 'update'])->name('cart.items.update');
    Route::delete('/cart/items/{product}', [CartController::class, 'destroy'])->name('cart.items.destroy');
    Route::delete('/cart', [CartController::class, 'clear'])->name('cart.clear');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])
        ->name('orders.status.update');
    Route::post('/orders/{order}/pay', [OrderController::class, 'pay'])->name('orders.pay');
});


Route::middleware(['auth', 'verified', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/', [DashboardController::class, 'index'])
            ->name('dashboard');

        Route::resource('products', ProductAdminController::class);

        Route::resource('users', UserAdminController::class);

        Route::patch(
            'users/{user}/password',
            [UserAdminController::class, 'resetPassword']
        )->name('users.password');

        Route::resource('orders', OrderAdminController::class);
    });

Route::post('/payments/yookassa/webhook', [YooKassaController::class, 'webhook']);

Route::prefix('payments/yookassa')->name('payments.yookassa.')->group(function () {

    Route::get('/return', [YooKassaController::class, 'return'])->name('return');
    Route::post('/webhook', [YooKassaController::class, 'webhook'])->name('webhook');
});
