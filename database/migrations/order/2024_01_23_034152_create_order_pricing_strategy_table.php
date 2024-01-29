<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderPricingStrategyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_pricing_strategy', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_order');
            $table->unsignedBigInteger('id_pricing_strategy');
            $table->timestamps();

            /*
            $table->foreign('id_order')
                ->references('id')
                ->on('order');
            $table->foreign('id_pricing_strategy')
                ->references('id')
                ->on('pricing_strategy');
            */
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_pricing_strategy');
    }
}
