<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProductReview;
use App\Models\Product;
use App\Models\User;

class ProductReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create multiple users for reviews
        $users = [];
        for ($i = 1; $i <= 5; $i++) {
            $users[] = User::firstOrCreate(
                ['email' => "user{$i}@example.com"],
                [
                    'name' => "User {$i}",
                    'password' => bcrypt('password'),
                ]
            );
        }

        // Get all products
        $products = Product::all();

        $reviews = [
            [
                'rating' => 5,
                'comment' => 'Excellent quality! The jersey fits perfectly and the material is very comfortable. Highly recommend for any Arsenal fan.',
                'is_verified' => true
            ],
            [
                'rating' => 4,
                'comment' => 'Great jersey, love the design. Only giving 4 stars because the delivery took a bit longer than expected.',
                'is_verified' => true
            ],
            [
                'rating' => 5,
                'comment' => 'Perfect! The scarf is warm and the colors are vibrant. Exactly what I was looking for.',
                'is_verified' => true
            ],
            [
                'rating' => 3,
                'comment' => 'Good quality print, but the frame could be better. Still looks great on the wall.',
                'is_verified' => false
            ],
            [
                'rating' => 5,
                'comment' => 'Amazing training jacket! Very comfortable and the Arsenal branding looks great.',
                'is_verified' => true
            ],
            [
                'rating' => 4,
                'comment' => 'Nice keychain, good quality metal. Perfect gift for Arsenal fans.',
                'is_verified' => true
            ],
            [
                'rating' => 5,
                'comment' => 'Absolutely love this jersey! The fit is perfect and the quality is outstanding.',
                'is_verified' => true
            ],
            [
                'rating' => 4,
                'comment' => 'Good scarf, keeps me warm during matches. Would buy again.',
                'is_verified' => true
            ],
            [
                'rating' => 5,
                'comment' => 'Fantastic product! The quality exceeds my expectations.',
                'is_verified' => true
            ],
            [
                'rating' => 4,
                'comment' => 'Very good product, would recommend to other Arsenal fans.',
                'is_verified' => true
            ]
        ];

        $reviewIndex = 0;
        foreach ($products as $product) {
            // Add 1-3 reviews per product with different users
            $numReviews = rand(1, 3);
            $usedUsers = [];
            
            for ($i = 0; $i < $numReviews && $i < count($users); $i++) {
                // Get a user that hasn't reviewed this product yet
                $user = $users[array_rand($users)];
                while (in_array($user->id, $usedUsers)) {
                    $user = $users[array_rand($users)];
                }
                $usedUsers[] = $user->id;
                
                $reviewData = $reviews[$reviewIndex % count($reviews)];
                
                ProductReview::create([
                    'product_id' => $product->id,
                    'user_id' => $user->id,
                    'rating' => $reviewData['rating'],
                    'comment' => $reviewData['comment'],
                    'is_verified' => $reviewData['is_verified']
                ]);
                
                $reviewIndex++;
            }
        }
    }
}
