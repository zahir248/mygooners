<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'title' => 'Arsenal Home Jersey 2024/25',
                'description' => 'Official Arsenal home jersey for the 2024/25 season. Made with high-quality breathable fabric featuring the iconic red and white design. Perfect for match days and casual wear.',
                'price' => 75.00,
                'sale_price' => 65.00,
                'stock_quantity' => 15,
                'category' => 'Jerseys',
                'tags' => ['arsenal', 'jersey', 'home', '2024/25', 'official'],
                'is_featured' => true,
                'status' => 'active',
                'meta_title' => 'Arsenal Home Jersey 2024/25 - Official Kit',
                'meta_description' => 'Get the official Arsenal home jersey for the 2024/25 season. High-quality fabric with iconic red and white design.',
                'views_count' => 543
            ],
            [
                'title' => 'Arsenal Scarf - Classic Design',
                'description' => 'Classic Arsenal scarf with traditional red and white stripes. Perfect for keeping warm during match days or showing your support. Made from soft, durable material.',
                'price' => 25.00,
                'sale_price' => null,
                'stock_quantity' => 8,
                'category' => 'Accessories',
                'tags' => ['arsenal', 'scarf', 'accessories', 'classic'],
                'is_featured' => false,
                'status' => 'active',
                'meta_title' => 'Arsenal Scarf - Classic Red and White Design',
                'meta_description' => 'Classic Arsenal scarf with traditional red and white stripes. Perfect for match days.',
                'views_count' => 234
            ],
            [
                'title' => 'Arsenal Stadium Print - Framed',
                'description' => 'Beautiful framed print of the Emirates Stadium. High-quality artwork capturing the iconic home of Arsenal Football Club. Perfect for home or office decoration.',
                'price' => 45.00,
                'sale_price' => 35.00,
                'stock_quantity' => 3,
                'category' => 'Art & Prints',
                'tags' => ['arsenal', 'stadium', 'print', 'art', 'emirates'],
                'is_featured' => true,
                'status' => 'active',
                'meta_title' => 'Arsenal Stadium Print - Framed Artwork',
                'meta_description' => 'Beautiful framed print of the Emirates Stadium. Perfect for home decoration.',
                'views_count' => 189
            ],
            [
                'title' => 'Arsenal Training Jacket',
                'description' => 'Official Arsenal training jacket with modern design and comfortable fit. Perfect for training sessions or casual wear. Features moisture-wicking technology.',
                'price' => 55.00,
                'sale_price' => null,
                'stock_quantity' => 12,
                'category' => 'Training Wear',
                'tags' => ['arsenal', 'training', 'jacket', 'sportswear'],
                'is_featured' => false,
                'status' => 'active',
                'meta_title' => 'Arsenal Training Jacket - Official Sportswear',
                'meta_description' => 'Official Arsenal training jacket with modern design and moisture-wicking technology.',
                'views_count' => 156
            ],
            [
                'title' => 'Arsenal Keychain - Club Badge',
                'description' => 'Premium Arsenal keychain featuring the official club badge. Made from high-quality metal with enamel finish. Perfect gift for any Arsenal fan.',
                'price' => 12.00,
                'sale_price' => 8.00,
                'stock_quantity' => 25,
                'category' => 'Accessories',
                'tags' => ['arsenal', 'keychain', 'badge', 'accessories'],
                'is_featured' => false,
                'status' => 'active',
                'meta_title' => 'Arsenal Keychain - Official Club Badge',
                'meta_description' => 'Premium Arsenal keychain featuring the official club badge. Perfect gift for fans.',
                'views_count' => 98
            ]
        ];

        foreach ($products as $productData) {
            $productData['slug'] = Str::slug($productData['title']);
            Product::create($productData);
        }
    }
}
