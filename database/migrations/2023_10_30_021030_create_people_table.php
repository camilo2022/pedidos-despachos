<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('people', function (Blueprint $table) {
            $table->id();
            $table->string('client_number_document');
            $table->enum('type', ['REFERENCIA PERSONAL', 'SERVICIO AL CLIENTE', 'SOPORTE TECNICO', 'VENTAS', 'FACTURACION', 'RECURSOS HUMANOS', 'MARKETING', 'COMPRAS', 'CARTERA', 'BODEGA', 'ADMINISTRADOR'])->nullable();
            $table->string('name');
            $table->string('last_name');
            $table->string('phone_number');
            $table->string('email');
            $table->index(['client_number_document', 'type'])->unique();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('people');
    }
};
