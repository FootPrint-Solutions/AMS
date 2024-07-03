<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTableWorkOrderPrintTemplateDetailsSub extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // create table work_order_print_template_details_sub
        Schema::create('work_order_print_template_details_sub', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('work_order_print_template_details_id');
            $table->string('name');
            $table->string('value');
            $table->timestamps();
            $table->foreign('work_order_print_template_details_id', 'fk_template_details_id')
                ->references('id')->on('work_order_print_template_details')
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
        // drop table work_order_print_template_details_sub
        Schema::dropIfExists('work_order_print_template_details_sub');
    }
}
