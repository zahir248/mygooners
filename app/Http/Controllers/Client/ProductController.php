<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->get('category');
        $search = $request->get('search');
        $sort = $request->get('sort', 'newest');
        
        // Sample products data
        $products = collect([
            (object) [
                'id' => 1,
                'title' => 'Arsenal Home Jersey 2024/25',
                'slug' => 'arsenal-home-jersey-2024-25',
                'description' => 'Official Arsenal home jersey for the 2024/25 season. Made with high-quality materials and featuring the classic red design. Available in all sizes.',
                'price' => 75.00,
                'sale_price' => 65.00,
                'images' => [
                    'https://images.unsplash.com/photo-1551698618-1dfe5d97d256?w=600&h=600&fit=crop',
                    'https://images.unsplash.com/photo-1574629810360-7efbbe195018?w=600&h=600&fit=crop'
                ],
                'category' => 'Jerseys',
                'tags' => ['arsenal', 'jersey', 'home', '2024/25', 'official'],
                'is_featured' => true,
                'stock_quantity' => 15,
                'views_count' => 543,
                'average_rating' => 4.8,
                'reviews_count' => 12,
                'created_at' => now()->subDays(10)
            ],
            (object) [
                'id' => 2,
                'title' => 'Arsenal Scarf - Classic Design',
                'slug' => 'arsenal-scarf-classic-design',
                'description' => 'Classic Arsenal scarf with traditional red and white design. Perfect for match days and cold weather. High-quality knitted material.',
                'price' => 25.00,
                'sale_price' => null,
                'images' => [
                    'https://images.unsplash.com/photo-1574629810360-7efbbe195018?w=600&h=600&fit=crop'
                ],
                'category' => 'Accessories',
                'tags' => ['arsenal', 'scarf', 'classic', 'accessories', 'match day'],
                'is_featured' => false,
                'stock_quantity' => 8,
                'views_count' => 234,
                'average_rating' => 4.6,
                'reviews_count' => 8,
                'created_at' => now()->subDays(15)
            ],
            (object) [
                'id' => 3,
                'title' => 'Arsenal Stadium Print - Framed',
                'slug' => 'arsenal-stadium-print-framed',
                'description' => 'Beautiful framed print of the Emirates Stadium. Perfect for any Arsenal fan\'s home or office. High-quality print on premium paper.',
                'price' => 45.00,
                'sale_price' => 35.00,
                'images' => [
                    'https://images.unsplash.com/photo-1579952363873-27d3bfad9c0d?w=600&h=600&fit=crop'
                ],
                'category' => 'Art & Prints',
                'tags' => ['arsenal', 'stadium', 'print', 'framed', 'emirates'],
                'is_featured' => true,
                'stock_quantity' => 3,
                'views_count' => 189,
                'average_rating' => 4.9,
                'reviews_count' => 5,
                'created_at' => now()->subDays(5)
            ],
            (object) [
                'id' => 4,
                'title' => 'Arsenal Training Tracksuit',
                'slug' => 'arsenal-training-tracksuit',
                'description' => 'Professional Arsenal training tracksuit as worn by the first team. Comfortable and durable for training sessions.',
                'price' => 120.00,
                'sale_price' => 95.00,
                'images' => [
                    'https://images.unsplash.com/photo-1551698618-1dfe5d97d256?w=600&h=600&fit=crop'
                ],
                'category' => 'Training Wear',
                'tags' => ['arsenal', 'tracksuit', 'training', 'professional', 'first team'],
                'is_featured' => false,
                'stock_quantity' => 12,
                'views_count' => 321,
                'average_rating' => 4.7,
                'reviews_count' => 15,
                'created_at' => now()->subDays(8)
            ],
            (object) [
                'id' => 5,
                'title' => 'Arsenal Mug - Gunners Pride',
                'slug' => 'arsenal-mug-gunners-pride',
                'description' => 'High-quality ceramic mug with Arsenal logo and "Gunners Pride" design. Perfect for your morning coffee or tea.',
                'price' => 12.00,
                'sale_price' => null,
                'images' => [
                    'https://images.unsplash.com/photo-1486286701208-1d58e9338013?w=600&h=600&fit=crop'
                ],
                'category' => 'Accessories',
                'tags' => ['arsenal', 'mug', 'gunners', 'ceramic', 'coffee'],
                'is_featured' => false,
                'stock_quantity' => 25,
                'views_count' => 156,
                'average_rating' => 4.4,
                'reviews_count' => 6,
                'created_at' => now()->subDays(12)
            ],
            (object) [
                'id' => 6,
                'title' => 'Arsenal Away Jersey 2024/25',
                'slug' => 'arsenal-away-jersey-2024-25',
                'description' => 'Official Arsenal away jersey for the 2024/25 season. Stunning design with modern fit and premium materials.',
                'price' => 75.00,
                'sale_price' => null,
                'images' => [
                    'https://images.unsplash.com/photo-1577223625816-7546f30a2b62?w=600&h=600&fit=crop'
                ],
                'category' => 'Jerseys',
                'tags' => ['arsenal', 'jersey', 'away', '2024/25', 'official'],
                'is_featured' => true,
                'stock_quantity' => 18,
                'views_count' => 432,
                'average_rating' => 4.8,
                'reviews_count' => 9,
                'created_at' => now()->subDays(7)
            ]
        ]);

        // Filter by category if provided
        if ($category) {
            $products = $products->filter(function ($product) use ($category) {
                return strtolower($product->category) === strtolower($category);
            });
        }

        // Filter by search if provided
        if ($search) {
            $products = $products->filter(function ($product) use ($search) {
                return str_contains(strtolower($product->title), strtolower($search)) ||
                       str_contains(strtolower($product->description), strtolower($search));
            });
        }

        // Sort products
        switch ($sort) {
            case 'price_low':
                $products = $products->sortBy(function ($product) {
                    return $product->sale_price ?? $product->price;
                });
                break;
            case 'price_high':
                $products = $products->sortByDesc(function ($product) {
                    return $product->sale_price ?? $product->price;
                });
                break;
            case 'popular':
                $products = $products->sortByDesc('views_count');
                break;
            case 'rating':
                $products = $products->sortByDesc('average_rating');
                break;
            default:
                $products = $products->sortByDesc('created_at');
        }

        // Get categories for filter
        $categories = [
            'Jerseys',
            'Accessories',
            'Art & Prints',
            'Training Wear',
            'Footwear',
            'Collectibles',
            'Home & Garden'
        ];

        return view('client.shop.index', compact('products', 'categories', 'category', 'search', 'sort'));
    }

    public function show($slug)
    {
        // Sample product data
        $product = (object) [
            'id' => 1,
            'title' => 'Arsenal Home Jersey 2024/25',
            'slug' => 'arsenal-home-jersey-2024-25',
            'description' => 'Official Arsenal home jersey for the 2024/25 season. Made with high-quality materials and featuring the classic red design. Available in all sizes from XS to XXL.',
            'price' => 75.00,
            'sale_price' => 65.00,
            'images' => [
                'https://images.unsplash.com/photo-1551698618-1dfe5d97d256?w=800&h=800&fit=crop',
                'https://images.unsplash.com/photo-1574629810360-7efbbe195018?w=800&h=800&fit=crop',
                'https://images.unsplash.com/photo-1579952363873-27d3bfad9c0d?w=800&h=800&fit=crop'
            ],
            'category' => 'Jerseys',
            'tags' => ['arsenal', 'jersey', 'home', '2024/25', 'official'],
            'is_featured' => true,
            'stock_quantity' => 15,
            'views_count' => 543,
            'average_rating' => 4.8,
            'reviews_count' => 12,
            'discount_percentage' => 13,
            'sizes' => ['XS', 'S', 'M', 'L', 'XL', 'XXL'],
            'created_at' => now()->subDays(10)
        ];

        // Sample reviews
        $reviews = collect([
            (object) [
                'id' => 1,
                'rating' => 5,
                'comment' => 'Excellent quality jersey! Perfect fit and the material feels premium. Highly recommend!',
                'user' => (object) ['name' => 'John Smith'],
                'created_at' => now()->subWeeks(2)
            ],
            (object) [
                'id' => 2,
                'rating' => 5,
                'comment' => 'Great jersey, exactly as described. Fast shipping and well packaged.',
                'user' => (object) ['name' => 'Sarah Johnson'],
                'created_at' => now()->subWeeks(3)
            ],
            (object) [
                'id' => 3,
                'rating' => 4,
                'comment' => 'Good quality jersey, though the sizing runs a bit small. Order a size up.',
                'user' => (object) ['name' => 'Mike Wilson'],
                'created_at' => now()->subWeeks(4)
            ]
        ]);

        // Related products
        $relatedProducts = collect([
            (object) [
                'id' => 2,
                'title' => 'Arsenal Scarf - Classic Design',
                'slug' => 'arsenal-scarf-classic-design',
                'description' => 'Classic Arsenal scarf with traditional design.',
                'price' => 25.00,
                'sale_price' => null,
                'images' => ['https://images.unsplash.com/photo-1574629810360-7efbbe195018?w=400&h=400&fit=crop'],
                'category' => 'Accessories',
                'average_rating' => 4.6,
                'reviews_count' => 8
            ],
            (object) [
                'id' => 6,
                'title' => 'Arsenal Away Jersey 2024/25',
                'slug' => 'arsenal-away-jersey-2024-25',
                'description' => 'Official Arsenal away jersey for the 2024/25 season.',
                'price' => 75.00,
                'sale_price' => null,
                'images' => ['https://images.unsplash.com/photo-1577223625816-7546f30a2b62?w=400&h=400&fit=crop'],
                'category' => 'Jerseys',
                'average_rating' => 4.8,
                'reviews_count' => 9
            ]
        ]);

        return view('client.shop.show', compact('product', 'reviews', 'relatedProducts'));
    }

    public function category($category)
    {
        return $this->index(request()->merge(['category' => $category]));
    }
} 