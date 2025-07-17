<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableSalesInvoiceBattery extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_invoice_batteries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('sales_invoice_id');
            $table->unsignedBigInteger('battery_id');
            $table->string('battery_name', 255);
            $table->double('battery_price_retail');
            $table->decimal('tax', 5, 2)->default('0.00');
            $table->double('tax_price')->default(0);
            $table->decimal('discount', 5, 2)->default('0.00');
            $table->double('discount_price')->default(0);
            $table->double('price_net')->default(0);
            $table->double('quantity')->default(1);
            $table->string('battery_production_code', 255)->nullable()->unique();
            $table->string('image', 255)->nullable();
            $table->timestamps();

            $table->index('battery_id');
            $table->index('sales_invoice_id');

            $table->foreign('battery_id')
                ->references('id')->on('batteries')
                ->onUpdate('restrict')
                ->onDelete('restrict');

            $table->foreign('sales_invoice_id')
                ->references('id')->on('sales_invoices')
                ->onUpdate('restrict')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales_invoice_batteries');
    }
}
