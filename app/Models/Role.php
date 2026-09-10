<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    // تحديد اسم المفتاح الرئيسي (لأنه ليس id الافتراضي)
    protected $primaryKey = 'role_id';
    // إذا كان اسم الجدول هو 'roles'، فهذا السطر اختياري
    // protected $table = 'roles';

    protected $keyType = 'integer'; // المفتاح الأساسي هو int/integer

    public $incrementing = true;

    // السماح بالتعبئة الجماعية لاسم الدور ووصفه
    protected $fillable = ['name', 'description'];

    /**
     * الدور يمكن أن يرتبط بعدة مستخدمين.
     */
    public function users(): BelongsToMany
    {
        // belongsToMany(النموذج المرتبط, جدول الربط, المفتاح المحلي, المفتاح المرتبط)
        return $this->belongsToMany(User::class, 'user_roles', 'role_id', 'user_id');
    }
}
