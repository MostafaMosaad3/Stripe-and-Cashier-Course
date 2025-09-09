<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\ProfileController;
use App\Models\Cart;
use App\Models\Course;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $courses = Course::all();
    return view('Home' , get_defined_vars()) ;
})->name('Home');




Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');



// Courses Routes
Route::controller(CourseController::class)->group(function () {
    Route::get('courses/{course:slug}' , 'show')->name('courses.show');
});


// Checkout Routes
Route::controller(CheckoutController::class)
    ->middleware('auth')
    ->group(function () {
        Route::get('/checkout', 'checkout')->name('checkout');
        Route::get('/checkout/success', 'success')->name('checkout.success');
        Route::get('/checkout/cancel', 'cancel')->name('checkout.cancel');
    });



// Checkout Routes
route::controller(CartController::class)->group(function () {
    Route::get('/cart' , 'index')->name('cart.index');
    Route::get('/addToCart/{course:slug}' , 'addToCart')->name('cart.store');
    Route::get('/removeFromCart/{course:slug}' , 'removeFromCart')->name('cart.delete');
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
