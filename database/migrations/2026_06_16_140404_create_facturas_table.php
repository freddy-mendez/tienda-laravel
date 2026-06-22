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
        Schema::create('facturas', function (Blueprint $table) {
            $table->id('factura_id');
            $table->string('numero_factura', 20)->unique();
            $table->dateTime('fecha_emision')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->foreignId('cliente_id')->references('cliente_id')->on('clientes');
            $table->decimal('subtotal', 12, 2);
            $table->decimal('total_iva', 12, 2);
            $table->decimal('total_pagar', 12, 2);
            $table->enum('metodo_pago', ['Efectivo', 'Tarjeta', 'Transferencia', 'Nequi/Daviplata'])->default('Efectivo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facturas');
    }
};
