<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemExpense extends Model
{
    protected $table = 'system_expenses';
    protected $primaryKey = 'expense_id';

    protected $fillable = [
        'category',
        'amount',
        'description',
        'expense_date',
        'logged_by_admin_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
    ];

    public function loggedByAdmin()
    {
        return $this->belongsTo(User::class, 'logged_by_admin_id', 'user_id');
    }
}
