<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTipeAtWorkOrdersPrintTemplate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // add kolom tipe di tabel work_order_print_templates sesudah kolom step_no dengan tipe varchar nullable
        Schema::table('work_order_print_templates', function (Blueprint $table) {
            $table->string('tipe')->nullable()->after('step_no');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // hapus kolom tipe di tabel work_order_print_templates
        Schema::table('work_order_print_templates', function (Blueprint $table) {
            $table->dropColumn('tipe');
        });
    }
}
