<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableJurnalEntryDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('journal_entry_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('journal_entry_id');
            $table->unsignedBigInteger('chart_of_account_id');
            $table->string('account_number');
            $table->string('account_name');
            $table->text('description')->nullable();
            $table->double('debit');
            $table->double('credit');
            $table->timestamps();

            $table->foreign('journal_entry_id')->references('id')->on('journal_entries');
            $table->foreign('chart_of_account_id')->references('id')->on('chart_of_accounts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('journal_entry_details');
    }
}
