<?php

use App\Models\Category;
use App\Models\ClothingLine;
use App\Models\Silhouette;
use App\Models\Subcategory;
use App\Models\Trademark;
use App\Models\TypeOfBoot;
use App\Models\TypeOfButt;
use App\Models\TypeOfGarment;
use App\Models\TypeOfWaistband;
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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Trademark::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignIdFor(Category::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignIdFor(Subcategory::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignIdFor(ClothingLine::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignIdFor(Silhouette::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignIdFor(TypeOfGarment::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignIdFor(TypeOfWaistband::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignIdFor(TypeOfButt::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignIdFor(TypeOfBoot::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->string('item');
            $table->string('code')->unique();
            $table->float('price')->default(89900.00);
            $table->string('description')->nullable();
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
        Schema::dropIfExists('products');
    }
};
