<?php

use App\Models\Person;
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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Person::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->enum('gender', ['M', 'F']);
            $table->date('birth_date');
            $table->string('department');
            $table->string('position');
            $table->enum('blood_type', ['-', 'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])->default('-');
            $table->string('arl');
            $table->string('eps');
            $table->string('pension_fund');
            $table->string('compensation_fund');
            $table->dateTime('admission_date');
            $table->dateTime('termination_date')->nullable();
            $table->string('shift')->nullable();
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
        Schema::dropIfExists('employees');
    }
};
