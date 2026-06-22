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
        Schema::create('detalle_facturas', function (Blueprint $table) {
            $table->id('detalle_id');
            $table->foreignId('factura_id')->references('factura_id')->on('facturas');
            $table->foreignId('producto_id')->references('producto_id')->on('productos');
            $table->integer('cantidad');
            $table->decimal('precio_venta', 12, 2);
            $table->decimal('subtotal_linea', 12, 2);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_facturas');
    }
};
