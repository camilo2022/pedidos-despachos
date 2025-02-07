<?php

use App\Models\Store;
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
        Schema::create('cash_register_controls', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Store::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->date('date');
            $table->foreignId('start_user_id')->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->decimal('start_balance', 10, 2);
            $table->time('start_time');
            $table->foreignId('end_user_id')->nullable()->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->decimal('end_balance', 10, 2)->nullable();
            $table->time('end_time')->nullable();
            $table->enum('status', ['Abierta', 'Cerrada'])->default('Abierta');
            $table->index([])->unique();
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
        Schema::dropIfExists('cash_register_controls');
    }
};
