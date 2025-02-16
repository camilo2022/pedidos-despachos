<?php

use App\Models\CashRegister;
use App\Models\User;
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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->morphs('model');
            $table->string('reference');
            $table->enum('status', ['Pendiente', 'Pagado', 'Anulado'])->default('Pendiente');
            $table->foreignIdFor(User::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignIdFor(CashRegister::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->index(['model_type', 'model_id', 'reference'])->unique();
            $table->timestamps();
        });

        DB::unprepared('DROP PROCEDURE IF EXISTS store_consecutive;');

        DB::unprepared('
            CREATE PROCEDURE store_consecutive(IN store_id INT)
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
        Schema::dropIfExists('invoices');
    }
};
