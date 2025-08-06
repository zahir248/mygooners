<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition()
    {
        return [
            'order_number' => 'MG' . now()->format('Ymd') . strtoupper(substr(md5(uniqid()), 0, 6)),
            'user_id' => User::factory(),
            'status' => $this->faker->randomElement(['pending', 'processing', 'shipped', 'delivered', 'cancelled']),
            'subtotal' => $this->faker->randomFloat(2, 10, 1000),
            'shipping_cost' => $this->faker->randomFloat(2, 0, 50),
            'tax' => $this->faker->randomFloat(2, 0, 100),
            'total' => function (array $attributes) {
                return $attributes['subtotal'] + $attributes['shipping_cost'] + $attributes['tax'];
            },
            'payment_method' => $this->faker->randomElement(['toyyibpay', 'stripe']),
            'payment_status' => $this->faker->randomElement(['pending', 'paid', 'failed', 'refunded']),
            'shipping_name' => $this->faker->name,
            'shipping_email' => $this->faker->email,
            'shipping_phone' => $this->faker->phoneNumber,
            'shipping_address' => $this->faker->address,
            'shipping_city' => $this->faker->city,
            'shipping_state' => $this->faker->state,
            'shipping_postal_code' => $this->faker->postcode,
            'shipping_country' => $this->faker->country,
            'billing_name' => $this->faker->name,
            'billing_email' => $this->faker->email,
            'billing_phone' => $this->faker->phoneNumber,
            'billing_address' => $this->faker->address,
            'billing_city' => $this->faker->city,
            'billing_state' => $this->faker->state,
            'billing_postal_code' => $this->faker->postcode,
            'billing_country' => $this->faker->country,
            'notes' => $this->faker->optional()->sentence,
            'tracking_number' => $this->faker->optional()->bothify('TRK#########'),
            'shipping_courier' => $this->faker->optional()->randomElement(['PosLaju', 'J&T Express', 'Ninja Van', 'GDEX']),
        ];
    }

    public function pending()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'pending',
                'payment_status' => 'pending',
            ];
        });
    }

    public function processing()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'processing',
                'payment_status' => 'paid',
            ];
        });
    }

    public function shipped()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'shipped',
                'payment_status' => 'paid',
                'shipped_at' => now(),
            ];
        });
    }

    public function delivered()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'delivered',
                'payment_status' => 'paid',
                'shipped_at' => now()->subDays(2),
                'delivered_at' => now(),
            ];
        });
    }

    public function cancelled()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'cancelled',
                'payment_status' => 'refunded',
            ];
        });
    }
} 