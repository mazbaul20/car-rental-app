<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Frontend\PageController;
use App\Http\Controllers\Admin\DashboardController;


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

//admin routes
Route::middleware(['auth','adminRole:admin'])->prefix('admin')->group(function() {
    Route::get('/dashboard',[DashboardController::class,'index'])->name('admin.dashboard');
    Route::get('/logout',[DashboardController::class,'Logout'])->name('admin.logout');
    Route::resource('cars', 'App\Http\Controllers\Admin\CarController');
    Route::resource('rentals', 'App\Http\Controllers\Admin\RentalController');
    Route::resource('customers', 'App\Http\Controllers\Admin\CustomerController');
});

//customer routes
// Route::middleware(['auth','customerRole:customer'])->group(function() {
//     Route::get('/dashboard',function(){
//         return view('dashboard');
//     })->name('dashboard');
// });

//customer routes
Route::middleware(['auth','customerRole:customer'])->prefix('customer')->group(function() {
    Route::get('/dashboard',function(){
        return "This is customer dashboard";
    });

    // Route::get('/rentals', ['Frontend\RentalController','index'])->name('frontend.rentals.index');
    Route::post('/rentals', ['App\Http\Controllers\Frontend\RentalController','store'])->name('customer.rental');
    Route::get('/rentals/{id}', ['App\Http\Controllers\Frontend\RentalController','show']);
    Route::delete('/rentals/{id}', ['App\Http\Controllers\Frontend\RentalController','destroy']);
});

//frontend routes
Route::get('/', [PageController::class, 'home'])->name('frontend.home');
Route::get('/about', [PageController::class, 'about'])->name('frontend.about');
Route::get( '/rentals', [PageController::class, 'rentals'])->name('frontend.rentals');
Route::get('/contact', [PageController::class, 'contact'])->name('frontend.contact');
Route::get('/cars/{id}/', [PageController::class, 'Details'])->name('carDetails');
