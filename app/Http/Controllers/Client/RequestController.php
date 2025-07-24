<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class RequestController extends Controller
{
    public function showServiceRequestForm()
    {
        return view('client.requests.service-request');
    }

    public function showProductRequestForm()
    {
        return view('client.requests.product-request');
    }

    public function storeServiceRequest(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'pricing' => 'required|string|max:255',
            'contact_info' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'tags' => 'nullable|string',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048'
        ]);

        // Generate slug from title
        $slug = Str::slug($validated['title']);
        $originalSlug = $slug;
        $counter = 1;

        // Ensure unique slug
        while (Service::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        // Handle tags
        $tags = null;
        if (!empty($validated['tags'])) {
            $tags = array_map('trim', explode(',', $validated['tags']));
        }

        // Handle image uploads
        $images = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('services', 'public');
                $images[] = $path;
            }
        }

        // Create service with pending status
        $service = Service::create([
            'user_id' => auth()->id(),
            'title' => $validated['title'],
            'slug' => $slug,
            'description' => $validated['description'],
            'location' => $validated['location'],
            'pricing' => $validated['pricing'],
            'contact_info' => $validated['contact_info'],
            'category' => $validated['category'],
            'tags' => $tags,
            'images' => $images,
            'status' => 'pending'
        ]);

        return redirect()->route('dashboard')->with('success', 'Permohonan perkhidmatan anda telah dihantar! Admin akan menyemak permohonan anda dalam masa 1-3 hari bekerja.');
    }

    public function storeProductRequest(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'category' => 'required|string|max:255',
            'tags' => 'nullable|string',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:10240',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'is_featured' => 'boolean',
            'variation_label' => 'nullable|string|max:255',
            'variations' => 'nullable|array',
            'variations.*.name' => 'nullable|string|max:255',
            'variations.*.sku' => 'nullable|string|max:255',
            'variations.*.price' => 'nullable|numeric|min:0',
            'variations.*.sale_price' => 'nullable|numeric|min:0',
            'variations.*.stock_quantity' => 'nullable|integer|min:0',
            'variations.*.is_active' => 'nullable|boolean',
            'variations.*.images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
        ]);

        // Validate sale price is less than regular price
        if (!empty($validated['sale_price']) && $validated['sale_price'] >= $validated['price']) {
            return back()->withErrors(['sale_price' => 'Harga jualan mestilah kurang daripada harga biasa.'])->withInput();
        }

        // Generate slug from title
        $slug = Str::slug($validated['title']);
        $originalSlug = $slug;
        $counter = 1;

        // Ensure unique slug
        while (Product::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        // Handle tags
        $tags = null;
        if (!empty($validated['tags'])) {
            $tags = array_map('trim', explode(',', $validated['tags']));
        }

        // Handle image uploads
        $images = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');
                $images[] = $path;
            }
        }

        // Create product with pending status
        $product = Product::create([
            'user_id' => auth()->id(),
            'title' => $validated['title'],
            'slug' => $slug,
            'description' => $validated['description'],
            'price' => $validated['price'],
            'sale_price' => $validated['sale_price'],
            'stock_quantity' => $validated['stock_quantity'],
            'category' => $validated['category'],
            'tags' => $tags,
            'images' => $images,
            'meta_title' => $request->input('title'), // Auto-set meta title to product title
            'meta_description' => $request->input('description'), // Auto-set meta description to product description
            'is_featured' => true, // Always set as featured
            'variation_label' => $validated['variation_label'] ?? null,
            'status' => 'pending'
        ]);

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

        return redirect()->route('dashboard')->with('success', 'Permohonan produk anda telah dihantar! Admin akan menyemak permohonan anda dalam masa 1-3 hari bekerja.');
    }

    public function previewPendingService($id)
    {
        $service = Service::where('id', $id)
            ->where('user_id', auth()->id())
            ->where('status', 'pending')
            ->firstOrFail();

        return view('client.requests.pending-service-preview', compact('service'));
    }

    public function previewPendingProduct($id)
    {
        $product = Product::where('id', $id)
            ->where('user_id', auth()->id())
            ->where('status', 'pending')
            ->firstOrFail();

        return view('client.requests.pending-product-preview', compact('product'));
    }

    public function previewRejectedService($id)
    {
        $service = Service::where('id', $id)
            ->where('user_id', auth()->id())
            ->where('status', 'rejected')
            ->firstOrFail();

        return view('client.requests.rejected-service-preview', compact('service'));
    }

    public function previewRejectedProduct($id)
    {
        $product = Product::where('id', $id)
            ->where('user_id', auth()->id())
            ->where('status', 'rejected')
            ->firstOrFail();

        return view('client.requests.rejected-product-preview', compact('product'));
    }

    public function previewOwnService($id)
    {
        $service = Service::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();
        return view('client.requests.service-preview', compact('service'));
    }

    public function editRejectedService($id)
    {
        $service = Service::where('id', $id)
            ->where('user_id', auth()->id())
            ->where('status', 'rejected')
            ->firstOrFail();

        return view('client.requests.edit-rejected-service', compact('service'));
    }

    public function updateRejectedService(Request $request, $id)
    {
        $service = Service::where('id', $id)
            ->where('user_id', auth()->id())
            ->where('status', 'rejected')
            ->firstOrFail();

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'pricing' => 'required|string|max:255',
            'contact_info' => 'required|string',
            'category' => 'required|string|max:255',
            'tags' => 'nullable|string',
            'current_images' => 'nullable|array',
            'current_images.*' => 'string',
            'new_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
        ]);

        // Check if any changes were made
        $hasChanges = false;
        $changes = [];

        // Check basic fields
        if ($service->title !== $request->title) {
            $hasChanges = true;
            $changes[] = 'Tajuk perkhidmatan';
        }
        if ($service->description !== $request->description) {
            $hasChanges = true;
            $changes[] = 'Penerangan';
        }
        if ($service->location !== $request->location) {
            $hasChanges = true;
            $changes[] = 'Lokasi';
        }
        if ($service->pricing !== $request->pricing) {
            $hasChanges = true;
            $changes[] = 'Harga';
        }
        if ($service->contact_info !== $request->contact_info) {
            $hasChanges = true;
            $changes[] = 'Maklumat hubungan';
        }
        if ($service->category !== $request->category) {
            $hasChanges = true;
            $changes[] = 'Kategori';
        }

        // Check tags
        $newTags = $request->tags ? explode(',', $request->tags) : [];
        $currentTags = is_array($service->tags) ? $service->tags : [];
        if (count(array_diff($newTags, $currentTags)) > 0 || count(array_diff($currentTags, $newTags)) > 0) {
            $hasChanges = true;
            $changes[] = 'Tag';
        }

        // Check images
        $currentImages = $request->input('current_images', []);
        $originalImages = is_array($service->images) ? $service->images : [];
        if (count(array_diff($currentImages, $originalImages)) > 0 || count(array_diff($originalImages, $currentImages)) > 0) {
            $hasChanges = true;
            $changes[] = 'Gambar';
        }

        // Check for new images
        if ($request->hasFile('new_images')) {
            $hasChanges = true;
            $changes[] = 'Gambar baharu';
        }

        // If no changes, show reminder
        if (!$hasChanges) {
            return redirect()->back()
                ->with('warning', 'Tiada perubahan dibuat. Sila kemaskini maklumat berdasarkan sebab penolakan sebelum menghantar semula.')
                ->withInput();
        }

        // Update basic fields
        $service->title = $request->title;
        $service->slug = Str::slug($request->title);
        $service->description = $request->description;
        $service->location = $request->location;
        $service->pricing = $request->pricing;
        $service->contact_info = $request->contact_info;
        $service->category = $request->category;
        $service->tags = $newTags;
        $service->status = 'pending';
        $service->rejection_reason = null; // Clear rejection reason

        // Handle images
        $newImages = [];
        
        if ($request->hasFile('new_images')) {
            foreach ($request->file('new_images') as $image) {
                $filename = 'services/' . time() . '_' . Str::slug($request->title) . '_' . Str::random(6) . '.' . $image->getClientOriginalExtension();
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
        
        $service->images = array_merge($currentImages, $newImages);
        $service->save();

        return redirect()->route('dashboard')
            ->with('success', 'Permohonan berjaya dihantar semula!');
    }

    public function editRejectedProduct($id)
    {
        $product = Product::where('id', $id)
            ->where('user_id', auth()->id())
            ->where('status', 'rejected')
            ->firstOrFail();

        return view('client.requests.edit-rejected-product', compact('product'));
    }

    public function updateRejectedProduct(Request $request, $id)
    {
        $product = Product::where('id', $id)
            ->where('user_id', auth()->id())
            ->where('status', 'rejected')
            ->firstOrFail();

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:price',
            'stock_quantity' => 'required|integer|min:0',
            'category' => 'required|string|max:255',
            'tags' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'variation_label' => 'nullable|string|max:255',
            'current_images' => 'nullable|array',
            'current_images.*' => 'string',
            'new_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            'variations' => 'nullable|array',
            'variations.*.id' => 'nullable|exists:product_variations,id',
            'variations.*.name' => 'nullable|string|max:255',
            'variations.*.sku' => 'nullable|string|max:255',
            'variations.*.price' => 'nullable|numeric|min:0',
            'variations.*.sale_price' => 'nullable|numeric|min:0',
            'variations.*.stock_quantity' => 'nullable|integer|min:0',
            'variations.*.is_active' => 'nullable|boolean',
            'variations.*.current_images' => 'nullable|array',
            'variations.*.current_images.*' => 'string',
            'variations.*.new_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
        ]);

        // Check if any changes were made
        $hasChanges = false;
        $changes = [];

        // Check basic fields
        if ($product->title !== $request->title) {
            $hasChanges = true;
            $changes[] = 'Tajuk produk';
        }
        if ($product->description !== $request->description) {
            $hasChanges = true;
            $changes[] = 'Penerangan';
        }
        if ($product->price != $request->price) {
            $hasChanges = true;
            $changes[] = 'Harga asal';
        }
        if ($product->sale_price != $request->sale_price) {
            $hasChanges = true;
            $changes[] = 'Harga jualan';
        }
        if ($product->stock_quantity != $request->stock_quantity) {
            $hasChanges = true;
            $changes[] = 'Kuantiti stok';
        }
        if ($product->category !== $request->category) {
            $hasChanges = true;
            $changes[] = 'Kategori';
        }


        // Check tags
        $newTags = $request->tags ? explode(',', $request->tags) : [];
        $currentTags = is_array($product->tags) ? $product->tags : [];
        if (count(array_diff($newTags, $currentTags)) > 0 || count(array_diff($currentTags, $newTags)) > 0) {
            $hasChanges = true;
            $changes[] = 'Tag';
        }

        // Check images
        $currentImages = $request->input('current_images', []);
        $originalImages = is_array($product->images) ? $product->images : [];
        if (count(array_diff($currentImages, $originalImages)) > 0 || count(array_diff($originalImages, $currentImages)) > 0) {
            $hasChanges = true;
            $changes[] = 'Gambar';
        }

        // Check for new images
        if ($request->hasFile('new_images')) {
            $hasChanges = true;
            $changes[] = 'Gambar baharu';
        }

        // Check variation label
        if ($product->variation_label !== $request->variation_label) {
            $hasChanges = true;
            $changes[] = 'Label varian';
        }



        // Check variations
        if ($request->has('variations') && is_array($request->variations)) {
            $existingVariations = $product->variations->toArray();
            $newVariations = $request->variations;
            
            // Simple check for variation changes
            if (count($existingVariations) !== count(array_filter($newVariations, function($v) { return !empty($v['name']); }))) {
                $hasChanges = true;
                $changes[] = 'Varian produk';
            } else {
                // Check individual variation changes
                foreach ($newVariations as $newVariation) {
                    if (!empty($newVariation['name'])) {
                        $found = false;
                        foreach ($existingVariations as $existingVariation) {
                            if (isset($newVariation['id']) && $newVariation['id'] == $existingVariation['id']) {
                                if ($newVariation['name'] !== $existingVariation['name'] ||
                                    $newVariation['price'] != $existingVariation['price'] ||
                                    $newVariation['stock_quantity'] != $existingVariation['stock_quantity']) {
                                    $hasChanges = true;
                                    $changes[] = 'Varian produk';
                                    break 2;
                                }
                                $found = true;
                                break;
                            }
                        }
                        if (!$found) {
                            $hasChanges = true;
                            $changes[] = 'Varian produk';
                            break;
                        }
                    }
                }
            }
        } else {
            // If no variations provided but product had variations
            if ($product->variations->count() > 0) {
                $hasChanges = true;
                $changes[] = 'Varian produk';
            }
        }

        // If no changes, show reminder
        if (!$hasChanges) {
            return redirect()->back()
                ->with('warning', 'Tiada perubahan dibuat. Sila kemaskini maklumat berdasarkan sebab penolakan sebelum menghantar semula.')
                ->withInput();
        }

        // Update basic fields
        $product->title = $request->title;
        $product->slug = Str::slug($request->title);
        $product->description = $request->description;
        $product->price = $request->price;
        $product->sale_price = $request->sale_price;
        $product->stock_quantity = $request->stock_quantity;
        $product->category = $request->category;
        $product->tags = $newTags;
        $product->meta_title = $request->input('title'); // Auto-set meta title to product title
        $product->meta_description = $request->input('description'); // Auto-set meta description to product description
        $product->is_featured = true; // Always set as featured
        $product->status = 'pending';
        $product->rejection_reason = null; // Clear rejection reason

        // Handle images
        $newImages = [];
        
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
        
        $product->images = array_merge($currentImages, $newImages);
        $product->is_featured = true; // Always set as featured
        $product->variation_label = $request->variation_label;
        $product->save();

        // Handle variations
        if ($request->has('variations') && is_array($request->variations)) {
            // Get existing variation IDs to track which ones to keep
            $existingVariationIds = $product->variations->pluck('id')->toArray();
            $updatedVariationIds = [];
            
            foreach ($request->variations as $variationData) {
                if (!empty($variationData['name'])) {
                    if (isset($variationData['id']) && $variationData['id']) {
                        // Update existing variation
                        $variation = \App\Models\ProductVariation::find($variationData['id']);
                        if ($variation && $variation->product_id == $product->id) {
                            $variation->name = $variationData['name'];
                            $variation->sku = $variationData['sku'] ?? null;
                            $variation->price = $variationData['price'] ?? null;
                            $variation->sale_price = $variationData['sale_price'] ?? null;
                            $variation->stock_quantity = $variationData['stock_quantity'] ?? 0;
                            $variation->is_active = $variationData['is_active'] ?? true;
                            
                            // Handle variation images
                            $currentVariationImages = $variationData['current_images'] ?? [];
                            $newVariationImages = [];
                            
                            if (isset($variationData['new_images']) && is_array($variationData['new_images'])) {
                                foreach ($variationData['new_images'] as $image) {
                                    if ($image && $image->isValid()) {
                                        $filename = 'variations/' . time() . '_' . Str::slug($variationData['name']) . '_' . Str::random(6) . '.' . $image->getClientOriginalExtension();
                                        $stored = Storage::disk('public')->putFileAs(
                                            dirname($filename),
                                            $image,
                                            basename($filename)
                                        );
                                        if ($stored) {
                                            $newVariationImages[] = $filename;
                                        }
                                    }
                                }
                            }
                            
                            $variation->images = array_merge($currentVariationImages, $newVariationImages);
                            $variation->save();
                            $updatedVariationIds[] = $variation->id;
                        }
                    } else {
                        // Create new variation
                        $variation = new \App\Models\ProductVariation();
                        $variation->product_id = $product->id;
                        $variation->name = $variationData['name'];
                        $variation->sku = $variationData['sku'] ?? null;
                        $variation->price = $variationData['price'] ?? null;
                        $variation->sale_price = $variationData['sale_price'] ?? null;
                        $variation->stock_quantity = $variationData['stock_quantity'] ?? 0;
                        $variation->is_active = $variationData['is_active'] ?? true;
                        
                        // Handle new variation images
                        $variationImages = [];
                        if (isset($variationData['new_images']) && is_array($variationData['new_images'])) {
                            foreach ($variationData['new_images'] as $image) {
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
                        $updatedVariationIds[] = $variation->id;
                    }
                }
            }
            
            // Delete variations that are no longer present
            $variationsToDelete = array_diff($existingVariationIds, $updatedVariationIds);
            if (!empty($variationsToDelete)) {
                \App\Models\ProductVariation::whereIn('id', $variationsToDelete)->delete();
            }
        } else {
            // If no variations provided, delete all existing variations
            $product->variations()->delete();
        }

        return redirect()->route('dashboard')
            ->with('success', 'Permohonan berjaya dihantar semula!');
    }

    public function updateServiceStatus(Request $request, $id)
    {
        $service = Service::where('id', $id)
            ->where('user_id', auth()->id())
            ->whereIn('status', ['active', 'inactive'])
            ->firstOrFail();

        $request->validate([
            'status' => 'required|in:active,inactive'
        ]);

        $service->status = $request->status;
        $service->save();

        $statusText = $request->status === 'active' ? 'aktif' : 'tidak aktif';
        return redirect()->route('dashboard')
            ->with('success', "Status perkhidmatan berjaya dikemaskini kepada {$statusText}!");
    }

    public function updateProductStatus(Request $request, $id)
    {
        $product = Product::where('id', $id)
            ->where('user_id', auth()->id())
            ->whereIn('status', ['active', 'inactive'])
            ->firstOrFail();

        $request->validate([
            'status' => 'required|in:active,inactive'
        ]);

        $product->status = $request->status;
        $product->save();

        $statusText = $request->status === 'active' ? 'aktif' : 'tidak aktif';
        return redirect()->route('dashboard')
            ->with('success', "Status produk berjaya dikemaskini kepada {$statusText}!");
    }

    public function showServiceEditRequestForm($id)
    {
        $service = Service::where('id', $id)
            ->where('user_id', auth()->id())
            ->whereIn('status', ['active', 'inactive'])
            ->firstOrFail();

        return view('client.requests.service-edit-request', compact('service'));
    }

    public function storeServiceEditRequest(Request $request, $id)
    {
        $service = Service::where('id', $id)
            ->where('user_id', auth()->id())
            ->whereIn('status', ['active', 'inactive'])
            ->firstOrFail();

        // Check if there's already a pending update request for this service
        $existingPendingRequest = Service::where('original_service_id', $service->id)
            ->where('is_update_request', true)
            ->where('status', 'pending')
            ->first();

        if ($existingPendingRequest) {
            return redirect()->back()
                ->with('error', 'Permohonan kemaskini untuk perkhidmatan ini masih menunggu kelulusan admin. Sila tunggu sehingga permohonan sedia ada diluluskan atau ditolak.')
                ->withInput();
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'pricing' => 'required|string|max:255',
            'contact_info' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'tags' => 'nullable|string',
            'current_images' => 'nullable|array',
            'current_images.*' => 'string',
            'new_images.*' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048'
        ]);

        // Check if any changes were made
        $hasChanges = false;
        $changes = [];

        // Check basic fields
        if ($service->title !== $validated['title']) {
            $hasChanges = true;
            $changes[] = 'Tajuk perkhidmatan';
        }
        if ($service->description !== $validated['description']) {
            $hasChanges = true;
            $changes[] = 'Penerangan';
        }
        if ($service->location !== $validated['location']) {
            $hasChanges = true;
            $changes[] = 'Lokasi';
        }
        if ($service->pricing !== $validated['pricing']) {
            $hasChanges = true;
            $changes[] = 'Harga';
        }
        if ($service->contact_info !== $validated['contact_info']) {
            $hasChanges = true;
            $changes[] = 'Maklumat hubungan';
        }
        if ($service->category !== $validated['category']) {
            $hasChanges = true;
            $changes[] = 'Kategori';
        }

        // Check tags
        $newTags = $validated['tags'] ? array_map('trim', explode(',', $validated['tags'])) : [];
        $currentTags = is_array($service->tags) ? $service->tags : [];
        if (count(array_diff($newTags, $currentTags)) > 0 || count(array_diff($currentTags, $newTags)) > 0) {
            $hasChanges = true;
            $changes[] = 'Tag';
        }

        // Check images
        $currentImages = $request->input('current_images', []);
        $originalImages = is_array($service->images) ? $service->images : [];
        if (count(array_diff($currentImages, $originalImages)) > 0 || count(array_diff($originalImages, $currentImages)) > 0) {
            $hasChanges = true;
            $changes[] = 'Gambar';
        }

        // Check for new images
        if ($request->hasFile('new_images')) {
            $hasChanges = true;
            $changes[] = 'Gambar baharu';
        }

        // If no changes, show warning
        if (!$hasChanges) {
            return redirect()->back()
                ->with('warning', 'Tiada perubahan dibuat. Sila kemaskini maklumat sebelum menghantar permohonan.')
                ->withInput();
        }

        // Delete any existing rejected update requests for this service
        Service::where('original_service_id', $service->id)
            ->where('is_update_request', true)
            ->where('status', 'rejected')
            ->delete();

        // Create a new service record for the update request
        $updateService = new Service();
        $updateService->user_id = auth()->id();
        $updateService->title = $validated['title'];
        
        // Generate unique slug for update request
        $baseSlug = Str::slug($validated['title']);
        $slug = $baseSlug . '-update-' . $service->id . '-' . time();
        
        $updateService->slug = $slug;
        $updateService->description = $validated['description'];
        $updateService->location = $validated['location'];
        $updateService->pricing = $validated['pricing'];
        $updateService->contact_info = $validated['contact_info'];
        $updateService->category = $validated['category'];
        $updateService->tags = $newTags;
        $updateService->status = 'pending';
        $updateService->is_update_request = true; // Flag to identify this is an update request
        $updateService->original_service_id = $service->id; // Reference to original service

        // Handle images
        $newImages = [];
        
        if ($request->hasFile('new_images')) {
            foreach ($request->file('new_images') as $image) {
                $path = $image->store('services', 'public');
                $newImages[] = $path;
            }
        }
        
        $updateService->images = array_merge($currentImages, $newImages);
        $updateService->save();

        return redirect()->route('dashboard')
            ->with('success', 'Permohonan kemaskini perkhidmatan telah dihantar! Admin akan menyemak permohonan anda dalam masa 1-3 hari bekerja.');
    }

    public function showProductEditRequestForm($id)
    {
        $product = Product::where('id', $id)
            ->where('user_id', auth()->id())
            ->whereIn('status', ['active', 'inactive'])
            ->firstOrFail();

        return view('client.requests.product-edit-request', compact('product'));
    }

    public function storeProductEditRequest(Request $request, $id)
    {
        $product = Product::where('id', $id)
            ->where('user_id', auth()->id())
            ->whereIn('status', ['active', 'inactive'])
            ->firstOrFail();

        // Check if there's already a pending update request for this product
        $existingPendingRequest = Product::where('original_product_id', $product->id)
            ->where('is_update_request', true)
            ->where('status', 'pending')
            ->first();

        if ($existingPendingRequest) {
            return redirect()->back()
                ->with('error', 'Permohonan kemaskini untuk produk ini masih menunggu kelulusan admin. Sila tunggu sehingga permohonan sedia ada diluluskan atau ditolak.')
                ->withInput();
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:price',
            'stock_quantity' => 'required|integer|min:0',
            'tags' => 'nullable|string',
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
            'variations.*.current_images.*' => 'nullable|string',
            'variations.*.new_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
        ]);

        // Check if any changes were made
        $hasChanges = false;
        $changes = [];

        // Check basic fields
        if ($product->title !== $validated['title']) {
            $hasChanges = true;
            $changes[] = 'Tajuk produk';
        }
        if ($product->description !== $validated['description']) {
            $hasChanges = true;
            $changes[] = 'Penerangan';
        }
        if ($product->category !== $validated['category']) {
            $hasChanges = true;
            $changes[] = 'Kategori';
        }
        if ($product->price != $validated['price']) {
            $hasChanges = true;
            $changes[] = 'Harga asal';
        }
        if ($product->sale_price != $validated['sale_price']) {
            $hasChanges = true;
            $changes[] = 'Harga jualan';
        }
        if ($product->stock_quantity != $validated['stock_quantity']) {
            $hasChanges = true;
            $changes[] = 'Kuantiti stok';
        }
        if ($product->variation_label !== $validated['variation_label']) {
            $hasChanges = true;
            $changes[] = 'Label variasi';
        }

        // Check tags
        $newTags = $validated['tags'] ? array_map('trim', explode(',', $validated['tags'])) : [];
        $currentTags = is_array($product->tags) ? $product->tags : [];
        if (count(array_diff($newTags, $currentTags)) > 0 || count(array_diff($currentTags, $newTags)) > 0) {
            $hasChanges = true;
            $changes[] = 'Tag';
        }

        // Check images
        $currentImages = $request->input('current_images', []);
        $originalImages = is_array($product->images) ? $product->images : [];
        if (count(array_diff($currentImages, $originalImages)) > 0 || count(array_diff($originalImages, $currentImages)) > 0) {
            $hasChanges = true;
            $changes[] = 'Gambar';
        }

        // Check for new images
        if ($request->hasFile('new_images')) {
            $hasChanges = true;
            $changes[] = 'Gambar baharu';
        }

        // Check variations
        $requestVariations = $request->input('variations', []);
        $existingVariations = $product->variations;
        
        if (count($requestVariations) !== $existingVariations->count()) {
            $hasChanges = true;
            $changes[] = 'Variasi produk';
        } else {
            foreach ($requestVariations as $index => $requestVariation) {
                if ($index < $existingVariations->count()) {
                    $existingVariation = $existingVariations[$index];
                    if ($existingVariation->name !== $requestVariation['name'] ||
                        $existingVariation->sku !== $requestVariation['sku'] ||
                        $existingVariation->price != $requestVariation['price'] ||
                        $existingVariation->sale_price != $requestVariation['sale_price'] ||
                        $existingVariation->stock_quantity != $requestVariation['stock_quantity'] ||
                        $existingVariation->is_active != ($requestVariation['is_active'] ?? true)) {
                        $hasChanges = true;
                        $changes[] = 'Variasi produk';
                        break;
                    }
                }
            }
        }

        // If no changes, show warning
        if (!$hasChanges) {
            return redirect()->back()
                ->with('warning', 'Tiada perubahan dibuat. Sila kemaskini maklumat sebelum menghantar permohonan.')
                ->withInput();
        }

        // Delete any existing rejected update requests for this product
        Product::where('original_product_id', $product->id)
            ->where('is_update_request', true)
            ->where('status', 'rejected')
            ->delete();

        // Create a new product record for the update request
        $updateProduct = new Product();
        $updateProduct->user_id = auth()->id();
        $updateProduct->title = $validated['title'];
        
        // Generate unique slug for update request
        $baseSlug = Str::slug($validated['title']);
        $slug = $baseSlug . '-update-' . $product->id . '-' . time();
        
        $updateProduct->slug = $slug;
        $updateProduct->description = $validated['description'];
        $updateProduct->category = $validated['category'];
        $updateProduct->price = $validated['price'];
        $updateProduct->sale_price = $validated['sale_price'];
        $updateProduct->stock_quantity = $validated['stock_quantity'];
        $updateProduct->tags = $newTags;
        $updateProduct->meta_title = $validated['title'];
        $updateProduct->meta_description = $validated['description'];
        $updateProduct->is_featured = true;
        $updateProduct->variation_label = $validated['variation_label'];
        $updateProduct->status = 'pending';
        $updateProduct->is_update_request = true; // Flag to identify this is an update request
        $updateProduct->original_product_id = $product->id; // Reference to original product

        // Handle images
        $newImages = [];
        
        if ($request->hasFile('new_images')) {
            foreach ($request->file('new_images') as $image) {
                $filename = 'products/' . time() . '_' . Str::slug($validated['title']) . '_' . Str::random(6) . '.' . $image->getClientOriginalExtension();
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
        
        $updateProduct->images = array_merge($currentImages, $newImages);
        $updateProduct->save();

        // Handle variations
        if ($request->has('variations') && is_array($request->variations)) {
            foreach ($request->variations as $variationData) {
                if (!empty($variationData['name'])) {
                    $variation = new \App\Models\ProductVariation();
                    $variation->product_id = $updateProduct->id;
                    $variation->name = $variationData['name'];
                    $variation->sku = $variationData['sku'] ?? null;
                    $variation->price = $variationData['price'] ?? null;
                    $variation->sale_price = $variationData['sale_price'] ?? null;
                    $variation->stock_quantity = $variationData['stock_quantity'] ?? 0;
                    $variation->is_active = $variationData['is_active'] ?? true;
                    
                    // Handle variation images
                    $variationImages = [];
                    
                    // Add current images
                    if (isset($variationData['current_images']) && is_array($variationData['current_images'])) {
                        $variationImages = array_merge($variationImages, $variationData['current_images']);
                    }
                    
                    // Add new images
                    if (isset($variationData['new_images']) && is_array($variationData['new_images'])) {
                        foreach ($variationData['new_images'] as $image) {
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

        return redirect()->route('dashboard')
            ->with('success', 'Permohonan kemaskini produk telah dihantar! Admin akan menyemak permohonan anda dalam masa 1-3 hari bekerja.');
    }

    public function previewProductUpdateRequest($id)
    {
        $updateRequest = Product::where('id', $id)
            ->where('user_id', auth()->id())
            ->where('is_update_request', true)
            ->whereIn('status', ['pending', 'rejected'])
            ->firstOrFail();

        $originalProduct = Product::find($updateRequest->original_product_id);
        
        if (!$originalProduct) {
            return redirect()->route('dashboard')->with('error', 'Produk asal tidak dijumpai.');
        }

        // Get changes for comparison
        $changes = $this->getProductChanges($originalProduct, $updateRequest);

        return view('client.requests.product-update-preview', compact('updateRequest', 'originalProduct', 'changes'));
    }

    private function getProductChanges($originalProduct, $updateProduct)
    {
        $changes = [];
        
        // Compare basic fields
        if ($originalProduct->title !== $updateProduct->title) {
            $changes['title'] = [
                'old' => $originalProduct->title,
                'new' => $updateProduct->title
            ];
        }
        
        if ($originalProduct->description !== $updateProduct->description) {
            $changes['description'] = [
                'old' => $originalProduct->description,
                'new' => $updateProduct->description
            ];
        }
        
        if ($originalProduct->category !== $updateProduct->category) {
            $changes['category'] = [
                'old' => $originalProduct->category,
                'new' => $updateProduct->category
            ];
        }
        
        if ($originalProduct->price != $updateProduct->price) {
            $changes['price'] = [
                'old' => $originalProduct->price,
                'new' => $updateProduct->price
            ];
        }
        
        if ($originalProduct->sale_price != $updateProduct->sale_price) {
            $changes['sale_price'] = [
                'old' => $originalProduct->sale_price,
                'new' => $updateProduct->sale_price
            ];
        }
        
        if ($originalProduct->stock_quantity != $updateProduct->stock_quantity) {
            $changes['stock_quantity'] = [
                'old' => $originalProduct->stock_quantity,
                'new' => $updateProduct->stock_quantity
            ];
        }
        
        if ($originalProduct->variation_label !== $updateProduct->variation_label) {
            $changes['variation_label'] = [
                'old' => $originalProduct->variation_label,
                'new' => $updateProduct->variation_label
            ];
        }
        
        // Compare tags
        $originalTags = is_array($originalProduct->tags) ? $originalProduct->tags : [];
        $newTags = is_array($updateProduct->tags) ? $updateProduct->tags : [];
        if (count(array_diff($originalTags, $newTags)) > 0 || count(array_diff($newTags, $originalTags)) > 0) {
            $changes['tags'] = [
                'old' => implode(', ', $originalTags),
                'new' => implode(', ', $newTags)
            ];
        }
        
        // Compare images
        $originalImages = is_array($originalProduct->images) ? $originalProduct->images : [];
        $newImages = is_array($updateProduct->images) ? $updateProduct->images : [];
        if (count(array_diff($originalImages, $newImages)) > 0 || count(array_diff($newImages, $originalImages)) > 0) {
            $changes['images'] = [
                'old_count' => count($originalImages),
                'new_count' => count($newImages),
                'added' => array_diff($newImages, $originalImages),
                'removed' => array_diff($originalImages, $newImages)
            ];
        }
        
        // Compare variations
        $originalVariations = $originalProduct->variations;
        $newVariations = $updateProduct->variations;
        
        if ($originalVariations->count() !== $newVariations->count()) {
            $changes['variations'] = [
                'old_count' => $originalVariations->count(),
                'new_count' => $newVariations->count(),
                'type' => 'count_change'
            ];
        } else {
            $variationChanges = [];
            foreach ($newVariations as $index => $newVariation) {
                if ($index < $originalVariations->count()) {
                    $originalVariation = $originalVariations[$index];
                    $variationChange = [];
                    
                    if ($originalVariation->name !== $newVariation->name) {
                        $variationChange['name'] = [
                            'old' => $originalVariation->name,
                            'new' => $newVariation->name
                        ];
                    }
                    
                    if ($originalVariation->sku !== $newVariation->sku) {
                        $variationChange['sku'] = [
                            'old' => $originalVariation->sku,
                            'new' => $newVariation->sku
                        ];
                    }
                    
                    if ($originalVariation->price != $newVariation->price) {
                        $variationChange['price'] = [
                            'old' => $originalVariation->price,
                            'new' => $newVariation->price
                        ];
                    }
                    
                    if ($originalVariation->sale_price != $newVariation->sale_price) {
                        $variationChange['sale_price'] = [
                            'old' => $originalVariation->sale_price,
                            'new' => $newVariation->sale_price
                        ];
                    }
                    
                    if ($originalVariation->stock_quantity != $newVariation->stock_quantity) {
                        $variationChange['stock_quantity'] = [
                            'old' => $originalVariation->stock_quantity,
                            'new' => $newVariation->stock_quantity
                        ];
                    }
                    
                    if ($originalVariation->is_active != $newVariation->is_active) {
                        $variationChange['is_active'] = [
                            'old' => $originalVariation->is_active ? 'Aktif' : 'Tidak Aktif',
                            'new' => $newVariation->is_active ? 'Aktif' : 'Tidak Aktif'
                        ];
                    }
                    
                    if (!empty($variationChange)) {
                        $variationChanges[$index] = $variationChange;
                    }
                }
            }
            
            if (!empty($variationChanges)) {
                $changes['variations'] = [
                    'type' => 'field_changes',
                    'changes' => $variationChanges
                ];
            }
        }
        
        return $changes;
    }

    public function cancelProductUpdateRequest($id)
    {
        $updateRequest = Product::where('id', $id)
            ->where('user_id', auth()->id())
            ->where('is_update_request', true)
            ->whereIn('status', ['pending', 'rejected'])
            ->firstOrFail();

        // Delete the update request
        $updateRequest->delete();

        return redirect()->route('dashboard')
            ->with('success', 'Permohonan kemaskini produk telah dibatalkan.');
    }

    public function previewServiceUpdateRequest($id)
    {
        $updateRequest = Service::where('id', $id)
            ->where('user_id', auth()->id())
            ->where('is_update_request', true)
            ->whereIn('status', ['pending', 'rejected'])
            ->firstOrFail();

        $originalService = Service::find($updateRequest->original_service_id);
        
        if (!$originalService) {
            return redirect()->route('dashboard')->with('error', 'Perkhidmatan asal tidak dijumpai.');
        }

        // Get changes for comparison
        $changes = $this->getServiceChanges($originalService, $updateRequest);

        return view('client.requests.service-update-preview', compact('updateRequest', 'originalService', 'changes'));
    }

    private function getServiceChanges($originalService, $updateService)
    {
        $changes = [];
        
        // Compare basic fields
        if ($originalService->title !== $updateService->title) {
            $changes['title'] = [
                'old' => $originalService->title,
                'new' => $updateService->title
            ];
        }
        
        if ($originalService->description !== $updateService->description) {
            $changes['description'] = [
                'old' => $originalService->description,
                'new' => $updateService->description
            ];
        }
        
        if ($originalService->location !== $updateService->location) {
            $changes['location'] = [
                'old' => $originalService->location,
                'new' => $updateService->location
            ];
        }
        
        if ($originalService->pricing !== $updateService->pricing) {
            $changes['pricing'] = [
                'old' => $originalService->pricing,
                'new' => $updateService->pricing
            ];
        }
        
        if ($originalService->contact_info !== $updateService->contact_info) {
            $changes['contact_info'] = [
                'old' => $originalService->contact_info,
                'new' => $updateService->contact_info
            ];
        }
        
        if ($originalService->category !== $updateService->category) {
            $changes['category'] = [
                'old' => $originalService->category,
                'new' => $updateService->category
            ];
        }
        
        // Compare tags
        $originalTags = is_array($originalService->tags) ? $originalService->tags : [];
        $newTags = is_array($updateService->tags) ? $updateService->tags : [];
        if (count(array_diff($originalTags, $newTags)) > 0 || count(array_diff($newTags, $originalTags)) > 0) {
            $changes['tags'] = [
                'old' => implode(', ', $originalTags),
                'new' => implode(', ', $newTags)
            ];
        }
        
        // Compare images
        $originalImages = is_array($originalService->images) ? $originalService->images : [];
        $newImages = is_array($updateService->images) ? $updateService->images : [];
        if (count(array_diff($originalImages, $newImages)) > 0 || count(array_diff($newImages, $originalImages)) > 0) {
            $changes['images'] = [
                'old_count' => count($originalImages),
                'new_count' => count($newImages),
                'added' => array_diff($newImages, $originalImages),
                'removed' => array_diff($originalImages, $newImages)
            ];
        }
        
        return $changes;
    }

    public function cancelServiceRequest($id)
    {
        $service = Service::where('id', $id)
            ->where('user_id', auth()->id())
            ->where('status', 'pending')
            ->where('is_update_request', false)
            ->firstOrFail();

        // Delete the service
        $service->delete();

        return redirect()->route('dashboard')
            ->with('success', 'Permohonan perkhidmatan telah dibatalkan.');
    }

    public function cancelProductRequest($id)
    {
        $product = Product::where('id', $id)
            ->where('user_id', auth()->id())
            ->where('status', 'pending')
            ->firstOrFail();

        // Delete the product
        $product->delete();

        return redirect()->route('dashboard')
            ->with('success', 'Permohonan produk telah dibatalkan.');
    }

    public function cancelServiceUpdateRequest($id)
    {
        $updateRequest = Service::where('id', $id)
            ->where('user_id', auth()->id())
            ->where('is_update_request', true)
            ->whereIn('status', ['pending', 'rejected'])
            ->firstOrFail();

        // Delete the update request
        $updateRequest->delete();

        return redirect()->route('dashboard')
            ->with('success', 'Permohonan kemaskini perkhidmatan telah dibatalkan.');
    }

    public function forgetServiceUpdateRequest($id)
    {
        $updateRequest = Service::where('id', $id)
            ->where('user_id', auth()->id())
            ->where('is_update_request', true)
            ->where('status', 'rejected')
            ->firstOrFail();

        $updateRequest->delete();

        return redirect()->route('dashboard')
            ->with('success', 'Permohonan kemaskini perkhidmatan berjaya dilupakan.');
    }

    public function forgetProductUpdateRequest($id)
    {
        $updateRequest = Product::where('id', $id)
            ->where('user_id', auth()->id())
            ->where('is_update_request', true)
            ->where('status', 'rejected')
            ->firstOrFail();

        $updateRequest->delete();

        return redirect()->route('dashboard')
            ->with('success', 'Permohonan kemaskini produk berjaya dilupakan.');
    }

    public function forgetServiceRequest($id)
    {
        $service = Service::where('id', $id)
            ->where('user_id', auth()->id())
            ->where('status', 'rejected')
            ->where('is_update_request', false)
            ->firstOrFail();

        $service->delete();

        return redirect()->route('dashboard')
            ->with('success', 'Permohonan perkhidmatan berjaya dilupakan.');
    }

    public function forgetProductRequest($id)
    {
        $product = Product::where('id', $id)
            ->where('user_id', auth()->id())
            ->where('status', 'rejected')
            ->where('is_update_request', false)
            ->firstOrFail();

        $product->delete();

        return redirect()->route('dashboard')
            ->with('success', 'Permohonan produk berjaya dilupakan.');
    }
} 