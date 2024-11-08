<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnWoTemplateInstructionId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add column work_order_instruction_id to work_order_instruction_template_answers table
        Schema::table('work_order_instruction_template_answers', function (Blueprint $table) {
            $table->unsignedBigInteger('work_order_instruction_id')->nullable()->after('work_order_id');

            // Specify a shorter name for the foreign key constraint
            $table->foreign('work_order_instruction_id', 'wo_instruction_fk')
                ->references('id')
                ->on('work_order_instructions')
                ->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // drop column work_order_instruction_id from work_order_print_templates table
        Schema::table('work_order_instruction_template_answers', function (Blueprint $table) {
            $table->dropForeign(['work_order_instruction_id']);
            $table->dropColumn('work_order_instruction_id');
        });
    }
}
