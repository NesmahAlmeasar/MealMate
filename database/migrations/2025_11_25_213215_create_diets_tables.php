<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Create nutritionists table
        Schema::create('nutritionists', function (Blueprint $table) {
            $table->unsignedBigInteger('nutritionist_id')->primary();
            $table->string('Academic_level', 100);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->foreign('nutritionist_id')->references('user_id')->on('users')->onDelete('cascade');
        });

        // 2. Create diets table
        Schema::create('diets', function (Blueprint $table) {
            $table->id('diets_id');
            $table->string('name');
            $table->string('photo_url')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_public')->default(true);
            $table->text('warning')->nullable();
            $table->text('advice')->nullable();
            $table->unsignedBigInteger('nutritionist_id')->nullable();
            $table->timestamps();

            $table->foreign('nutritionist_id')->references('nutritionist_id')->on('nutritionists')->onDelete('set null');
        });

        // 3. Create restrictions table
        Schema::create('restrictions', function (Blueprint $table) {
            $table->id('restriction_id');
            $table->text('restriction')->nullable();
            $table->string('field_name', 100);
            $table->string('operator', 10);
            $table->string('value', 255);
            $table->unsignedBigInteger('diets_id');
            $table->timestamps();

            $table->foreign('diets_id')->references('diets_id')->on('diets')->onDelete('cascade');
        });

        // 4. Create diet_meals pivot table
        Schema::create('diet_meals', function (Blueprint $table) {
            $table->id('diet_meals_id');
            $table->unsignedBigInteger('diets_id');
            $table->unsignedBigInteger('meals_id');
            $table->timestamps();

            $table->unique(['diets_id', 'meals_id']);
            $table->foreign('diets_id')->references('diets_id')->on('diets')->onDelete('cascade');
            $table->foreign('meals_id')->references('meals_id')->on('meals')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diet_meals');
        Schema::dropIfExists('restrictions');
        Schema::dropIfExists('diets');
        Schema::dropIfExists('nutritionists');
    }
};
