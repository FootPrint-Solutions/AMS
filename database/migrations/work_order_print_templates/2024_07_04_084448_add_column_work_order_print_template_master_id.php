<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnWorkOrderPrintTemplateMasterId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // add column work_order_print_template_master_id to work_order_print_template_details_sub table
        Schema::table('work_order_print_template_details_sub', function (Blueprint $table) {
            $table->unsignedBigInteger('work_order_print_template_master_id')->nullable()->after('id');
            $table->foreign('work_order_print_template_master_id', 'fk_template_master_id_new')
                ->references('id')
                ->on('work_order_print_template_master')
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
        // drop column work_order_print_template_master_id from work_order_print_template_details_sub table
        Schema::table('work_order_print_template_details_sub', function (Blueprint $table) {
            $table->dropForeign('fk_template_master_id_new');
            $table->dropColumn('work_order_print_template_master_id');
        });
    }
}
