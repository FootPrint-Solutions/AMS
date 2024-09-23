<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnDateAtWorkOrderTechnicians extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add column date_at to work_order_technicians
        Schema::table('work_order_instructions', function (Blueprint $table) {
            $table->date('date')->nullable()->after('work_order_instruction_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop column date_at from work_order_technicians
        Schema::table('work_order_instructions', function (Blueprint $table) {
            $table->dropColumn('date');
        });
    }
}
