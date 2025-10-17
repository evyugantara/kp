<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');         // akun login penghuni
            $table->unsignedBigInteger('room_id');         // kamar yang ditempati
            $table->string('phone', 30)->nullable();
            $table->string('nik', 30)->nullable();
            $table->string('alamat', 255)->nullable();
            $table->date('tanggal_masuk')->nullable();
            $table->date('tanggal_keluar')->nullable();
            $table->string('status', 20)->default('Aktif'); // Aktif | Booking | Selesai
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('room_id')->references('id')->on('rooms')->onDelete('cascade');

            $table->index(['room_id','status']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('tenants');
    }
};
