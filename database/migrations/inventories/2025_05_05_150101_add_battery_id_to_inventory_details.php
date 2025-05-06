<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBatteryIdToInventoryDetails extends Migration
{
    public function up()
    {
        Schema::table('inventory_details', function (Blueprint $table) {
            $table->unsignedBigInteger('battery_id')->nullable()->after('inventory_id');
            $table->foreign('battery_id')->references('id')->on('batteries')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('inventory_details', function (Blueprint $table) {
            $table->dropForeign(['battery_id']);
            $table->dropColumn('battery_id');
        });
    }
}
