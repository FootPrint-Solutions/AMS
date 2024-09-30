<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnPermissionAtUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add column permission to users table.
        Schema::table('users', function (Blueprint $table) {
            $table->string('permission')->default('')->after('image');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop column permission from users table.
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('permission');
        });
    }
}
