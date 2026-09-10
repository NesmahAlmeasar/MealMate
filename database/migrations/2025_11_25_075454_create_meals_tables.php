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
        // 1. Create category table
        Schema::create('category', function (Blueprint $table) {
            $table->id('category_id');
            $table->string('category_name', 100)->unique();
            $table->timestamps();
        });

        // 2. Create meals table
        Schema::create('meals', function (Blueprint $table) {
            $table->id('meals_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('photo_url')->nullable();
            $table->decimal('price', 8, 2);
            $table->string('state', 50)->default('pending'); // pending, approved, rejected
            $table->string('proper_time', 50)->nullable(); // breakfast, lunch, dinner
            $table->decimal('quantity_g', 8, 2)->nullable();
            $table->decimal('calories', 6, 2)->nullable();
            $table->decimal('protein_g', 6, 2)->nullable();
            $table->decimal('fat_g', 6, 2)->nullable();
            $table->decimal('carbs_g', 6, 2)->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->timestamps();

            $table->foreign('category_id')->references('category_id')->on('category')->onDelete('set null');
        });

        // 3. Create ingredients table
        Schema::create('ingredients', function (Blueprint $table) {
            $table->id('ingredients_id');
            $table->string('name_ar');
            $table->string('usda_term')->nullable();
            $table->boolean('is_vegan')->default(false);
            $table->boolean('has_gluten')->default(false);
            $table->boolean('has_dairy')->default(false);
            $table->decimal('calories', 6, 2)->nullable();
            $table->decimal('protein_g', 6, 2)->nullable();
            $table->decimal('fat_g', 6, 2)->nullable();
            $table->decimal('carbs_g', 6, 2)->nullable();
            $table->timestamps();
        });

        // 4. Create meals_ingredients pivot table
        Schema::create('meals_ingredients', function (Blueprint $table) {
            $table->unsignedBigInteger('meals_id');
            $table->unsignedBigInteger('ingredients_id');
            $table->decimal('quantity_g', 8, 2)->nullable(); // كمية المكون في الوجبة
            $table->primary(['meals_id', 'ingredients_id']);

            $table->foreign('meals_id')->references('meals_id')->on('meals')->onDelete('cascade');
            $table->foreign('ingredients_id')->references('ingredients_id')->on('ingredients')->onDelete('cascade');
        });

        // 5. Create allergies table (if not exists)
        if (! Schema::hasTable('allergies')) {
            Schema::create('allergies', function (Blueprint $table) {
                $table->id('allergies_id');
                $table->string('allergies', 100)->unique();
                $table->timestamps();
            });
        }

        // 6. Create ingredients_allergies pivot table
        Schema::create('ingredients_allergies', function (Blueprint $table) {
            $table->unsignedBigInteger('ingredients_id');
            $table->unsignedBigInteger('allergies_id');
            $table->primary(['ingredients_id', 'allergies_id']);

            $table->foreign('ingredients_id')->references('ingredients_id')->on('ingredients')->onDelete('cascade');
            $table->foreign('allergies_id')->references('allergies_id')->on('allergies')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ingredients_allergies');
        Schema::dropIfExists('meals_ingredients');
        Schema::dropIfExists('ingredients');
        Schema::dropIfExists('meals');
        Schema::dropIfExists('category');
        // Don't drop allergies table as it might be used by other parts of the system
    }
};
