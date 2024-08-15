<?php

use App\Models\Departament;
use App\Models\Province;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->id()->comment('Identificador de la ciudad.');
            $table->foreignIdFor(Departament::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->string('name')->comment('Nombre de la ciudad.');
            $table->string('code')->unique()->comment('Codigo de la ciudad.');
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
        Schema::dropIfExists('cities');
    }
}
