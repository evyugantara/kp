<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // penghuni
            $table->foreignId('room_id')->constrained('rooms')->cascadeOnDelete();
            $table->string('period', 7)->index(); // "2025-10" (YYYY-MM)
            $table->unsignedInteger('amount');    // total ditagihkan (Rp)
            $table->unsignedInteger('paid_amount')->default(0);
            $table->date('due_date')->nullable();
            $table->enum('status', ['UNPAID','PAID','OVERDUE'])->default('UNPAID');
            $table->string('notes')->nullable();
            $table->timestamps();

            $table->unique(['user_id','room_id','period']); // proteksi double tagihan per bulan
        });
    }

    public function down(): void {
        Schema::dropIfExists('bills');
    }
};
