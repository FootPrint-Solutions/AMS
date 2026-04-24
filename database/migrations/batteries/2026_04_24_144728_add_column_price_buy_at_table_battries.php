<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnPriceBuyAtTableBattries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('batteries', function (Blueprint $table) {
            $table->double('price_buy')->nullable()->after('price_retail');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('batteries', function (Blueprint $table) {
            $table->dropColumn('price_buy');
        });
    }
}
