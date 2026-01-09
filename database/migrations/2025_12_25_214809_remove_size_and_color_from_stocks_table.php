<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class RemoveSizeAndColorFromStocksTable extends Migration
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
                $table->dropUnique('unique_stock_transaction');
            });
        } catch (\Exception $e) {
            // Try alternative constraint name
            try {
                Schema::table('stocks', function (Blueprint $table) {
                    $table->dropUnique('unique_stock_entry');
                });
            } catch (\Exception $e2) {
                // Constraint might not exist, continue
            }
        }

        // Drop size and color columns
        Schema::table('stocks', function (Blueprint $table) {
            $table->dropColumn(['size', 'color']);
        });

        // Recreate unique constraint without size and color
        Schema::table('stocks', function (Blueprint $table) {
            $table->unique(['brand', 'category', 'stock_date', 'transaction_type'], 'unique_stock_transaction');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop unique constraint
        try {
            Schema::table('stocks', function (Blueprint $table) {
                $table->dropUnique('unique_stock_transaction');
            });
        } catch (\Exception $e) {
            // Continue
        }

        // Add size and color columns back
        Schema::table('stocks', function (Blueprint $table) {
            $table->string('size')->nullable()->after('category');
            $table->string('color')->nullable()->after('size');
        });

        // Recreate unique constraint with size and color
        Schema::table('stocks', function (Blueprint $table) {
            $table->unique(['brand', 'category', 'size', 'color', 'stock_date', 'transaction_type'], 'unique_stock_transaction');
        });
    }
}
