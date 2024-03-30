<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOpeningAndClosingMessageToMessageTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('message_templates', function (Blueprint $table) {
            $table->text('opening_message')->after('name');
            $table->text('closing_message')->after('opening_message');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('message_templates', function (Blueprint $table) {
            //
        });
    }
}
