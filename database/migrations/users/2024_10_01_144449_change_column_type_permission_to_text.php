<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnTypePermissionToText extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Hapus foreign key constraint pada kolom role_id
            if (Schema::hasColumn('users', 'role_id')) {
                $table->dropForeign(['role_id']); // pastikan nama foreign key benar
                $table->dropColumn('role_id'); // drop kolom role_id
            }

            // Ubah tipe kolom permission menjadi TEXT
            $table->text('permission')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Kembalikan tipe kolom permission menjadi string (VARCHAR)
            $table->string('permission')->change();

            // Tambahkan kembali kolom role_id dan foreign key jika dibutuhkan
            $table->unsignedBigInteger('role_id')->nullable();

            // Restore foreign key constraint, pastikan tabel rolesx dan kolom id benar
            $table->foreign('role_id')->references('id')->on('rolesx')->onDelete('restrict')->onUpdate('restrict');
        });
    }
}
