<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

class CreateMenuSubTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu_sub', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('id_menu');
            $table->integer('order');
            $table->string('url');
            $table->boolean('hide')->default(0);
            $table->timestamps();

            /*
            $table->foreign('id_menu')
                ->references('id')
                ->on('menu');
            */

            Artisan::call('db:seed', array('--class' => 'MenuSubSeeder', '--force' => true));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('menu_sub');
    }
}
