<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $appends = [
        'calculated_stock',
        'total_variation_stock',
        'total_stock',
        'is_in_stock',
        'is_out_of_stock',
        'stock_label',
    ];

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
        'views_count',
        'rejection_reason'
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
        return $this->hasMany(ProductReview::class)->latest();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function variations()
    {
        return $this->hasMany(ProductVariation::class)->ordered();
    }

    public function favourites()
    {
        return $this->hasMany(Favourite::class);
    }

    public function favouritedBy()
    {
        return $this->belongsToMany(User::class, 'favourites');
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
        return $query->where(function ($q) {
            $q->where(function ($baseProductQuery) {
                $baseProductQuery
                    ->whereDoesntHave('activeVariations')
                    ->where('stock_quantity', '>', 0);
            })->orWhereHas('activeVariations', function ($variationQuery) {
                $variationQuery->where('stock_quantity', '>', 0);
            });
        });
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function getAverageRatingAttribute()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

    public function getReviewsCountAttribute()
    {
        return $this->reviews()->count();
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
        if ($this->relationLoaded('activeVariations')) {
            return $this->activeVariations->isNotEmpty();
        }

        return $this->activeVariations()->exists();
    }

    public function getTotalVariationStockAttribute()
    {
        if (!$this->hasVariations()) {
            return 0;
        }

        if ($this->relationLoaded('activeVariations')) {
            return (int) $this->activeVariations->sum('stock_quantity');
        }

        return (int) $this->activeVariations()->sum('stock_quantity');
    }

    public function getCalculatedStockAttribute()
    {
        if ($this->hasVariations()) {
            return $this->total_variation_stock;
        }

        return (int) $this->stock_quantity;
    }

    public function getIsInStockAttribute()
    {
        return $this->calculated_stock > 0;
    }

    public function getIsOutOfStockAttribute()
    {
        return !$this->is_in_stock;
    }

    public function getStockLabelAttribute()
    {
        return $this->is_in_stock ? 'In Stock' : 'No Stock';
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
        return $this->calculated_stock;
    }

    public function getVariantUrl($variantName)
    {
        $encodedName = urlencode($variantName);
        return route('shop.show', $this->slug) . '?variant=' . $encodedName;
    }
} 
