<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    use HasFactory;

    protected $table = 'ingredients';
    protected $primaryKey = 'ingredients_id';
    public $incrementing = true;

    protected $fillable = [
        'name_ar',
        'usda_term',
        'is_vegan',
        'has_gluten',
        'has_dairy',
        'calories',
        'protein_g',
        'fat_g',
        'carbs_g',
    ];

    protected $casts = [
        'is_vegan' => 'boolean',
        'has_gluten' => 'boolean',
        'has_dairy' => 'boolean',
    ];

    /**
     * Relationship with Meals (Many-to-Many)
     */
    public function meals()
    {
        return $this->belongsToMany(
            Meal::class,
            'meals_ingredients',
            'ingredients_id',
            'meals_id'
        )->withPivot('quantity_g');
    }

    /**
     * Relationship with Allergies (Many-to-Many)
     */
    public function allergies()
    {
        return $this->belongsToMany(
            Allergy::class,
            'ingredients_allergies',
            'ingredients_id',
            'allergies_id'
        );
    }
}
