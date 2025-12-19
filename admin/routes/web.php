<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RestaurantLoginController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\AdminController;

Route::get('/', function () {
    return redirect()->route('restaurant.login');
});

// Restaurant Authentication Routes
Route::prefix('restaurant')->name('restaurant.')->group(function () {
    Route::get('/login', [RestaurantLoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [RestaurantLoginController::class, 'login'])->name('login.submit');
    Route::post('/logout', [RestaurantLoginController::class, 'logout'])->name('logout');
    
    // Protected Restaurant Routes
    Route::middleware('restaurant.auth')->group(function () {
        Route::get('/dashboard', [RestaurantController::class, 'dashboard'])->name('dashboard');
        Route::get('/edit', [RestaurantController::class, 'edit'])->name('edit');
        Route::put('/update', [RestaurantController::class, 'update'])->name('update');
        Route::post('/remove-menu', [RestaurantController::class, 'removeMenu'])->name('remove-menu');
    });
});

// Admin Authentication Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminLoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminLoginController::class, 'login'])->name('login.submit');
    Route::post('/logout', [AdminLoginController::class, 'logout'])->name('logout');
    
    // Protected Admin Routes
    Route::middleware('admin.auth')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/restaurants/create', [AdminController::class, 'create'])->name('restaurants.create');
        Route::post('/restaurants', [AdminController::class, 'store'])->name('restaurants.store');
        Route::get('/restaurants/{id}/edit', [AdminController::class, 'edit'])->name('restaurants.edit');
        Route::put('/restaurants/{id}', [AdminController::class, 'update'])->name('restaurants.update');
        Route::delete('/restaurants/{id}', [AdminController::class, 'destroy'])->name('restaurants.destroy');
        Route::post('/restaurants/{id}/remove-menu', [AdminController::class, 'removeMenu'])->name('restaurants.remove-menu');
    });
});
