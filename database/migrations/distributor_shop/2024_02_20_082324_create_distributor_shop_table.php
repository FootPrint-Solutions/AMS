<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDistributorShopTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('distributor_shop', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('id_distributor');
            $table->string('address');
            $table->string('contact_person');
            $table->string('contact');
            $table->string('email');
            $table->string('note')->nullable();
            $table->timestamps();

            /*
            $table->foreign('id_distributor')
                ->references('id')
                ->on('distributor');
            */
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('distributor_shop');
    }
}
