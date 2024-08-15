<?php

use App\Models\OrderDispatchDetail;
use App\Models\OrderPackage;
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
        Schema::create('order_packing_details', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(OrderPackage::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignIdFor(OrderDispatchDetail::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('order_packing_details');
    }
};
