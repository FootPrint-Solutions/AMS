<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveFkFromSalesOrderBatteries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales_order_battery', function (Blueprint $table) {
            $table->dropForeign(['battery_id']);
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
            $table->foreign('battery_id')->references('id')->on('batteries')->onDelete('cascade');
        });
    }
}
