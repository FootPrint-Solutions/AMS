<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDiscountPriceToPromoBatteryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('promo_battery', function (Blueprint $table) {
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
        Schema::table('promo_battery', function (Blueprint $table) {
            //
        });
    }
}
