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
        Schema::create('clientes', function (Blueprint $table) {
            $table->id('cliente_id');
            $table->enum('tipo_documento', ['CC', 'NIT', 'CE', 'PP'])->default('CC');
            $table->string('numero_documento', 15)->unique();
            $table->string('nombre', 50);
            $table->string('apellido', 50);
            $table->string('telefono', 15)->nullable();
            $table->string('email', 100)->unique();
            $table->string('direccion', 150)->nullable();
            $table->string('ciudad', 50)->default('Bogotá');
            $table->timestamp('fecha_registro')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->foreignId('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
