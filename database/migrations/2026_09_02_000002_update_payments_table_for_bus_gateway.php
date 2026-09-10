<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('payments')) {
            Schema::table('payments', function (Blueprint $table) {
                // Make consultation_id nullable if it exists
                if (Schema::hasColumn('payments', 'consultation_id')) {
                    $table->unsignedBigInteger('consultation_id')->nullable()->change();
                }

                if (!Schema::hasColumn('payments', 'cart_id')) {
                    $table->unsignedBigInteger('cart_id')->nullable()->after('payment_id');
                }

                if (!Schema::hasColumn('payments', 'payable_type')) {
                    $table->string('payable_type', 50)->default('CART')->after('cart_id')->comment('CART, CONSULTING, WALLET_REFILL');
                }

                if (!Schema::hasColumn('payments', 'payable_id')) {
                    $table->unsignedBigInteger('payable_id')->nullable()->after('payable_type');
                }

                if (!Schema::hasColumn('payments', 'user_id')) {
                    $table->unsignedBigInteger('user_id')->nullable()->after('payable_id');
                }

                if (!Schema::hasColumn('payments', 'payment_method')) {
                    $table->string('payment_method', 50)->default('BUS_GATEWAY')->after('currency');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('payments')) {
            Schema::table('payments', function (Blueprint $table) {
                $columnsToDrop = [];
                foreach (['cart_id', 'payable_type', 'payable_id', 'user_id', 'payment_method'] as $col) {
                    if (Schema::hasColumn('payments', $col)) {
                        $columnsToDrop[] = $col;
                    }
                }
                if (!empty($columnsToDrop)) {
                    $table->dropColumn($columnsToDrop);
                }
            });
        }
    }
};
