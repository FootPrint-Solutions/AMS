<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnDateComplete extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add column date_complete to work_order_instructions table
        Schema::table('work_order_instructions', function (Blueprint $table) {
            $table->date('date_complete')->nullable()->after('date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop column date_complete from work_order_instructions table
        Schema::table('work_order_instructions', function (Blueprint $table) {
            $table->dropColumn('date_complete');
        });
    }
}
