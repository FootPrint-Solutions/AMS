<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnNameToStepNoTableWorkOrderPrintTemplateDetailsSub extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // rename column name to step_no
        Schema::table('work_order_print_template_details_sub', function (Blueprint $table) {
            $table->renameColumn('name', 'step_no');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // rename column step_no to name
        Schema::table('work_order_print_template_details_sub', function (Blueprint $table) {
            $table->renameColumn('step_no', 'name');
        });
    }
}
