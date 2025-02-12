<?php

use App\Models\Color;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Promotion;
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
        Schema::create('invoice_details', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Invoice::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignIdFor(Warehouse::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignIdFor(Product::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignIdFor(Size::class)->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignIdFor(Color::class)->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('quantity')->default(0);
            $table->foreignIdFor(Promotion::class)->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->float('price');
            $table->float('discount')->default(0);
            $table->float('subtotal');
            $table->float('total');
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
        Schema::dropIfExists('invoice_details');
    }
};
