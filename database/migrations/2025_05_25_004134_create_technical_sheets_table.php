<?php

use App\Models\Category;
use App\Models\ClothingLine;
use App\Models\Color;
use App\Models\Correria;
use App\Models\Location;
use App\Models\Silhouette;
use App\Models\Subcategory;
use App\Models\TypeOfBoot;
use App\Models\TypeOfButt;
use App\Models\TypeOfGarment;
use App\Models\TypeOfWaistband;
use App\Models\User;
use App\Models\WashTone;
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
        Schema::create('technical_sheets', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Color::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignIdFor(WashTone::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignIdFor(Category::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignIdFor(Subcategory::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignIdFor(ClothingLine::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignIdFor(Silhouette::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignIdFor(TypeOfGarment::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignIdFor(TypeOfWaistband::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignIdFor(TypeOfButt::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignIdFor(TypeOfBoot::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->boolean('physical_sample')->default(false);
            $table->string('description')->nullable();
            $table->longText('observation_clothing')->nullable();
            $table->boolean('tubular_cord')->default(false);
            $table->boolean('nit')->default(false);
            $table->boolean('embroidery')->default(false);
            $table->foreignIdFor(Location::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('clip');
            $table->unsignedBigInteger('pin');
            $table->unsignedBigInteger('buttonhole_waistband');
            $table->unsignedBigInteger('button');
            $table->unsignedBigInteger('clip');
            $table->longText('observation_dry_cleaner')->nullable();
            $table->longText('observation_laundry')->nullable();
            $table->enum('status', ['Cancelado', 'En elaboracion', 'Revision', 'Aprobado'])->default('En elaboracion');
            $table->foreignIdFor(Correria::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('technical_sheets');
    }
};
