<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
use App\Models\Order;
use App\Models\CartItem;
use App\Models\Review;

class Product extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'description',
        'price',
        'image',
        'category_id'
    ];

    protected $attributes = [
        'image' => null,
        'sku' => null
    ];
    
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function averageRating()
    {
        return $this->reviews()->where('is_approved', true)->avg('rating') ?? 0;
    }

    public function reviewsCount()
    {
        return $this->reviews()->where('is_approved', true)->count();
    }
}
