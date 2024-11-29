<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnWoInstructionTemplateOptionsId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // add column work_order_instruction_template_detail_id to work_order_instruction_template_answers table
        Schema::table('work_order_instruction_template_answers', function (Blueprint $table) {
            $table->unsignedBigInteger('work_order_instruction_template_detail_id')->after('work_order_instruction_id');
            $table->foreign('work_order_instruction_template_detail_id', 'wo_instr_temp_opt_id_foreign')->references('id')->on('work_order_instruction_template_details')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // drop column work_order_instruction_template_options_id from work_order_instruction_template_answers table
        Schema::table('work_order_instruction_template_answers', function (Blueprint $table) {
            $table->dropForeign('wo_instr_temp_opt_id_foreign');
            $table->dropColumn('work_order_instruction_template_detail_id');
        });
    }
}
