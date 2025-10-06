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
        'status',
        'author_id'
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

    /**
     * Get the author of the article
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
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
            // Process embed codes within content first
            $content = $this->processInlineEmbeds($this->content);
            
            // Allow only safe HTML tags for rich text content, including class attributes
            $allowedTags = '<p><br><strong><b><em><i><u><a><ul><ol><li><h1><h2><h3><h4><h5><h6><blockquote><pre><code><img><div><span><iframe><script><figure><figcaption>';
            $content = strip_tags($content, $allowedTags);
            
            // Preserve class attributes for images (for side-by-side layout)
            $content = $this->preserveImageClasses($content);
            
            // Clean up any duplicate class attributes
            $content = $this->cleanupDuplicateClasses($content);
            
            // Ensure figure tags have proper class attributes for captions
            $content = $this->ensureFigureClasses($content);
            
            // Add inline styles to captions to ensure they work
            $content = $this->addInlineCaptionStyles($content);
            
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
     * Process inline embed codes within content
     */
    private function processInlineEmbeds($content)
    {
        // Process Twitter embeds
        if ($this->twitter_embed) {
            $wrappedEmbed = '<div class="inline-embed">' . $this->twitter_embed . '</div>';
            $content = str_replace('[TWITTER_EMBED]', $wrappedEmbed, $content);
        }
        
        // Process Facebook embeds
        if ($this->facebook_embed) {
            $wrappedEmbed = '<div class="inline-embed">' . $this->facebook_embed . '</div>';
            $content = str_replace('[FACEBOOK_EMBED]', $wrappedEmbed, $content);
        }
        
        // Process Instagram embeds
        if ($this->instagram_embed) {
            $wrappedEmbed = '<div class="inline-embed">' . $this->instagram_embed . '</div>';
            $content = str_replace('[INSTAGRAM_EMBED]', $wrappedEmbed, $content);
        }
        
        // Process TikTok embeds
        if ($this->tiktok_embed) {
            $wrappedEmbed = '<div class="inline-embed">' . $this->tiktok_embed . '</div>';
            $content = str_replace('[TIKTOK_EMBED]', $wrappedEmbed, $content);
        }
        
        // Process custom embeds
        if ($this->custom_embed) {
            $wrappedEmbed = '<div class="inline-embed">' . $this->custom_embed . '</div>';
            $content = str_replace('[CUSTOM_EMBED]', $wrappedEmbed, $content);
        }
        
        return $content;
    }

    /**
     * Preserve class attributes for images and figures to maintain side-by-side layout and captions
     */
    private function preserveImageClasses($content)
    {
        // Preserve class attributes for img tags
        preg_match_all('/<img[^>]*class=["\']([^"\']*)["\'][^>]*>/i', $this->content, $imgMatches);
        
        if (!empty($imgMatches[0])) {
            foreach ($imgMatches[0] as $index => $originalImg) {
                $classValue = $imgMatches[1][$index];
                
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
        
        // Preserve class attributes for figure tags
        preg_match_all('/<figure[^>]*class=["\']([^"\']*)["\'][^>]*>/i', $this->content, $figureMatches);
        
        if (!empty($figureMatches[0])) {
            foreach ($figureMatches[0] as $index => $originalFigure) {
                $classValue = $figureMatches[1][$index];
                
                // Find the corresponding figure tag in the processed content
                preg_match_all('/<figure[^>]*>/i', $content, $processedFigures);
                
                if (isset($processedFigures[0][$index])) {
                    $processedFigure = $processedFigures[0][$index];
                    
                    // Check if the processed figure already has a class attribute
                    if (strpos($processedFigure, 'class=') === false) {
                        // Add class attribute to the processed figure tag
                        $newFigure = preg_replace('/<figure([^>]*)>/i', '<figure$1 class="' . $classValue . '">', $processedFigure);
                        
                        // Replace in content
                        $content = str_replace($processedFigure, $newFigure, $content);
                    }
                }
            }
        }
        
        return $content;
    }
    
    /**
     * Clean up duplicate class attributes and malformed HTML
     */
    private function cleanupDuplicateClasses($content)
    {
        // Fix duplicate class attributes on figure tags
        $content = preg_replace('/<figure\s+class="([^"]*)"\s+class="([^"]*)"/', '<figure class="$1"', $content);
        
        // Fix duplicate class attributes on img tags
        $content = preg_replace('/<img([^>]*?)class="([^"]*)"([^>]*?)class="([^"]*)"/', '<img$1class="$2"$3', $content);
        
        // Clean up malformed img tags with trailing spaces
        $content = preg_replace('/<img([^>]*?)\s+>/', '<img$1>', $content);
        
        return $content;
    }
    
    /**
     * Ensure figure tags have proper class attributes for captions
     */
    private function ensureFigureClasses($content)
    {
        // Find all figure tags that contain figcaption but don't have class="image"
        preg_match_all('/<figure[^>]*>.*?<figcaption[^>]*>.*?<\/figcaption>.*?<\/figure>/is', $content, $matches);
        
        foreach ($matches[0] as $figure) {
            // Check if the figure already has a class attribute
            if (strpos($figure, 'class=') === false) {
                // Add class="image" to figure tags that contain figcaption
                $newFigure = str_replace('<figure', '<figure class="image"', $figure);
                $content = str_replace($figure, $newFigure, $content);
            }
        }
        
        // Also handle cases where images might be in paragraphs with captions as separate paragraphs
        // Look for patterns like <p><img...></p><p>caption text</p>
        $content = preg_replace_callback(
            '/(<p[^>]*><img[^>]*><\/p>)\s*(<p[^>]*>([^<]+)<\/p>)/i',
            function($matches) {
                $imgParagraph = $matches[1];
                $captionParagraph = $matches[2];
                $captionText = $matches[3];
                
                // Extract any existing classes from the img tag
                preg_match('/class="([^"]*)"/', $imgParagraph, $classMatches);
                $imgClasses = $classMatches ? $classMatches[1] : 'image-centered';
                
                // Create new img tag with proper classes
                $imgTag = preg_replace('/class="[^"]*"/', 'class="' . $imgClasses . '"', $imgParagraph);
                
                // Wrap in figure with proper structure
                $result = '<figure class="image">' . $imgTag . '<figcaption>' . $captionText . '</figcaption></figure>';
                
                return $result;
            },
            $content
        );
        
        return $content;
    }
    
    /**
     * Add inline styles to captions and images to ensure they work with Tailwind CSS
     */
    private function addInlineCaptionStyles($content)
    {
        // Add inline styles to figcaption elements
        $content = preg_replace_callback(
            '/<figcaption([^>]*)>(.*?)<\/figcaption>/is',
            function($matches) {
                $attributes = $matches[1];
                $text = $matches[2];
                
                // Add inline styles to ensure caption styling works
                $style = 'style="display: block !important; text-align: center !important; font-style: italic !important; color: #666 !important; font-size: 0.9em !important; padding: 12px 0 !important; border-top: 1px solid #eee !important; width: 60% !important; margin: 0 auto !important; background: transparent !important; font-weight: normal !important; line-height: 1.4 !important;"';
                
                return '<figcaption' . $attributes . ' ' . $style . '>' . $text . '</figcaption>';
            },
            $content
        );
        
        // Add inline styles to figure elements to ensure they're centered
        $content = preg_replace_callback(
            '/<figure([^>]*)>(.*?)<\/figure>/is',
            function($matches) {
                $attributes = $matches[1];
                $content = $matches[2];
                
                // Add inline styles to ensure figure is centered
                $style = 'style="display: block !important; margin: 2em auto !important; text-align: center !important; max-width: 70% !important; width: auto !important;"';
                
                return '<figure' . $attributes . ' ' . $style . '>' . $content . '</figure>';
            },
            $content
        );
        
        // Add inline styles to img elements to ensure they're centered
        $content = preg_replace_callback(
            '/<img([^>]*)>/is',
            function($matches) {
                $attributes = $matches[1];
                
                // Add inline styles to ensure image is centered
                $style = 'style="display: block !important; max-width: 100% !important; height: auto !important; margin: 0 auto !important; border-radius: 4px !important;"';
                
                return '<img' . $attributes . ' ' . $style . '>';
            },
            $content
        );
        
        return $content;
    }
} 