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
        Schema::create('client_diets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('clients_id');
            $table->unsignedBigInteger('diets_id');
            $table->timestamps();

            // Foreign keys
            $table->foreign('clients_id')
                ->references('clients_id')
                ->on('clients')
                ->onDelete('cascade');

            $table->foreign('diets_id')
                ->references('diets_id')
                ->on('diets')
                ->onDelete('cascade');

            // Unique constraint to prevent duplicate entries
            $table->unique(['clients_id', 'diets_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_diets');
    }
};
