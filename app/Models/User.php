<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'google_id',
        'password',
        'profile_image',
        'bio',
        'location',
        'phone',
        'role',
        'trust_score',
        'is_verified',
        'status',
        'last_login',
        'admin_request_data',
        'is_seller',
        'seller_status',
        'seller_rejection_reason',
        'seller_application_date',
        'business_name',
        'business_type',
        'business_registration',
        'business_address',
        'operating_area',
        'website',
        'id_document',
        'selfie_with_id',
        'years_experience',
        'skills',
        'service_areas'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login' => 'datetime',
            'seller_application_date' => 'datetime',
            'password' => 'hashed',
            'trust_score' => 'decimal:2',
            'is_verified' => 'boolean',
            'admin_request_data' => 'array'
        ];
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function serviceReviews()
    {
        return $this->hasMany(ServiceReview::class);
    }

    public function productReviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    public function billingDetails()
    {
        return $this->hasMany(BillingDetail::class);
    }

    public function shippingDetails()
    {
        return $this->hasMany(ShippingDetail::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function refunds()
    {
        return $this->hasMany(Refund::class);
    }

    public function isAdmin()
    {
        return $this->role === 'admin' || $this->role === 'super_admin';
    }

    public function isSuperAdmin()
    {
        return $this->role === 'super_admin';
    }

    public function isGoogleUser()
    {
        return !empty($this->google_id);
    }
}
