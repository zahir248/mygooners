<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'cover_image',
        'category',
        'tags',
        'youtube_video_id',
        'is_featured',
        'published_at',
        'meta_title',
        'meta_description',
        'keywords',
        'views_count',
        'status'
    ];

    protected $casts = [
        'tags' => 'array',
        'published_at' => 'datetime',
        'is_featured' => 'boolean'
    ];

    /**
     * Ensure tags is always an array
     */
    public function getTagsAttribute($value)
    {
        if (is_null($value)) {
            return [];
        }
        
        if (is_string($value)) {
            return json_decode($value, true) ?: [];
        }
        
        return $value ?: [];
    }

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

    public function getCoverImageUrlAttribute()
    {
        if ($this->cover_image) {
            // Check if file exists in storage
            if (Storage::disk('public')->exists($this->cover_image)) {
                return Storage::disk('public')->url($this->cover_image);
            }
            
            // If file doesn't exist, log it but don't clear the path automatically
            \Log::warning('Cover image file not found', [
                'article_id' => $this->id,
                'cover_image_path' => $this->cover_image,
                'storage_exists' => Storage::disk('public')->exists($this->cover_image),
                'full_path' => storage_path('app/public/' . $this->cover_image)
            ]);
        }
        return null;
    }

    /**
     * Get formatted date in Malaysian format
     */
    public function getFormattedDateAttribute()
    {
        return $this->created_at ? $this->created_at->format('j M Y') : 'Tidak Diketahui';
    }

    /**
     * Get formatted time in Malaysian format
     */
    public function getFormattedTimeAttribute()
    {
        return $this->created_at ? $this->created_at->format('H:i') : 'Tidak Diketahui';
    }

    /**
     * Get formatted published date in Malaysian format
     */
    public function getFormattedPublishedDateAttribute()
    {
        return $this->published_at ? $this->published_at->format('j M Y') : 'Tidak Diterbitkan';
    }

    /**
     * Get content with HTML formatting
     */
    public function getFormattedContentAttribute()
    {
        if (!$this->content) {
            return '';
        }
        
        // If content contains HTML tags, sanitize and return it (from TinyMCE)
        if (strip_tags($this->content) !== $this->content) {
            // Allow only safe HTML tags for rich text content
            $allowedTags = '<p><br><strong><b><em><i><u><a><ul><ol><li><h1><h2><h3><h4><h5><h6><blockquote><pre><code><img><div><span>';
            return strip_tags($this->content, $allowedTags);
        }
        
        // Fallback for plain text content - convert line breaks to HTML
        $paragraphs = preg_split('/\n/', $this->content);
        
        $formattedContent = '';
        foreach ($paragraphs as $paragraph) {
            $paragraph = trim($paragraph);
            if (!empty($paragraph)) {
                // Convert single line breaks within paragraphs to <br> tags
                $paragraph = nl2br($paragraph);
                $formattedContent .= '<p class="mb-4">' . $paragraph . '</p>';
            }
        }
        
        return $formattedContent;
    }
} 