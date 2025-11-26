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
    public function meals()
    {
        return $this->hasMany(Meal::class, 'category_id', 'category_id');
    }
}
