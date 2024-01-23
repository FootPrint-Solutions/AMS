<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBatteryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('battery', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->unsignedBigInteger('id_brand');
            $table->unsignedBigInteger('id_subbrand_category');
            $table->unsignedBigInteger('id_usage_type');
            $table->unsignedBigInteger('id_size_category');
            $table->unsignedBigInteger('id_technology');
            $table->double('dimension_length');
            $table->double('dimension_width');
            $table->double('dimension_height');
            $table->double('standard_cca'); // A
            $table->double('capacity'); // AH
            $table->integer('warranty'); // Months
            $table->double('price_retail');
            $table->binary('image');
            $table->timestamps();

            $table->foreign('id_brand')
                ->references('id')
                ->on('battery_brand');
            $table->foreign('id_subbrand_category')
                ->references('id')
                ->on('battery_subbrand_category');
            $table->foreign('id_usage_type')
                ->references('id')
                ->on('battery_usage_type');
            $table->foreign('id_size_category')
                ->references('id')
                ->on('battery_size_category');
            $table->foreign('id_technology')
                ->references('id')
                ->on('battery_technology');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('battery');
    }
}
