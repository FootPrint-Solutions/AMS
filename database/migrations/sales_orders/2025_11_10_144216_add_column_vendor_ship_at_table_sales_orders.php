<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnVendorShipAtTableSalesOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('vendor')->nullable()->after('source_id')->comment('this field store distributor shop id');
            $table->unsignedBigInteger('ship_to')->nullable()->after('vendor')->comment('this field store distributor id');
            $table->string('type')->default('regular')->after('ship_to')->comment('this field store type of sales order (regular, recycle)');

            // Make existing columns nullable (requires doctrine/dbal)
            $table->unsignedBigInteger('customer_id')->nullable()->change();
            $table->unsignedBigInteger('vehicle_id')->nullable()->change();
            $table->unsignedBigInteger('distributor_shop_id')->nullable()->change();
            $table->unsignedBigInteger('distributor_shop_technician_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->dropColumn(['vendor', 'ship_to', 'type']);

            // Revert existing columns to not nullable
            $table->unsignedBigInteger('customer_id')->nullable(false)->change();
            $table->unsignedBigInteger('vehicle_id')->nullable(false)->change();
            $table->unsignedBigInteger('distributor_shop_id')->nullable(false)->change();
            $table->unsignedBigInteger('distributor_shop_technician_id')->nullable(false)->change();
        });
    }
}
