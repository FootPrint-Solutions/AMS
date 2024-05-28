e<?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateBatteryPricesTable extends Migration
    {
        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create('battery_prices', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('battery_id');
                $table->string('period')->nullable();
                $table->decimal('discount', 5, 2)->default(0)->nullable();
                $table->double('net_price')->default(0);
                $table->timestamps();
                $table->softDeletes();

                // Set foreign key.
                $table->foreign('battery_id')
                    ->references('id')
                    ->on('batteries');
            });
        }

        /**
         * Reverse the migrations.
         *
         * @return void
         */
        public function down()
        {
            Schema::dropIfExists('battery_prices');
        }
    }
