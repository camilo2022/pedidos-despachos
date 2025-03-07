<?php

use App\Models\Invoice;
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
        Schema::create('libranzas', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Invoice::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->float('value');
            $table->enum('status', ['Pendiente', 'Aprobado', 'Cancelado', 'Proceso', 'Pagado'])->default('Pendiente');
            $table->string('code', 8);
            $table->string('sms');
            $table->enum('share', [1, 2, 3, 4])->default(4);
            $table->foreignIdFor(Store::class)->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('libranzas');
    }
};
