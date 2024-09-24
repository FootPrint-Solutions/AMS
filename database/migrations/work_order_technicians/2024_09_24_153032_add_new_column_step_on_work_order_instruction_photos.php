<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewColumnStepOnWorkOrderInstructionPhotos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add the new column.
        Schema::table('work_order_instruction_photos', function (Blueprint $table) {
            $table->string('step')->after('work_order_instruction_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop the new column.
        Schema::table('work_order_instruction_photos', function (Blueprint $table) {
            $table->dropColumn('step');
        });
    }
}
