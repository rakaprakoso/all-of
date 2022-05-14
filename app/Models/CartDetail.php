<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartDetail extends Model
{
    protected $guarded = [];

    public function cart(){
        return $this->belongsTo(
            'App\Models\Cart',
            'cart_id', #From
            'id', #To
        );
    }
    public function product(){
        return $this->belongsTo( #OneToOne
            'App\Models\Product',
            'product_id', #From
            'id', #To
        );
    }
}
