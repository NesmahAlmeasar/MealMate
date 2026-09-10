<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nutritionist extends Model
{
    use HasFactory;

    protected $table = 'nutritionists';

    protected $primaryKey = 'nutritionist_id';

    public $incrementing = false;

    protected $fillable = [
        'nutritionist_id',
        'Academic_level',
        'description',
    ];

    /**
     * Relationship with User
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'nutritionist_id', 'user_id');
    }

    /**
     * Relationship with Diets
     */
    public function diets(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Diet::class, 'nutritionist_id', 'nutritionist_id');
    }

    /**
     * Relationship with Certificates
     */
    public function certificates(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Certificate::class, 'nutritionist_id', 'nutritionist_id');
    }
}
