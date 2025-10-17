<?php
// ======================================================================
// FILE: database/migrations/2025_10_16_000000_create_rooms_table.php (NEW)
//  (Timestamp di nama file boleh beda; yang penting isi sama.)
// ======================================================================

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();          // misal: A1, B-02
            $table->string('nama');                    // Kamar A1
            $table->unsignedInteger('harga');          // rupiah / bulan
            $table->boolean('tersedia')->default(true);
            $table->json('fasilitas')->nullable();     // ["AC","KM Dalam","Wifi"]
            $table->text('deskripsi')->nullable();
            $table->string('foto_path')->nullable();   // storage/app/public/rooms/xxx.jpg
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('rooms');
    }
};
