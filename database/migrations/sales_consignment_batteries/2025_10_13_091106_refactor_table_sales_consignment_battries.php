<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RefactorTableSalesConsignmentBattries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('sales_consignment_batteries');

        Schema::create('sales_consignment_batteries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('sales_consignment_id');
            $table->unsignedBigInteger('sales_invoice_id');
            $table->string('sales_invoice_number')->unique();
            $table->string('invoice_number')->nullable();
            $table->date('date');
            $table->decimal('discount', 5, 2)->default(0.00);
            $table->double('discount_price')->default(0);
            $table->double('subtotal')->default(0);
            $table->double('total')->default(0);
            $table->timestamps();

            $table->index('sales_consignment_id');
            $table->index('sales_invoice_id');
            $table->index('sales_invoice_number');

            $table->foreign('sales_consignment_id')->references('id')->on('sales_consignments')->onDelete('cascade');
            $table->foreign('sales_invoice_id')->references('id')->on('sales_invoices')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales_consignment_batteries');
    }
}
