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
            $content = strip_tags($this->content, $allowedTags);
            
            // Add Bootstrap classes to headings for proper styling
            $content = $this->addBootstrapClassesToHeadings($content);
            
            return $content;
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
    
    /**
     * Add Bootstrap classes to headings for proper styling
     */
    private function addBootstrapClassesToHeadings($content)
    {
        // Add Bootstrap classes to headings
        $content = preg_replace('/<h1([^>]*)>/', '<h1$1 class="text-4xl font-bold text-gray-900 mb-4 mt-6">', $content);
        $content = preg_replace('/<h2([^>]*)>/', '<h2$1 class="text-3xl font-bold text-gray-900 mb-3 mt-5">', $content);
        $content = preg_replace('/<h3([^>]*)>/', '<h3$1 class="text-2xl font-semibold text-gray-900 mb-3 mt-4">', $content);
        $content = preg_replace('/<h4([^>]*)>/', '<h4$1 class="text-xl font-semibold text-gray-900 mb-2 mt-3">', $content);
        $content = preg_replace('/<h5([^>]*)>/', '<h5$1 class="text-lg font-semibold text-gray-900 mb-2 mt-3">', $content);
        $content = preg_replace('/<h6([^>]*)>/', '<h6$1 class="text-base font-semibold text-gray-900 mb-2 mt-2">', $content);
        
        // Add Bootstrap classes to paragraphs
        $content = preg_replace('/<p([^>]*)>/', '<p$1 class="mb-4 text-gray-700 leading-relaxed">', $content);
        
        // Add Bootstrap classes to other elements
        $content = preg_replace('/<strong([^>]*)>/', '<strong$1 class="font-bold text-gray-900">', $content);
        $content = preg_replace('/<em([^>]*)>/', '<em$1 class="italic">', $content);
        $content = preg_replace('/<u([^>]*)>/', '<u$1 class="underline">', $content);
        $content = preg_replace('/<a([^>]*)>/', '<a$1 class="text-red-600 hover:text-red-700 hover:underline">', $content);
        $content = preg_replace('/<ul([^>]*)>/', '<ul$1 class="mb-4 pl-6 list-disc">', $content);
        $content = preg_replace('/<ol([^>]*)>/', '<ol$1 class="mb-4 pl-6 list-decimal">', $content);
        $content = preg_replace('/<li([^>]*)>/', '<li$1 class="mb-1">', $content);
        $content = preg_replace('/<blockquote([^>]*)>/', '<blockquote$1 class="border-l-4 border-red-600 pl-4 my-4 text-gray-600 italic">', $content);
        
        return $content;
    }
} 