<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUpdatedBy extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add updated_by column
        Schema::table('work_order_instructions', function (Blueprint $table) {
            // add column created_by
            $table->unsignedBigInteger('updated_by')->nullable()->after('updated_at');
            $table->foreign('updated_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop updated_by column
        Schema::table('work_order_instructions', function (Blueprint $table) {
            $table->dropForeign(['updated_by']);
            $table->dropColumn('updated_by');
        });
    }
}
