<?php

use App\Models\CashRegister;
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
        Schema::create('cash_register_controls', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(CashRegister::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->date('date');
            $table->foreignIdFor(User::class, 'start_user_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->decimal('start_balance', 10, 2);
            $table->time('start_time');
            $table->foreignIdFor(User::class, 'end_user_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->decimal('end_balance', 10, 2)->nullable();
            $table->time('end_time')->nullable();
            $table->enum('status', ['Abierta', 'Cerrada'])->default('Abierta');
            $table->index(['cash_register_id', 'date'])->unique();
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
