<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddColumnTypeInWorkOrderTemplateDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('work_order_print_template_details', function (Blueprint $table) {
            // Add column type in work order template detail after step no
            $table->string('type')->after('step_no')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('work_order_print_template_details', function (Blueprint $table) {
            // Drop column type in work order template detail
            $table->dropColumn('type');
        });
    }
}
