<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableBackupBattery extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('battery_backups', function (Blueprint $table) {
            $table->id();
            $table->string('backup_number', 255);
            $table->dateTime('backup_date');
            $table->unsignedBigInteger('battery_id')->nullable();
            $table->string('code', 255)->nullable();
            $table->string('name', 100);
            $table->string('name_alternate', 50)->nullable();
            $table->foreignId('brand_id')->constrained('battery_brands')->onDelete('restrict')->onUpdate('restrict');
            $table->foreignId('subbrand_category_id')->nullable()->constrained('battery_subbrand_categories')->onDelete('restrict')->onUpdate('restrict');
            $table->foreignId('usage_type_id')->nullable()->constrained('battery_usage_types')->onDelete('restrict')->onUpdate('restrict');
            $table->foreignId('size_category_id')->nullable()->constrained('battery_size_categories')->onDelete('restrict')->onUpdate('restrict');
            $table->foreignId('technology_id')->nullable()->constrained('battery_technologies')->onDelete('restrict')->onUpdate('restrict');
            $table->double('dimension_length')->default(0);
            $table->double('dimension_width')->default(0);
            $table->double('dimension_height')->default(0);
            $table->double('standard_cca')->default(0);
            $table->double('capacity')->default(0);
            $table->integer('warranty')->default(0);
            $table->double('price_retail')->default(0);
            $table->double('price_buy')->nullable();
            $table->string('image', 255)->nullable();
            $table->boolean('status')->default(true)->comment('0: inactive, 1: active');
            $table->string('type', 255)->default('regular');
            $table->boolean('editable_price')->default(false);
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
        Schema::dropIfExists('battery_backups');
    }
}
