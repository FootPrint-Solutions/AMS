<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddTableWorkOrderPrintTemplateDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create table for work order print template details
        Schema::create('work_order_print_template_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('work_order_print_template_master_id');
            $table->string('step_no');
            $table->string('message');
            $table->timestamps();

            $table->foreign('work_order_print_template_master_id', 'fk_template_master_id')
                ->references('id')->on('work_order_print_template_master')
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
        // Drop table for work order print template details
        Schema::dropIfExists('work_order_print_template_details');
    }
}
