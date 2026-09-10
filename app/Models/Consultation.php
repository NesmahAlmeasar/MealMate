<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consultation extends Model
{
    use HasFactory;

    protected $table = 'consultations';

    protected $primaryKey = 'consultation_id';

    protected $fillable = [
        'client_id',
        'nutritionist_id',
        'type_id',
        'status',          // pending, active, completed, cancelled
        'payment_status',  // pending, paid, failed, refunded
        'start_time',
        'end_time',
        'diet_id',        // nullable, linked if diet plan is created
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function client(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id', 'user_id');
        // Note: Assuming User model is linked. If strict 'Client' model exists, use that.
        // But user_id is the base key.
    }

    public function nutritionist(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'nutritionist_id', 'user_id');
    }

    public function type(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ConsultationType::class, 'type_id', 'type_id');
    }

    public function payments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Payment::class, 'consultation_id', 'consultation_id');
    }

    public function diet(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Diet::class, 'diet_id', 'diets_id');
    }
}
