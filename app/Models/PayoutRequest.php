<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayoutRequest extends Model
{
    protected $table = 'payout_requests';
    protected $primaryKey = 'payout_id';

    protected $fillable = [
        'user_id',
        'amount',
        'bank_name',
        'account_number',
        'status',
        'admin_notes',
        'requested_at',
        'processed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'requested_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
