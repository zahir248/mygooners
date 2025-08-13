<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Favourite;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class FavouriteController extends Controller
{

    /**
     * Display user's favourite products
     */
    public function index()
    {
        $favourites = auth()->user()->favouriteProducts()
            ->with(['variations', 'reviews'])
            ->paginate(12);

        return view('client.favourites.index', compact('favourites'));
    }

    /**
     * Add product to favourites
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $user = auth()->user();
        $productId = $request->product_id;

        // Check if already favourited
        $existingFavourite = Favourite::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->first();

        if ($existingFavourite) {
            return response()->json([
                'success' => false,
                'message' => 'Produk sudah ada dalam kegemaran anda'
            ]);
        }

        // Create new favourite
        Favourite::create([
            'user_id' => $user->id,
            'product_id' => $productId
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Produk berjaya ditambah ke kegemaran'
        ]);
    }

    /**
     * Remove product from favourites
     */
    public function destroy(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $user = auth()->user();
        $productId = $request->product_id;

        $favourite = Favourite::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->first();

        if (!$favourite) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak dijumpai dalam kegemaran'
            ]);
        }

        $favourite->delete();

        return response()->json([
            'success' => true,
            'message' => 'Produk berjaya dikeluarkan dari kegemaran'
        ]);
    }

    /**
     * Check if a product is favourited by the current user
     */
    public function check(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $user = auth()->user();
        $productId = $request->product_id;

        $isFavourited = Favourite::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->exists();

        return response()->json([
            'success' => true,
            'is_favourited' => $isFavourited
        ]);
    }

    /**
     * Get count of user's favourites
     */
    public function count(): JsonResponse
    {
        $user = auth()->user();
        $count = $user->favourites()->count();

        return response()->json([
            'success' => true,
            'count' => $count
        ]);
    }

    /**
     * Handle flash message for favourites actions
     */
    public function flashMessage(Request $request)
    {
        $message = $request->input('message');
        $type = $request->input('type', 'success');
        $redirectUrl = $request->input('redirect_url', route('shop.index'));

        // Set the appropriate session message
        if ($type === 'error') {
            session()->flash('error', $message);
        } elseif ($type === 'warning') {
            session()->flash('warning', $message);
        } else {
            session()->flash('success', $message);
        }

        // Redirect back to the original page
        return redirect($redirectUrl);
    }
} 