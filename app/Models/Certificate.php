<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    protected $primaryKey = 'certificate_id';

    protected $fillable = [
        'certificate_type',
        'nutritionist_id',
        'photo_url',
    ];

    public function nutritionist()
    {
        return $this->belongsTo(Nutritionist::class, 'nutritionist_id', 'nutritionist_id');
    }
}
