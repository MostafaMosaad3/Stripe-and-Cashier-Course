<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Cashier\Checkout;

class CheckoutController extends Controller
{
    public function checkout()
    {
        $cart = Cart::session( )->first();

        $prices = $cart->courses()->pluck('stripe_price_id')->toArray();

        $sessionOptions = [
            'mode' => 'subscription',
            'success_url' => route('checkout.success').'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('checkout.cancel').'?session_id={CHECKOUT_SESSION_ID}',
            'metadata' => [
                'cart_id' => $cart->id
            ]
        ] ;

        $checkout = Auth::user()->checkout($prices, $sessionOptions);

        return $checkout;

    }


    public function success(Request $request){

        $checkoutSession = $request->user()->stripe()->checkout->sessions->retrieve($request->get('session_id'));

        $cart = Cart::findOrFail($checkoutSession->metadata->cart_id);

        $order = Order::create([
            'user_id' => $request->user()->id,
        ]);

        $order->courses()->attach($cart->courses->pluck('id')->toArray());

        return redirect()->route('Home' , ['message' => 'Payment Successful']);


    }


    public function cancel(Request $request){

    }

}
