<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBatteryUrlsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('battery_urls', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('battery_id');
            $table->string('platform');
            $table->string('url');
            $table->timestamps();

            // Set foreign key.
            $table->foreign('battery_id')
                ->references('id')
                ->on('batteries');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('battery_urls');
    }
}
