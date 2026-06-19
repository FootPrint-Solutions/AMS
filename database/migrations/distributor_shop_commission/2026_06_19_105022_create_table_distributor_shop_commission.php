<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableDistributorShopCommission extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('distributor_shop_commission', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('distributor_shop_id');
            $table->unsignedBigInteger('battery_id');
            $table->string('type');
            $table->double('commission');
            $table->timestamps();

            $table->foreign('distributor_shop_id')->references('id')->on('distributor_shops')->onDelete('cascade');
            $table->foreign('battery_id')->references('id')->on('batteries')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('distributor_shop_commission');
    }
}
