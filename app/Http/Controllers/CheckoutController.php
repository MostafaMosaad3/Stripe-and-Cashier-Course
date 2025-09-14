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

    public function enableCoupon()
    {
        $cart = Cart::session( )->first();

        $prices = $cart->courses()->pluck('stripe_price_id')->toArray();

        $sessionOptions = [
            'mode' => 'subscription',
            'success_url' => route('Home' , ['message' => 'Payment Success']),
            'cancel_url' => route('Home' , ['message' => 'Payment Failed']),
//            'allow_promotion_codes' => true,

        ] ;



        return Auth::user()
//            ->withCoupon('H2ViH3KP')
            ->withPromotionCode('promo_1S5QZq1PZWotrSWXeT2o8fX5')
            ->checkout($prices, $sessionOptions);


    }
    public function nonStripeItems()
    {
        $cart = Cart::session( )->first();

        $prices = $cart->courses()->sum('price');

        $sessionOptions = [
//            'mode' => 'subscription',
            'success_url' => route('Home' , ['message' => 'Payment Success']),
            'cancel_url' => route('Home' , ['message' => 'Payment Failed']),
        ] ;



        return Auth::user()->checkoutCharge($prices, 'Courses ', 1 ,$sessionOptions);


    }
    public function lineItems()
    {
        $cart = Cart::session( )->first();

        $courses = $cart->courses()->get()->map(function ($course) {
            return [
                'price_data' => [
                    'currency' => env('CASHIER_CURRENCY'),
                    'product_data' => [
                        'name' => $course->name,
                    ],
                    'unit_amount' => $course->price,
                ],
                'quantity' => 1,
                 'adjustable_quantity' => [
                     'enabled' => true ,
                     'maximum' => 100 ,
                     'minimum' => 1 ,
                 ]
            ];
        })->toArray();




        $sessionOptions = [
//            'mode' => 'subscription',
            'success_url' => route('Home' , ['message' => 'Payment Success']),
            'cancel_url' => route('Home' , ['message' => 'Payment Failed']),
            'line_items' => $courses,
        ] ;



        return Auth::user()->checkout(null ,$sessionOptions);


    }
    public function guestCheckout()
    {
        $cart = Cart::session( )->first();

        $courses = $cart->courses()->get()->map(function ($course) {
            return [
                'price_data' => [
                    'currency' => env('CASHIER_CURRENCY'),
                    'product_data' => [
                        'name' => $course->name,
                    ],
                    'unit_amount' => $course->price,
                ],
                'quantity' => 1,
                 'adjustable_quantity' => [
                     'enabled' => true ,
                     'maximum' => 100 ,
                     'minimum' => 1 ,
                 ]
            ];
        })->toArray();




        $sessionOptions = [
//            'mode' => 'subscription',
            'success_url' => route('Home' , ['message' => 'Payment Success']),
            'cancel_url' => route('Home' , ['message' => 'Payment Failed']),
            'line_items' => $courses,
        ] ;



        return Checkout::guest()->create(null ,$sessionOptions);


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


    public function cancel(Request $request)
    {

    }

}
