<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Cashier\Cashier;

class Course extends Model
{
    protected $guarded = [];

    public function carts()
    {
        return $this->belongsToMany(Cart::class,'cart_course','course_id','cart_id');
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class,'course_order','course_id','order_id');
    }

    public function price()
    {
        return Cashier::formatAmount($this->price , env('CASHIER_CURRENCY'));
    }



}
