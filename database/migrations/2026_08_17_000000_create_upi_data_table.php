<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUpiDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('upi_data', function (Blueprint $table) {
            $table->id();
            $table->string('upi_serial_num')->nullable();
            $table->string('customer_name');
            $table->string('customer_number');
            $table->decimal('amount', 10, 2);
            $table->string('customer_photo')->nullable();
            $table->string('customer_id_photo')->nullable();
            $table->string('upi_screenshot_photo')->nullable();
            $table->text('comment')->nullable();
            $table->string('deviceuniqueid')->nullable();
            $table->string('devicename')->nullable();
            $table->integer('is_deleted')->default(0);
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
        Schema::dropIfExists('upi_data');
    }
}
