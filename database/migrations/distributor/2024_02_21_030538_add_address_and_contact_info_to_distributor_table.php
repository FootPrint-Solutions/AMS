<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAddressAndContactInfoToDistributorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('distributor', function (Blueprint $table) {
            $table->string('address')->after('name');
            $table->string('contact_person')->after('address');
            $table->string('contact')->after('contact_person');
            $table->string('email')->after('contact');
            $table->string('note')->after('email')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('distributor', function (Blueprint $table) {
            //
        });
    }
}
