<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnInvoiceIdAtTableJournalEntryDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('journal_entry_details', function (Blueprint $table) {
            $table->unsignedBigInteger('invoice_id')->nullable()->after('ref');
            $table->string('invoice_type')->nullable()->after('invoice_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('journal_entry_details', function (Blueprint $table) {
            $table->dropColumn('invoice_id');
            $table->dropColumn('invoice_type');
        });
    }
}
