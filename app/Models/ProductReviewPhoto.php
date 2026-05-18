<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;

class ProductReviewPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_review_id',
        'image_path',
    ];

    public function review()
    {
        return $this->belongsTo(ProductReview::class, 'product_review_id');
    }

    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image_path) {
            return null;
        }

        return URL::to('/storage/' . ltrim($this->image_path, '/'));
    }
}

