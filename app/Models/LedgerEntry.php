<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LedgerEntry extends Model
{
    protected $table = 'ledger_entries';
    protected $primaryKey = 'entry_id';
    public $timestamps = false;

    const CREATED_AT = 'created_at';

    protected $fillable = [
        'transaction_ref',
        'debit_user_id',
        'credit_user_id',
        'amount',
        'entry_type',
        'description',
        'created_by_admin_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    public function debitUser()
    {
        return $this->belongsTo(User::class, 'debit_user_id', 'user_id');
    }

    public function creditUser()
    {
        return $this->belongsTo(User::class, 'credit_user_id', 'user_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'created_by_admin_id', 'user_id');
    }
}
