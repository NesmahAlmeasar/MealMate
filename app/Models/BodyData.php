<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BodyData extends Model
{
    use HasFactory;

    protected $table = 'body_data';

    protected $primaryKey = 'clients_id';

    public $incrementing = false;

    protected $fillable = [
        'clients_id',
        'birth_date',
        'height_cm',
        'weight_kg',
        'sex',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'height_cm' => 'decimal:2',
        'weight_kg' => 'decimal:2',
    ];

    /**
     * Relationship with Client
     */
    public function client()
    {
        return $this->belongsTo(Client::class, 'clients_id', 'clients_id');
    }
}
