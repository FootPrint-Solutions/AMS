<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTableBatteryImages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create the battery_images table
        Schema::create('battery_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('battery_id')->index();
            $table->string('image_path');
            $table->string('image_type')->nullable();
            $table->timestamps();
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
        Schema::dropIfExists('battery_images');
    }
}
