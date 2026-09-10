<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $table = 'clients';

    protected $primaryKey = 'clients_id';

    public $incrementing = false;

    protected $fillable = [
        'clients_id',
        'diets_id',
    ];

    /**
     * Relationship with User
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'clients_id', 'user_id');
    }

    /**
     * Relationship with Diet
     */
    public function diet(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Diet::class, 'diets_id', 'diets_id');
    }

    /**
     * Relationship with Carts/Orders
     */
    public function carts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Cart::class, 'clients_id', 'clients_id');
    }

    /**
     * Relationship with BodyData (One-to-One)
     */
    public function bodyData(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(BodyData::class, 'clients_id', 'clients_id');
    }

    /**
     * Relationship with Lifestyle (One-to-One)
     */
    public function lifestyle(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Lifestyle::class, 'clients_id', 'clients_id');
    }

    /**
     * Relationship with ChronicDiseases (Many-to-Many)
     */
    public function chronicDiseases(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(
            ChronicDisease::class,
            'clients_chronic_diseases',
            'clients_id',
            'chronic_diseases_id'
        );
    }

    /**
     * Relationship with Allergies (Many-to-Many)
     */
    public function allergies(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(
            Allergy::class,
            'clients_allergies',
            'clients_id',
            'allergies_id'
        );
    }

    /**
     * Relationship with MedicalRecords (Many-to-Many)
     */
    public function medicalRecords(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(
            MedicalRecord::class,
            'clients_medical_record',
            'clients_id',
            'medical_record_id'
        );
    }

    /**
     * Relationship with Diets (Many-to-Many)
     * Private diets assigned to this client
     */
    public function diets(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(
            Diet::class,
            'client_diets',
            'clients_id',
            'diets_id'
        )->withTimestamps();
    }

    /**
     * Relationship with Locations (Many-to-Many)
     * Client addresses
     */
    public function locations(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(
            Location::class,
            'client_addresses',
            'clients_id',
            'location_id'
        )->withTimestamps();
    }

    /**
     * Relationship with Consultations (One-to-Many)
     */
    public function consultations(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Consultation::class, 'client_id', 'clients_id');
    }
}
