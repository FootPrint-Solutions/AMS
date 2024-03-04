<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

class CreateMenuSubsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu_subs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('menu_id');
            $table->integer('order');
            $table->string('url');
            $table->boolean('hide')->default(0);
            $table->timestamps();

            // Set foreign key.
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
        Schema::dropIfExists('menu_subs');
    }
}
