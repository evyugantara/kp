<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('order_id')->unique()->after('id');
            $table->foreignId('user_id')->after('bill_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tenant_id')->nullable()->after('user_id')->constrained('tenants')->nullOnDelete();
            $table->foreignId('room_id')->nullable()->after('tenant_id')->constrained('rooms')->nullOnDelete();
            $table->string('payment_type')->nullable()->after('amount');
            $table->string('snap_token')->nullable()->after('gateway');
            $table->string('snap_redirect_url')->nullable()->after('snap_token');
            $table->json('raw_notification')->nullable()->after('snap_redirect_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['room_id']);
            $table->dropForeign(['tenant_id']);
            $table->dropForeign(['user_id']);
            $table->dropColumn([
                'order_id', 'user_id', 'tenant_id', 'room_id',
                'payment_type', 'snap_token', 'snap_redirect_url', 'raw_notification'
            ]);
        });
    }
};
