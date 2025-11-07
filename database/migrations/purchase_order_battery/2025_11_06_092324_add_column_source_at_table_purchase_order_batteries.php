<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnSourceAtTablePurchaseOrderBatteries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_order_batteries', function (Blueprint $table) {
            $table->string('source')->default('regular')->after('purchase_order_id');

            $table->dropForeign(['battery_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_order_batteries', function (Blueprint $table) {
            $table->dropColumn('source');

            $table->foreign('battery_id')->references('id')->on('batteries')->onDelete('cascade');
        });
    }
}
