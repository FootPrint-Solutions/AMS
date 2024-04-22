<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveQuotationIdFromSalesOrderBatteryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales_order_battery', function (Blueprint $table) {
            $table->dropForeign('sales_order_battery_quotation_id_foreign');
            $table->dropColumn('quotation_id');
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
