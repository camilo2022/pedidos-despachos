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
            $table->string('client_name');
            $table->string('client_address')->default('N/A');
            $table->string('client_number_document');
            $table->string('client_number_phone')->nullable();
            $table->string('client_branch_code')->default('001');
            $table->string('client_branch_name')->nullable();
            $table->string('client_branch_address')->nullable();
            $table->string('client_branch_number_phone')->nullable();
            $table->string('country');
            $table->string('departament');
            $table->string('city');
            $table->string('number_phone')->nullable();
            $table->string('email')->nullable();
            $table->string('zone')->default('N/A');
            $table->enum('type', ['DEBITO', 'CREDITO'])->nullable()->default(null);
            $table->index(['client_number_document', 'client_branch_code'])->unique();
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
