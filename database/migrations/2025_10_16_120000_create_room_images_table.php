<?php
// ======================================================================
// FILE: database/migrations/2025_10_16_120000_create_room_images_table.php  (NEW)
// Buat tabel galeri gambar untuk setiap kamar
// ======================================================================

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('room_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('rooms')->cascadeOnDelete();
            $table->string('path');           // storage path (public disk)
            $table->unsignedInteger('sort')->default(0); // urutan
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('room_images');
    }
};
