<?php

use App\Models\Module;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('submodules', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('type')->default('subitem');
            $table->string('url')->unique();
            $table->string('icon')->unique();
            $table->foreignIdFor(Module::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignIdFor(Permission::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            /* $table->unsignedBigInteger('module_id');
            $table->unsignedBigInteger('permission_id')->unique();
            $table->foreign('module_id')->references('id')->on('modules')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('permission_id')->references('id')->on('permissions')->onUpdate('cascade')->onDelete('cascade'); */
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
        Schema::dropIfExists('submodules');
    }
};
