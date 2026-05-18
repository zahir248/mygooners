<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;

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
        $profileImg = trim((string) optional($this->user)->profile_image);
        if ($profileImg === '') {
            return null;
        }

        if (Str::startsWith($profileImg, ['http://', 'https://'])) {
            return $profileImg;
        }

        if (Str::startsWith($profileImg, ['profile_images/', 'profiles/', 'users/'])) {
            return URL::to('/profile-image/' . basename($profileImg));
        }

        return URL::to('/storage/' . ltrim($profileImg, '/'));
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
