<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChronicDisease extends Model
{
    use HasFactory;

    protected $table = 'chronic_diseases';

    protected $primaryKey = 'chronic_diseases_id';

    public $incrementing = true;

    protected $fillable = [
        'chronic_diseases',
    ];

    /**
     * Relationship with Clients (Many-to-Many)
     */
    public function clients()
    {
        return $this->belongsToMany(
            Client::class,
            'clients_chronic_diseases',
            'chronic_diseases_id',
            'clients_id'
        );
    }
}
