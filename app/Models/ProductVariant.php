<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $guarded = ['id'];

    public function variant_price()
    {
        return $this->hasOne(ProductVariantPrice::class,'product_variant_one','id');
    }
}
