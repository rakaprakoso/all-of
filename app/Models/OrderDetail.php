<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    // protected $with = ['product'];
    // protected $appends = ['order_product'];
    protected $hidden = ['product'];

    public function product(){
        return $this->belongsTo(
            'App\Models\Product',
            'product_id', #From
            'id', #To
        );
    }
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    // public function getOrderProductAttribute()
    // {
    //     return [
    //                 'product_name' => $this->product->name,
    //                 'thumbnail_img' => $this->product->thumbnail_img,
    //     ];
    // }
}
