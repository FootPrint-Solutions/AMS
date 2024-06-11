<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDiscountPriceAndTaxToSalesOrderBatteryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales_order_battery', function (Blueprint $table) {
            $table->decimal('tax', 5, 2)->default(0)->after('battery_price_retail');
            $table->double('tax_price')->default(0)->after('tax');
            $table->double('discount_price')->default(0)->after('discount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales_order_battery', function (Blueprint $table) {
            //
        });
    }
}
