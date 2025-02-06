<?php

use App\Models\Color;
use App\Models\Product;
use App\Models\Size;
use App\Models\Warehouse;
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
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            //$table->morphs('model');
            $table->foreignIdFor(Warehouse::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignIdFor(Product::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignIdFor(Size::class)->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignIdFor(Color::class)->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('quantity')->default(0);
            $table->enum('system', ['SIESA', 'VISUAL TNS', 'PORTAL TNS', 'PROYECCION', 'TIENDA'])->nullable()->default(null);
            $table->index([/*'model_type', 'model_id',*/ 'warehouse_id', 'product_id', 'size_id', 'color_id', 'system'], 'inv_wrh_id_prd_id_sz_id_clr_id_sys_index`')->unique();
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
        Schema::dropIfExists('inventories');
    }
};
