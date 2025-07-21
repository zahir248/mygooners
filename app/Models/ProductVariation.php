<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariation extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'name',
        'sku',
        'price',
        'sale_price',
        'stock_quantity',
        'images',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'images' => 'array',
        'is_active' => 'boolean',
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    public function getFinalPriceAttribute()
    {
        return $this->sale_price ?? $this->price ?? $this->product->price;
    }

    public function getOriginalPriceAttribute()
    {
        return $this->price ?? $this->product->price;
    }

    public function getDiscountPercentageAttribute()
    {
        $originalPrice = $this->original_price;
        $finalPrice = $this->final_price;
        
        if ($originalPrice > $finalPrice) {
            return round((($originalPrice - $finalPrice) / $originalPrice) * 100);
        }
        return 0;
    }


}
