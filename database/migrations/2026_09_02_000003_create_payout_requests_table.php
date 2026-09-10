<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('payout_requests')) {
            Schema::create('payout_requests', function (Blueprint $table) {
                $table->id('payout_id');
                $table->unsignedBigInteger('user_id')->comment('صاحب الطلب (أخصائي أو مطعم)');
                $table->decimal('amount', 10, 2);
                $table->string('bank_name', 100)->nullable();
                $table->string('account_number', 100)->nullable();
                $table->enum('status', ['pending', 'approved', 'rejected', 'completed'])->default('pending');
                $table->text('admin_notes')->nullable();
                $table->timestamp('requested_at')->useCurrent();
                $table->timestamp('processed_at')->nullable();
                $table->timestamps();

                $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('payout_requests');
    }
};
