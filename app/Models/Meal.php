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
        'proper_time',
        'quantity_g',
        'calories',
        'protein_g',
        'fat_g',
        'carbs_g',
        'category_id',
    ];

    /**
     * Relationship with Category
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    /**
     * Relationship with Ingredients (Many-to-Many)
     */
    public function ingredients()
    {
        return $this->belongsToMany(
            Ingredient::class,
            'meals_ingredients',
            'meals_id',
            'ingredients_id'
        )->withPivot('quantity_g');
    }

    /**
     * Scope for approved meals
     */
    public function scopeApproved($query)
    {
        return $query->where('state', 'approved');
    }

    /**
     * Scope for pending meals
     */
    public function scopePending($query)
    {
        return $query->where('state', 'pending');
    }

    /**
     * Scope for rejected meals
     */
    public function scopeRejected($query)
    {
        return $query->where('state', 'rejected');
    }

    /**
     * Relationship with Diets (Many-to-Many)
     */
    public function diets()
    {
        return $this->belongsToMany(
            Diet::class,
            'diet_meals',
            'meals_id',
            'diets_id'
        );
    }
}
