<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'price', 'description', 'stock'];
    public function carts()
    {
        return $this->belongsToMany(Cart::class, 'cart_products')->withPivot('quantity');
    }
    public function orders()
    {
        return $this->belongsToMany(OrderProduct::class, 'order_products')->withPivot('quantity');
    }

    public function getTotalPriceAttribute()
    {
        return $this->quantity * $this->price;
    }
}
