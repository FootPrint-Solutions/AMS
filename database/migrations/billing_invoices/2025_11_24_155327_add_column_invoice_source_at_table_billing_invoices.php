<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnInvoiceSourceAtTableBillingInvoices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('billing_invoices', function (Blueprint $table) {
            $table->string('invoice_source')->nullable()->after('invoice_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('billing_invoices', function (Blueprint $table) {
            $table->dropColumn('invoice_source');
        });
    }
}
