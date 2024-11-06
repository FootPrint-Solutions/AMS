<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeTypeColumnWorkOrderInstructionTemplate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Change type column description to blob
        Schema::table('work_order_instruction_templates', function (Blueprint $table) {
            $table->binary('description')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Change type column description to text
        Schema::table('work_order_instruction_templates', function (Blueprint $table) {
            $table->text('description')->change();
        });
    }
}
