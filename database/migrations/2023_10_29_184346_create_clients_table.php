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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address')->default('N/A');
            $table->string('number_document');
            $table->string('cell_phone_number')->nullable();
            $table->string('branch_code')->default('001');
            $table->string('branch_name')->nullable();
            $table->string('branch_address')->nullable();
            $table->string('branch_number_phone')->nullable();
            $table->string('country');
            $table->string('departament');
            $table->string('city');
            $table->string('number_phone')->nullable();
            $table->string('email')->nullable();
            $table->string('zone')->default('N/A');
            $table->index(['number_document', 'branch_code'])->unique();
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
        Schema::dropIfExists('clients');
    }
};
