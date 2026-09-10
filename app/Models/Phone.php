<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Phone extends Model
{
    use HasFactory;

    protected $table = 'phone';

    protected $primaryKey = 'phone_id';

    public $timestamps = true;

    protected $fillable = [
        'phone_number',
        'restaurants_id',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class, 'restaurants_id', 'restaurants_id');
    }
}
