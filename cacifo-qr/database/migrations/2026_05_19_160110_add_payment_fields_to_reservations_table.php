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
            // Armazena o valor cobrado com 3 casas decimais (ex: 1.255)
            $table->decimal('amount_paid', 8, 3)->nullable()->after('status');

            // Regista o método utilizado
            $table->string('payment_method')->nullable()->after('amount_paid');

            // Estado do pagamento 
            $table->string('payment_status')->default('pending')->after('payment_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn(['amount_paid', 'payment_method', 'payment_status']);
        });
    }
};
