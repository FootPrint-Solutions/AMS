<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnSourceIdAtTableSalesOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add column source_id at table sales_orders after column source_platform with default value 0
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->string('source_id')->after('source_platform')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop column source_id
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->dropColumn('source_id');
        });
    }
}
