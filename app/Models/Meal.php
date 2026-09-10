<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meal extends Model
{
    use HasFactory;

    protected $table = 'meals';

    protected $primaryKey = 'meals_id';

    public $incrementing = true;

    protected $fillable = [
        'name',
        'description',
        'photo_url',
        'price',
        'state',
        'preparation_time',
        'quantity_g',
        'calories',
        'protein_g',
        'fat_g',
        'carbs_g',
        'category_id',
        'restaurant_id',
    ];

    /**
     * Relationship with Category
     */
    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    /**
     * Relationship with Restaurant
     */
    public function restaurant(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id', 'restaurants_id');
    }

    /**
     * Relationship with Ingredients (Many-to-Many)
     */
    public function ingredients(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(
            Ingredient::class,
            'meals_ingredients',
            'meals_id',
            'ingredients_id'
        )->withPivot('quantity_g');
    }

    /**
     * Relationship with Diets (Many-to-Many)
     */
    public function diets(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(
            Diet::class,
            'diet_meals',
            'meals_id',
            'diets_id'
        );
    }

    /**
     * Scope for approved meals
     */
    public function scopeApproved($query)
    {
        return $query->where('state', 'approved');
    }
}
