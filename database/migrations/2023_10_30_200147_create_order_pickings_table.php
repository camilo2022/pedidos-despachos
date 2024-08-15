<?php

use App\Models\OrderDispatch;
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
        Schema::create('order_pickings', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(OrderDispatch::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('picking_user_id');
            $table->enum('picking_status', ['En curso', 'Cancelado', 'Revision', 'Aprobado'])->default('En curso');
            $table->datetime('picking_date')->nullable();
            $table->foreign('picking_user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('order_pickings');
    }
};
