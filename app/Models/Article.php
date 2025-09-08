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
        'twitter_embed',
        'facebook_embed',
        'instagram_embed',
        'tiktok_embed',
        'custom_embed',
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
            // Allow only safe HTML tags for rich text content, including class attributes
            $allowedTags = '<p><br><strong><b><em><i><u><a><ul><ol><li><h1><h2><h3><h4><h5><h6><blockquote><pre><code><img><div><span>';
            $content = strip_tags($this->content, $allowedTags);
            
            // Preserve class attributes for images (for side-by-side layout)
            $content = $this->preserveImageClasses($content);
            
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
     * Add inline styles to headings for proper styling (works on all deployments)
     */
    private function addBootstrapClassesToHeadings($content)
    {
        // Add inline styles to headings
        $content = preg_replace('/<h1([^>]*)>/', '<h1$1 style="font-size: 2.25rem; font-weight: 700; line-height: 1.2; margin-top: 2rem; margin-bottom: 1rem; color: #111827;">', $content);
        $content = preg_replace('/<h2([^>]*)>/', '<h2$1 style="font-size: 1.875rem; font-weight: 600; line-height: 1.3; margin-top: 1.5rem; margin-bottom: 0.75rem; color: #111827;">', $content);
        $content = preg_replace('/<h3([^>]*)>/', '<h3$1 style="font-size: 1.5rem; font-weight: 600; line-height: 1.4; margin-top: 1.25rem; margin-bottom: 0.5rem; color: #111827;">', $content);
        $content = preg_replace('/<h4([^>]*)>/', '<h4$1 style="font-size: 1.25rem; font-weight: 600; line-height: 1.4; margin-top: 1rem; margin-bottom: 0.5rem; color: #111827;">', $content);
        $content = preg_replace('/<h5([^>]*)>/', '<h5$1 style="font-size: 1.125rem; font-weight: 600; line-height: 1.4; margin-top: 0.75rem; margin-bottom: 0.5rem; color: #111827;">', $content);
        $content = preg_replace('/<h6([^>]*)>/', '<h6$1 style="font-size: 1rem; font-weight: 600; line-height: 1.4; margin-top: 0.75rem; margin-bottom: 0.5rem; color: #111827;">', $content);
        
        // Add inline styles to paragraphs
        $content = preg_replace('/<p([^>]*)>/', '<p$1 style="margin-bottom: 1rem; color: #374151; line-height: 1.7;">', $content);
        
        // Add inline styles to other elements
        $content = preg_replace('/<strong([^>]*)>/', '<strong$1 style="font-weight: 700; color: #111827;">', $content);
        $content = preg_replace('/<em([^>]*)>/', '<em$1 style="font-style: italic;">', $content);
        $content = preg_replace('/<u([^>]*)>/', '<u$1 style="text-decoration: underline;">', $content);
        $content = preg_replace('/<a([^>]*)>/', '<a$1 style="color: #dc2626; text-decoration: none;">', $content);
        $content = preg_replace('/<ul([^>]*)>/', '<ul$1 style="margin-bottom: 1rem; padding-left: 1.5rem; list-style-type: disc;">', $content);
        $content = preg_replace('/<ol([^>]*)>/', '<ol$1 style="margin-bottom: 1rem; padding-left: 1.5rem; list-style-type: decimal;">', $content);
        $content = preg_replace('/<li([^>]*)>/', '<li$1 style="margin-bottom: 0.25rem;">', $content);
        $content = preg_replace('/<blockquote([^>]*)>/', '<blockquote$1 style="border-left: 4px solid #dc2626; padding-left: 1rem; margin: 1.5rem 0; color: #6b7280; font-style: italic;">', $content);
        
        return $content;
    }
    
    /**
     * Preserve class attributes for images to maintain side-by-side layout
     */
    private function preserveImageClasses($content)
    {
        // Extract class attributes from original content before strip_tags
        preg_match_all('/<img[^>]*class=["\']([^"\']*)["\'][^>]*>/i', $this->content, $matches);
        
        if (!empty($matches[0])) {
            foreach ($matches[0] as $index => $originalImg) {
                $classValue = $matches[1][$index];
                
                // Find the corresponding img tag in the processed content
                preg_match_all('/<img[^>]*>/i', $content, $processedImgs);
                
                if (isset($processedImgs[0][$index])) {
                    $processedImg = $processedImgs[0][$index];
                    
                    // Add class attribute to the processed img tag
                    $newImg = preg_replace('/<img([^>]*)>/i', '<img$1 class="' . $classValue . '">', $processedImg);
                    
                    // Replace in content
                    $content = str_replace($processedImg, $newImg, $content);
                }
            }
        }
        
        return $content;
    }
} 