<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentMethodCheckoutController extends Controller
{
    public function index()
    {
        return view('checkout.payment-method');
    }


    public function post(Request $request)
    {
        $cart = Cart::session()->first() ;
        $amount = $cart->courses->sum('price');
        $paymentMethod = $request->payment_method;

        $payment = Auth::user()->charge($amount, $paymentMethod , [
            'return_url' => route('Home' , ['message' => 'Payment Successful']),
        ]);


        if($payment->status == 'succeeded'){
            $order = Order::create([
                'user_id' => Auth::id(),
            ]);

            $order->courses()->attach($cart->courses->pluck('id')->toArray());
            $cart->delete();

            return redirect()->route('Home' , ['message' => 'Payment Success']);
        }
    }
}
