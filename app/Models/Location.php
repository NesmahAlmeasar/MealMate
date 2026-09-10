<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $table = 'locations';

    protected $primaryKey = 'location_id';

    public $timestamps = true;

    protected $fillable = [
        'latitude_x',
        'longitude_y',
        'description',
    ];

    public function restaurant()
    {
        return $this->hasOne(Restaurant::class, 'location_id', 'location_id');
    }
}
