<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnSourcePlatformAtTableSalesOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // add column source_platform after midtrans_payment_link default internal 
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->string('source_platform')->default('internal')->after('midtrans_payment_link');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // drop column source_platform
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->dropColumn('source_platform');
        });
    }
}
