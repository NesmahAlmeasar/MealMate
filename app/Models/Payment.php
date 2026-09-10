<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'payments';

    protected $primaryKey = 'payment_id';

    protected $fillable = [
        'consultation_id',
        'cart_id',
        'payable_type',
        'payable_id',
        'user_id',
        'token',
        'transaction_id',
        'amount',
        'currency',
        'status',
        'payment_method',
        'gateway_response',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function consultation()
    {
        return $this->belongsTo(Consultation::class, 'consultation_id', 'consultation_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function cart()
    {
        return $this->belongsTo(Cart::class, 'cart_id', 'cart_id');
    }
}
