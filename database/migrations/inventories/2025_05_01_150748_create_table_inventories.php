<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableInventories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inventory_id');
            $table->enum('type', ['in', 'out', 'adjustment'])->default('in');
            $table->string('reference')->nullable(); // nomor referensi dari transaksi
            $table->integer('quantity'); // jumlah barang masuk/keluar
            $table->text('note')->nullable();
            $table->timestamps();

            $table->foreign('inventory_id')->references('id')->on('inventories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('table_inventories');
    }
}
