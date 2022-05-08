<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $with = ['product'];
    public function product(){
        return $this->belongsTo(
            'App\Models\Product',
            'product_id', #From
            'id', #To
        );
    }
}
