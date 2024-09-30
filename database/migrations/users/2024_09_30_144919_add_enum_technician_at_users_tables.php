<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEnumTechnicianAtUsersTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // drop column level from users table.
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('level');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // add column level to users table.
        Schema::table('users', function (Blueprint $table) {
            $table->enum('level', ['admin', 'technician', 'user'])->default('user')->after('email');
        });
    }
}
