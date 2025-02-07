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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id()->comment('Identificador del metodo de pago.');
            $table->string('name')->unique()->comment('Nombre del metodo de pago.');
            $table->boolean('is_cash')->default(false);
            $table->boolean('is_libranza')->default(false);
            $table->boolean('is_transfer')->default(false);
            $table->boolean('is_card')->default(false);
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
        Schema::dropIfExists('payment_methods');
    }
};
