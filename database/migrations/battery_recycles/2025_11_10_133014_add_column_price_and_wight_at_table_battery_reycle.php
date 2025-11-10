<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnPriceAndWightAtTableBatteryReycle extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('battery_recycles', function (Blueprint $table) {
            $table->double('price')->nullable()->after('status');
            $table->double('weight')->nullable()->after('price');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('battery_recycles', function (Blueprint $table) {
            $table->dropColumn('price');
            $table->dropColumn('weight');
        });
    }
}
