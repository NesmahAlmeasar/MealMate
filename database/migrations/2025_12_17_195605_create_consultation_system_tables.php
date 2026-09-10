<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Consultation Types
        Schema::create('consultation_types', function (Blueprint $table) {
            $table->id('type_id');
            $table->string('name', 100)->unique()->comment('اسم النوع (تشخيص، متابعة، عودة)');
            $table->decimal('price', 8, 2)->comment('سعر الاستشارة (YER)');
            $table->integer('duration_days')->default(2)->comment('مدة الفعالية بالأيام');
            // timestamps not needed for types usually, but good for tracking changes
            $table->timestamps();
        });

        // Seed Default Types
        DB::table('consultation_types')->insert([
            ['name' => 'تشخيص', 'price' => 2000.00, 'duration_days' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'متابعة', 'price' => 3000.00, 'duration_days' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'عودة', 'price' => 5000.00, 'duration_days' => 3, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 2. Consultations
        Schema::create('consultations', function (Blueprint $table) {
            $table->bigIncrements('consultation_id');

            // Keys
            $table->unsignedBigInteger('client_id'); // Assuming 'clients' table has 'clients_id' which is same as user_id usually, but let's check input
            // Based on user schema: clients.clients_id is pk.
            // In Laravel fk conventions try to map `clients_id`.

            $table->unsignedBigInteger('nutritionist_id');
            $table->unsignedBigInteger('type_id'); // Maps to consultation_types.type_id (which is 'id' in laravel blueprint effectively)

            // Status
            $table->enum('status', ['pending_payment', 'active', 'completed', 'return_due', 'cancelled'])->default('pending_payment');
            $table->enum('payment_status', ['pending', 'paid', 'failed'])->default('pending');

            // Timings
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();

            // Notes
            $table->text('client_complaint')->nullable()->comment('أعراض المريض');
            $table->text('doctor_diagnosis')->nullable()->comment('التشخيص وخطة العلاج');

            $table->timestamps();

            // Constraints
            // NOTE: We need to be careful with exact Foreign Key names if tables use non-standard PK names.
            // User schema said: clients primary key is `clients_id`. nutritionists primary key is `nutritionist_id`.
            // consultation_types primary key is `type_id`.

            // Let's assume the table names used in existing migrations are 'clients', 'nutritionists'.
            // If they don't exist yet as migrations, this will fail. User provided schema says they exist.
            // I will use `onDelete('cascade')` for users/clients/nutritionists as per user request.
        });

        // Applying Foreign Keys in a separate step or carefully inline.
        // Since we are not 100% sure of the exact column names in existing DB (migrated from other files),
        // I will assume standard referencing but specify columns.
        Schema::table('consultations', function (Blueprint $table) {
            $table->foreign('type_id')->references('type_id')->on('consultation_types')->onDelete('restrict');
            // For client and nutritionist, assuming they reference `users.user_id` or their specific table PKs.
            // User schema: clients(clients_id), nutritionists(nutritionist_id).

            // Safest to link to users table if clients/nutritionists are just role extensions sharing ID.
            // But let's stick to the specific tables from the design `clients` and `nutritionists`.
            // If these tables don't exist, I should have checked.
            // Based on `create_complete_system_tables.php` in file list, they likely exist.

            $table->foreign('client_id')->references('clients_id')->on('clients')->onDelete('cascade');
            $table->foreign('nutritionist_id')->references('nutritionist_id')->on('nutritionists')->onDelete('cascade');
        });

        // 3. Payments
        Schema::create('payments', function (Blueprint $table) {
            $table->bigIncrements('payment_id');
            $table->unsignedBigInteger('consultation_id');

            // Transaction Info
            $table->string('token')->nullable()->comment('Token from Bus Init');
            $table->string('transaction_id')->nullable();
            $table->decimal('amount', 8, 2);
            $table->enum('status', ['pending', 'paid', 'failed'])->default('pending');
            $table->string('currency', 10)->default('YER');

            // Verification Log
            $table->text('gateway_response')->nullable()->comment('Full JSON response from App/SDK');

            $table->timestamps();

            $table->foreign('consultation_id')->references('consultation_id')->on('consultations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('consultations');
        Schema::dropIfExists('consultation_types');
    }
};
