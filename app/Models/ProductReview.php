<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProductReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'order_id',
        'order_item_id',
        'rating',
        'comment',
        'is_verified',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_verified' => 'boolean'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function photos()
    {
        return $this->hasMany(ProductReviewPhoto::class);
    }

    public function getReviewerNameAttribute()
    {
        if ($this->user) {
            return $this->user->name;
        }
        return 'Anonim'; // Anonymous in Malay
    }

    public function getReviewerAvatarAttribute()
    {
        return $this->getReviewerAvatarUrlAttribute() ?? asset('images/profile-image-default.png');
    }

    public function getReviewerAvatarUrlAttribute()
    {
        if ($this->user && !empty($this->user->profile_image_url)) {
            return $this->user->profile_image_url;
        }

        $profileImg = trim((string) optional($this->user)->profile_image);
        if ($profileImg === '') {
            return null;
        }

        if (Str::startsWith($profileImg, ['http://', 'https://'])) {
            return $profileImg;
        }

        return url('/profile-image/' . basename($profileImg));
    }

    public function getPhotoUrlsAttribute(): array
    {
        return $this->photos->map(fn ($photo) => $photo->image_url)->filter()->values()->all();
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }
} 
