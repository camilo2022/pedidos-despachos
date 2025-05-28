<?php

use App\Models\Process;
use App\Models\Subprocess;
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
        Schema::create('technical_sheet_laundries', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(TechnicalSheet::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignIdFor(Process::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignIdFor(Subprocess::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('technical_sheet_laundries');
    }
};
