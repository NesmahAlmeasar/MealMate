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
    public function user()
    {
        return $this->belongsTo(User::class, 'clients_id', 'user_id');
    }

    /**
     * Relationship with Diet
     */
    public function diet()
    {
        return $this->belongsTo(Diet::class, 'diets_id', 'diets_id');
    }

    /**
     * Relationship with Carts/Orders
     */
    public function carts()
    {
        return $this->hasMany(Cart::class, 'clients_id', 'clients_id');
    }
}
