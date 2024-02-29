<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDistributorShopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('distributor_shops', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('distributor_id');
            $table->tinyInteger('type')->default('0')->comment('0: separate shop, 1: with distributor address');
            $table->text('address');
            $table->text('latitude')->nullable();
            $table->text('longitude')->nullable();
            $table->string('contact_person');
            $table->string('contact');
            $table->string('email')->nullable();
            $table->string('note')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Set foreign key.
            $table->foreign('distributor_id')
                ->references('id')
                ->on('distributors');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('distributor_shops');
    }
}
