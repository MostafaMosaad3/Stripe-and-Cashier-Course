<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\PaymentMethodCheckoutController;
use App\Http\Controllers\ProfileController;
use App\Models\Cart;
use App\Models\Course;
use Illuminate\Support\Facades\Route;
use Laravel\Cashier\Http\Controllers\PaymentController;

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
    ->group(function () {
        Route::get('/checkout', 'checkout')->middleware('auth')->name('checkout');
        Route::get('/checkout/enableCoupon', 'enableCoupon')->middleware('auth')->name('checkout.enableCoupon');
        Route::get('/checkout/nonStripeItems', 'nonStripeItems')->middleware('auth')->name('checkout.nonStripeItems');
        Route::get('/checkout/lineItems', 'lineItems')->middleware('auth')->name('checkout.lineItems');
        Route::get('/checkout/guestCheckout', 'guestCheckout')->name('checkout.guest');
        Route::get('/checkout/success', 'success')->middleware('auth')->name('checkout.success');
        Route::get('/checkout/cancel', 'cancel')->middleware('auth')->name('checkout.cancel');
    });


// Direct Integration - PaymentMethod Routes
ROute::controller(PaymentMethodCheckoutController::class)
    ->group(function () {
        Route::get('/direct/paymentMethod', 'index')->middleware('auth')->name('direct.paymentMethod');
        Route::post('/direct/paymentMethod/post', 'post')->middleware('auth')->name('direct.paymentMethod.post');
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
