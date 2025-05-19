<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnWhatsappStatusAtTableSalesOnline extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales_online', function (Blueprint $table) {
            $table->enum('whatsapp_status', ['pending', 'sent', 'failed'])
                ->default('pending')
                ->after('address')
                ->comment('Status of WhatsApp message: pending, sent, failed');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
