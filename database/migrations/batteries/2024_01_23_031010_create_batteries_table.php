<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBatteriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('batteries', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('name_alternate', 50)->nullable();
            $table->unsignedBigInteger('brand_id');
            $table->unsignedBigInteger('subbrand_category_id');
            $table->unsignedBigInteger('usage_type_id');
            $table->unsignedBigInteger('size_category_id');
            $table->unsignedBigInteger('technology_id');
            $table->double('dimension_length');
            $table->double('dimension_width');
            $table->double('dimension_height');
            $table->double('standard_cca'); // A
            $table->double('capacity'); // AH
            $table->integer('warranty'); // Months
            $table->double('price_retail');
            $table->string('image')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Set foreign key.
            $table->foreign('brand_id')
                ->references('id')
                ->on('battery_brands');
            $table->foreign('subbrand_category_id')
                ->references('id')
                ->on('battery_subbrand_categories');
            $table->foreign('usage_type_id')
                ->references('id')
                ->on('battery_usage_types');
            $table->foreign('size_category_id')
                ->references('id')
                ->on('battery_size_categories');
            $table->foreign('technology_id')
                ->references('id')
                ->on('battery_technologies');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('batteries');
    }
}
