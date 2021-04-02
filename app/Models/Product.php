<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'title', 'sku', 'description'
    ];

    public function images()
    {
        return $this->hasMany(ProductImage::class,'product_id','id');
    }
    public function variants()
    {
        return $this->hasMany(ProductVariant::class,'product_id','id');
    }
    public function variant_price()
    {
        return $this->hasManyThrough(ProductVariantPrice::class,ProductVariant::class,'product_id','product_variant_one');
    }
}
