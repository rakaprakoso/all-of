<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $with =[
        // 'order_details','payment','coupon_detail'
    ];
    protected $appends = [
        // 'totalPrice','affiliate',
        'product',
        'name'
    ];

    protected $casts = [
        'discount' => 'integer',
    ];
    protected $hidden = ['order_details'];

    public function payment(){
        return $this->belongsTo(
            'App\Models\Payment',
            'order_id', #From
            'order_id', #To
        );
    }
    public function coupon_detail(){
        return $this->belongsTo(
            'App\Models\CouponCode',
            'couponcode', #From
            'code', #To
        );
    }
    public function sales(){
        return $this->belongsTo(
            'App\Models\Admin',
            'sales_id', #From
            'id', #To
        );
    }
    public function order_details(){
        return $this->hasMany(
            'App\Models\OrderDetail',
            'order_id', #To
            'id', #From
        );
    }

    public function getProductAttribute()
    {
        return $this->order_details->map(function ($detail) {
            return [
                'name' => $detail->product->name,
                'topping' => $detail->variant,
                'image' => $detail->product->thumbnail_img,
                'price' => $detail->price,
                'qty' => $detail->qty,
            ];
        });
    }

    public function getTotalPriceAttribute()
    {
        return $this->order_details->sum(function($t){
            return $t->qty * $t->price;
        });
    }

    public function getAffiliateAttribute()
    {
        if ($this->coupon_detail) {
            return $this->coupon_detail->coupon_type == 'affiliate' ? true : false;
        }else {
            return false;
        }
    }

    public function getNameAttribute()
    {
        return $this->name_buyer;
    }
    // public function getDiscountAttribute()
    // {
    //     return round($this->discount);
    // }
}
