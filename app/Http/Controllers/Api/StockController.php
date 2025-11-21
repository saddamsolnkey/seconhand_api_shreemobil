<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Stock;
use Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    /**
     * Get stock list for a specific date or all stocks
     * GET /api/stock-list?date=2025-11-21 (optional - if not provided, returns all stocks)
     */
    public function stockList(Request $request)
    {
        $date = $request->get('date');
        
        $query = Stock::query();
        
        // If date is provided, filter by date; otherwise get all stocks
        if ($date) {
            $query->where('stock_date', $date);
        }
        
        $stocks = $query->orderBy('stock_date', 'desc')
            ->orderBy('brand')
            ->orderBy('size')
            ->orderBy('color')
            ->get();

        return response([
            'data' => $stocks,
            'date' => $date ? $date : 'all',
            'message' => $date ? 'Stock list retrieved successfully for ' . $date : 'All stock list retrieved successfully'
        ], 200);
    }

    /**
     * Add or update stock for a date (single item)
     * POST /api/stock-add
     * Only brand is required, other fields are optional
     */
    public function stockAdd(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'brand' => 'required|string|max:255',
            'size' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:255',
            'quantity' => 'nullable|integer|min:0',
            'stock_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response([
                'error' => $validator->errors(),
                'message' => 'Validation Error'
            ], 422);
        }

        $data = $request->all();
        
        // Set default values if not provided
        if (!isset($data['quantity'])) {
            $data['quantity'] = 0;
        }
        if (!isset($data['stock_date'])) {
            $data['stock_date'] = Carbon::today()->toDateString();
        }
        if (!isset($data['size'])) {
            $data['size'] = null;
        }
        if (!isset($data['color'])) {
            $data['color'] = null;
        }
        
        // Check if stock entry already exists (match by brand, size, color, and date)
        $query = Stock::where('brand', $data['brand']);
        
        if (isset($data['size']) && $data['size'] !== null) {
            $query->where('size', $data['size']);
        } else {
            $query->whereNull('size');
        }
        
        if (isset($data['color']) && $data['color'] !== null) {
            $query->where('color', $data['color']);
        } else {
            $query->whereNull('color');
        }
        
        $query->where('stock_date', $data['stock_date']);
        $stock = $query->first();

        if ($stock) {
            // Update existing
            if (isset($data['quantity'])) {
                $stock->quantity = $data['quantity'];
            }
            if (isset($data['notes'])) {
                $stock->notes = $data['notes'];
            }
            if (isset($data['size'])) {
                $stock->size = $data['size'];
            }
            if (isset($data['color'])) {
                $stock->color = $data['color'];
            }
            $stock->save();
            
            return response([
                'data' => $stock,
                'message' => 'Stock updated successfully'
            ], 200);
        } else {
            // Create new
            $stock = Stock::create($data);
            
            return response([
                'data' => $stock,
                'message' => 'Stock added successfully'
            ], 201);
        }
    }

    /**
     * Add multiple stocks for a date (bulk add)
     * POST /api/stock-bulk-add
     * Only brand is required in stocks array, other fields are optional
     */
    public function stockBulkAdd(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'stock_date' => 'nullable|date',
            'stocks' => 'required|array|min:1',
            'stocks.*.brand' => 'required|string|max:255',
            'stocks.*.size' => 'nullable|string|max:255',
            'stocks.*.color' => 'nullable|string|max:255',
            'stocks.*.quantity' => 'nullable|integer|min:0',
            'stocks.*.notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response([
                'error' => $validator->errors(),
                'message' => 'Validation Error'
            ], 422);
        }

        // Set default date if not provided
        $stockDate = $request->stock_date ? $request->stock_date : Carbon::today()->toDateString();
        $stocks = [];
        $created = 0;
        $updated = 0;

        foreach ($request->stocks as $stockData) {
            // Set default values if not provided
            if (!isset($stockData['quantity'])) {
                $stockData['quantity'] = 0;
            }
            if (!isset($stockData['size'])) {
                $stockData['size'] = null;
            }
            if (!isset($stockData['color'])) {
                $stockData['color'] = null;
            }
            $stockData['stock_date'] = $stockDate;
            
            // Check if stock entry already exists
            $query = Stock::where('brand', $stockData['brand']);
            
            if (isset($stockData['size']) && $stockData['size'] !== null) {
                $query->where('size', $stockData['size']);
            } else {
                $query->whereNull('size');
            }
            
            if (isset($stockData['color']) && $stockData['color'] !== null) {
                $query->where('color', $stockData['color']);
            } else {
                $query->whereNull('color');
            }
            
            $query->where('stock_date', $stockDate);
            $stock = $query->first();

            if ($stock) {
                // Update existing
                if (isset($stockData['quantity'])) {
                    $stock->quantity = $stockData['quantity'];
                }
                if (isset($stockData['notes'])) {
                    $stock->notes = $stockData['notes'];
                }
                if (isset($stockData['size'])) {
                    $stock->size = $stockData['size'];
                }
                if (isset($stockData['color'])) {
                    $stock->color = $stockData['color'];
                }
                $stock->save();
                $updated++;
            } else {
                // Create new
                $stock = Stock::create($stockData);
                $created++;
            }
            $stocks[] = $stock;
        }

        return response([
            'data' => $stocks,
            'created' => $created,
            'updated' => $updated,
            'message' => "Stock bulk add completed. Created: $created, Updated: $updated"
        ], 201);
    }

    /**
     * Update stock by ID
     * POST /api/stock-update/{id}
     * All fields are optional - only update what is provided
     */
    public function stockUpdate(Request $request, $id)
    {
        $stock = Stock::find($id);

        if (!$stock) {
            return response([
                'error' => 'Stock not found',
                'message' => 'Stock with ID ' . $id . ' does not exist'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'brand' => 'nullable|string|max:255',
            'size' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:255',
            'quantity' => 'nullable|integer|min:0',
            'stock_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response([
                'error' => $validator->errors(),
                'message' => 'Validation Error'
            ], 422);
        }

        // Update only provided fields
        if ($request->has('brand')) {
            $stock->brand = $request->brand;
        }
        if ($request->has('size')) {
            $stock->size = $request->size;
        }
        if ($request->has('color')) {
            $stock->color = $request->color;
        }
        if ($request->has('quantity')) {
            $stock->quantity = $request->quantity;
        }
        if ($request->has('stock_date')) {
            $stock->stock_date = $request->stock_date;
        }
        if ($request->has('notes')) {
            $stock->notes = $request->notes;
        }
        
        $stock->save();

        return response([
            'data' => $stock,
            'message' => 'Stock updated successfully'
        ], 200);
    }

    /**
     * Delete stock
     * GET /api/stock-delete/{id}
     */
    public function stockDelete($id)
    {
        $stock = Stock::find($id);

        if (!$stock) {
            return response([
                'error' => 'Stock not found',
                'message' => 'Stock with ID ' . $id . ' does not exist'
            ], 404);
        }

        $stock->delete();

        return response([
            'message' => 'Stock deleted successfully'
        ], 200);
    }

    /**
     * Get daily report with quantity changes
     * GET /api/stock-daily-report?date=2025-11-22
     */
    public function dailyReport(Request $request)
    {
        $date = $request->get('date', Carbon::today()->toDateString());
        $currentDate = Carbon::parse($date);
        $previousDate = $currentDate->copy()->subDay();

        // Get current date stocks
        $currentStocks = Stock::where('stock_date', $date)
            ->orderBy('brand')
            ->orderBy('size')
            ->orderBy('color')
            ->get();

        // Get previous date stocks
        $previousStocks = Stock::where('stock_date', $previousDate->toDateString())
            ->get()
            ->keyBy(function ($item) {
                return $item->brand . '|' . $item->size . '|' . $item->color;
            });

        $report = [];
        foreach ($currentStocks as $current) {
            $key = $current->brand . '|' . $current->size . '|' . $current->color;
            $previous = $previousStocks->get($key);

            $previousQuantity = $previous ? $previous->quantity : 0;
            $change = $current->quantity - $previousQuantity;
            $changeType = $change > 0 ? 'plus' : ($change < 0 ? 'minus' : 'no_change');
            $changeText = $change > 0 ? "+$change" : ($change < 0 ? "$change" : "0");

            $report[] = [
                'id' => $current->id,
                'brand' => $current->brand,
                'size' => $current->size,
                'color' => $current->color,
                'quantity' => $current->quantity,
                'previous_quantity' => $previousQuantity,
                'change' => $change,
                'change_type' => $changeType,
                'change_text' => $changeText,
                'stock_date' => $current->stock_date,
            ];
        }

        return response([
            'data' => $report,
            'date' => $date,
            'previous_date' => $previousDate->toDateString(),
            'message' => 'Daily report retrieved successfully'
        ], 200);
    }

    /**
     * Get weekly report with quantity changes
     * GET /api/stock-weekly-report?week_start=2025-11-18
     */
    public function weeklyReport(Request $request)
    {
        $weekStart = $request->get('week_start');
        
        if ($weekStart) {
            $startDate = Carbon::parse($weekStart);
        } else {
            // Default to start of current week (Monday)
            $startDate = Carbon::now()->startOfWeek();
        }
        
        $endDate = $startDate->copy()->endOfWeek();

        // Get all stocks in the week
        $stocks = Stock::whereBetween('stock_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->orderBy('stock_date')
            ->orderBy('brand')
            ->orderBy('size')
            ->orderBy('color')
            ->get();

        // Group by date
        $dailyReports = [];
        $dates = [];

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dateStr = $date->toDateString();
            $dates[] = $dateStr;
            
            $dayStocks = $stocks->where('stock_date', $dateStr);
            $previousDate = $date->copy()->subDay();
            $previousStocks = Stock::where('stock_date', $previousDate->toDateString())
                ->get()
                ->keyBy(function ($item) {
                    return $item->brand . '|' . $item->size . '|' . $item->color;
                });

            $dayReport = [];
            foreach ($dayStocks as $current) {
                $key = $current->brand . '|' . $current->size . '|' . $current->color;
                $previous = $previousStocks->get($key);

                $previousQuantity = $previous ? $previous->quantity : 0;
                $change = $current->quantity - $previousQuantity;
                $changeType = $change > 0 ? 'plus' : ($change < 0 ? 'minus' : 'no_change');
                $changeText = $change > 0 ? "+$change" : ($change < 0 ? "$change" : "0");

                $dayReport[] = [
                    'brand' => $current->brand,
                    'size' => $current->size,
                    'color' => $current->color,
                    'quantity' => $current->quantity,
                    'previous_quantity' => $previousQuantity,
                    'change' => $change,
                    'change_type' => $changeType,
                    'change_text' => $changeText,
                ];
            }

            $dailyReports[$dateStr] = $dayReport;
        }

        return response([
            'data' => $dailyReports,
            'week_start' => $startDate->toDateString(),
            'week_end' => $endDate->toDateString(),
            'dates' => $dates,
            'message' => 'Weekly report retrieved successfully'
        ], 200);
    }

    /**
     * Get monthly report with quantity changes
     * GET /api/stock-monthly-report?month=2025-11
     */
    public function monthlyReport(Request $request)
    {
        $monthInput = $request->get('month');
        
        if ($monthInput) {
            $startDate = Carbon::parse($monthInput)->startOfMonth();
        } else {
            $startDate = Carbon::now()->startOfMonth();
        }
        
        $endDate = $startDate->copy()->endOfMonth();

        // Get all stocks in the month
        $stocks = Stock::whereBetween('stock_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->orderBy('stock_date')
            ->orderBy('brand')
            ->orderBy('size')
            ->orderBy('color')
            ->get();

        // Group by date
        $dailyReports = [];
        $dates = [];

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dateStr = $date->toDateString();
            $dates[] = $dateStr;
            
            $dayStocks = $stocks->where('stock_date', $dateStr);
            $previousDate = $date->copy()->subDay();
            $previousStocks = Stock::where('stock_date', $previousDate->toDateString())
                ->get()
                ->keyBy(function ($item) {
                    return $item->brand . '|' . $item->size . '|' . $item->color;
                });

            $dayReport = [];
            foreach ($dayStocks as $current) {
                $key = $current->brand . '|' . $current->size . '|' . $current->color;
                $previous = $previousStocks->get($key);

                $previousQuantity = $previous ? $previous->quantity : 0;
                $change = $current->quantity - $previousQuantity;
                $changeType = $change > 0 ? 'plus' : ($change < 0 ? 'minus' : 'no_change');
                $changeText = $change > 0 ? "+$change" : ($change < 0 ? "$change" : "0");

                $dayReport[] = [
                    'brand' => $current->brand,
                    'size' => $current->size,
                    'color' => $current->color,
                    'quantity' => $current->quantity,
                    'previous_quantity' => $previousQuantity,
                    'change' => $change,
                    'change_type' => $changeType,
                    'change_text' => $changeText,
                ];
            }

            $dailyReports[$dateStr] = $dayReport;
        }

        return response([
            'data' => $dailyReports,
            'month' => $startDate->format('Y-m'),
            'month_start' => $startDate->toDateString(),
            'month_end' => $endDate->toDateString(),
            'dates' => $dates,
            'message' => 'Monthly report retrieved successfully'
        ], 200);
    }

    /**
     * Get stock summary by date range
     * GET /api/stock-summary?from_date=2025-11-01&to_date=2025-11-30
     */
    public function stockSummary(Request $request)
    {
        $fromDate = $request->get('from_date', Carbon::now()->startOfMonth()->toDateString());
        $toDate = $request->get('to_date', Carbon::today()->toDateString());

        $stocks = Stock::whereBetween('stock_date', [$fromDate, $toDate])
            ->select('brand', 'size', 'color', DB::raw('SUM(quantity) as total_quantity'), DB::raw('COUNT(*) as entry_count'))
            ->groupBy('brand', 'size', 'color')
            ->orderBy('brand')
            ->orderBy('size')
            ->orderBy('color')
            ->get();

        return response([
            'data' => $stocks,
            'from_date' => $fromDate,
            'to_date' => $toDate,
            'message' => 'Stock summary retrieved successfully'
        ], 200);
    }
}

