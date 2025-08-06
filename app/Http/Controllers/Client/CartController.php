<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariation;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = Cart::getOrCreateCart();
        $cart->load(['items.product', 'items.variation']);
        
        // Debug logging for cart items
        foreach ($cart->items as $item) {
            \Log::info('Cart item loaded:', [
                'item_id' => $item->id,
                'product_id' => $item->product_id,
                'variation_id' => $item->product_variation_id,
                'product_title' => $item->product->title,
                'variation_name' => $item->variation ? $item->variation->name : 'null',
                'display_name' => $item->display_name
            ]);
        }
        
        return view('client.cart.index', compact('cart'));
    }

    public function add(Request $request)
    {
        // Debug logging
        \Log::info('Cart add request received:', [
            'product_id' => $request->product_id,
            'variation_id' => $request->variation_id,
            'quantity' => $request->quantity,
            'all_data' => $request->all()
        ]);
        
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'variation_id' => 'nullable|exists:product_variations,id',
            'quantity' => 'required|integer|min:1|max:10'
        ]);

        $cart = Cart::getOrCreateCart();
        $product = Product::findOrFail($request->product_id);
        
        // Determine price and variation
        $price = $product->price;
        $variationId = null;
        
        if ($request->variation_id) {
            $variation = ProductVariation::findOrFail($request->variation_id);
            $price = $variation->sale_price ?: $variation->price;
            $variationId = $variation->id;
            
            \Log::info('Variation found:', [
                'variation_id' => $variation->id,
                'variation_name' => $variation->name,
                'price' => $price
            ]);
        } else {
            $price = $product->sale_price ?: $product->price;
            \Log::info('No variation, using base product price:', ['price' => $price]);
        }

        // Check stock availability
        $stockQuantity = $variationId ? 
            ProductVariation::find($variationId)->stock_quantity : 
            $product->stock_quantity;
            
        if ($stockQuantity < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Stok tidak mencukupi'
            ], 400);
        }

        // Check if item already exists in cart
        $existingItem = CartItem::where([
            'cart_id' => $cart->id,
            'product_id' => $request->product_id,
            'product_variation_id' => $variationId
        ])->first();

        if ($existingItem) {
            // Item exists, add to existing quantity
            $newQuantity = $existingItem->quantity + $request->quantity;
            
            // Check if new quantity exceeds stock
            if ($newQuantity > $stockQuantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok tidak mencukupi untuk menambah kuantiti ini'
                ], 400);
            }
            
            $existingItem->update(['quantity' => $newQuantity]);
            $cartItem = $existingItem;
        } else {
            // Item doesn't exist, create new item
            $cartItem = CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $request->product_id,
                'product_variation_id' => $variationId,
                'quantity' => $request->quantity,
                'price' => $price
            ]);
        }
        
        \Log::info('Cart item created/updated:', [
            'cart_item_id' => $cartItem->id,
            'product_id' => $cartItem->product_id,
            'variation_id' => $cartItem->product_variation_id,
            'quantity' => $cartItem->quantity,
            'price' => $cartItem->price
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Produk ditambah ke troli',
            'cart_count' => $cart->fresh()->item_count,
            'cart_total' => number_format($cart->fresh()->total, 2)
        ]);
    }

    public function update(Request $request, $itemId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:10'
        ]);

        $cartItem = CartItem::findOrFail($itemId);
        $cart = $cartItem->cart;
        
        // Check if this cart belongs to current user/session
        if (!$this->canAccessCart($cart)) {
            return response()->json([
                'success' => false,
                'message' => 'Akses tidak dibenarkan'
            ], 403);
        }

        // Check stock availability
        $stockQuantity = $cartItem->variation ? 
            $cartItem->variation->stock_quantity : 
            $cartItem->product->stock_quantity;
            
        if ($stockQuantity < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Stok tidak mencukupi'
            ], 400);
        }

        $cartItem->update(['quantity' => $request->quantity]);

        return response()->json([
            'success' => true,
            'message' => 'Kuantiti dikemas kini',
            'cart_count' => $cart->fresh()->item_count,
            'cart_total' => number_format($cart->fresh()->total, 2),
            'item_subtotal' => number_format($cartItem->subtotal, 2)
        ]);
    }

    public function remove($itemId)
    {
        $cartItem = CartItem::findOrFail($itemId);
        $cart = $cartItem->cart;
        
        // Check if this cart belongs to current user/session
        if (!$this->canAccessCart($cart)) {
            return response()->json([
                'success' => false,
                'message' => 'Akses tidak dibenarkan'
            ], 403);
        }

        $cartItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item dikeluarkan dari troli',
            'cart_count' => $cart->fresh()->item_count,
            'cart_total' => number_format($cart->fresh()->total, 2)
        ]);
    }

    public function clear()
    {
        $cart = Cart::getOrCreateCart();
        
        if (!$this->canAccessCart($cart)) {
            return response()->json([
                'success' => false,
                'message' => 'Akses tidak dibenarkan'
            ], 403);
        }

        $cart->items()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Troli dikosongkan',
            'cart_count' => 0,
            'cart_total' => '0.00'
        ]);
    }

    public function count()
    {
        $cart = Cart::getOrCreateCart();
        
        return response()->json([
            'count' => $cart->item_count,
            'total' => number_format($cart->total, 2)
        ]);
    }

    private function canAccessCart($cart)
    {
        $user = auth()->user();
        $sessionId = session()->getId();

        if ($user) {
            return $cart->user_id === $user->id;
        } else {
            return $cart->session_id === $sessionId;
        }
    }
}
