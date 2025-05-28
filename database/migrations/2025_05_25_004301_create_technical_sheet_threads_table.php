<?php

use App\Models\Color;
use App\Models\Extension;
use App\Models\Location;
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
        Schema::create('technical_sheet_threads', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(TechnicalSheet::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignIdFor(Location::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignIdFor(Supply::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignIdFor(Extension::class, 'tone_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignIdFor(Extension::class, 'caliber_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->enum('type', ['Sencillo', 'Doble']);
            $table->index(['technical_sheet_id', 'location_id'])->unique();
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
        Schema::dropIfExists('technical_sheet_threads');
    }
};
