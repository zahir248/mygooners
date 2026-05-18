<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'search' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:255',
            'per_page' => 'nullable|integer|min:1|max:50',
            'sort' => 'nullable|in:newest,price_low,price_high,popular',
        ]);

        $perPage = $validated['per_page'] ?? 12;
        $sort = $validated['sort'] ?? 'newest';

        $query = Product::query()
            ->where('status', 'active')
            ->with([
                'activeVariations:id,product_id,name,price,sale_price,stock_quantity,is_active,sort_order',
                'reviews:id,product_id,rating',
            ]);

        if (!empty($validated['category'])) {
            $query->where('category', $validated['category']);
        }

        if (!empty($validated['search'])) {
            $search = $validated['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
            });
        }

        switch ($sort) {
            case 'price_low':
                $query->orderByRaw('COALESCE(sale_price, price) ASC');
                break;
            case 'price_high':
                $query->orderByRaw('COALESCE(sale_price, price) DESC');
                break;
            case 'popular':
                $query->orderBy('views_count', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $products = $query->paginate($perPage);

        $items = $products->getCollection()->map(function (Product $product) {
            $primaryImage = $this->resolvePrimaryImage($product);
            $averageRating = $product->reviews->count() > 0
                ? round((float) $product->reviews->avg('rating'), 1)
                : 4.5;

            return [
                'id' => $product->id,
                'product_name' => $product->title,
                'price' => $this->getDisplayPrice($product),
                'image' => $primaryImage,
                'description_summary' => Str::limit(strip_tags((string) $product->description), 120),
                'category' => $product->category,
                'stock_quantity' => $this->getStockQuantity($product),
                'star_rating' => $averageRating,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => $items->count() > 0 ? 'Products fetched successfully.' : 'No products found.',
            'data' => $items,
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
        ], 200);
    }

    public function show($id)
    {
        if (!ctype_digit((string) $id)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid product id.',
                'errors' => [
                    'id' => ['Product id must be a numeric value.'],
                ],
            ], 422);
        }

        $product = Product::query()
            ->where('status', 'active')
            ->with([
                'activeVariations:id,product_id,name,sku,price,sale_price,stock_quantity,images,is_active,sort_order,created_at,updated_at',
                'reviews:id,product_id,user_id,order_id,order_item_id,rating,comment,is_verified,created_at,updated_at',
                'reviews.user:id,name,profile_image',
                'reviews.photos:id,product_review_id,image_path',
            ])
            ->find((int) $id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.',
            ], 404);
        }

        $sizeOptions = $product->activeVariations
            ->pluck('name')
            ->filter()
            ->values();

        $usesSizeFallback = $sizeOptions->isEmpty();
        if ($usesSizeFallback) {
            $sizeOptions = collect(['S', 'M', 'L', 'XL']);
        }

        $hasRealReviews = $product->reviews->isNotEmpty();
        $starRating = $hasRealReviews
            ? round((float) $product->reviews->avg('rating'), 1)
            : 4.5;

        $customerReviews = $hasRealReviews
            ? $product->reviews->map(function ($review) {
                return [
                    'review_id' => $review->id,
                    'id' => $review->id,
                    'reviewer_name' => $review->reviewer_name,
                    'reviewer_avatar' => $review->reviewer_avatar,
                    'reviewer_avatar_url' => $review->reviewer_avatar_url,
                    'rating' => (int) $review->rating,
                    'comment' => $review->comment,
                    'date' => optional($review->created_at)->format('Y-m-d'),
                    'photos' => $review->photo_urls,
                    'is_verified' => (bool) $review->is_verified,
                    'is_verified_purchase' => (bool) $review->is_verified,
                    'created_at' => optional($review->created_at)->toIso8601String(),
                ];
            })->values()
            : collect($this->fallbackReviews());

        $images = $this->resolveAllImages($product);
        $primaryImage = $images[0] ?? null;

        $placeholderFields = [];
        if ($usesSizeFallback) {
            $placeholderFields[] = 'size_options';
        }
        if (!$hasRealReviews) {
            $placeholderFields[] = 'star_rating';
            $placeholderFields[] = 'customer_reviews';
        }

        return response()->json([
            'success' => true,
            'message' => 'Product detail fetched successfully.',
            'data' => [
                'id' => $product->id,
                'product_name' => $product->title,
                'slug' => $product->slug,
                'price' => $this->getDisplayPrice($product),
                'original_price' => (float) $product->price,
                'sale_price' => $product->sale_price !== null ? (float) $product->sale_price : null,
                'image' => $primaryImage,
                'images' => $images,
                'description' => $product->description,
                'size_options' => $sizeOptions->all(),
                'stock_quantity' => $this->getStockQuantity($product),
                'star_rating' => $starRating,
                'customer_reviews' => $customerReviews,
                'reviews_count' => $hasRealReviews ? $product->reviews->count() : count($this->fallbackReviews()),
                'category' => $product->category,
                'variation_label' => $product->variation_label ?: 'Size',
                'variations' => $product->activeVariations->map(function ($variation) {
                    return [
                        'id' => $variation->id,
                        'name' => $variation->name,
                        'sku' => $variation->sku,
                        'price' => $variation->price !== null ? (float) $variation->price : null,
                        'sale_price' => $variation->sale_price !== null ? (float) $variation->sale_price : null,
                        'stock_quantity' => (int) $variation->stock_quantity,
                        'images' => $this->resolveVariationImages($variation->images ?? []),

                    ];
                })->values(),
                'created_at' => optional($product->created_at)->toIso8601String(),
                'updated_at' => optional($product->updated_at)->toIso8601String(),
            ],
            'meta' => [
                'field_sources' => [
                    'size_options' => $usesSizeFallback ? 'placeholder_default' : 'database_product_variations',
                    'star_rating' => $hasRealReviews ? 'database_product_reviews' : 'placeholder_default',
                    'customer_reviews' => $hasRealReviews ? 'database_product_reviews' : 'placeholder_default',
                ],
                'placeholder_fields' => $placeholderFields,
            ],
        ], 200);

        
    }

    private function getDisplayPrice(Product $product): float
    {
        if ($product->activeVariations->isNotEmpty()) {
            $finalPrices = $product->activeVariations->map(function ($variation) {
                return (float) ($variation->sale_price ?? $variation->price ?? $product->sale_price ?? $product->price);
            });

            return (float) $finalPrices->min();
        }

        return (float) ($product->sale_price ?? $product->price);
    }

    private function getStockQuantity(Product $product): int
    {
        if ($product->activeVariations->isNotEmpty()) {
            return (int) $product->activeVariations->sum('stock_quantity');
        }

        return (int) $product->stock_quantity;
    }

    private function resolvePrimaryImage(Product $product): ?string
    {
        $images = $this->resolveAllImages($product);
        return $images[0] ?? asset('images/official-logo.png');
    }

    private function resolveAllImages(Product $product): array
    {
        $productImages = collect($product->images ?? [])->map(function ($imagePath) {
            if (!$imagePath) {
                return null;
            }

            if (Str::startsWith($imagePath, ['http://', 'https://'])) {
                return $imagePath;
            }

            return route('product.image', basename($imagePath));
        })->filter()->values();

        if ($productImages->isNotEmpty()) {
            return $productImages->all();
        }

        $variationImages = collect($product->activeVariations)->flatMap(function ($variation) {
            return $this->resolveVariationImages($variation->images ?? []);
        })->filter()->values();

        return $variationImages->all();
    }

    private function resolveVariationImages($images): array
    {
        if (!is_array($images) || empty($images)) {
            return [];
        }

        return collect($images)->map(function ($imagePath) {
            if (!$imagePath) {
                return null;
            }

            if (Str::startsWith($imagePath, ['http://', 'https://'])) {
                return $imagePath;
            }

            return route('variation.image', basename($imagePath));
        })->filter()->values()->all();
    }

    private function fallbackReviews(): array
    {
        return [
            [
                'id' => null,
                'review_id' => null,
                'reviewer_name' => 'MyGooners Customer',
                'reviewer_avatar' => asset('images/profile-image-default.png'),
                'reviewer_avatar_url' => null,
                'rating' => 5,
                'comment' => 'Great product quality and fast delivery. Worth buying.',
                'date' => null,
                'photos' => [],
                'is_verified' => false,
                'is_verified_purchase' => false,
                'created_at' => null,
            ],
            [
                'id' => null,
                'review_id' => null,
                'reviewer_name' => 'Arsenal Fan',
                'reviewer_avatar' => asset('images/profile-image-default.png'),
                'reviewer_avatar_url' => null,
                'rating' => 4,
                'comment' => 'Good fit and comfortable material. Recommended.',
                'date' => null,
                'photos' => [],
                'is_verified' => false,
                'is_verified_purchase' => false,
                'created_at' => null,
            ],
        ];
    }
}
