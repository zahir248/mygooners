<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'user_id' => 'integer',
        'session_id' => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    public function getTotalAttribute()
    {
        return $this->items->sum(function ($item) {
            return $item->price * $item->quantity;
        });
    }

    public function getItemCountAttribute()
    {
        return $this->items->count();
    }

    public static function getOrCreateCart()
    {
        $user = auth()->user();
        $sessionId = session()->getId();

        if ($user) {
            // For authenticated users, get or create cart by user_id
            // Ensure user_id is cast to integer
            $userId = (int) $user->id;
            $cart = static::firstOrCreate(['user_id' => $userId]);
            
            return $cart;
        } else {
            // For guest users, get or create cart by session_id
            $cart = static::firstOrCreate(['session_id' => $sessionId]);
            
            return $cart;
        }
    }
}
