<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'description',
        'price',
        'sale_price',
        'stock_quantity',
        'category',
        'tags',
        'images',
        'is_featured',
        'status',
        'variation_label',
        'meta_title',
        'meta_description',
        'views_count'
    ];

    protected $casts = [
        'tags' => 'array',
        'images' => 'array',
        'is_featured' => 'boolean',
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'stock_quantity' => 'integer'
    ];

    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function variations()
    {
        return $this->hasMany(ProductVariation::class)->ordered();
    }

    public function activeVariations()
    {
        return $this->hasMany(ProductVariation::class)->active()->ordered();
    }

    public function getActiveVariationsWithComputedPropertiesAttribute()
    {
        return $this->activeVariations()->get()->map(function ($variation) {
            // Add computed properties for frontend
            $variation->final_price = $variation->sale_price ?: $variation->price;
            $variation->original_price = $variation->price;
            return $variation;
        });
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function getAverageRatingAttribute()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

    public function getDiscountPercentageAttribute()
    {
        if ($this->sale_price && $this->price > $this->sale_price) {
            return round((($this->price - $this->sale_price) / $this->price) * 100);
        }
        return 0;
    }

    public function hasVariations()
    {
        return $this->variations()->count() > 0;
    }

    public function getMinPriceAttribute()
    {
        if ($this->hasVariations()) {
            $minPrice = $this->activeVariations()->min('price');
            return $minPrice ?? $this->price;
        }
        return $this->price;
    }

    public function getMaxPriceAttribute()
    {
        if ($this->hasVariations()) {
            $maxPrice = $this->activeVariations()->max('price');
            return $maxPrice ?? $this->price;
        }
        return $this->price;
    }

    public function getMinSalePriceAttribute()
    {
        if ($this->hasVariations()) {
            $minSalePrice = $this->activeVariations()->whereNotNull('sale_price')->min('sale_price');
            return $minSalePrice ?? $this->sale_price;
        }
        return $this->sale_price;
    }

    public function getTotalStockAttribute()
    {
        if ($this->hasVariations()) {
            return $this->activeVariations()->sum('stock_quantity');
        }
        return $this->stock_quantity;
    }

    public function getVariantUrl($variantName)
    {
        $encodedName = urlencode($variantName);
        return route('shop.show', $this->slug) . '?variant=' . $encodedName;
    }
} 