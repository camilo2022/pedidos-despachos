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
        Schema::create('order_packings', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(OrderDispatch::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('packing_user_id');
            $table->enum('packing_status', ['En curso', 'Aprobado'])->default('En curso');
            $table->datetime('packing_date')->nullable();
            $table->foreign('packing_user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('order_packings');
    }
};
