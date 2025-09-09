<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Cashier\Cashier;

class Cart extends Model
{
    protected $guarded = [] ;

    protected $with = ['courses'];

    public function scopeSession(){
        return $this->where('session_id',session()->getId());
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class,'cart_course','cart_id','course_id');
    }


    public function total()
    {
        return Cashier::formatAmount($this->courses()->sum('price') , env('CASHIER_CURRENCY'));
    }
}
