<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnSourceAtTableBillingInvoiceExpense extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('billing_invoice_expenses', function (Blueprint $table) {
            $table->string('source')->after('amount')->default('sales_order');
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
            $table->dropColumn('source');
        });
    }
}
