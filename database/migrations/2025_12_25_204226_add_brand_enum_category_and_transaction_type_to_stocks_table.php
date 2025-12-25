<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddBrandEnumCategoryAndTransactionTypeToStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Drop existing unique constraint if it exists
        try {
            Schema::table('stocks', function (Blueprint $table) {
                $table->dropUnique('unique_stock_entry');
            });
        } catch (\Exception $e) {
            // Constraint might not exist, continue
        }

        Schema::table('stocks', function (Blueprint $table) {
            // Add category field (text type)
            $table->string('category')->nullable()->after('brand');
            
            // Add transaction_type field (in or out)
            $table->enum('transaction_type', ['in', 'out'])->default('in')->after('quantity');
        });

        // Modify brand to be enum if it's currently string
        // First, we need to handle existing data - set default for existing records
        DB::statement("UPDATE stocks SET brand = 'Apple' WHERE brand NOT IN ('Apple', 'Samsung', 'OPPO', 'vivo') OR brand IS NULL");
        
        // Alter brand column to enum
        DB::statement("ALTER TABLE stocks MODIFY brand ENUM('Apple', 'Samsung', 'OPPO', 'vivo') NOT NULL");

        // Recreate unique constraint with new structure
        Schema::table('stocks', function (Blueprint $table) {
            $table->unique(['brand', 'category', 'size', 'color', 'stock_date', 'transaction_type'], 'unique_stock_transaction');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stocks', function (Blueprint $table) {
            $table->dropUnique('unique_stock_transaction');
            $table->dropColumn(['category', 'transaction_type']);
        });
        
        // Revert brand back to string if needed
        DB::statement("ALTER TABLE stocks MODIFY brand VARCHAR(255) NOT NULL");
        
        // Recreate original unique constraint
        Schema::table('stocks', function (Blueprint $table) {
            $table->unique(['brand', 'size', 'color', 'stock_date'], 'unique_stock_entry');
        });
    }
}
