<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users','role')) {
                $table->string('role')->default('penghuni')->index(); // 'pengelola' | 'penghuni'
            }
            if (!Schema::hasColumn('users','phone')) {
                $table->string('phone')->nullable(); // WA pengelola/penghuni (62xxxxxxxxxx)
            }
        });
    }

    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users','role'))  $table->dropColumn('role');
            if (Schema::hasColumn('users','phone')) $table->dropColumn('phone');
        });
    }
};
