<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Jika tabel belum ada: buat dari nol
        if (!Schema::hasTable('announcements')) {
            Schema::create('announcements', function (Blueprint $table) {
                $table->id();
                $table->string('judul', 180);
                $table->text('isi');
                $table->timestamp('starts_at')->nullable();
                $table->timestamp('ends_at')->nullable();
                $table->boolean('is_published')->default(true);
                $table->foreignId('created_by')->nullable()->constrained('users')->cascadeOnDelete();
                $table->timestamps();
            });
            return;
        }

        // Jika tabel sudah ada: tambahkan kolom yang belum ada (ADD-ONLY)
        Schema::table('announcements', function (Blueprint $table) {
            if (!Schema::hasColumn('announcements', 'judul')) {
                $table->string('judul', 180)->after('id');
            }
            if (!Schema::hasColumn('announcements', 'isi')) {
                $table->text('isi')->after('judul');
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
            // timestamps kalau belum ada
            if (!Schema::hasColumn('announcements', 'created_at') || !Schema::hasColumn('announcements', 'updated_at')) {
                $table->timestamps();
            }
        });

        // Tambahkan FK created_by → users.id jika belum ada (diamkan bila sudah ada)
        try {
            Schema::table('announcements', function (Blueprint $table) {
                $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            });
        } catch (\Throwable $e) {
            // abaikan jika constraint sudah ada
        }
    }

    public function down(): void
    {
        // Rollback: hapus tabel jika ada (hati-hati: akan menghapus data pengumuman)
        if (Schema::hasTable('announcements')) {
            Schema::drop('announcements');
        }
    }
};
