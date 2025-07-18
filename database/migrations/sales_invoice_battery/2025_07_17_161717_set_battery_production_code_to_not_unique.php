<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SetBatteryProductionCodeToNotUnique extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales_invoice_batteries', function (Blueprint $table) {
            $table->dropUnique(['battery_production_code']);
            $table->index('battery_production_code', 'idx_battery_production_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales_invoice_batteries', function (Blueprint $table) {
            $table->dropIndex('idx_battery_production_code');
            $table->unique('battery_production_code', 'unique_battery_production_code');
        });
    }
}
