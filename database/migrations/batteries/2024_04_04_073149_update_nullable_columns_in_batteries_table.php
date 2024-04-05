<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateNullableColumnsInBatteriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('batteries', function (Blueprint $table) {
            $table->unsignedBigInteger('subbrand_category_id')->nullable()->change();
            $table->unsignedBigInteger('usage_type_id')->nullable()->change();
            $table->unsignedBigInteger('size_category_id')->nullable()->change();
            $table->unsignedBigInteger('technology_id')->nullable()->change();
            $table->float('standard_cca')->nullable()->change();
            $table->integer('warranty')->nullable()->change();
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
