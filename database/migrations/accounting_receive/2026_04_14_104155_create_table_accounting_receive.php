<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableAccountingReceive extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounting_receive', function (Blueprint $table) {
            $table->id();
            $table->string('voucher_number')->unique();
            $table->string('to')->nullable();
            $table->string('bank_account_no')->nullable();
            $table->string('address')->nullable();
            $table->unsignedBigInteger('account_id');
            $table->string('account_name');
            $table->date('date');
            $table->double('total');
            $table->enum('status', ['draft', 'post'])->default('draft');
            $table->enum('type', ['cash', 'bank']);
            $table->string('note')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('account_id')->references('id')->on('chart_of_accounts')->onDelete('cascade');
        });

        Schema::create('accounting_receive_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cb_receive_id');
            $table->unsignedBigInteger('account_id');
            $table->string('account_name');
            $table->string('description')->nullable();
            $table->double('total');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('cb_receive_id')->references('id')->on('accounting_receive')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accounting_receive');
        Schema::dropIfExists('accounting_receive_details');
    }
}
