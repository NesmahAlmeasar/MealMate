<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lifestyle extends Model
{
    use HasFactory;

    protected $table = 'lifestyle';

    protected $primaryKey = 'clients_id';

    public $incrementing = false;

    protected $fillable = [
        'clients_id',
        'smoking',
        'activity_level',
        'sleeping_hours',
    ];

    protected $casts = [
        'smoking' => 'boolean',
        'sleeping_hours' => 'decimal:2',
    ];

    /**
     * Relationship with Client
     */
    public function client()
    {
        return $this->belongsTo(Client::class, 'clients_id', 'clients_id');
    }
}
