<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDistributorShopTechniciansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('distributor_shop_technicians', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('distributor_shop_id');
            $table->string('contact');
            $table->string('email')->nullable();
            $table->string('note')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Set foreign key.
            $table->foreign('distributor_shop_id')
                ->references('id')
                ->on('distributor_shops');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('distributor_shop_technicians');
    }
}
