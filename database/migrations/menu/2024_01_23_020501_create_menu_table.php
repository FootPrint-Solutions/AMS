<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('id_parent');
            $table->integer('order');
            $table->string('url');
            $table->boolean('hide')->default(0);
            $table->timestamps();

            /*
            $table->foreign('id_parent')
                ->references('id')
                ->on('menu_parent');
            */
        });

        // Artisan::call('db:seed', array('--class' => 'MenuSeeder'));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('menu');
    }
}
