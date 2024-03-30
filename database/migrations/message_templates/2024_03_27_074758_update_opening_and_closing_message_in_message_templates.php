<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateOpeningAndClosingMessageInMessageTemplates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('message_templates', function (Blueprint $table) {
            $table->text('opening_message')->nullable()->change();
            $table->text('closing_message')->nullable()->change();
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
