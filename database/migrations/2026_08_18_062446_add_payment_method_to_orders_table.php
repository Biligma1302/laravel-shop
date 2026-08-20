<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Добавляем строковую колонку для метода оплаты (делаем ее nullable на случай старых записей)
            $table->string('payment_method')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Удаляем колонку, если потребуется откатить миграцию
            $table->dropColumn('payment_method');
        });
    }
};
