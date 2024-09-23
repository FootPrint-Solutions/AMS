<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableWorkOrderTechnicianPhotos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('work_order_instruction_photos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('work_order_instruction_id');
            $table->foreign('work_order_instruction_id')->references('id')->on('work_order_instructions')->onDelete('cascade');
            $table->string('image');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('work_order_instruction_photos');
    }
}
