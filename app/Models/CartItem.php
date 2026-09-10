<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $table = 'cart_item';

    protected $primaryKey = 'cart_item_id';

    protected $fillable = [
        'quantity',
        'cart_id',
        'meals_id',
    ];

    /**
     * Relationship with Cart
     */
    public function cart()
    {
        return $this->belongsTo(Cart::class, 'cart_id', 'cart_id');
    }

    /**
     * Relationship with Meal
     */
    public function meal()
    {
        return $this->belongsTo(Meal::class, 'meals_id', 'meals_id');
    }
}
