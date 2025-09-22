<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentIntentCheckoutController extends Controller
{
    public function index()
    {
        $cart = Cart::session()->first();
        $amount = $cart->courses()->sum('price');

        $intent = Auth::user()->pay($amount);

        return view('checkout.payment-intent' , compact('intent'));
    }


    public function post(Request $request)
    {
        $cart = Cart::session()->first();
        $paymentIntentId = $request->payment_intent_id;
        $paymentIntent = Auth::user()->findPayment($paymentIntentId);

        if ($paymentIntent->status == 'succeeded') {
            $order = Order::create([
                'user_id' => Auth::user()->id,
            ]);
            $order->courses()->attach($cart->courses->pluck('id')->toArray());
            $cart->delete();
            return redirect()->route('Home', ['message'=> 'Payment successful!']);
        }
    }


}
