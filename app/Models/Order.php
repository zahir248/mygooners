<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'status',
        'subtotal',
        'shipping_cost',
        'tax',
        'total',
        'payment_method',
        'payment_status',
        'shipping_name',
        'shipping_email',
        'shipping_phone',
        'shipping_address',
        'shipping_city',
        'shipping_state',
        'shipping_postal_code',
        'shipping_country',
        'billing_name',
        'billing_email',
        'billing_phone',
        'billing_address',
        'billing_city',
        'billing_state',
        'billing_postal_code',
        'billing_country',
        'notes',
        'fpl_manager_name',
        'fpl_team_name',
        'tracking_number',
        'shipping_courier',
        'toyyibpay_bill_code',
        'stripe_payment_intent_id',
        'shipped_at',
        'delivered_at',
    ];

    protected $casts = [
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function refunds()
    {
        return $this->hasMany(Refund::class);
    }

    public function latestRefund()
    {
        return $this->hasOne(Refund::class)->latest();
    }

    public function hasActiveRefund()
    {
        return $this->refunds()->whereIn('status', ['approved', 'processing'])->exists();
    }

    public function hasPendingRefund()
    {
        return $this->refunds()->where('status', 'pending')->exists();
    }

    public function canRequestRefund()
    {
        // Check if order is delivered and within 3 days
        if ($this->status !== 'delivered' || !$this->delivered_at) {
            return false;
        }

        // Check if already has active refund
        if ($this->hasActiveRefund()) {
            return false;
        }

        $daysSinceDelivery = $this->delivered_at->diffInDays(now());
        return $daysSinceDelivery <= 3;
    }

    public function getDaysRemainingForRefund()
    {
        if ($this->status !== 'delivered' || !$this->delivered_at) {
            return null;
        }

        $daysSinceDelivery = $this->delivered_at->diffInDays(now());
        $daysRemaining = 3 - $daysSinceDelivery;
        
        return max(0, $daysRemaining);
    }

    /**
     * Get formatted countdown string for refund deadline
     */
    public function getFormattedRefundCountdown()
    {
        if ($this->status !== 'delivered' || !$this->delivered_at) {
            return null;
        }

        $now = now();
        $refundDeadline = $this->delivered_at->addDays(3);
        
        if ($now >= $refundDeadline) {
            return 'Tempoh refund telah tamat';
        }

        $diff = $now->diff($refundDeadline);
        
        if ($diff->days > 0) {
            return $diff->days . ' hari lagi';
        } elseif ($diff->h > 0) {
            return $diff->h . ' jam lagi';
        } elseif ($diff->i > 0) {
            return $diff->i . ' minit lagi';
        } else {
            return 'Sekarang';
        }
    }

    public function generateOrderNumber()
    {
        $prefix = 'MG';
        $date = now()->format('Ymd');
        $random = strtoupper(substr(md5(uniqid()), 0, 6));
        
        return $prefix . $date . $random;
    }

    public function getStatusBadgeClass()
    {
        return match($this->status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'processing' => 'bg-blue-100 text-blue-800',
            'shipped' => 'bg-purple-100 text-purple-800',
            'delivered' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800',
            'refunded' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getPaymentStatusBadgeClass()
    {
        return match($this->payment_status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'paid' => 'bg-green-100 text-green-800',
            'failed' => 'bg-red-100 text-red-800',
            'refunded' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getFormattedTotal()
    {
        return 'RM' . number_format($this->total, 2);
    }

    public function getFormattedSubtotal()
    {
        return 'RM' . number_format($this->subtotal, 2);
    }

    public function getFormattedShippingCost()
    {
        return 'RM' . number_format($this->shipping_cost, 2);
    }

    public function getFormattedTax()
    {
        return 'RM' . number_format($this->tax, 2);
    }

    public function getPaymentMethodDisplayName()
    {
        return match($this->payment_method) {
            'toyyibpay' => 'ToyyibPay',
            'stripe' => 'Stripe',
            default => ucfirst($this->payment_method),
        };
    }

    public function getPaymentStatusDisplayName()
    {
        return match($this->payment_status) {
            'paid' => 'DIBAYAR',
            'pending' => 'MENUNGGU',
            'failed' => 'GAGAL',
            'refunded' => 'DIPULANGKAN',
            default => ucfirst($this->payment_status),
        };
    }

    public function getOrderStatusDisplayName()
    {
        return match($this->status) {
            'pending' => 'MENUNGGU',
            'processing' => 'DIPROSES',
            'shipped' => 'DIHANTAR',
            'delivered' => 'DITERIMA',
            'cancelled' => 'DIBATALKAN',
            'refunded' => 'DIPULANGKAN',
            default => ucfirst($this->status),
        };
    }

    public function getTrackingUrl()
    {
        if (!$this->tracking_number) {
            return null;
        }

        $courier = strtolower(trim($this->shipping_courier ?? ''));
        
        // Direct mapping for common couriers
        if (str_contains($courier, 'shopee')) {
            $trackingCourier = 'shopee';
        } elseif (str_contains($courier, 'pos')) {
            $trackingCourier = 'poslaju';
        } elseif (str_contains($courier, 'j&t') || str_contains($courier, 'jt')) {
            $trackingCourier = 'jt';
        } elseif (str_contains($courier, 'ninja')) {
            $trackingCourier = 'ninjavan';
        } elseif (str_contains($courier, 'citylink')) {
            $trackingCourier = 'citylink';
        } elseif (str_contains($courier, 'gdex')) {
            $trackingCourier = 'gdex';
        } elseif (str_contains($courier, 'skynet')) {
            $trackingCourier = 'skynet';
        } elseif (str_contains($courier, 'abx')) {
            $trackingCourier = 'abx';
        } elseif (str_contains($courier, 'lex')) {
            $trackingCourier = 'lex';
        } elseif (str_contains($courier, 'fedex')) {
            $trackingCourier = 'fedex';
        } elseif (str_contains($courier, 'dhl')) {
            $trackingCourier = 'dhl';
        } elseif (str_contains($courier, 'ups')) {
            $trackingCourier = 'ups';
        } elseif (str_contains($courier, 'tnt')) {
            $trackingCourier = 'tnt';
        } elseif (str_contains($courier, 'aramex')) {
            $trackingCourier = 'aramex';
        } elseif (str_contains($courier, 'lazada')) {
            $trackingCourier = 'lazada';
        } elseif (str_contains($courier, 'grab')) {
            $trackingCourier = 'grab';
        } elseif (str_contains($courier, 'gojek')) {
            $trackingCourier = 'gojek';
        } elseif (str_contains($courier, 'lalamove')) {
            $trackingCourier = 'lalamove';
        } elseif (str_contains($courier, 'uber')) {
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

    /**
     * Check if order should be automatically marked as delivered
     */
    public function shouldBeAutoDelivered()
    {
        return $this->status === 'shipped' 
            && $this->shipped_at 
            && $this->shipped_at->diffInDays(now()) >= 7
            && !$this->delivered_at;
    }

    /**
     * Get days since order was shipped
     */
    public function getDaysSinceShipped()
    {
        if (!$this->shipped_at) {
            return null;
        }

        return $this->shipped_at->diffInDays(now());
    }

    /**
     * Check if order was automatically delivered (not manually marked by user)
     */
    public function wasAutoDelivered()
    {
        return $this->status === 'delivered' 
            && $this->delivered_at 
            && $this->shipped_at 
            && $this->shipped_at->diffInDays($this->delivered_at) >= 7;
    }

    /**
     * Get countdown days for auto-delivery (returns positive number or 0)
     */
    public function getAutoDeliveryCountdown()
    {
        if (!$this->shipped_at || $this->status !== 'shipped') {
            return null;
        }

        $daysSinceShipped = $this->shipped_at->diffInDays(now());
        $countdown = 7 - $daysSinceShipped;
        
        // Return 0 if already passed the 7-day mark
        return max(0, $countdown);
    }

    /**
     * Get formatted countdown string for auto-delivery
     */
    public function getFormattedAutoDeliveryCountdown()
    {
        if (!$this->shipped_at || $this->status !== 'shipped') {
            return null;
        }

        $now = now();
        $autoDeliveryDate = $this->shipped_at->addDays(7);
        
        if ($now >= $autoDeliveryDate) {
            return 'Sekarang';
        }

        $diff = $now->diff($autoDeliveryDate);
        
        if ($diff->days > 0) {
            return $diff->days . ' hari lagi';
        } elseif ($diff->h > 0) {
            return $diff->h . ' jam lagi';
        } elseif ($diff->i > 0) {
            return $diff->i . ' minit lagi';
        } else {
            return 'Sekarang';
        }
    }

    /**
     * Check if auto-delivery countdown is active (between 1-7 days)
     */
    public function isAutoDeliveryCountdownActive()
    {
        $countdown = $this->getAutoDeliveryCountdown();
        return $countdown !== null && $countdown > 0 && $countdown <= 7;
    }

    /**
     * Check if auto-delivery has passed (should have been auto-delivered)
     */
    public function isAutoDeliveryOverdue()
    {
        if (!$this->shipped_at || $this->status !== 'shipped') {
            return false;
        }

        $daysSinceShipped = $this->shipped_at->diffInDays(now());
        return $daysSinceShipped >= 7;
    }
}
