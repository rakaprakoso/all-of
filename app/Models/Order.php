<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $with =['order_details','payment','coupon_detail'];
    protected $appends = ['totalPrice','affiliate'];

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
}
