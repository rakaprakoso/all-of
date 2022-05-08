<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
    public function user()
    {
        return $this->belongsTo(
            'App\User',
            'user_id', #From
            'id', #To
        );
    }
}
