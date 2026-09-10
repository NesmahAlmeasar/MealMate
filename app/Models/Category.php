<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table = 'category';

    protected $primaryKey = 'category_id';

    public $incrementing = true;

    protected $fillable = [
        'category_name',
    ];

    /**
     * Relationship with Meals
     */
    public function meals(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Meal::class, 'category_id', 'category_id');
    }

    /**
     * Relationship with Restaurants (Through Meals)
     */
    public function restaurants(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(
            Restaurant::class,
            Meal::class,
            'category_id', // Foreign key on meals table...
            'restaurants_id', // Foreign key on restaurants table...
            'category_id', // Local key on category table...
            'restaurant_id' // Local key on meals table...
        )->distinct();
    }
}
