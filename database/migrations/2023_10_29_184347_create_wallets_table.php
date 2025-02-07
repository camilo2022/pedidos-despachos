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
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->string('number_document')->unique();
            $table->unsignedBigInteger('zero_to_thirty')->default(0);
            $table->unsignedBigInteger('one_to_thirty')->default(0);
            $table->unsignedBigInteger('thirty_one_to_sixty')->default(0);
            $table->unsignedBigInteger('sixty_one_to_ninety')->default(0);
            $table->unsignedBigInteger('ninety_one_to_one_hundred_twenty')->default(0);
            $table->unsignedBigInteger('one_hundred_twenty_one_to_one_hundred_fifty')->default(0);
            $table->unsignedBigInteger('one_hundred_fifty_one_to_one_hundred_eighty_one')->default(0);
            $table->unsignedBigInteger('eldest_to_one_hundred_eighty_one')->default(0);
            $table->unsignedBigInteger('total')->default(0);
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
        Schema::dropIfExists('wallets');
    }
};
