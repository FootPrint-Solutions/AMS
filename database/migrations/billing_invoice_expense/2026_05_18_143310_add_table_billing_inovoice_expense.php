<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTableBillingInovoiceExpense extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('billing_invoice_expenses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('billing_invoice_id');
            $table->unsignedBigInteger('sales_invoice_id')->nullable();
            $table->unsignedBigInteger('debit_account_id')->nullable();
            $table->unsignedBigInteger('credit_account_id')->nullable();
            $table->string('description');
            $table->decimal('amount', 15, 2);
            $table->timestamps();

            $table->foreign('billing_invoice_id')->references('id')->on('billing_invoices')->onDelete('cascade');
            $table->foreign('sales_invoice_id')->references('id')->on('sales_invoices')->onDelete('set null');
            $table->foreign('debit_account_id')->references('id')->on('chart_of_accounts')->onDelete('set null');
            $table->foreign('credit_account_id')->references('id')->on('chart_of_accounts')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('billing_invoice_expenses');
    }
}
