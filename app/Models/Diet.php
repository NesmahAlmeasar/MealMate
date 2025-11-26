<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diet extends Model
{
    use HasFactory;

    protected $table = 'diets';
    protected $primaryKey = 'diets_id';
    public $incrementing = true;

    protected $fillable = [
        'name',
        'photo_url',
        'description',
        'is_public',
        'warning',
        'advice',
        'nutritionist_id',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    /**
     * Relationship with Nutritionist
     */
    public function nutritionist()
    {
        return $this->belongsTo(Nutritionist::class, 'nutritionist_id', 'nutritionist_id');
    }

    /**
     * Relationship with Meals (Many-to-Many)
     */
    public function meals()
    {
        return $this->belongsToMany(
            Meal::class,
            'diet_meals',
            'diets_id',
            'meals_id',
            'diets_id',
            'meals_id'
        );
    }

    /**
     * Relationship with Restrictions
     */
    public function restrictions()
    {
        return $this->hasMany(Restriction::class, 'diets_id', 'diets_id');
    }
}
