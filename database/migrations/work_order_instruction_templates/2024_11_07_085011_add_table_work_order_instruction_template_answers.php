<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTableWorkOrderInstructionTemplateAnswers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // add new table work_order_instruction_template_answers
        Schema::create('work_order_instruction_template_answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('work_order_id');
            $table->string('name');
            $table->longText('description');
            $table->text('instruction');
            $table->text('instruction_step');
            $table->string('type');
            $table->string('group');
            $table->boolean('is_required');
            $table->text('answer');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('work_order_id')->references('id')->on('work_orders');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // drop table work_order_instruction_template_answers
        Schema::dropIfExists('work_order_instruction_template_answers');
    }
}
