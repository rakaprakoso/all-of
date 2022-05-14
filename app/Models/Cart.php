<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Cart extends Model
{
    public function cart_detail()
    {
        return $this->hasMany(
            'App\Models\CartDetail',
            'cart_id', #To
            'id', #From
        );
    }
    public function getTotalQty() {
        return $this->cart_detail->sum(function($cart_detail) {
            return $cart_detail->qty;
          });
        // return $this->cart_detail()->sum(DB::raw('qty'));
      }
    public function getTotalPrice() {
        return $this->cart_detail->sum(function($cart_detail) {
            return ($cart_detail->qty * $cart_detail->product->usedPrice);
          });
        // return $this->cart_detail()->sum(DB::raw('qty'));
      }
    public function user()
    {
        return $this->belongsTo(
            'App\User',
            'user_id', #From
            'id', #To
        );
    }
}
