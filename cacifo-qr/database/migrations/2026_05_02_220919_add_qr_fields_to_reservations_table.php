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
        Schema::table('reservations', function (Blueprint $table) {
            $table->string('qr_token')->nullable()->unique()->after('status');
            $table->dateTime('qr_expires_at')->nullable()->after('qr_token');
            $table->boolean('used')->default(false)->after('qr_expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn(['qr_token', 'qr_expires_at', 'used']);
        });
    }
};
