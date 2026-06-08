<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnIsInstallationIncludedAtTableSalesOrderBattery extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales_order_battery', function (Blueprint $table) {
            $table->boolean('is_installation_included')->default(false)->after('image');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales_order_battery', function (Blueprint $table) {
            $table->dropColumn('is_installation_included');
        });
    }
}
