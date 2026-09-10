<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Fix role name case sensitivity issue
        DB::table('roles')
            ->where('role_id', 5)
            ->update([
                'name' => 'Restaurant Manager', // Capital M
                'description' => 'مدير مطعم، يقوم بإدارة المطعم الخاص به وطلباته ووجباته.',
                'updated_at' => now(),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('roles')
            ->where('role_id', 5)
            ->update([
                'name' => 'Restaurant Manager', // lowercase m
                'updated_at' => now(),
            ]);
    }
};
