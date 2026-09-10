<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Create client_diets table
if (! Schema::hasTable('client_diets')) {
    Schema::create('client_diets', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('clients_id');
        $table->unsignedBigInteger('diets_id');
        $table->timestamps();

        $table->foreign('clients_id')
            ->references('clients_id')
            ->on('clients')
            ->onDelete('cascade');

        $table->foreign('diets_id')
            ->references('diets_id')
            ->on('diets')
            ->onDelete('cascade');

        $table->unique(['clients_id', 'diets_id']);
    });

    echo "✅ Table 'client_diets' created successfully!\n";
} else {
    echo "ℹ️ Table 'client_diets' already exists.\n";
}
