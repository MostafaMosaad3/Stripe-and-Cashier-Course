<?php

namespace App\Providers;

use App\Models\Cart;
use Illuminate\Support\Facades\Schema;

use Illuminate\Support\ServiceProvider;
use Illuminate\View\View;
use Illuminate\Support\Facades;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
        // View Composer Way
        Facades\View::composer('*', function (view $view) {
            $cart = Cart::session()->first() ;
            $view->with('cart', $cart);
        });


        // Share View Way (Wrong in this case)
//        View::Share('cart', $cart);
    }
}
