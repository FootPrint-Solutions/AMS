<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFkBillingInvoiceToBilling extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('billing_invoices', function (Blueprint $table) {
            if (!\Illuminate\Support\Facades\Schema::hasColumn('billing_invoices', 'billing_id')) {
                $table->unsignedBigInteger('billing_id');
            }
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $doctrineTable = $sm->listTableDetails('billing_invoices');
            if (!$doctrineTable->hasForeignKey('billing_invoices_billing_id_foreign')) {
                $table->foreign('billing_id')->references('id')->on('billings')->onDelete('cascade');
            }
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
            $table->dropForeign(['billing_id']);
        });
    }
}
