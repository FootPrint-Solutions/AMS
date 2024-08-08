<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrackingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trackings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('work_order_id');
            $table->text('latitude_start');
            $table->text('longitude_start');
            $table->text('latitude_current');
            $table->text('longitude_current');
            $table->text('latitude_destination');
            $table->text('longitude_destination');
            $table->text('latitude_end')->nullable();
            $table->text('longitude_end')->nullable();
            $table->timestamps();

            $table->foreign('work_order_id')
                ->references('id')
                ->on('work_orders');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trackings');
    }
}
