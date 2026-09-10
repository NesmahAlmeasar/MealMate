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
        // Drop columns from consultations table
        Schema::table('consultations', function (Blueprint $table) {
            $columnsToDrop = [];
            
            if (Schema::hasColumn('consultations', 'reminder_date')) {
                $columnsToDrop[] = 'reminder_date';
            }
            if (Schema::hasColumn('consultations', 'reminder_sent')) {
                $columnsToDrop[] = 'reminder_sent';
            }
            if (Schema::hasColumn('consultations', 'client_complaint')) {
                $columnsToDrop[] = 'client_complaint';
            }
            if (Schema::hasColumn('consultations', 'doctor_diagnosis')) {
                $columnsToDrop[] = 'doctor_diagnosis';
            }

            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });

        // Drop certificate table if it exists (singular)
        if (Schema::hasTable('certificate')) {
            Schema::drop('certificate');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consultations', function (Blueprint $table) {
            // Restore columns (nullable for safety)
            $table->dateTime('reminder_date')->nullable();
            $table->boolean('reminder_sent')->default(false);
            $table->text('client_complaint')->nullable();
            $table->text('doctor_diagnosis')->nullable();
        });

        // Cannot easily restore 'certificate' table without knowing its schema
        // Assuming it's not needed as it was "extra"
    }
};
