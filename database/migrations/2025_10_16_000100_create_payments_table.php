<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('payments', function (Blueprint $t) {
            $t->id();
            $t->string('order_id')->unique();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->foreignId('tenant_id')->nullable()->constrained('tenants')->nullOnDelete();
            $t->foreignId('room_id')->nullable()->constrained('rooms')->nullOnDelete();

            $t->unsignedBigInteger('amount');          // rupiah
            $t->string('status')->default('pending');  // pending|paid|expire|cancel|failed
            $t->string('payment_type')->nullable();    // qris|...
            $t->string('gateway')->default('midtrans');

            $t->string('snap_token')->nullable();
            $t->string('snap_redirect_url')->nullable();

            $t->json('raw_notification')->nullable();
            $t->timestamp('paid_at')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('payments');
    }
};
