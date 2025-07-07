<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'description',
        'location',
        'pricing',
        'contact_info',
        'category',
        'tags',
        'is_verified',
        'trust_score',
        'views_count',
        'status',
        'images'
    ];

    protected $casts = [
        'tags' => 'array',
        'images' => 'array',
        'is_verified' => 'boolean',
        'trust_score' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviews()
    {
        return $this->hasMany(ServiceReview::class);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function getAverageRatingAttribute()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }
} 