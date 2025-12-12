<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnSalesOrderBatteryId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_order_batteries', function (Blueprint $table) {
            $table->unsignedBigInteger('sales_order_battery_id')->nullable()->after('purchase_order_id');

            $table->foreign('sales_order_battery_id')
                ->references('id')
                ->on('sales_order_battery')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_order_batteries', function (Blueprint $table) {
            $table->dropForeign(['sales_order_battery_id']);
            $table->dropColumn('sales_order_battery_id');
        });
    }
}
