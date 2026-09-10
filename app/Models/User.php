<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $primaryKey = 'user_id';

    protected $keyType = 'string';

    public $incrementing = true;

    protected $fillable = [
        'Fname',
        'Lname',
        'email',
        'password',
        'phone',
        'photo_url',
        'account_state',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'password' => 'hashed',
        ];
    }

    public function roles(): BelongsToMany
    {
        // belongsToMany(النموذج المرتبط, جدول الربط, المفتاح المحلي, المفتاح المرتبط)
        return $this->belongsToMany(Role::class, 'user_roles', 'user_id', 'role_id');
    }

    /**
     * Relationship with Client
     */
    /**
     * Relationship with Client
     */
    public function client(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Client::class, 'clients_id', 'user_id');
    }

    /**
     * دالة مساعدة للتحقق من وجود دور معين للمستخدم.
     */
    public function hasRole(string $roleName): bool
    {
        return $this->roles->pluck('name')->contains($roleName);
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::created(function (User $user) {
            // إنشاء سجل عميل تلقائياً لكل مستخدم جديد لضمان تكامل البيانات
            // يمكن لاحقاً إضافة شرط للتحقق من الدور إذا تم تمريره
            \App\Models\Client::firstOrCreate(['clients_id' => $user->user_id]);
        });

        static::deleting(function (User $user) {
            // حذف سجل المستخدم المرتبط عند حذف المستخدم
            if ($user->client) {
                $user->client->delete();
            }
        });
    }

    /**
     * Get the notifications for the user.
     */
    public function notifications(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Notification::class, 'user_id', 'user_id')->orderBy('created_at', 'desc');
    }

    /**
     * Ensure related records exist based on assigned roles.
     * This should be called after roles are synced or attached.
     */
    public function ensureRoleRecords()
    {
        $roles = $this->roles->pluck('name')->toArray();

        // 1. If Client or User (Normal User), ensure Client record
        // The booted() method handles new users, but this ensures updates too.
        if (in_array('Client', $roles) || in_array('User', $roles) || empty($roles)) { // Fallback to Client if no role
            \App\Models\Client::firstOrCreate(['clients_id' => $this->user_id]);
        }

        // 2. If Specialist or Nutrition Manager, ensure Nutritionist record
        if (in_array('Specialist', $roles) || in_array('Nutrition Manager', $roles)) {
            \App\Models\Nutritionist::firstOrCreate(
                ['nutritionist_id' => $this->user_id],
                [
                    'Academic_level' => 'Bachelor', // Default
                    'description' => 'Specialist account', // Default
                ]
            );
        }
    }

    // --- Chat System Relationships & Logic ---

    public function sentMessages(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Message::class, 'sender_id', 'user_id');
    }

    public function receivedMessages(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Message::class, 'receiver_id', 'user_id');
    }

    /**
     * Check if user is authorized to chat with another user.
     *
     * @param  int  $otherUserId
     * @return bool
     */
    public function canChatWith($otherUserId)
    {
        // Admin can chat with anyone
        if ($this->hasRole('Admin')) {
            return true;
        }

        // Check if there's an active consultation
        $hasConsultation = \App\Models\Consultation::where('status', 'active')
            ->where('end_time', '>', now())
            ->where(function ($q) use ($otherUserId) {
                $q->where(function ($sub) use ($otherUserId) {
                    $sub->where('client_id', $this->user_id)
                        ->where('nutritionist_id', $otherUserId);
                })->orWhere(function ($sub) use ($otherUserId) {
                    $sub->where('client_id', $otherUserId)
                        ->where('nutritionist_id', $this->user_id);
                });
            })->exists();

        if ($hasConsultation) {
            return true;
        }

        // Check if other user has sent a message before (so we can reply)
        $hasReceivedMessage = Message::where('sender_id', $otherUserId)
            ->where('receiver_id', $this->user_id)
            ->exists();

        if ($hasReceivedMessage) {
            return true;
        }

        // Restaurant Manager can send to anyone who sent them a message
        if ($this->hasRole('Restaurant Manager')) {
            return $hasReceivedMessage;
        }

        return false;
    }

    /**
     * Relationship with Restaurant (Manager)
     */
    public function managedRestaurant(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Restaurant::class, 'manager_id', 'user_id');
    }
}
