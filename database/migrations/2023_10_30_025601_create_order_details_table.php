<?php

use App\Models\Color;
use App\Models\Order;
use App\Models\Product;
use App\Models\Tone;
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
        Schema::create('order_details', function (Blueprint $table) {
            $table->id()->comment('Identificador del detalle del pedido.');
            /* $table->unsignedBigInteger('order_id')->comment('Identificador del pedido.');
            $table->unsignedBigInteger('product_id')->comment('Identificador del producto.');
            $table->unsignedBigInteger('color_id')->comment('Identificador del color.'); */
            $table->foreignIdFor(Order::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignIdFor(Product::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignIdFor(Color::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->float('price', 8, 2)->comment('Valor de venta del producto.');
            $table->float('negotiated_price', 8, 2)->comment('Valor de negociado venta del producto.');
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
            $table->unsignedBigInteger('seller_user_id')->nullable()->comment('Identificador del usuario vendedor.');
            $table->datetime('seller_date')->comment('Fecha del vendedor');
            $table->longText('seller_observation')->nullable()->comment('Observacion del vendedor');
            $table->unsignedBigInteger('wallet_user_id')->nullable()->comment('Identificador del usuario de cartera.');
            $table->datetime('wallet_date')->nullable()->comment('Fecha de cartera.');
            $table->unsignedBigInteger('dispatch_user_id')->nullable()->comment('Identificador del usuario de despacho.');
            $table->datetime('dispatch_date')->nullable()->comment('Fecha de despacho');
            $table->enum('status', ['Pendiente', 'Cancelado', 'Aprobado', 'Autorizado', 'Agotado', 'Suspendido', 'Comprometido', 'Despachado'])->default('Pendiente')->comment('Estado del detalle del pedido.');
            /* $table->foreign('order_id')->references('id')->on('orders')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('color_id')->references('id')->on('colors')->onUpdate('cascade')->onDelete('cascade'); */
            $table->foreign('seller_user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('wallet_user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('dispatch_user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('order_details');
    }
};
