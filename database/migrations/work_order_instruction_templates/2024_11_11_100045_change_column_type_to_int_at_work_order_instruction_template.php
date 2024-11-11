<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnTypeToIntAtWorkOrderInstructionTemplate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('work_order_instruction_templates', function (Blueprint $table) {
            // Change column instruction to integer
            $table->integer('instruction')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('work_order_instruction_templates', function (Blueprint $table) {
            //  Revert column instruction to text
            $table->text('instruction')->change();
        });
    }
}
