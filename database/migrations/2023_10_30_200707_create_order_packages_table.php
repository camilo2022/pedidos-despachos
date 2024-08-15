<?php

use App\Models\OrderPacking;
use App\Models\PackageType;
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
        Schema::create('order_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(OrderPacking::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignIdFor(PackageType::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->string('weight')->nullable();
            $table->enum('package_status', ['Abierto', 'Cerrado'])->default('Abierto');
            $table->datetime('package_date');
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
        Schema::dropIfExists('order_packages');
    }
};
