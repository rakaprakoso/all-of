<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'LIKE', "%$search%");
    }
    public function scopeType($query,$type)
    {
        return $query->where('type', $type);
    }

    protected $guarded = ['id', 'created_at', 'updated_at'];
    public function products()
    {
        return $this->belongsToMany('App\Models\Product',
        'product_categories', 'category_id','product_id');
    }
}
