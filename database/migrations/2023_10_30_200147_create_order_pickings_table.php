<?php

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
        Schema::create('order_pickings', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(OrderDispatch::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignIdFor(User::class, 'picking_user_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->enum('picking_status', ['En curso', 'Cancelado', 'Revision', 'Aprobado'])->default('En curso');
            $table->datetime('picking_date')->nullable();
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
