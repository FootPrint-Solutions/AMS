<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPromoIdInBatteryPrices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('battery_prices', function (Blueprint $table) {
            $table->unsignedBigInteger('promo_id')->after('battery_id');

            // Set foreign key.
            $table->foreign('promo_id')
                ->references('id')
                ->on('promos');
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
