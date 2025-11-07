<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewColumnBatteryRecyclesToInventoryRecycle extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inventory_recycles', function (Blueprint $table) {
            $table->unsignedBigInteger('battery_recycle_id')->nullable()->after('battery_id');
            $table->index('battery_recycle_id', 'ir_battery_recycle_id_idx');

            $table->foreign('battery_recycle_id', 'ir_battery_recycle_id_fk')
                ->references('id')->on('battery_recycles')
                ->onDelete('set null');
        });

        Schema::table('inventory_recycle_details', function (Blueprint $table) {
            $table->unsignedBigInteger('battery_recycle_id')->nullable()->after('battery_id');
            $table->index('battery_recycle_id', 'ird_battery_recycle_id_idx');

            $table->foreign('battery_recycle_id', 'ird_battery_recycle_id_fk')
                ->references('id')->on('battery_recycles')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inventory_recycles', function (Blueprint $table) {
            $table->dropIndex('ir_battery_recycle_id_idx');
            $table->dropColumn('battery_recycle_id');
        });

        Schema::table('inventory_recycle_details', function (Blueprint $table) {
            $table->dropIndex('ird_battery_recycle_id_idx');
            $table->dropColumn('battery_recycle_id');
        });
    }
}
