<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsultationType extends Model
{
    use HasFactory;

    protected $table = 'consultation_types';

    protected $primaryKey = 'type_id';

    protected $fillable = [
        'name',
        'price',
        'duration_days',
    ];
}
