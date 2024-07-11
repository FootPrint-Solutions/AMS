<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnLevelUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // add column level enum ('user', 'developer') default 'user' after column image in table users
        Schema::table('users', function (Blueprint $table) {
            $table->enum('level', ['user', 'developer'])->default('user')->after('image');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // drop column level in table users
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('level');
        });
    }
}
