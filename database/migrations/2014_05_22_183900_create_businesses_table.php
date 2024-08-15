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
        Schema::create('businesses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('branch');
            $table->string('number_document');
            $table->string('country');
            $table->string('departament');
            $table->string('city');
            $table->string('address');
            $table->longText('order_footer');
            $table->string('order_notify_email');
            $table->longText('dispatch_footer');
            $table->longText('packing_footer');
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
        Schema::dropIfExists('businesses');
    }
};
