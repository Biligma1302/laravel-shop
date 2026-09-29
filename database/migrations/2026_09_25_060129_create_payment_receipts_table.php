<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payment_receipts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_payment_id')->constrained()->onDelete('cascade');

            $table->string('external_receipt_id')->nullable(); // ID чека в ЮKassa
            $table->string('type')->default('payment'); // payment (оплата) или refund (возврат)
            $table->string('status')->default('pending'); // pending, delivered, canceled
            $table->string('send_to_customer')->nullable(); // email или телефон, куда ушел чек

            $table->json('request_payload')->nullable();
            $table->json('response_payload')->nullable();
            $table->text('error_message')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_receipts');
    }
};
