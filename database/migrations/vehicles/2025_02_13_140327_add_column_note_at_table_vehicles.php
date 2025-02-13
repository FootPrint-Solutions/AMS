<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnNoteAtTableVehicles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add column note at table vehicles
        Schema::table('vehicles', function (Blueprint $table) {
            $table->text('note')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop column note at table vehicles
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn('note');
        });
    }
}
