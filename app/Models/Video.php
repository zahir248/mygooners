<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'youtube_video_id',
        'thumbnail',
        'duration',
        'category',
        'tags',
        'is_featured',
        'published_at',
        'views_count',
        'status'
    ];

    protected $casts = [
        'tags' => 'array',
        'is_featured' => 'boolean',
        'published_at' => 'datetime'
    ];

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function getYoutubeUrlAttribute()
    {
        return "https://www.youtube.com/watch?v=" . $this->youtube_video_id;
    }

    public function getEmbedUrlAttribute()
    {
        return "https://www.youtube.com/embed/" . $this->youtube_video_id;
    }

    public function getThumbnailUrlAttribute()
    {
        if ($this->thumbnail) {
            return route('video.thumbnail', $this->thumbnail);
        }
        return "https://img.youtube.com/vi/" . $this->youtube_video_id . "/maxresdefault.jpg";
    }
} 