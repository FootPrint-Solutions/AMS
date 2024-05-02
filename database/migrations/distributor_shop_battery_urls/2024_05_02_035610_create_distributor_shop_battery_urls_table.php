<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDistributorShopBatteryUrlsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('distributor_shop_battery_urls', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('distributor_shop_battery_id');
            $table->double('price');
            $table->string('url');
            $table->timestamps();

            // Set foreign key references.
            $table->foreign('distributor_shop_battery_id', 'distributor_shop_battery_url_foreign')
                ->references('id')
                ->on('distributor_shop_battery');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('distributor_shop_battery_urls');
    }
}
