<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Pastikan tabel ada
        if (!Schema::hasTable('announcements')) {
            Schema::create('announcements', function (Blueprint $table) {
                $table->id();
                $table->string('judul', 180)->nullable(); // nullable dulu supaya bisa backfill
                $table->text('isi')->nullable();
                $table->timestamp('starts_at')->nullable();
                $table->timestamp('ends_at')->nullable();
                $table->boolean('is_published')->default(true);
                $table->foreignId('created_by')->nullable();
                $table->timestamps();
            });
        } else {
            // Tambahkan kolom yang kurang (ADD-ONLY)
            Schema::table('announcements', function (Blueprint $table) {
                if (!Schema::hasColumn('announcements', 'judul')) {
                    $table->string('judul', 180)->nullable()->after('id');
                }
                if (!Schema::hasColumn('announcements', 'isi')) {
                    $table->text('isi')->nullable()->after('judul');
                }
                if (!Schema::hasColumn('announcements', 'starts_at')) {
                    $table->timestamp('starts_at')->nullable()->after('isi');
                }
                if (!Schema::hasColumn('announcements', 'ends_at')) {
                    $table->timestamp('ends_at')->nullable()->after('starts_at');
                }
                if (!Schema::hasColumn('announcements', 'is_published')) {
                    $table->boolean('is_published')->default(true)->after('ends_at');
                }
                if (!Schema::hasColumn('announcements', 'created_by')) {
                    $table->foreignId('created_by')->nullable()->after('is_published');
                }
                // timestamps jika belum ada
                if (!Schema::hasColumn('announcements', 'created_at') || !Schema::hasColumn('announcements', 'updated_at')) {
                    $table->timestamps();
                }
            });
        }

        // Backfill dari kolom lama jika ada (tanpa perlu doctrine/dbal)
        $cols = Schema::getColumnListing('announcements');
        $hasTitle   = in_array('title', $cols, true);
        $hasContent = in_array('content', $cols, true);

        if ($hasTitle && in_array('judul', $cols, true)) {
            DB::statement("UPDATE announcements SET judul = COALESCE(judul, title)");
        }
        if ($hasContent && in_array('isi', $cols, true)) {
            DB::statement("UPDATE announcements SET isi = COALESCE(isi, content)");
        }

        // Setelah backfill, kalau ada judul/isi NULL, jadikan string kosong biar aman dengan validasi
        DB::statement("UPDATE announcements SET judul = '' WHERE judul IS NULL");
        DB::statement("UPDATE announcements SET isi   = '' WHERE isi   IS NULL");

        // Tambahkan foreign key created_by → users.id (diamkan jika sudah ada)
        try {
            Schema::table('announcements', function (Blueprint $table) {
                // MySQL akan error jika FK sudah ada—biarkan try/catch
                $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            });
        } catch (\Throwable $e) {
            // abaikan jika sudah ada constraint
        }
    }

    public function down(): void
    {
        // Tidak menghapus tabel agar aman. Bila ingin revert manual, lakukan di DB.
    }
};
