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
        'rating',
        'comment',
        'is_verified'
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

    public function getReviewerNameAttribute()
    {
        if ($this->user) {
            return $this->user->name;
        }
        return 'Anonim'; // Anonymous in Malay
    }

    public function getReviewerAvatarAttribute()
    {
        if ($this->user && $this->user->profile_image) {
            $profileImg = trim($this->user->profile_image);
            if (Str::startsWith($profileImg, 'http')) {
                return $profileImg;
            }
            return asset('storage/' . $profileImg);
        }
        return asset('images/profile-image-default.png');
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }
} 