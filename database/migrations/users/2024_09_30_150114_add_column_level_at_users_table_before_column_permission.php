<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnLevelAtUsersTableBeforeColumnPermission extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add column level string to users table.
        Schema::table('users', function (Blueprint $table) {
            $table->string('level')->default('user')->after('image');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop column level from users table.
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('level');
        });
    }
}
