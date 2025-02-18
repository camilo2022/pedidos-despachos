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
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('document_number');
            $table->string('phone_number');
            $table->string('address');
            $table->string('email');
            $table->string('prefix', 3)->unique();
            $table->unsignedInteger('consecutive')->default(1);
            $table->enum('status', ['Abierta', 'Cerrada']);
            $table->foreignIdFor(Business::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        DB::unprepared('DROP PROCEDURE IF EXISTS stores;');

        DB::unprepared('
            CREATE PROCEDURE stores(IN store_id INT)
            BEGIN
                DECLARE store_prefix VARCHAR(3);
                DECLARE current_consecutive INT;

                SELECT prefix, consecutive INTO store_prefix, current_consecutive
                FROM stores
                WHERE id = store_id
                LIMIT 1;

                IF current_consecutive IS NULL THEN
                    SET current_consecutive = 1;
                END IF;

                UPDATE stores SET consecutive = current_consecutive + 1 WHERE id = store_id;

                SELECT CONCAT(store_prefix, "-", LPAD(current_consecutive, 6, "0")) AS next_invoice;
            END;
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stores');
    }
};
