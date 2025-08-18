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
        'images',
        'rejection_reason',
        'is_update_request',
        'original_service_id'
    ];

    protected $casts = [
        'tags' => 'array',
        'images' => 'array',
        'is_verified' => 'boolean',
        'trust_score' => 'decimal:2',
        'is_update_request' => 'boolean'
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

    /**
     * Update the service trust score based on approved reviews
     */
    public function updateTrustScore()
    {
        $averageRating = $this->reviews()
            ->where('status', 'approved')
            ->avg('rating') ?? 0;
        
        $this->update(['trust_score' => round($averageRating, 2)]);
        
        return $this->trust_score;
    }
} 