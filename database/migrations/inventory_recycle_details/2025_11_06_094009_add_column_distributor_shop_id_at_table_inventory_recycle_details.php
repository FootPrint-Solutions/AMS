<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnDistributorShopIdAtTableInventoryRecycleDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inventory_recycle_details', function (Blueprint $table) {
            $table->unsignedBigInteger('distributor_shop_id')->after('inventory_id')->nullable();

            $table->foreign('distributor_shop_id')->references('id')->on('distributor_shops')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inventory_recycle_details', function (Blueprint $table) {
            $table->dropForeign(['distributor_shop_id']);
            $table->dropColumn('distributor_shop_id');
        });
    }
}
