<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. جدول المستخدمين (Users) ⭐️ تم التعديل هنا ⭐️
        Schema::create('users', function (Blueprint $table) {
            $table->id('user_id');
            $table->string('phone', 20)->nullable()->unique();

            // ⭐️⭐️ الاسم الأول والأخير (Fname & Lname) ⭐️⭐️
            $table->string('Fname', 255)->notNullable(); // الاسم الأول
            $table->string('Lname', 255)->notNullable(); // الاسم الأخير

            $table->string('email')->nullable()->unique();
            $table->string('photo_url')->nullable();

            // تم حذف عمود الدور (Role) من هنا لأننا نستخدم جدول ربط منفصل
            $table->string('account_state', 50)->default('active');

            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        // 2. جدول الأدوار (Roles)
        Schema::create('roles', function (Blueprint $table) {
            $table->id('role_id');
            $table->string('name', 50)->unique();
            $table->string('description', 255)->nullable();
            $table->timestamps();
        });

        // 3. جدول الربط بين المستخدمين والأدوار (User-Roles)
        Schema::create('user_roles', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('role_id');

            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('role_id')->references('role_id')->on('roles')->onDelete('cascade');

            $table->primary(['user_id', 'role_id']);
        });

        // 4. جداول النظام (Password Reset Tokens & Sessions)
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_roles');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
