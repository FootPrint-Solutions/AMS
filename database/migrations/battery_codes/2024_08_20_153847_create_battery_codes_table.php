<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBatteryCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('battery_codes', function (Blueprint $table) {
            $table->unsignedBigInteger('battery_id')->nullable();
            $table->string('code');
            $table->timestamps();

            // Set foreign key references.
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
        Schema::dropIfExists('battery_codes');
    }
}
