<?php

use App\Models\Libranza;
use App\Models\User;
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
        Schema::create('libranza_discounts', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Libranza::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->float('value');
            $table->foreignIdFor(User::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('libranza_discounts');
    }
};
