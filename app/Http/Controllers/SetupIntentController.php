<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SetupIntentController extends Controller
{
    public function index()
    {
        $setupIntent = Auth::user()->createSetupIntent();
        return view('checkout.setup-intent', get_defined_vars());
    }

    public function post(Request $request)
    {
        $cart = Cart::session()->first();
        $amount = $cart->courses->sum('price');
        $paymentMethod = $request->payment_method;
        Auth::user()->createOrGetStripeCustomer();
        $payment = Auth::user()->charge($amount, $paymentMethod, [
            'return_url' => route('home', ['message' => 'Payment successful!']),
        ]);

        if ($payment->status == 'succeeded') {
            $order = Order::create([
                'user_id' => Auth::user()->id,
            ]);
            $order->courses()->attach($cart->courses->pluck('id')->toArray());
            $cart->delete();
            return redirect()->route('Home', ['message'=> 'Payment successful!']);
        }
    }
}
