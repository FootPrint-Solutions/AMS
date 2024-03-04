<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoleMenuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('role_menu', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('menu_id');
            $table->boolean('view');
            $table->boolean('create');
            $table->boolean('update');
            $table->boolean('delete');
            $table->timestamps();

            // Set foreign key.
            $table->foreign('role_id')
                ->references('id')
                ->on('roles');
            $table->foreign('menu_id')
                ->references('id')
                ->on('menus');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('role_menu');
    }
}
