<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnAttachmentFileWorkOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // add column attachment_file to work_order table after column image
        Schema::table('work_orders', function (Blueprint $table) {
            $table->string('attachment_file')->nullable()->after('image');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // drop column attachment_file from work_order table
        Schema::table('work_orders', function (Blueprint $table) {
            $table->dropColumn('attachment_file');
        });
    }
}
