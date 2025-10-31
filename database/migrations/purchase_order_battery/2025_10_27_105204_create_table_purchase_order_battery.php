<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTablePurchaseOrderBattery extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('purchase_order_batteries')) {
            Schema::create('purchase_order_batteries', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('purchase_order_id');
                $table->unsignedBigInteger('battery_id');
                $table->string('battery_name', 255);
                $table->double('battery_price_retail');
                $table->decimal('tax', 5, 2)->default(0.00);
                $table->double('tax_price')->default(0);
                $table->decimal('discount', 5, 2)->default(0.00);
                $table->double('discount_price')->default(0);
                $table->double('price_net')->default(0);
                $table->double('quantity')->default(1);
                $table->string('battery_production_code', 255)->nullable();
                $table->timestamps();

                $table->foreign('battery_id')->references('id')->on('batteries')->onDelete('restrict')->onUpdate('restrict');
                $table->foreign('purchase_order_id')->references('id')->on('purchase_orders')->onDelete('restrict')->onUpdate('restrict');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_order_batteries');
    }
}
