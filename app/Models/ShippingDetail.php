<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'is_default',
        'label',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFullAddressAttribute()
    {
        return "{$this->address}, {$this->city}, {$this->state} {$this->postal_code}, {$this->country}";
    }

    public function getDisplayLabelAttribute()
    {
        return $this->label ?: $this->name;
    }

    public static function getDefaultForUser($userId)
    {
        return static::where('user_id', $userId)
                    ->where('is_default', true)
                    ->first();
    }

    public static function setAsDefault($id, $userId)
    {
        // Remove default from all other shipping details for this user
        static::where('user_id', $userId)
              ->where('is_default', true)
              ->update(['is_default' => false]);

        // Set this one as default
        return static::where('id', $id)
                    ->where('user_id', $userId)
                    ->update(['is_default' => true]);
    }
}
