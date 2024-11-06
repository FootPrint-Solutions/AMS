<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeTypeColumnWorkOrderInstructionTemplateAgain extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Change type column description to longtext   
        Schema::table('work_order_instruction_templates', function (Blueprint $table) {
            $table->longText('description')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Change type column description to blob
        Schema::table('work_order_instruction_templates', function (Blueprint $table) {
            $table->binary('description')->change();
        });
    }
}
