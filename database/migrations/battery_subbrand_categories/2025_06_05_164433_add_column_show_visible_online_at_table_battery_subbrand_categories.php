<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnShowVisibleOnlineAtTableBatterySubbrandCategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('battery_subbrand_categories', function (Blueprint $table) {
            $table->boolean('show_visible_online')->default(true)->after('deleted_at');
            $table->timestamp('visible_online_at')->nullable()->after('show_visible_online');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('battery_subbrand_categories', function (Blueprint $table) {
            $table->dropColumn('show_visible_online');
            $table->dropColumn('visible_online_at');
        });
    }
}
