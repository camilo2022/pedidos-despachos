<?php

use App\Models\OrderProduction;
use App\Models\TechnicalSheetDetail;
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
        Schema::create('order_production_details', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(OrderProduction::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignIdFor(TechnicalSheetDetail::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->string('lot', 10);
            $table->index(['order_production_id', 'technical_sheet_detail_id'])->unique();
            $table->index(['order_production_id', 'technical_sheet_detail_id', 'lot'])->unique();
            $table->timestamps();
        });

        DB::unprepared('DROP PROCEDURE IF EXISTS order_production_details');
        
        DB::unprepared('
            CREATE PROCEDURE order_production_details(IN p_order_production_id INT, IN p_technical_sheet_detail_id INT)
            BEGIN
                DECLARE last_lot VARCHAR(10);
                DECLARE next_lot VARCHAR(10);
                DECLARE i INT DEFAULT 0;
                DECLARE n INT DEFAULT 0;

                SELECT lot INTO last_lot
                FROM order_production_details
                WHERE order_production_id = p_order_production_id
                AND technical_sheet_detail_id = p_technical_sheet_detail_id
                ORDER BY LENGTH(lot) DESC, lot DESC
                LIMIT 1;

                IF last_lot IS NULL THEN
                    SET next_lot = "A";
                ELSE
                    SET i = 0;
                    SET n = 0;
                    WHILE i < LENGTH(last_lot) DO
                        SET n = n * 26 + (ASCII(SUBSTRING(last_lot, i + 1, 1)) - ASCII("A") + 1);
                        SET i = i + 1;
                    END WHILE;

                    SET n = n + 1;
                    SET next_lot = "";

                    WHILE n > 0 DO
                        SET n = n - 1;
                        SET next_lot = CONCAT(CHAR(n % 26 + ASCII("A")), next_lot);
                        SET n = FLOOR(n / 26);
                    END WHILE;
                END IF;

                SELECT next_lot AS lot;
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
        Schema::dropIfExists('order_production_details');
    }
};
