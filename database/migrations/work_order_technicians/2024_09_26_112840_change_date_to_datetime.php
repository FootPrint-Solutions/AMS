<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeDateToDatetime extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('work_order_instructions', function (Blueprint $table) {
            // change column date to datetime in work_order_instructions column date_complete
            $table->dateTime('date_complete')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('datetime', function (Blueprint $table) {
            // change column date to datetime in work_order_instructions column date_complete
            $table->date('date_complete')->change();
        });
    }
}
