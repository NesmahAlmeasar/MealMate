<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Allergy extends Model
{
    use HasFactory;

    protected $table = 'allergies';

    protected $primaryKey = 'allergies_id';

    public $incrementing = true;

    protected $fillable = [
        'allergies',
    ];

    /**
     * Relationship with Ingredients (Many-to-Many)
     */
    public function ingredients()
    {
        return $this->belongsToMany(
            Ingredient::class,
            'ingredients_allergies',
            'allergies_id',
            'ingredients_id'
        );
    }

    /**
     * Relationship with Clients (Many-to-Many)
     */
    public function clients()
    {
        return $this->belongsToMany(
            Client::class,
            'clients_allergies',
            'allergies_id',
            'clients_id'
        );
    }
}
