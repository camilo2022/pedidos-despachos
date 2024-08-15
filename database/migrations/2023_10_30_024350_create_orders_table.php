<?php

use App\Models\Business;
use App\Models\Client;
use App\Models\Correria;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::create('orders', function (Blueprint $table) {
            $table->id()->comment('Identificador del pedido.');
            /* $table->unsignedBigInteger('client_id')->comment('Identificador del cliente.');
            $table->unsignedBigInteger('client_branch_id')->comment('Identificador de la sucursal del cliente.');
            $table->unsignedBigInteger('transporter_id')->comment('Identificador de la transportadora del pedido');
            $table->unsignedBigInteger('sale_channel_id')->comment('Identificador del canal de venta del pedido'); */
            $table->foreignIdFor(Client::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->enum('dispatch_type', ['De inmediato', 'Antes de', 'Despues de', 'Total', 'Semanal'])->comment('Cuando despachar.');
            $table->date('dispatch_date')->nullable()->comment('Fecha de cuando despachar.');
            $table->unsignedBigInteger('seller_user_id')->comment('Identificador del usuario de vendedor.');
            $table->enum('seller_status', ['Pendiente', 'Cancelado', 'Aprobado'])->default('Pendiente')->comment('Estado del vendedor.');
            $table->datetime('seller_date')->nullable()->comment('Fecha del vendedor.');
            $table->longText('seller_observation')->nullable()->comment('Observacion del vendedor.');
            $table->unsignedBigInteger('seller_dispatch_official')->comment('Condicion de despacho OFC vendedor');
            $table->unsignedBigInteger('seller_dispatch_document')->comment('Condicion de despacho DCO vendedor');
            $table->unsignedBigInteger('wallet_user_id')->nullable()->comment('Identificador del usuario de cartera.');
            $table->enum('wallet_status', ['Pendiente', 'Cancelado', 'Suspendido', 'En mora', 'Parcialmente Aprobado', 'Aprobado', 'Autorizado'])->default('Pendiente')->comment('Estado de cartera.');
            $table->datetime('wallet_date')->nullable()->comment('Fecha de cartera');
            $table->longText('wallet_observation')->nullable()->comment('Observacion de cartera');
            $table->unsignedBigInteger('wallet_dispatch_official')->nullable()->comment('Condicion de despacho OFC cartera');
            $table->unsignedBigInteger('wallet_dispatch_document')->nullable()->comment('Condicion de despacho DCO cartera');
            $table->enum('dispatch_status', ['Pendiente', 'Cancelado', 'Parcialmente Aprobado', 'Aprobado', 'Parcialmente Despachado', 'Despachado'])->default('Pendiente')->comment('Estado de despacho.');
            $table->datetime('dispatched_date')->nullable()->comment('Fecha de despacho.');
            /* $table->unsignedBigInteger('correria_id')->comment('Identificador de la correria.'); */
            $table->foreignIdFor(Correria::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignIdFor(Business::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            /* $table->foreign('client_id')->references('id')->on('clients')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('client_branch_id')->references('id')->on('client_branches')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('transporter_id')->references('id')->on('transporters')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('sale_channel_id')->references('id')->on('sale_channels')->onUpdate('cascade')->onDelete('cascade'); */
            $table->foreign('seller_user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('wallet_user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            /* $table->foreign('correria_id')->references('id')->on('correrias')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('business_id')->references('id')->on('businesses')->onUpdate('cascade')->onDelete('cascade'); */
            $table->timestamps();
        });

        DB::unprepared('DROP PROCEDURE IF EXISTS order_seller_status');

        DB::unprepared('
            CREATE PROCEDURE order_seller_status(IN order_id INT)
            BEGIN
                DECLARE totalCancelados INT;
                DECLARE totalDetalles INT;

                SELECT COUNT(*) INTO totalCancelados FROM order_details WHERE order_details.order_id = order_id AND status IN ("Agotado", "Cancelado");
                SELECT COUNT(*) INTO totalDetalles FROM order_details WHERE order_details.order_id = order_id;

                IF totalCancelados = totalDetalles THEN
                    UPDATE orders SET seller_status = "Cancelado", wallet_status = "Cancelado", dispatch_status = "Cancelado" WHERE id = order_id;
                END IF;
            END
        ');

        DB::unprepared('DROP PROCEDURE IF EXISTS order_wallet_status');
        
        DB::unprepared('
            CREATE PROCEDURE order_wallet_status(IN order_id INT)
            BEGIN
                DECLARE totalPendiente INT;
                DECLARE totalCanceladoAgotado INT;
                DECLARE totalAprobado INT;
                DECLARE totalSuspendido INT;
                DECLARE totalComprometido INT;
                DECLARE totalDespachado INT;
                DECLARE totalDetalles INT;

                SELECT COUNT(*) INTO totalPendiente FROM order_details WHERE order_details.order_id = order_id AND status = "Pendiente";
                SELECT COUNT(*) INTO totalCanceladoAgotado FROM order_details WHERE order_details.order_id = order_id AND status IN ("Cancelado", "Agotado");
                SELECT COUNT(*) INTO totalAprobado FROM order_details WHERE order_details.order_id = order_id AND status = "Aprobado";
                SELECT COUNT(*) INTO totalSuspendido FROM order_details WHERE order_details.order_id = order_id AND status = "Suspendido";
                SELECT COUNT(*) INTO totalComprometido FROM order_details WHERE order_details.order_id = order_id AND status = "Comprometido";
                SELECT COUNT(*) INTO totalDespachado FROM order_details WHERE order_details.order_id = order_id AND status = "Despachado";
                SELECT COUNT(*) INTO totalDetalles FROM order_details WHERE order_details.order_id = order_id;

                IF totalCanceladoAgotado = totalDetalles THEN
                    UPDATE orders SET wallet_status = "Cancelado", dispatch_status = "Cancelado" WHERE id = order_id;
                ELSEIF totalSuspendido = (totalDetalles - totalDespachado - totalComprometido - totalCanceladoAgotado) THEN
                    UPDATE orders SET wallet_status = "Suspendido" WHERE id = order_id;
                ELSEIF totalPendiente > 0 THEN
                    UPDATE orders SET wallet_status = "Parcialmente Aprobado" WHERE id = order_id;
                ELSEIF (totalPendiente + totalSuspendido) = 0 THEN
                    UPDATE orders SET wallet_status = "Aprobado" WHERE id = order_id;
                ELSEIF (totalDespachado + totalComprometido + totalAprobado) = (totalDetalles - totalPendiente - totalCanceladoAgotado - totalSuspendido) THEN
                    UPDATE orders SET wallet_status = "Aprobado" WHERE id = order_id;
                ELSE
                    UPDATE orders SET wallet_status = "Parcialmente Aprobado" WHERE id = order_id;
                END IF;
            END
        ');

        DB::unprepared('DROP PROCEDURE IF EXISTS order_dispatch_status');

        DB::unprepared('
            CREATE PROCEDURE order_dispatch_status(IN order_id INT)
            BEGIN
                DECLARE totalComprometido INT;
                DECLARE totalAprobado INT;
                DECLARE totalDespachado INT;
                DECLARE totalDetalles INT;

                SELECT COUNT(*) INTO totalComprometido FROM order_details WHERE order_details.order_id = order_id AND status = "Comprometido";
                SELECT COUNT(*) INTO totalAprobado FROM order_details WHERE order_details.order_id = order_id AND status = "Aprobado";
                SELECT COUNT(*) INTO totalDespachado FROM order_details WHERE order_details.order_id = order_id AND status = "Despachado";
                SELECT COUNT(*) INTO totalDetalles FROM order_details WHERE order_details.order_id = order_id AND status NOT IN ("Agotado", "Cancelado");

                IF totalDespachado = totalDetalles THEN
                    UPDATE orders SET dispatch_status = "Despachado", dispatched_date = NOW() WHERE id = order_id;
                ELSEIF totalDespachado > 0 THEN
                    UPDATE orders SET dispatch_status = "Parcialmente Despachado" WHERE id = order_id;
                ELSEIF totalComprometido = totalDetalles THEN
                    UPDATE orders SET dispatch_status = "Aprobado" WHERE id = order_id;
                ELSEIF totalComprometido > 0 THEN
                    UPDATE orders SET dispatch_status = "Parcialmente Aprobado" WHERE id = order_id;
                ELSE
                    UPDATE orders SET dispatch_status = "Pendiente" WHERE id = order_id;
                END IF;
            END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
