<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAlternativeAddressAtSalesOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // add column alternative_address
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->text('alternative_address')->nullable()->after('address');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // drop column alternative_address
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->dropColumn('alternative_address');
        });
    }
}
