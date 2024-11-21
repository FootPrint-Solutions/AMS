<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnWorkOrderInstructionTemplateOptionIdAtWoInstctructionTemplaet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // add column work_order_instruction_template_option_id in work_order_instruction_templates table
        Schema::table('work_order_instruction_templates', function (Blueprint $table) {
            $table->unsignedBigInteger('work_order_instruction_template_option_id')->nullable()->after('ID');
            $table->foreign('work_order_instruction_template_option_id', 'wo_inst_templ_opt_id_foreign')->references('id')->on('work_order_instruction_template_options')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // drop column work_order_instruction_template_option_id in work_order_instruction_templates table
        Schema::table('work_order_instruction_templates', function (Blueprint $table) {
            $table->dropForeign(['work_order_instruction_template_option_id']);
            $table->dropColumn('work_order_instruction_template_option_id');
        });
    }
}
