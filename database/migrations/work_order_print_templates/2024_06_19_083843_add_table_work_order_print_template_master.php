<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddTableWorkOrderPrintTemplateMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create table for work order print template master
        DB::transaction(function () {
            Schema::create('work_order_print_template_master', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->timestamps();
            });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop table for work order print template master
        Schema::dropIfExists('work_order_print_template_master');
    }
}
