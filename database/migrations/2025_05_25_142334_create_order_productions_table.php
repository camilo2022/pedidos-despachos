<?php

use App\Models\TechnicalSheet;
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
        Schema::create('order_productions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(TechnicalSheet::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->string('consecutive')->unique();
            $table->enum('status', ['Cancelado', 'En elaboracion', 'Aprobado'])->default('En elaboracion');
            $table->foreignIdFor(User::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });

        DB::unprepared('DROP PROCEDURE IF EXISTS order_productions');

        DB::unprepared('
            CREATE PROCEDURE order_productions(IN order_production_id INT)
                BEGIN
                DECLARE consecutive VARCHAR(50);
                DECLARE technical_sheet_id INT;
                DECLARE current_date DATE;
                DECLARE sequence INT DEFAULT 1;

                SELECT technical_sheet_id, DATE(created_at)
                INTO technical_sheet_id, current_date
                FROM order_productions
                WHERE id = order_production_id;

                SELECT COUNT(*) INTO sequence
                FROM order_productions
                WHERE technical_sheet_id = technical_sheet_id
                AND DATE(created_at) = current_date
                AND id < order_production_id;

                SET sequence = sequence + 1;

                SET consecutive = CONCAT(
                    "OP", LPAD(order_production_id, 5, "0"), "-", "FT", LPAD(technical_sheet_id, 5, "0"), "-",
                    DATE_FORMAT(current_date, "%Y%m%d"), "-", LPAD(sequence, 3, "0")
                );
                
                SELECT consecutive;
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
        Schema::dropIfExists('order_productions');
    }
};
