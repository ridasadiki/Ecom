<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000'
        ]);

        // Check if user has already reviewed this product
        $existingReview = Review::where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->first();

        if ($existingReview) {
            return back()->with('error', 'You have already reviewed this product');
        }

        // Check if user has purchased this product
        $verifiedPurchase = Order::where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->where('is_delivered', true)
            ->exists();

        Review::create([
            'user_id' => Auth::id(),
            'product_id' => $product->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'verified_purchase' => $verifiedPurchase,
            'is_approved' => true
        ]);

        return back()->with('success', 'Thank you for your review!');
    }

    public function destroy(Review $review)
    {
        if ($review->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            return back()->with('error', 'Unauthorized action');
        }

        $review->delete();
        return back()->with('success', 'Review deleted successfully');
    }
}
