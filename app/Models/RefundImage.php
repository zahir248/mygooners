<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefundImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'refund_id',
        'image_path',
        'image_name',
        'image_type',
        'sort_order',
    ];

    public function refund()
    {
        return $this->belongsTo(Refund::class);
    }

    public function getImageUrlAttribute()
    {
        return asset('storage/' . $this->image_path);
    }

    public function getThumbnailUrlAttribute()
    {
        $pathInfo = pathinfo($this->image_path);
        $thumbnailPath = $pathInfo['dirname'] . '/thumbnails/' . $pathInfo['basename'];
        return asset('storage/' . $thumbnailPath);
    }
} 