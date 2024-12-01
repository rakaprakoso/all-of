<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Product extends Model
{
    use HasFactory;

    // Relationships
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'product_categories', 'product_id', 'category_id');
    }


    // Accessors
    public function getMoneyAttribute()
    {
        return sprintf('Rp. %s', number_format(floatval($this->price), 0, null, '.'));
    }

    public function getUsedPriceAttribute()
    {
        return $this->discount_price ? $this->discount_price : $this->price;
    }

    // Appended Attributes
    protected $appends = ['money', 'used_price', 'images_array'];

    // Eager Load Relationships
    protected $with = ['images'];

    // Scope
    public function scopeShow($query)
    {
        return $query->where('preview', true);
    }

    // Custom JSON format for images
    public function getImagesArrayAttribute()
    {
        $images = [];
        if ($this->thumbnail_img) {
            $images[] = $this->thumbnail_img;
        }
        $images=array_merge($images, $this->images->pluck('image_path')->toArray());
        return $images; // Assuming 'url' is the column in ProductImage
    }

    // Transform category and tag attributes to arrays
    public function getCategoryAttribute()
    {
        $categories = $this->categories->filter(function ($category) {
            return $category->type === 'product_category';
        })->pluck('name');

        return $categories->toArray();
    }

    public function getTagAttribute($value)
    {
        $categories = $this->categories->filter(function ($category) {
            return $category->type === 'product_tag';
        })->pluck('name');

        return $categories->toArray();
    }

    public function getDiscountAttribute()
    {
        //return $this->discount_price;
        if ($this->discount_price && $this->discount_price >0) {
            $discount = ($this->price - $this->discount_price)/$this->price*100;
        }
        return $discount ?? 0;
    }
    public function getNewAttribute()
    {
        return $this->created_at >= Carbon::now()->subMonth();
    }

    // Serialize the object as desired
    public function toArray()
    {
        return [
            'id' => $this->id,
            'sku' => $this->sku,
            'slug' => $this->slug,
            'name' => $this->name,
            'price' => $this->price,
            'discount' => $this->discount ?? 0,
            'new' => $this->new ?? false,
            'rating' => $this->rating ?? 0,
            'saleCount' => $this->sale_count ?? 0,
            'category' => $this->category,
            'tag' => $this->tag,
            'stock' => $this->stock ?? 0,
            'image' => $this->images_array,
            'thumbnail_img' => $this->thumbnail_img,
            'shortDescription' => $this->short_description,
            'fullDescription' => $this->description,
        ];
    }
}
