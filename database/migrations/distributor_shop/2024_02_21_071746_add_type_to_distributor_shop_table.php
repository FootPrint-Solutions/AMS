<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeToDistributorShopTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('distributor_shop', function (Blueprint $table) {
            $table->tinyInteger('type')->after('id_distributor')->default('0')->comment('0: separate shop, 1: with distributor address');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('distributor_shop', function (Blueprint $table) {
            //
        });
    }
}
