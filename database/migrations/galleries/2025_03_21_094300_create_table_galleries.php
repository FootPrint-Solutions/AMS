<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableGalleries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('galleries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('battery_id');
            $table->unsignedBigInteger('vehicle_id');
            $table->string('photo');
            $table->tinyInteger('status')->default(1); // Kolom untuk status (1 = aktif, 0 = tidak aktif)
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('battery_id')->references('id')->on('batteries')->onDelete('cascade');
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('galleries');
    }
}
