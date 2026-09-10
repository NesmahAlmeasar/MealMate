<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->text('description')->nullable()->after('name')->comment('وصف المطعم');
            $table->string('email')->nullable()->after('description')->comment('البريد الإلكتروني للمطعم');
            $table->string('state', 50)->default('active')->after('photo_url')->comment('حالة المطعم (active, inactive, pending)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn(['description', 'email', 'state']);
        });
    }
};
