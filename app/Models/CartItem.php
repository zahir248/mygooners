<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'product_id',
        'product_variation_id',
        'quantity',
        'price'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'quantity' => 'integer'
    ];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variation()
    {
        return $this->belongsTo(ProductVariation::class, 'product_variation_id');
    }

    public function getSubtotalAttribute()
    {
        return $this->price * $this->quantity;
    }

    public function getDisplayNameAttribute()
    {
        \Log::info('Display name calculation:', [
            'product_title' => $this->product->title,
            'variation_id' => $this->product_variation_id,
            'variation_loaded' => $this->relationLoaded('variation'),
            'variation_name' => $this->variation ? $this->variation->name : 'null'
        ]);
        
        if ($this->variation) {
            return $this->product->title . ' - ' . $this->variation->name;
        }
        return $this->product->title;
    }
}
