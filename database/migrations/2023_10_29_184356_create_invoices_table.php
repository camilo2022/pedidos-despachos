<?php

use App\Models\CashRegister;
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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->morphs('model');
            $table->string('reference');
            $table->enum('status', ['Pendiente', 'Pagado', 'Anulado'])->default('Pendiente');
            $table->foreignIdFor(User::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignIdFor(CashRegister::class)->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->index(['model_type', 'model_id', 'reference'])->unique();
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
        Schema::dropIfExists('invoices');
    }
};
