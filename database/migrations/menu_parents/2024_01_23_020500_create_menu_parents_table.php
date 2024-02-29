<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenuParentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu_parents', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('order');
            $table->string('url')->nullable();
            $table->boolean('hide')->default(0);
            $table->string('icon');
            $table->string('role')->default('1')->comment('0: developer, 1: user');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('menu_parents');
    }
}
