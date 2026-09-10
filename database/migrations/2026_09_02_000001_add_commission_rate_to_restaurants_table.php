<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('restaurants') && !Schema::hasColumn('restaurants', 'commission_rate')) {
            Schema::table('restaurants', function (Blueprint $table) {
                $table->decimal('commission_rate', 5, 2)->default(10.00)->comment('نسبة عمولة المنصة من المطعم %');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('restaurants') && Schema::hasColumn('restaurants', 'commission_rate')) {
            Schema::table('restaurants', function (Blueprint $table) {
                $table->dropColumn('commission_rate');
            });
        }
    }
};
