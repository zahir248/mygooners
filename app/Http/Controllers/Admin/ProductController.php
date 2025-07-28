<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        // Show active, inactive, and rejected products
        $query = Product::with(['user', 'reviews', 'variations'])
            ->whereIn('status', ['active', 'inactive', 'rejected']);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Stock filter
        if ($request->filled('stock')) {
            switch ($request->stock) {
                case 'in_stock':
                    $query->where('stock_quantity', '>', 10);
                    break;
                case 'low_stock':
                    $query->whereBetween('stock_quantity', [1, 10]);
                    break;
                case 'out_of_stock':
                    $query->where('stock_quantity', 0);
                    break;
            }
        }

        // Featured filter
        if ($request->has('featured') && $request->featured) {
            $query->where('is_featured', true);
        }

        $products = $query->orderBy('created_at', 'desc')->paginate(10);

        $categories = Product::distinct()->pluck('category')->filter()->sort()->values();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = [
            'Jerseys' => 'Jersi',
            'Training Wear' => 'Pakaian Latihan',
            'Accessories' => 'Aksesori',
            'Footwear' => 'Kasut',
            'Collectibles' => 'Koleksi',
            'Other' => 'Lain-lain'
        ];

        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        \Log::info('Product store method called', ['request' => $request->all()]);
        
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:price',
            'stock_quantity' => 'required|integer|min:0',
            'tags' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'is_featured' => 'boolean',
            'variation_label' => 'nullable|string|max:255',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            'variations' => 'nullable|array',
            'variations.*.name' => 'nullable|string|max:255',
            'variations.*.sku' => 'nullable|string|max:255',
            'variations.*.price' => 'nullable|numeric|min:0',
            'variations.*.sale_price' => 'nullable|numeric|min:0',
            'variations.*.stock_quantity' => 'nullable|integer|min:0',
            'variations.*.is_active' => 'nullable|boolean',
            'variations.*.images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
        ]);

        $product = new Product();
        $product->title = $request->title;
        $product->slug = Str::slug($request->title);
        $product->description = $request->description;
        $product->category = $request->category;
        $product->price = $request->price;
        $product->sale_price = $request->sale_price;
        $product->stock_quantity = $request->stock_quantity;
        $product->tags = $request->tags ? explode(',', $request->tags) : [];
        $product->meta_title = $request->meta_title;
        $product->meta_description = $request->meta_description;
        $product->status = $request->status;
        $product->is_featured = $request->has('is_featured');
        $product->variation_label = $request->variation_label;
        $product->user_id = auth()->id();

        // Handle image uploads
        $images = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $filename = 'products/' . time() . '_' . Str::slug($request->title) . '_' . Str::random(6) . '.' . $image->getClientOriginalExtension();
                $stored = Storage::disk('public')->putFileAs(
                    dirname($filename),
                    $image,
                    basename($filename)
                );
                if ($stored) {
                    $images[] = $filename;
                }
            }
        }
        $product->images = $images;

        $product->save();

        // Handle variations
        if ($request->has('variations')) {
            foreach ($request->variations as $variationData) {
                if (!empty($variationData['name'])) {
                    $variation = $product->variations()->create([
                        'name' => $variationData['name'],
                        'sku' => $variationData['sku'] ?? null,
                        'price' => $variationData['price'] ?? $product->price,
                        'sale_price' => $variationData['sale_price'] ?? null,
                        'stock_quantity' => $variationData['stock_quantity'] ?? 0,
                        'is_active' => $variationData['is_active'] ?? true,
                    ]);

                    // Handle variation images
                    if (isset($variationData['images']) && is_array($variationData['images'])) {
                        $variationImages = [];
                        foreach ($variationData['images'] as $image) {
                            if ($image->isValid()) {
                                $filename = 'variations/' . time() . '_' . Str::slug($variation->name) . '_' . Str::random(6) . '.' . $image->getClientOriginalExtension();
                                $stored = Storage::disk('public')->putFileAs(
                                    dirname($filename),
                                    $image,
                                    basename($filename)
                                );
                                if ($stored) {
                                    $variationImages[] = $filename;
                                }
                            }
                        }
                        $variation->images = $variationImages;
                        $variation->save();
                    }
                }
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Produk berjaya dicipta.');
    }

    public function show($id)
    {
        $product = Product::with(['user', 'reviews'])->findOrFail($id);

        return view('admin.products.show', compact('product'));
    }

    public function details($id)
    {
        $product = Product::with(['user', 'reviews', 'variations'])->findOrFail($id);
        
        $response = [
            'product' => $product,
            'user' => $product->user,
            'created_at' => $product->created_at->format('j M Y, H:i'),
            'images' => $product->images ? array_map(function($image) {
                return $image ? route('product.image', basename($image)) : null;
            }, $product->images) : [],
            'variations' => []
        ];

        // Format variations
        if ($product->variations && $product->variations->count() > 0) {
            foreach ($product->variations as $variation) {
                $variationData = [
                    'id' => $variation->id,
                    'name' => $variation->name,
                    'sku' => $variation->sku,
                    'price' => $variation->price,
                    'sale_price' => $variation->sale_price,
                    'stock_quantity' => $variation->stock_quantity,
                    'is_active' => $variation->is_active,
                    'images' => $variation->images ? array_map(function($image) {
                        return $image ? route('variation.image', basename($image)) : null;
                    }, $variation->images) : [],
                ];

                $response['variations'][] = $variationData;
            }
        }
        
        return response()->json($response);
    }

    public function edit($id)
    {
        $product = Product::with(['variations'])->findOrFail($id);

        $categories = [
            'Jerseys' => 'Jersi',
            'Training Wear' => 'Pakaian Latihan',
            'Accessories' => 'Aksesori',
            'Footwear' => 'Kasut',
            'Collectibles' => 'Koleksi',
            'Other' => 'Lain-lain'
        ];

        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        
        \Log::info('Product update method called', [
            'product_id' => $id,
            'request_data' => $request->all(),
            'variation_label' => $request->input('variation_label'),
            'delete_variations' => $request->input('delete_variations', [])
        ]);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:price',
            'stock_quantity' => 'required|integer|min:0',
            'tags' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'status' => 'required|in:active,inactive,rejected',
            'is_featured' => 'boolean',
            'variation_label' => 'nullable|string|max:255',
            'current_images' => 'nullable|array',
            'current_images.*' => 'string',
            'new_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            'variations' => 'nullable|array',
            'variations.*.name' => 'nullable|string|max:255',
            'variations.*.sku' => 'nullable|string|max:255',
            'variations.*.price' => 'nullable|numeric|min:0',
            'variations.*.sale_price' => 'nullable|numeric|min:0',
            'variations.*.stock_quantity' => 'nullable|integer|min:0',
            'variations.*.is_active' => 'nullable|boolean',
            'variations.*.images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            'delete_variations' => 'nullable|array',
            'delete_variations.*' => 'integer',
        ]);

        $product->title = $request->title;
        $product->slug = Str::slug($request->title);
        $product->description = $request->description;
        $product->category = $request->category;
        $product->price = $request->price;
        $product->sale_price = $request->sale_price;
        $product->stock_quantity = $request->stock_quantity;
        $product->tags = $request->tags ? explode(',', $request->tags) : [];
        
        $product->meta_title = $request->meta_title;
        $product->meta_description = $request->meta_description;
        $product->status = $request->status;
        $product->is_featured = $request->has('is_featured');
        $product->variation_label = $request->variation_label;

        // Handle image management
        $currentImages = $request->input('current_images', []);
        $newImages = [];
        
        // Process new uploaded images
        if ($request->hasFile('new_images')) {
            foreach ($request->file('new_images') as $image) {
                $filename = 'products/' . time() . '_' . Str::slug($request->title) . '_' . Str::random(6) . '.' . $image->getClientOriginalExtension();
                $stored = Storage::disk('public')->putFileAs(
                    dirname($filename),
                    $image,
                    basename($filename)
                );
                if ($stored) {
                    $newImages[] = $filename;
                }
            }
        }
        
        // Combine current and new images (current_images maintains the order from frontend)
        $product->images = array_merge($currentImages, $newImages);
        
        // Delete removed images from storage
        $originalImages = $product->getOriginal('images') ?? [];
        $removedImages = array_diff($originalImages, $currentImages);
        foreach ($removedImages as $removedImage) {
            Storage::disk('public')->delete($removedImage);
        }

        $product->save();

        // Handle variations
        if ($request->has('variations')) {
            foreach ($request->variations as $variationData) {
                if (!empty($variationData['name'])) {
                    if (isset($variationData['id'])) {
                        // Update existing variation
                        $variation = $product->variations()->find($variationData['id']);
                        if ($variation) {
                            $variation->update([
                                'name' => $variationData['name'],
                                'sku' => $variationData['sku'] ?? null,
                                'price' => $variationData['price'] ?? $product->price,
                                'sale_price' => $variationData['sale_price'] ?? null,
                                'stock_quantity' => $variationData['stock_quantity'] ?? 0,
                                'is_active' => $variationData['is_active'] ?? true,
                            ]);
                        }
                    } else {
                        // Create new variation
                        $variation = $product->variations()->create([
                            'name' => $variationData['name'],
                            'sku' => $variationData['sku'] ?? null,
                            'price' => $variationData['price'] ?? $product->price,
                            'sale_price' => $variationData['sale_price'] ?? null,
                            'stock_quantity' => $variationData['stock_quantity'] ?? 0,
                            'is_active' => $variationData['is_active'] ?? true,
                        ]);
                    }
                }
            }
        }

        // Delete variations marked for deletion
        if ($request->has('delete_variations')) {
            foreach ($request->delete_variations as $variationId) {
                $variation = $product->variations()->find($variationId);
                if ($variation) {
                    // Delete variation images from storage
                    if ($variation->images) {
                        foreach ($variation->images as $image) {
                            Storage::disk('public')->delete($image);
                        }
                    }
                    $variation->delete();
                }
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Produk berjaya dikemas kini.');
    }

    public function toggleStatus($id)
    {
        $product = Product::findOrFail($id);
        $product->status = $product->status === 'active' ? 'inactive' : 'active';
        $product->save();

        return response()->json([
            'success' => true,
            'message' => 'Status produk berjaya dikemas kini.',
            'new_status' => $product->status
        ]);
    }

    public function toggleFeatured($id)
    {
        $product = Product::findOrFail($id);
        $product->is_featured = !$product->is_featured;
        $product->save();

        return response()->json([
            'success' => true,
            'message' => 'Status featured produk berjaya dikemas kini.',
            'is_featured' => $product->is_featured
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $product->status = $request->status;
        $product->save();

        $statusMessages = [
            'active' => 'Produk telah diaktifkan!',
            'inactive' => 'Produk telah dinyahaktifkan!',
            'rejected' => 'Produk telah ditolak!'
        ];

        $message = $statusMessages[$request->status] ?? 'Status produk telah dikemas kini!';

        return redirect()->back()->with('success', $message);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        
        // Delete product images from storage
        if ($product->images) {
            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        // Delete variation images from storage
        foreach ($product->variations as $variation) {
            if ($variation->images) {
                foreach ($variation->images as $image) {
                    Storage::disk('public')->delete($image);
                }
            }
        }

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Produk berjaya dipadam.'
        ]);
    }

    public function deleteVariation($variationId)
    {
        $variation = \App\Models\ProductVariation::findOrFail($variationId);
        
        // Delete variation images from storage
        if ($variation->images) {
            foreach ($variation->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        $variation->delete();

        return response()->json([
            'success' => true,
            'message' => 'Variasi produk berjaya dipadam.'
        ]);
    }

    public function getVariationForEdit($variationId)
    {
        $variation = \App\Models\ProductVariation::findOrFail($variationId);
        
        return response()->json([
            'variation' => $variation,
            'images' => $variation->images ? array_map(function($image) {
                return $image ? route('variation.image', basename($image)) : null;
            }, $variation->images) : []
        ]);
    }

    public function updateVariation(Request $request, $variationId)
    {
        $variation = \App\Models\ProductVariation::findOrFail($variationId);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
        ]);

        $variation->name = $request->name;
        $variation->sku = $request->sku;
        $variation->price = $request->price;
        $variation->sale_price = $request->sale_price;
        $variation->stock_quantity = $request->stock_quantity;
        $variation->is_active = $request->has('is_active');

        // Handle image uploads
        if ($request->hasFile('images')) {
            $images = [];
            foreach ($request->file('images') as $image) {
                $filename = 'variations/' . time() . '_' . Str::slug($variation->name) . '_' . Str::random(6) . '.' . $image->getClientOriginalExtension();
                $stored = Storage::disk('public')->putFileAs(
                    dirname($filename),
                    $image,
                    basename($filename)
                );
                if ($stored) {
                    $images[] = $filename;
                }
            }
            $variation->images = $images;
        }

        $variation->save();

        return response()->json([
            'success' => true,
            'message' => 'Variasi produk berjaya dikemas kini.'
        ]);
    }
} 