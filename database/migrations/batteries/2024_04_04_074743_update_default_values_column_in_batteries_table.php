<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateDefaultValuesColumnInBatteriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('batteries', function (Blueprint $table) {
            $table->float('dimension_length')->default(0)->change();
            $table->float('dimension_width')->default(0)->change();
            $table->float('dimension_height')->default(0)->change();
            $table->float('standard_cca')->nullable(false)->default(0)->change();
            $table->float('capacity')->default(0)->change();
            $table->integer('warranty')->nullable(false)->default(0)->change();
            $table->float('price_retail')->default(0)->change();
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
            //
        });
    }
}
