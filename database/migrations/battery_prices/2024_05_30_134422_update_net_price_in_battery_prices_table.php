<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateNetPriceInBatteryPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('battery_prices', function (Blueprint $table) {
            $table->renameColumn('net_price', 'price_net');
            $table->double('price_retail')->default(0)->after('battery_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('battery_prices', function (Blueprint $table) {
            //
        });
    }
}
