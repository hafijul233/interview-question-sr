<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariantPrice extends Model
{
    protected $fillable = ['product_variant_one', 'product_variant_two', 'product_variant_three', 'price', 'stock', 'product_id'];

    public function variantOne()
    {
        return $this->hasOne(ProductVariant::class, 'id', 'product_variant_one');
    }

    public function variantTwo()
    {
        return $this->hasOne(ProductVariant::class, 'id', 'product_variant_two');
    }

    public function variantThree()
    {
        return $this->hasOne(ProductVariant::class, 'id', 'product_variant_three');
    }
}
