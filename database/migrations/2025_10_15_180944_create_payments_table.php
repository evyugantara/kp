<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bill_id')->constrained('bills')->cascadeOnDelete();

            // metode pembayaran
            $table->enum('method', ['BANK','QRIS'])->index();

            // integrasi gateway
            $table->string('gateway')->nullable();     // 'midtrans' | 'xendit' | 'tripay' | manual
            $table->string('external_id')->nullable(); // id dari gateway
            $table->string('va_number')->nullable();   // kalau BANK (VA)
            $table->string('qris_ref')->nullable();    // kalau QRIS (ref/qr string)

            // nominal & status
            $table->unsignedInteger('amount'); // dibayar (Rp)
            $table->enum('status', ['PENDING','PAID','FAILED','EXPIRED'])->default('PENDING')->index();

            // waktu & raw callback untuk audit
            $table->timestamp('paid_at')->nullable();
            $table->json('raw_callback')->nullable();

            $table->timestamps();

            $table->index(['external_id','gateway']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('payments');
    }
};
