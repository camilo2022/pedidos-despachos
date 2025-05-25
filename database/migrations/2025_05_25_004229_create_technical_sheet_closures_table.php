<?php

use App\Models\Color;
use App\Models\Location;
use App\Models\Size;
use App\Models\Supply;
use App\Models\TechnicalSheet;
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
        Schema::create('technical_sheet_closures', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(TechnicalSheet::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignIdFor(Location::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignIdFor(Supply::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignIdFor(Color::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignIdFor(Size::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('quantity')->default(0);
            $table->float('length')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('technical_sheet_closures');
    }
};
