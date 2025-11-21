<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->string('brand'); // e.g., "samsung", "apple", "nokiya"
            $table->string('size'); // e.g., "256gb"
            $table->string('color'); // e.g., "black", "gold", "blue"
            $table->integer('quantity')->default(0);
            $table->date('stock_date'); // Date of stock entry
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Indexes for faster queries
            $table->index('stock_date');
            $table->index(['brand', 'size', 'color']);
            $table->unique(['brand', 'size', 'color', 'stock_date'], 'unique_stock_entry');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stocks');
    }
}

