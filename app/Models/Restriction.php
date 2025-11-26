<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restriction extends Model
{
    use HasFactory;

    protected $table = 'restrictions';
    protected $primaryKey = 'restriction_id';
    public $incrementing = true;

    protected $fillable = [
        'restriction',
        'field_name',
        'operator',
        'value',
        'diets_id',
    ];

    /**
     * Relationship with Diet
     */
    public function diet()
    {
        return $this->belongsTo(Diet::class, 'diets_id', 'diets_id');
    }
}
