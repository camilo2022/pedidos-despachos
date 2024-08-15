<?php

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
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->morphs('model');
            $table->enum('file', ['DOCUMENTO DE IDENTIFICACION', 'FIRMA GARANTIA', 'RUT', 'CAMARA DE COMERCIO', 'PORTADA', 'IMAGEN', 'VIDEO', 'CERTIFICADO', 'FACTURA', 'MEMBRETE', 'FIRMA']);
            $table->string('name');
            $table->string('path');
            $table->string('mime');
            $table->string('extension');
            $table->string('size');
            $table->foreignIdFor(User::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->json('metadata');
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
        Schema::dropIfExists('files');
    }
};
