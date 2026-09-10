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
        'description',
        'email',
        'photo_url',
        'state',
        'manager_id',
        'location_id',
        'commission_rate',
    ];

    /**
     * Default commission rate if not set.
     */
    protected $attributes = [
        'commission_rate' => 10.00,
    ];

    /**
     * Relationship with Manager (User)
     */
    public function manager(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id', 'user_id');
    }

    /**
     * Relationship with Carts/Orders
     */
    public function carts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Cart::class, 'restaurants_id', 'restaurants_id');
    }

    /**
     * Relationship with Meals
     */
    public function meals(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Meal::class, 'restaurant_id', 'restaurants_id');
    }

    /**
     * Relationship with Categories
     */
    /**
     * Relationship with Categories (Through Meals)
     * This simulates the previous interaction but derives it from existing meals.
     */
    public function categories(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(
            Category::class,
            Meal::class,
            'restaurant_id', // Foreign key on meals table...
            'category_id', // Foreign key on category table...
            'restaurants_id', // Local key on restaurants table...
            'category_id' // Local key on meals table...
        )->distinct();
    }

    /**
     * Relationship with Phones
     */
    public function phones(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Phone::class, 'restaurants_id', 'restaurants_id');
    }

    /**
     * Relationship with Location (1:1)
     */
    public function location(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id', 'location_id');
    }
}
