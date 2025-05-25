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
            $table->foreignIdFor(Warehouse::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignIdFor(Product::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignIdFor(Size::class)->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignIdFor(Color::class)->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('quantity')->default(0);
            $table->enum('system', ['SIESA', 'VISUAL TNS', 'PORTAL TNS', 'PROYECCION', 'TIENDA'])->nullable()->default(null);
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
