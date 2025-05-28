<?php

use App\Models\Business;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('last_name');
            $table->string('document_number')->unique();
            $table->string('phone_number');
            $table->string('address');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('title', ['SUPER ADMINISTRADOR', 'ADMINISTRADOR', 'VENDEDOR', 'VENDEDOR ESPECIAL', 'CARTERA', 'FILTRADOR', 'BODEGA', 'COORDINADOR BODEGA', 'FACTURADOR', 'PROMOTORA', 'COORDINADOR PROMOTORA', 'USUARIO', 'TIENDAS', 'CAJERO VENDEDOR', 'CAJERO SUPERVISOR', 'REPORTES'])->default('USUARIO');
            $table->enum('zone', ['N/A', 'NACIONAL', 'MEDELLIN', 'PERIFERIA'])->default('N/A');
            $table->foreignIdFor(Business::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
