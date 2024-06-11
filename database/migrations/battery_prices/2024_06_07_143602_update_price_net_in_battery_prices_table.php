<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdatePriceNetInBatteryPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('battery_prices', function (Blueprint $table) {
            DB::statement('ALTER TABLE battery_prices MODIFY COLUMN price_net DOUBLE AS (price_retail - discount_price) STORED');
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
