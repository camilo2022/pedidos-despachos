<?php

use App\Models\InvoiceDetail;
use App\Models\PaymentMethod;
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
        Schema::create('invoice_detail_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(InvoiceDetail::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignIdFor(PaymentMethod::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->float('payment', 8, 2);
            $table->index(['invoice_detail_id', 'payment_method_id'])->unique();
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
        Schema::dropIfExists('invoice_detail_payments');
    }
};
