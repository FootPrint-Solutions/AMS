<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class WorkOrderInstructionTemplateOptionAddColumnStatusAfterName extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //  add column status after name in work_order_instruction_template_options table
        Schema::table('work_order_instruction_template_options', function (Blueprint $table) {
            $table->tinyInteger('status')->default(0)->after('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // drop column status in work_order_instruction_template_options table
        Schema::table('work_order_instruction_template_options', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
