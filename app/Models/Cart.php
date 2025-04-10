<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CartItem;
use App\Models\User;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = ['user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    public function addItem($productId, $quantity = 1)
    {
        $existingItem = $this->items()->where('product_id', $productId)->first();

        if ($existingItem) {
            $existingItem->update([
                'quantity' => $existingItem->quantity + $quantity
            ]);
            return $existingItem;
        }

        return $this->items()->create([
            'product_id' => $productId,
            'quantity' => $quantity
        ]);
    }

    public function removeItem($productId)
    {
        return $this->items()->where('product_id', $productId)->delete();
    }

    public function updateItemQuantity($productId, $quantity)
    {
        return $this->items()->where('product_id', $productId)->update(['quantity' => $quantity]);
    }

    public function clearCart()
    {
        return $this->items()->delete();
    }
}
