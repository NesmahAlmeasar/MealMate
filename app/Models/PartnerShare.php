<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartnerShare extends Model
{
    protected $table = 'partner_shares';
    protected $primaryKey = 'share_id';

    protected $fillable = [
        'partner_user_id',
        'share_percentage',
    ];

    protected $casts = [
        'share_percentage' => 'decimal:2',
    ];

    public function partner()
    {
        return $this->belongsTo(User::class, 'partner_user_id', 'user_id');
    }
}
