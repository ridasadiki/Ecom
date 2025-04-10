<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Cart;
use App\Models\Review;
use App\Models\WishlistItem;
use App\Models\Product;
use App\Models\ChatMessage;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isCustomer()
    {
        return $this->role === 'customer';
    }

    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    /**
     * Get or create a cart for the user
     */
    public function getCart()
    {
        $cart = $this->cart;
        
        if (!$cart) {
            $cart = Cart::create([
                'user_id' => $this->id
            ]);
        }
        
        return $cart;
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function wishlistItems()
    {
        return $this->hasMany(WishlistItem::class);
    }

    public function hasInWishlist(Product $product)
    {
        return $this->wishlistItems()->where('product_id', $product->id)->exists();
    }

    public function chatMessages()
    {
        return $this->hasMany(ChatMessage::class);
    }
}
