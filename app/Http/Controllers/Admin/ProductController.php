<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        // Sample products for admin management
        $products = collect([
            (object) [
                'id' => 1,
                'title' => 'Arsenal Home Jersey 2024/25',
                'category' => 'Jerseys',
                'price' => 75.00,
                'sale_price' => 65.00,
                'stock_quantity' => 15,
                'is_featured' => true,
                'status' => 'active',
                'views_count' => 543,
                'average_rating' => 4.8,
                'reviews_count' => 12,
                'created_at' => now()->subDays(10)
            ],
            (object) [
                'id' => 2,
                'title' => 'Arsenal Scarf - Classic Design',
                'category' => 'Accessories',
                'price' => 25.00,
                'sale_price' => null,
                'stock_quantity' => 8,
                'is_featured' => false,
                'status' => 'active',
                'views_count' => 234,
                'average_rating' => 4.6,
                'reviews_count' => 8,
                'created_at' => now()->subDays(15)
            ],
            (object) [
                'id' => 3,
                'title' => 'Arsenal Stadium Print - Framed',
                'category' => 'Art & Prints',
                'price' => 45.00,
                'sale_price' => 35.00,
                'stock_quantity' => 3,
                'is_featured' => true,
                'status' => 'active',
                'views_count' => 189,
                'average_rating' => 4.9,
                'reviews_count' => 5,
                'created_at' => now()->subDays(5)
            ],
        ]);

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = [
            'Jerseys',
            'Accessories',
            'Art & Prints',
            'Training Wear',
            'Footwear',
            'Collectibles',
            'Home & Garden'
        ];

        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'is_featured' => 'boolean',
            'status' => 'required|in:active,inactive,draft'
        ]);

        // Here you would normally save to database
        // Product::create($request->all());

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully!');
    }

    public function edit($id)
    {
        // Sample product data
        $product = (object) [
            'id' => $id,
            'title' => 'Arsenal Home Jersey 2024/25',
            'description' => 'Official Arsenal home jersey for the 2024/25 season...',
            'category' => 'Jerseys',
            'price' => 75.00,
            'sale_price' => 65.00,
            'stock_quantity' => 15,
            'is_featured' => true,
            'status' => 'active',
            'images' => ['https://images.unsplash.com/photo-1551698618-1dfe5d97d256?w=600&h=600&fit=crop'],
            'tags' => ['arsenal', 'jersey', 'home', '2024/25', 'official']
        ];

        $categories = [
            'Jerseys',
            'Accessories',
            'Art & Prints',
            'Training Wear',
            'Footwear',
            'Collectibles',
            'Home & Garden'
        ];

        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'is_featured' => 'boolean',
            'status' => 'required|in:active,inactive,draft'
        ]);

        // Here you would normally update the database
        // Product::findOrFail($id)->update($request->all());

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully!');
    }

    public function destroy($id)
    {
        // Here you would normally delete from database
        // Product::findOrFail($id)->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully!');
    }
} 