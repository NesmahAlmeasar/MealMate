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
        Schema::table('messages', function (Blueprint $table) {
            // Add soft delete
            $table->softDeletes();

            // Add indexes for performance
            $table->index(['sender_id', 'receiver_id', 'created_at'], 'messages_conversation_index');
            $table->index(['receiver_id', 'is_read'], 'messages_unread_index');
        });

        // Update foreign key constraints
        Schema::table('messages', function (Blueprint $table) {
            // Drop existing FKs to redefine them
            $table->dropForeign(['sender_id']);
            $table->dropForeign(['receiver_id']);

            $table->foreign('sender_id')
                ->references('user_id')
                ->on('users')
                ->onDelete('restrict'); // Changed from cascade

            $table->foreign('receiver_id')
                ->references('user_id')
                ->on('users')
                ->onDelete('restrict'); // Changed from cascade
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropIndex('messages_conversation_index');
            $table->dropIndex('messages_unread_index');

            // Restore original foreign keys
            $table->dropForeign(['sender_id']);
            $table->dropForeign(['receiver_id']);

            $table->foreign('sender_id')
                ->references('user_id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('receiver_id')
                ->references('user_id')
                ->on('users')
                ->onDelete('cascade');
        });
    }
};
