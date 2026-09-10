<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalRecord extends Model
{
    use HasFactory;

    protected $table = 'medical_record';

    protected $primaryKey = 'medical_record_id';

    public $incrementing = true;

    protected $fillable = [
        'medical_record',
    ];

    /**
     * Relationship with Clients (Many-to-Many)
     */
    public function clients()
    {
        return $this->belongsToMany(
            Client::class,
            'clients_medical_record',
            'medical_record_id',
            'clients_id'
        );
    }
}
