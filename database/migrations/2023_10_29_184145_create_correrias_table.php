<?php

use App\Models\Business;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::create('correrias', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('code')->unique();
            $table->datetime('start_date');
            $table->datetime('end_date');
            $table->foreignIdFor(Business::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        DB::unprepared('DROP PROCEDURE IF EXISTS correrias');

        DB::unprepared('
            CREATE PROCEDURE correrias(IN currentDateTime DATETIME, business_id INT)
            BEGIN
                -- Actualizar los registros que no cumplen la condición
                UPDATE correrias
                SET deleted_at = currentDateTime
                WHERE start_date > currentDateTime OR end_date < currentDateTime AND correrias.business_id = business_id;

                -- Restaurar los registros que cumplen la condición
                UPDATE correrias
                SET deleted_at = NULL
                WHERE start_date <= currentDateTime AND end_date >= currentDateTime AND correrias.business_id = business_id;
            END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('correrias');
    }
};
