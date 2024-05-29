<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPriceRetailToPromoBatteryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('promo_battery', function (Blueprint $table) {
            $table->double('price_retail')->default(0)->after('battery_id');
            $table->renameColumn('net_price', 'price_net');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('promo_battery', function (Blueprint $table) {
            //
        });
    }
}
