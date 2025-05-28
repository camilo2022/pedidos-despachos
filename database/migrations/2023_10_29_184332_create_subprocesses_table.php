<?php

use App\Models\Process;
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
        Schema::create('subprocesses', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Process::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->string('name')->unique();
            $table->json('options')->default(new Expression('(JSON_OBJECT())'));
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
        Schema::dropIfExists('subprocesses');
    }
};
