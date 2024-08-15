<?php

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderDispatch;
use App\Models\User;
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
        Schema::create('order_dispatch_details', function (Blueprint $table) {
            $table->id()->comment('Identificador del detalle de la orden de despacho.');
            $table->foreignIdFor(OrderDispatch::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignIdFor(Order::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignIdFor(OrderDetail::class)->constrained()->onUpdate('cascade')->onDelete('cascade')->unique();
            $table->unsignedBigInteger('T04')->default(0);
            $table->unsignedBigInteger('T06')->default(0);
            $table->unsignedBigInteger('T08')->default(0);
            $table->unsignedBigInteger('T10')->default(0);
            $table->unsignedBigInteger('T12')->default(0);
            $table->unsignedBigInteger('T14')->default(0);
            $table->unsignedBigInteger('T16')->default(0);
            $table->unsignedBigInteger('T18')->default(0);
            $table->unsignedBigInteger('T20')->default(0);
            $table->unsignedBigInteger('T22')->default(0);
            $table->unsignedBigInteger('T24')->default(0);
            $table->unsignedBigInteger('T26')->default(0);
            $table->unsignedBigInteger('T28')->default(0);
            $table->unsignedBigInteger('T30')->default(0);
            $table->unsignedBigInteger('T32')->default(0);
            $table->unsignedBigInteger('T34')->default(0);
            $table->unsignedBigInteger('T36')->default(0);
            $table->unsignedBigInteger('T38')->default(0);
            $table->unsignedBigInteger('TXXS')->default(0);
            $table->unsignedBigInteger('TXS')->default(0);
            $table->unsignedBigInteger('TS')->default(0);
            $table->unsignedBigInteger('TM')->default(0);
            $table->unsignedBigInteger('TL')->default(0);
            $table->unsignedBigInteger('TXL')->default(0);
            $table->unsignedBigInteger('TXXL')->default(0);
            $table->foreignIdFor(User::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->enum('status', ['Pendiente', 'Cancelado', 'Alistamiento', 'Revision', 'Empacado', 'Facturacion', 'Despachado'])->default('Pendiente')->comment('Estado del detalle de la orden de despacho.');
            $table->datetime('date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_dispatch_details');
    }
};
