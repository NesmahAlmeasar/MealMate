<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Wallets Table
        if (!Schema::hasTable('wallets')) {
            Schema::create('wallets', function (Blueprint $table) {
                $table->id('wallet_id');
                $table->unsignedBigInteger('user_id')->unique();
                $table->decimal('balance', 12, 2)->default(0.00)->comment('الرصيد المتاح حالياً');
                $table->decimal('total_earned', 12, 2)->default(0.00)->comment('إجمالي الأرباح التراكمية');
                $table->decimal('total_withdrawn', 12, 2)->default(0.00)->comment('إجمالي ما تم سحبه فعلياً');
                $table->decimal('pending_withdrawal', 12, 2)->default(0.00)->comment('مبالغ قيد طلب السحب');
                $table->timestamps();

                $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            });
        }

        // 2. Ledger Entries Table
        if (!Schema::hasTable('ledger_entries')) {
            Schema::create('ledger_entries', function (Blueprint $table) {
                $table->id('entry_id');
                $table->string('transaction_ref', 100);
                $table->unsignedBigInteger('debit_user_id')->comment('الحساب المدين (من خصم منه)');
                $table->unsignedBigInteger('credit_user_id')->comment('الحساب الدائن (من أضيف له)');
                $table->decimal('amount', 12, 2);
                $table->enum('entry_type', [
                    'MEAL_COMMISSION',
                    'CONSULTATION_FEE',
                    'CONSULTATION_PLATFORM',
                    'WALLET_REFILL',
                    'DEBT_SETTLEMENT',
                    'PENALTY_DEDUCTION',
                    'PARTNER_DIVIDEND',
                    'PAYOUT_WITHDRAWAL',
                    'SYSTEM_EXPENSE'
                ]);
                $table->text('description')->nullable();
                $table->unsignedBigInteger('created_by_admin_id')->nullable();
                $table->timestamp('created_at')->useCurrent();

                $table->foreign('debit_user_id')->references('user_id')->on('users')->onDelete('cascade');
                $table->foreign('credit_user_id')->references('user_id')->on('users')->onDelete('cascade');
                $table->foreign('created_by_admin_id')->references('user_id')->on('users')->onDelete('set null');
            });
        }

        // 3. Partner Shares Table
        if (!Schema::hasTable('partner_shares')) {
            Schema::create('partner_shares', function (Blueprint $table) {
                $table->id('share_id');
                $table->unsignedBigInteger('partner_user_id');
                $table->decimal('share_percentage', 5, 2)->comment('نسبة الشريك %');
                $table->timestamps();

                $table->foreign('partner_user_id')->references('user_id')->on('users')->onDelete('cascade');
            });
        }

        // 4. System Expenses Table
        if (!Schema::hasTable('system_expenses')) {
            Schema::create('system_expenses', function (Blueprint $table) {
                $table->id('expense_id');
                $table->string('category', 100);
                $table->decimal('amount', 10, 2);
                $table->text('description')->nullable();
                $table->date('expense_date');
                $table->unsignedBigInteger('logged_by_admin_id');
                $table->timestamps();

                $table->foreign('logged_by_admin_id')->references('user_id')->on('users')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('system_expenses');
        Schema::dropIfExists('partner_shares');
        Schema::dropIfExists('ledger_entries');
        Schema::dropIfExists('wallets');
    }
};
