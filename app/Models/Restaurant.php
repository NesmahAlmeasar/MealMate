<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    use HasFactory;

    protected $table = 'restaurants';
    protected $primaryKey = 'restaurants_id';

    protected $fillable = [
        'name',
        'photo_url',
    ];

    /**
     * Relationship with Carts/Orders
     */
    public function carts()
    {
        return $this->hasMany(Cart::class, 'restaurants_id', 'restaurants_id');
    }

    /**
     * Relationship with Categories (Many-to-Many)
     */
    public function categories()
    {
        return $this->belongsToMany(
            Category::class,
            'restaurant_categories',
            'restaurants_id',
            'category_id'
        );
    }
}
