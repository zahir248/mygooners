<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'user_id',
        'refund_reason',
        'bank_name',
        'bank_account_number',
        'bank_account_holder',
        'tracking_number',
        'shipping_courier',
        'status',
        'admin_notes',
        'rejection_reason',
        'refund_amount',
        'refunded_at',
        'receipt_image',
    ];

    protected $casts = [
        'refunded_at' => 'datetime',
        'refund_amount' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function images()
    {
        return $this->hasMany(RefundImage::class)->orderBy('sort_order');
    }



    public function getStatusBadgeClass()
    {
        return match($this->status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'approved' => 'bg-blue-100 text-blue-800',
            'rejected' => 'bg-red-100 text-red-800',
            'processing' => 'bg-purple-100 text-purple-800',
            'completed' => 'bg-green-100 text-green-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getStatusDisplayName()
    {
        return match($this->status) {
            'pending' => 'MENUNGGU',
            'approved' => 'DILULUSKAN',
            'rejected' => 'DITOLAK',
            'processing' => 'DIPROSES',
            'completed' => 'SELESAI',
            default => ucfirst($this->status),
        };
    }

    public function canBeRequested()
    {
        // Check if order is delivered and within 3 days
        if ($this->order->status !== 'delivered' || !$this->order->delivered_at) {
            return false;
        }

        $daysSinceDelivery = $this->order->delivered_at->diffInDays(now());
        return $daysSinceDelivery <= 3;
    }

    public function getDaysRemainingForRefund()
    {
        if ($this->order->status !== 'delivered' || !$this->order->delivered_at) {
            return null;
        }

        $daysSinceDelivery = $this->order->delivered_at->diffInDays(now());
        $daysRemaining = 3 - $daysSinceDelivery;
        
        return max(0, $daysRemaining);
    }

    public function getFormattedRefundAmount()
    {
        return 'RM' . number_format($this->refund_amount, 2);
    }

    public function getReceiptImageUrlAttribute()
    {
        if ($this->receipt_image) {
            return asset('storage/' . $this->receipt_image);
        }
        return null;
    }

    /**
     * Get tracking URL for return shipping
     */
    public function getTrackingUrl()
    {
        if (!$this->tracking_number || !$this->shipping_courier) {
            return null;
        }

        $courier = strtolower($this->shipping_courier);
        
        if (str_contains($courier, 'pos malaysia') || str_contains($courier, 'pos')) {
            $trackingCourier = 'poslaju';
        } elseif (str_contains($courier, 'j&t') || str_contains($courier, 'jt')) {
            $trackingCourier = 'jt';
        } elseif (str_contains($courier, 'ninja van') || str_contains($courier, 'ninja')) {
            $trackingCourier = 'ninjavan';
        } elseif (str_contains($courier, 'shopee express') || str_contains($courier, 'shopee')) {
            $trackingCourier = 'shopee';
        } elseif (str_contains($courier, 'lazada express') || str_contains($courier, 'lazada')) {
            $trackingCourier = 'lazada';
        } elseif (str_contains($courier, 'grabexpress') || str_contains($courier, 'grab')) {
            $trackingCourier = 'grab';
        } elseif (str_contains($courier, 'gojek')) {
            $trackingCourier = 'gojek';
        } elseif (str_contains($courier, 'lalamove')) {
            $trackingCourier = 'lalamove';
        } elseif (str_contains($courier, 'fedex')) {
            $trackingCourier = 'fedex';
        } elseif (str_contains($courier, 'dhl')) {
            $trackingCourier = 'dhl';
        } elseif (str_contains($courier, 'tnt')) {
            $trackingCourier = 'tnt';
        } elseif (str_contains($courier, 'uber eats') || str_contains($courier, 'uber')) {
            $trackingCourier = 'ubereats';
        } elseif (str_contains($courier, 'foodpanda')) {
            $trackingCourier = 'foodpanda';
        } elseif (str_contains($courier, 'deliveroo')) {
            $trackingCourier = 'deliveroo';
        } else {
            $trackingCourier = 'unknown';
        }

        return "https://tracking.my/{$trackingCourier}/{$this->tracking_number}";
    }
} 