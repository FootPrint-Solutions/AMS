<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveFkAtTableBillingInvoiceExpense extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('billing_invoice_expenses', function (Blueprint $table) {
            $table->dropForeign(['billing_invoice_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('billing_invoice_expenses', function (Blueprint $table) {
            $table->foreign('billing_invoice_id')->references('id')->on('billing_invoices');
        });
    }
}
