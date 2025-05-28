<?php

use App\Models\OrderProduction;
use App\Models\Supply;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Expression;
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
        Schema::create('order_cuts', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(OrderProduction::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('court_number');
            $table->foreignIdFor(Supply::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->float('width')->nullable()->default(0);
            $table->float('meters')->nullable()->default(0);
            $table->float('average')->default(0);
            $table->boolean('pocket')->default(false);
            $table->longText('observation')->nullable();
            $table->json('groups')->default(new Expression('(JSON_OBJECT())'));
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
        Schema::dropIfExists('order_cuts');
    }
};
