<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Stock;
use Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    /**
     * Display stock management page
     */
    public function index()
    {
        return view('admin.stocks.index');
    }

    /**
     * Get stock list for admin panel
     */
    public function getStockList(Request $request)
    {
        $date = $request->get('date', Carbon::today()->toDateString());
        
        $stocks = Stock::where('stock_date', $date)
            ->orderBy('brand')
            ->orderBy('category')
            ->orderBy('size')
            ->orderBy('color')
            ->orderBy('transaction_type')
            ->get();

        return response()->json([
            'data' => $stocks,
            'date' => $date,
            'message' => 'Stock list retrieved successfully'
        ], 200);
    }

    /**
     * Get current stock summary
     */
    public function getCurrentStock()
    {
        // Get all unique stock combinations
        $uniqueStocks = Stock::select('brand', 'category', 'size', 'color')
            ->groupBy('brand', 'category', 'size', 'color')
            ->get();

        $currentStocks = [];

        foreach ($uniqueStocks as $uniqueStock) {
            // Calculate total in and out quantities
            $queryIn = Stock::where('brand', $uniqueStock->brand);
            $queryOut = Stock::where('brand', $uniqueStock->brand);
            
            // Handle category
            if ($uniqueStock->category) {
                $queryIn->where('category', $uniqueStock->category);
                $queryOut->where('category', $uniqueStock->category);
            } else {
                $queryIn->whereNull('category');
                $queryOut->whereNull('category');
            }
            
            // Handle size
            if ($uniqueStock->size) {
                $queryIn->where('size', $uniqueStock->size);
                $queryOut->where('size', $uniqueStock->size);
            } else {
                $queryIn->whereNull('size');
                $queryOut->whereNull('size');
            }
            
            // Handle color
            if ($uniqueStock->color) {
                $queryIn->where('color', $uniqueStock->color);
                $queryOut->where('color', $uniqueStock->color);
            } else {
                $queryIn->whereNull('color');
                $queryOut->whereNull('color');
            }
            
            $totalIn = $queryIn->where('transaction_type', 'in')->sum('quantity');
            $totalOut = $queryOut->where('transaction_type', 'out')->sum('quantity');
            $currentQuantity = $totalIn - $totalOut;

            $currentStocks[] = [
                'brand' => $uniqueStock->brand,
                'category' => $uniqueStock->category,
                'size' => $uniqueStock->size,
                'color' => $uniqueStock->color,
                'total_in' => $totalIn,
                'total_out' => $totalOut,
                'current_quantity' => $currentQuantity,
            ];
        }

        // Sort by brand, category, size, color
        usort($currentStocks, function($a, $b) {
            $brandCompare = strcmp($a['brand'], $b['brand']);
            if ($brandCompare !== 0) return $brandCompare;
            
            $categoryCompare = strcmp($a['category'] ?? '', $b['category'] ?? '');
            if ($categoryCompare !== 0) return $categoryCompare;
            
            $sizeCompare = strcmp($a['size'] ?? '', $b['size'] ?? '');
            if ($sizeCompare !== 0) return $sizeCompare;
            
            return strcmp($a['color'] ?? '', $b['color'] ?? '');
        });

        return response()->json([
            'data' => $currentStocks,
            'total_items' => count($currentStocks),
            'message' => 'Current stock retrieved successfully'
        ], 200);
    }

    /**
     * Get transactions by date
     */
    public function getTransactionsByDate(Request $request)
    {
        $date = $request->get('date', Carbon::today()->toDateString());
        $brand = $request->get('brand');
        $category = $request->get('category');

        $query = Stock::where('stock_date', $date);
        
        if ($brand) {
            $query->where('brand', $brand);
        }
        
        if ($category) {
            $query->where('category', $category);
        }

        $transactions = $query->orderBy('brand')
            ->orderBy('category')
            ->orderBy('size')
            ->orderBy('color')
            ->orderBy('transaction_type')
            ->get();

        // Group by item and show in/out totals
        $result = [];
        foreach ($transactions as $transaction) {
            $key = $transaction->brand . '|' . ($transaction->category ?? '') . '|' . 
                   ($transaction->size ?? '') . '|' . ($transaction->color ?? '');
            
            if (!isset($result[$key])) {
                $result[$key] = [
                    'brand' => $transaction->brand,
                    'category' => $transaction->category,
                    'size' => $transaction->size,
                    'color' => $transaction->color,
                    'in_quantity' => 0,
                    'out_quantity' => 0,
                    'transactions' => []
                ];
            }
            
            if ($transaction->transaction_type === 'in') {
                $result[$key]['in_quantity'] += $transaction->quantity;
            } else {
                $result[$key]['out_quantity'] += $transaction->quantity;
            }
            
            $result[$key]['transactions'][] = [
                'id' => $transaction->id,
                'transaction_type' => $transaction->transaction_type,
                'quantity' => $transaction->quantity,
                'notes' => $transaction->notes,
                'created_at' => $transaction->created_at,
            ];
        }

        return response()->json([
            'data' => array_values($result),
            'date' => $date,
            'message' => 'Transactions retrieved successfully'
        ], 200);
    }

    /**
     * Get brands grouped by category with available stock
     */
    public function getBrandsGroupedByCategory()
    {
        // Get all unique stock combinations
        $uniqueStocks = Stock::select('brand', 'category', 'size', 'color')
            ->groupBy('brand', 'category', 'size', 'color')
            ->get();

        $groupedData = [];

        foreach ($uniqueStocks as $uniqueStock) {
            // Calculate total in and out quantities
            $queryIn = Stock::where('brand', $uniqueStock->brand);
            $queryOut = Stock::where('brand', $uniqueStock->brand);
            
            // Handle category
            if ($uniqueStock->category) {
                $queryIn->where('category', $uniqueStock->category);
                $queryOut->where('category', $uniqueStock->category);
            } else {
                $queryIn->whereNull('category');
                $queryOut->whereNull('category');
            }
            
            // Handle size
            if ($uniqueStock->size) {
                $queryIn->where('size', $uniqueStock->size);
                $queryOut->where('size', $uniqueStock->size);
            } else {
                $queryIn->whereNull('size');
                $queryOut->whereNull('size');
            }
            
            // Handle color
            if ($uniqueStock->color) {
                $queryIn->where('color', $uniqueStock->color);
                $queryOut->where('color', $uniqueStock->color);
            } else {
                $queryIn->whereNull('color');
                $queryOut->whereNull('color');
            }
            
            $totalIn = $queryIn->where('transaction_type', 'in')->sum('quantity');
            $totalOut = $queryOut->where('transaction_type', 'out')->sum('quantity');
            $currentQuantity = $totalIn - $totalOut;

            // Skip items with zero or negative stock
            if ($currentQuantity <= 0) {
                continue;
            }

            $brand = $uniqueStock->brand;
            $category = $uniqueStock->category ?? 'Uncategorized';

            // Initialize brand if not exists
            if (!isset($groupedData[$brand])) {
                $groupedData[$brand] = [
                    'brand' => $brand,
                    'total_stock' => 0,
                    'categories' => []
                ];
            }

            // Initialize category if not exists
            if (!isset($groupedData[$brand]['categories'][$category])) {
                $groupedData[$brand]['categories'][$category] = [
                    'category' => $category,
                    'total_stock' => 0,
                    'items' => []
                ];
            }

            // Add item to category
            $groupedData[$brand]['categories'][$category]['items'][] = [
                'size' => $uniqueStock->size,
                'color' => $uniqueStock->color,
                'total_in' => $totalIn,
                'total_out' => $totalOut,
                'current_quantity' => $currentQuantity,
            ];

            // Update totals
            $groupedData[$brand]['categories'][$category]['total_stock'] += $currentQuantity;
            $groupedData[$brand]['total_stock'] += $currentQuantity;
        }

        // Convert to array format and sort
        $result = [];
        foreach ($groupedData as $brand => $brandData) {
            $categories = [];
            foreach ($brandData['categories'] as $category => $categoryData) {
                $categories[] = $categoryData;
            }
            
            // Sort categories by total stock (descending)
            usort($categories, function($a, $b) {
                return $b['total_stock'] - $a['total_stock'];
            });

            $result[] = [
                'brand' => $brand,
                'total_stock' => $brandData['total_stock'],
                'categories' => $categories
            ];
        }

        // Sort brands by total stock (descending)
        usort($result, function($a, $b) {
            return $b['total_stock'] - $a['total_stock'];
        });

        return response()->json([
            'data' => $result,
            'message' => 'Brands grouped by category retrieved successfully'
        ], 200);
    }
}

