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
        $query = Product::with(['user', 'reviews', 'variations'])->where('status', '!=', 'pending');

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

    public function pending()
    {
        $products = Product::with(['user', 'reviews', 'variations'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.products.pending', compact('products'));
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
        $product->user_id = auth()->id(); // Set the current admin user as the creator
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
        $product->status = 'pending'; // Automatically set to pending for approval
        $product->is_featured = $request->has('is_featured');
        $product->variation_label = $request->variation_label;
        $product->views_count = 0;

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
        if ($request->has('variations') && is_array($request->variations)) {
            foreach ($request->variations as $variationData) {
                if (!empty($variationData['name'])) {
                    $variation = new \App\Models\ProductVariation();
                    $variation->product_id = $product->id;
                    $variation->name = $variationData['name'];
                    $variation->sku = $variationData['sku'] ?? null;
                    $variation->price = $variationData['price'] ?? null;
                    $variation->sale_price = $variationData['sale_price'] ?? null;
                    $variation->stock_quantity = $variationData['stock_quantity'] ?? 0;
                    $variation->is_active = $variationData['is_active'] ?? true;
                    
                    // Handle variation images
                    $variationImages = [];
                    if (isset($variationData['images']) && is_array($variationData['images'])) {
                        foreach ($variationData['images'] as $image) {
                            if ($image && $image->isValid()) {
                                $filename = 'variations/' . time() . '_' . Str::slug($variationData['name']) . '_' . Str::random(6) . '.' . $image->getClientOriginalExtension();
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
                    }
                    $variation->images = $variationImages;
                    
                    $variation->save();
                }
            }
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berjaya dicipta dan menunggu kelulusan!');
    }

    public function show($id)
    {
        $product = Product::with(['user', 'reviews'])->findOrFail($id);

        return view('admin.products.show', compact('product'));
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
            'status' => 'required|in:active,inactive,pending,rejected',
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
        
        \Log::info('Product updated successfully', [
            'product_id' => $product->id,
            'variation_label' => $product->variation_label,
            'title' => $product->title
        ]);

        // Handle variations
        // Delete variations marked for deletion
        if ($request->has('delete_variations') && is_array($request->delete_variations)) {
            $deleteVariations = array_filter($request->delete_variations, 'is_numeric');
            
            if (!empty($deleteVariations)) {
                // Get variations to delete their images first
                $variationsToDelete = \App\Models\ProductVariation::where('product_id', $product->id)
                    ->whereIn('id', $deleteVariations)
                    ->get();
                
                // Delete variation images from storage
                foreach ($variationsToDelete as $variation) {
                    if ($variation->images && is_array($variation->images)) {
                        foreach ($variation->images as $image) {
                            Storage::disk('public')->delete($image);
                        }
                    }
                }
                
                // Delete the variations
                $deletedCount = \App\Models\ProductVariation::where('product_id', $product->id)
                    ->whereIn('id', $deleteVariations)
                    ->delete();
                
                \Log::info('Variations deleted', [
                    'product_id' => $product->id,
                    'variation_ids' => $deleteVariations,
                    'deleted_count' => $deletedCount
                ]);
            }
        }

        // Handle new variations
        if ($request->has('variations') && is_array($request->variations)) {
            foreach ($request->variations as $variationData) {
                if (!empty($variationData['name'])) {
                    $variation = new \App\Models\ProductVariation();
                    $variation->product_id = $product->id;
                    $variation->name = $variationData['name'];
                    $variation->sku = $variationData['sku'] ?? null;
                    $variation->price = $variationData['price'] ?? null;
                    $variation->sale_price = $variationData['sale_price'] ?? null;
                    $variation->stock_quantity = $variationData['stock_quantity'] ?? 0;
                    $variation->is_active = $variationData['is_active'] ?? true;
                    
                    // Handle variation images
                    $variationImages = [];
                    if (isset($variationData['images']) && is_array($variationData['images'])) {
                        foreach ($variationData['images'] as $image) {
                            if ($image && $image->isValid()) {
                                $filename = 'variations/' . time() . '_' . Str::slug($variationData['name']) . '_' . Str::random(6) . '.' . $image->getClientOriginalExtension();
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
                    }
                    $variation->images = $variationImages;
                    
                    $variation->save();
                }
            }
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berjaya dikemaskini!');
    }

    public function approve($id)
    {
        $product = Product::findOrFail($id);
        $product->update([
            'status' => 'active'
        ]);

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk diluluskan dengan jayanya!');
    }

    public function reject($id)
    {
        $product = Product::findOrFail($id);
        $product->update(['status' => 'rejected']);

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk ditolak dengan jayanya!');
    }

    public function toggleStatus($id)
    {
        $product = Product::findOrFail($id);
        
        if ($product->status === 'active') {
            $product->update(['status' => 'inactive']);
            $message = 'Produk telah dinyahaktifkan!';
        } else {
            $product->update(['status' => 'active']);
            $message = 'Produk telah diaktifkan!';
        }

        return redirect()->route('admin.products.index')
            ->with('success', $message);
    }

    public function toggleFeatured($id)
    {
        $product = Product::findOrFail($id);
        
        $product->update(['is_featured' => !$product->is_featured]);
        
        $message = $product->is_featured ? 'Produk telah ditampilkan!' : 'Produk telah dinyahpaparkan!';

        return redirect()->route('admin.products.index')
            ->with('success', $message);
    }

    public function updateStatus(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        
        $request->validate([
            'status' => 'required|in:active,inactive'
        ]);
        
        $product->update(['status' => $request->status]);
        
        $statusMessages = [
            'active' => 'Produk telah diaktifkan!',
            'inactive' => 'Produk telah dinyahaktifkan!'
        ];
        
        $message = $statusMessages[$request->status] ?? 'Status produk telah dikemas kini!';

        return redirect()->route('admin.products.index')
            ->with('success', $message);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        
        // Delete associated images
        if ($product->images) {
            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }
        
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk dipadam dengan jayanya!');
    }

    public function deleteVariation($variationId)
    {
        try {
            $variation = \App\Models\ProductVariation::findOrFail($variationId);
            
            // Check if the user has permission to delete this variation
            // (variation belongs to a product that the current user can manage)
            $product = $variation->product;
            
            // For now, we'll allow deletion if the user is an admin
            // You can add more specific permission checks here if needed
            
            // Delete variation images from storage
            if ($variation->images && is_array($variation->images)) {
                foreach ($variation->images as $image) {
                    Storage::disk('public')->delete($image);
                }
            }
            
            $variation->delete();
            
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Varian berjaya dipadam!'
                ]);
            }
            
            return redirect()->back()->with('success', 'Varian berjaya dipadam!');
            
        } catch (\Exception $e) {
            \Log::error('Error deleting variation: ' . $e->getMessage());
            
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ralat memadam varian'
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Ralat memadam varian');
        }
    }

    public function getVariationForEdit($variationId)
    {
        try {
            $variation = \App\Models\ProductVariation::with('product')->findOrFail($variationId);
            
            return response()->json([
                'success' => true,
                'variation' => $variation
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Varian tidak dijumpai'
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Error getting variation for edit: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Ralat memuat data varian'
            ], 500);
        }
    }

    public function updateVariation(Request $request, $variationId)
    {
        try {
            $variation = \App\Models\ProductVariation::with('product')->findOrFail($variationId);
            
            // Debug: Log the incoming request data
            \Log::info('Variation update request data:', [
                'variation_id' => $variationId,
                'request_data' => $request->all(),
                'files' => $request->allFiles(),
                'has_new_images' => $request->hasFile('new_images'),
                'new_images_count' => $request->hasFile('new_images') ? count($request->file('new_images')) : 0
            ]);
            
            // Basic validation rules
            $validationRules = [
                'name' => 'nullable|string|max:255',
                'sku' => 'nullable|string|max:255',
                'price' => 'nullable|numeric|min:0',
                'sale_price' => 'nullable|numeric|min:0',
                'stock_quantity' => 'nullable|integer|min:0',
                'is_active' => 'nullable|boolean',
                'current_images' => 'nullable|array',
                'current_images.*' => 'string',
            ];
            
            // Add file validation only if files are uploaded
            if ($request->hasFile('new_images')) {
                foreach ($request->file('new_images') as $key => $file) {
                    if ($file && $file->isValid()) {
                        $validationRules["new_images.{$key}"] = 'image|mimes:jpeg,png,jpg,gif|max:10240';
                    }
                }
            }
            
            $request->validate($validationRules);

            // Validate sale price if provided
            if ($request->filled('sale_price') && $request->filled('price') && $request->sale_price >= $request->price) {
                return redirect()->back()
                    ->with('error', 'Harga jualan mesti lebih rendah daripada harga asal')
                    ->withInput();
            }
            
            // If sale price is provided but no price, use product price for validation
            if ($request->filled('sale_price') && !$request->filled('price')) {
                $productPrice = $variation->product->price;
                if ($request->sale_price >= $productPrice) {
                    return redirect()->back()
                        ->with('error', 'Harga jualan mesti lebih rendah daripada harga produk utama')
                        ->withInput();
                }
            }

            // Use existing data if no new data is provided
            $variation->name = $request->filled('name') ? $request->name : $variation->name;
            $variation->sku = $request->filled('sku') ? $request->sku : $variation->sku;
            $variation->price = $request->filled('price') ? $request->price : $variation->price;
            $variation->sale_price = $request->filled('sale_price') ? $request->sale_price : $variation->sale_price;
            $variation->stock_quantity = $request->filled('stock_quantity') ? $request->stock_quantity : $variation->stock_quantity;
            $variation->is_active = $request->has('is_active') ? $request->boolean('is_active', false) : $variation->is_active;

            // Handle image management
            $currentImages = $request->input('current_images', []);
            // Ensure current_images is always an array
            if (!is_array($currentImages)) {
                $currentImages = [];
            }
            $newImages = [];
            
            // Process new uploaded images
            if ($request->hasFile('new_images')) {
                foreach ($request->file('new_images') as $image) {
                    if ($image && $image->isValid()) {
                        $filename = 'variations/' . time() . '_' . Str::slug($request->name) . '_' . Str::random(6) . '.' . $image->getClientOriginalExtension();
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
            }
            
            // Combine current and new images
            $allImages = array_merge($currentImages, $newImages);
            // Ensure we only store string values in the images array
            $variation->images = array_filter($allImages, function($image) {
                return is_string($image) && !empty($image);
            });
            
            // Debug: Log image processing
            \Log::info('Image processing:', [
                'current_images' => $currentImages,
                'new_images' => $newImages,
                'all_images' => $allImages,
                'final_images' => $variation->images
            ]);
            
            // Delete removed images from storage
            $originalImages = $variation->getOriginal('images') ?? [];
            // Ensure originalImages is always an array
            if (!is_array($originalImages)) {
                $originalImages = [];
            }
            $removedImages = array_diff($originalImages, $currentImages);
            foreach ($removedImages as $removedImage) {
                if (is_string($removedImage)) {
                    Storage::disk('public')->delete($removedImage);
                }
            }

            $variation->save();
            
            return redirect()->back()->with('success', 'Varian berjaya dikemaskini!');
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()
                ->with('error', 'Varian tidak dijumpai');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed for variation update:', [
                'variation_id' => $variationId,
                'errors' => $e->errors()
            ]);
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            \Log::error('Error updating variation: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Ralat mengemaskini varian: ' . $e->getMessage())
                ->withInput();
        }
    }
} 