<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnAtTableDistribituroShop extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('distributor_shop_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('distributor_shop_id');
            $table->string('type');
            $table->unsignedBigInteger('chart_of_account_id');
            $table->double('commission')->default(0);
            $table->timestamps();

            $table->foreign('distributor_shop_id')
                ->references('id')
                ->on('distributor_shops')
                ->onDelete('cascade');

            $table->foreign('chart_of_account_id')
                ->references('id')
                ->on('chart_of_accounts')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('distributor_shop_accounts');
    }
}
