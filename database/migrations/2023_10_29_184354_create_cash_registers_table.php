<?php

use App\Models\Store;
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
        Schema::create('cash_registers', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Store::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->string('name');
            $table->enum('status', ['Activa', 'Inactiva'])->default('Activa');
            $table->foreignIdFor(User::class)->nullable()->unique()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->index(['store_id', 'name'])->unique();
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
        Schema::dropIfExists('cash_registers');
    }
};
