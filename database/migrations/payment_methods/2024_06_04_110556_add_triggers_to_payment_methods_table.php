<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddTriggersToPaymentMethodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
            CREATE TRIGGER prevent_update_on_specific_columns
            BEFORE UPDATE ON payment_methods
            FOR EACH ROW
            BEGIN
                IF OLD.id = 1 AND (NEW.status <> OLD.status OR NEW.note <> OLD.note) THEN
                    SIGNAL SQLSTATE "45000" SET MESSAGE_TEXT = "Updating status or note for this row is not allowed";
                END IF;
            END;
        ');

        DB::unprepared('
            CREATE TRIGGER prevent_delete_on_default_payment_method
            BEFORE DELETE ON payment_methods
            FOR EACH ROW
            BEGIN
                IF OLD.id = 1 THEN
                    SIGNAL SQLSTATE "45000" SET MESSAGE_TEXT = "This row cannot be deleted";
                END IF;
            END;
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payment_methods', function (Blueprint $table) {
            //
        });
    }
}
