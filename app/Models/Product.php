<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Product extends Model
{
    use HasFactory;
    // public function getPriceMoneyAttribute()
    // {
    //     return $this->price;
    // }
    // public function getPricettribute($value)
    // {
    //     return $value + 1000;
    // }
    // public function getPriceAttribute($price)
    // {
    //     return $this->attributes['price'] = sprintf('Rp. %s', number_format($price, 0, null, '.'));
    // }
    public function images(){
        return $this->hasMany(ProductImage::class);
    }
    public function getMoneyAttribute(){
        return sprintf('Rp. %s', number_format(floatval($this->price), 0, null, '.'));
    }

    public function getUsedPriceAttribute(){
        return $this->discount_price ? $this->discount_price : $this->price;
    }
    // public function getImagesArrAttribute(){
    //     return $this->images;
    // }
    protected $appends = ['money'];
    protected $with = ['images'];
    public function scopeShow($query){
        return $query->where('preview', true);
    }
}
