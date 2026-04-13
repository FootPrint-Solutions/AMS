<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableAccountingExpenseDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounting_expense_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cb_expense_id');
            $table->unsignedBigInteger('account_id');
            $table->string('account_name');
            $table->string('description')->nullable();
            $table->double('total');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('cb_expense_id')->references('id')->on('accounting_expenses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accounting_expense_details');
    }
}
