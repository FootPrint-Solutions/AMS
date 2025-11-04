<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewTableInventories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('battery_id');
            $table->string('code', 255)->nullable();
            $table->double('stock')->default(0);
            $table->timestamps();

            $table->unique('battery_id');
            $table->unique('code');

            $table->foreign('battery_id', 'inventories_battery_id_fk')
                ->references('id')->on('batteries')
                ->onDelete('restrict')
                ->onUpdate('restrict');
        });

        Schema::create('inventory_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('inventory_id');
            $table->unsignedBigInteger('battery_id')->nullable();
            $table->enum('type', ['in', 'out', 'adjustment'])->default('in');
            $table->string('reference', 255)->nullable();
            $table->integer('quantity');
            $table->boolean('sold')->default(0);
            $table->timestamp('sold_at')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('reference_type', 255)->nullable();

            $table->index('inventory_id', 'inventory_details_inventory_id_idx');
            $table->index('battery_id', 'inventory_details_battery_id_idx');

            $table->foreign('battery_id', 'inventory_details_battery_id_fk')
                ->references('id')->on('batteries')
                ->onDelete('cascade')
                ->onUpdate('restrict');

            $table->foreign('inventory_id', 'inventory_details_inventory_id_fk')
                ->references('id')->on('inventories')
                ->onDelete('cascade')
                ->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inventory_details');
        Schema::dropIfExists('inventories');
    }
}
