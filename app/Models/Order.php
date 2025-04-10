<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'quantity',
        'total_price',
        'is_delivered',   
        'client_name',     
        'client_address',  
        'client_phone',    
    ];


    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
