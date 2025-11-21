<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class MakeSizeAndColorNullableInStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Drop the unique constraint first if it exists
        try {
            Schema::table('stocks', function (Blueprint $table) {
                $table->dropUnique('unique_stock_entry');
            });
        } catch (\Exception $e) {
            // Constraint might not exist, continue
        }
        
        // Modify columns to be nullable using raw SQL
        DB::statement('ALTER TABLE stocks MODIFY size VARCHAR(255) NULL');
        DB::statement('ALTER TABLE stocks MODIFY color VARCHAR(255) NULL');
        
        // Recreate unique constraint (MySQL allows multiple NULLs in unique constraints)
        try {
            Schema::table('stocks', function (Blueprint $table) {
                $table->unique(['brand', 'size', 'color', 'stock_date'], 'unique_stock_entry');
            });
        } catch (\Exception $e) {
            // Constraint might already exist, continue
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop the unique constraint
        Schema::table('stocks', function (Blueprint $table) {
            $table->dropUnique('unique_stock_entry');
        });
        
        // Make columns not nullable again using raw SQL
        DB::statement('ALTER TABLE stocks MODIFY size VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE stocks MODIFY color VARCHAR(255) NOT NULL');
        
        // Recreate unique constraint
        Schema::table('stocks', function (Blueprint $table) {
            $table->unique(['brand', 'size', 'color', 'stock_date'], 'unique_stock_entry');
        });
    }
}
