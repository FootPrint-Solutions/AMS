<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTableWorkOrderInstructionTemplateDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create table for work order instruction template details
        Schema::create('work_order_instruction_template_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('work_order_instruction_template_id');
            $table->text('instruction');
            $table->string('type');
            $table->string('group');
            $table->boolean('is_required');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by');
            $table->timestamps();
            $table->softDeletes();

            // Define foreign keys with custom names to avoid the 64-character limit
            $table->foreign('work_order_instruction_template_id', 'wo_inst_tmpl_id_fk')
                ->references('id')
                ->on('work_order_instruction_templates');
            $table->foreign('created_by', 'created_by_fk')
                ->references('id')
                ->on('users');
            $table->foreign('updated_by', 'updated_by_fk')
                ->references('id')
                ->on('users');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop table work_order_instruction_template_details
        Schema::dropIfExists('work_order_instruction_template_details');
    }
}
