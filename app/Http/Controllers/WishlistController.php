<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\WishlistItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index()
    {
        $wishlistItems = WishlistItem::where('user_id', Auth::id())
            ->with('product.category')
            ->latest()
            ->get();

        return view('wishlist.index', compact('wishlistItems'));
    }

    public function toggle(Product $product)
    {
        $wishlistItem = WishlistItem::where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->first();

        if ($wishlistItem) {
            $wishlistItem->delete();
            $message = 'Product removed from wishlist';
            $added = false;
        } else {
            WishlistItem::create([
                'user_id' => Auth::id(),
                'product_id' => $product->id
            ]);
            $message = 'Product added to wishlist';
            $added = true;
        }

        if (request()->wantsJson()) {
            return response()->json([
                'message' => $message,
                'added' => $added
            ]);
        }

        return back()->with('success', $message);
    }

    public function moveToCart(WishlistItem $wishlistItem)
    {
        $user = Auth::user();
        $cart = $user->getCart();
        
        $cart->items()->create([
            'product_id' => $wishlistItem->product_id,
            'quantity' => 1
        ]);

        $wishlistItem->delete();

        return back()->with('success', 'Product moved to cart');
    }
}
