<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $table = 'cart';

    protected $primaryKey = 'cart_id';

    protected $fillable = [
        'date',
        'time',
        'total_price',
        'state',
        'clients_id',
        'location_id',
        'restaurants_id',
    ];

    protected $casts = [
        'date' => 'date',
        'total_price' => 'decimal:2',
    ];

    /**
     * Relationship with Client
     */
    /**
     * Relationship with Client
     */
    public function client(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Client::class, 'clients_id', 'clients_id');
    }

    /**
     * Relationship with Restaurant
     */
    public function restaurant(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Restaurant::class, 'restaurants_id', 'restaurants_id');
    }

    /**
     * Relationship with Cart Items
     */
    public function items(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CartItem::class, 'cart_id', 'cart_id');
    }

    /**
     * Relationship with Location
     */
    public function location(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id', 'location_id');
    }
}
