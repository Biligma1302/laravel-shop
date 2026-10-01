<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('daily_sales_reports', function (Blueprint $table) {
            $table->decimal('average_order_value', 10, 2)->default(0.00)->after('revenue');
        });
    }

    public function down(): void
    {
        Schema::table('daily_sales_reports', function (Blueprint $table) {
            $table->dropColumn('average_order_value');
        });
    }
};
