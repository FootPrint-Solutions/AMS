<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnSalesInvoiceIdToSalesOrderIdAtTableBillingInvoiceExpense extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('billing_invoice_expenses', function (Blueprint $table) {
            $table->dropForeign(['sales_invoice_id']);
            $table->dropColumn('sales_invoice_id');
            $table->unsignedBigInteger('sales_order_id')->nullable()->after('billing_invoice_id');
            $table->foreign('sales_order_id')->references('id')->on('sales_orders')->onDelete('set null');
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
            $table->dropForeign(['sales_order_id']);
            $table->dropColumn('sales_order_id');
            $table->unsignedBigInteger('sales_invoice_id')->nullable()->after('billing_invoice_id');
            $table->foreign('sales_invoice_id')->references('id')->on('sales_invoices')->onDelete('set null');
        });
    }
}
