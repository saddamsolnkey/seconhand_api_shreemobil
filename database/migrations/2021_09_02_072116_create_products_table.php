<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('mobile_name')->nullable();
            $table->string('mobile_emi')->nullable();
            $table->string('mobile_photo')->nullable();
            $table->string('mobile_bill_photo')->nullable();
            $table->float('mobile_price')->nullable();
            $table->timestamp('buy_date')->nullable();
            $table->integer('buyer_id_photo')->nullable();
            $table->integer('seller_id_photo')->nullable();
            $table->string('agent_name')->nullable();
            $table->string('sell_status')->nullable();
            $table->integer('user_id')->nullable();
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
        Schema::dropIfExists('products');
    }
}
