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
            ->orderBy('category')
            ->orderBy('transaction_type')
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
     * Can add both in and out transactions at the same time
     * Accepts: quantity_in, quantity_out, or both
     */
    public function stockAdd(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'brand' => 'required|in:Apple,Samsung,OPPO,vivo',
            'category' => 'nullable|string|max:255',
            'quantity' => 'nullable|integer|min:1', // For backward compatibility
            'quantity_in' => 'nullable|integer|min:1', // New: quantity for 'in' transaction
            'quantity_out' => 'nullable|integer|min:1', // New: quantity for 'out' transaction
            'transaction_type' => 'nullable|in:in,out', // Optional if using quantity_in/quantity_out
            'stock_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'notes_in' => 'nullable|string', // Separate notes for 'in' transaction
            'notes_out' => 'nullable|string', // Separate notes for 'out' transaction
        ]);

        // Validate that at least one quantity is provided
        if (!isset($request->quantity) && !isset($request->quantity_in) && !isset($request->quantity_out)) {
            return response([
                'error' => ['quantity' => ['At least one quantity (quantity, quantity_in, or quantity_out) must be provided']],
                'message' => 'Validation Error'
            ], 422);
        }

        // If using old format (quantity + transaction_type), validate it
        if (isset($request->quantity) && !isset($request->quantity_in) && !isset($request->quantity_out)) {
            if (!isset($request->transaction_type)) {
                return response([
                    'error' => ['transaction_type' => ['Transaction type is required when using quantity field']],
                    'message' => 'Validation Error'
                ], 422);
            }
        }

        if ($validator->fails()) {
            return response([
                'error' => $validator->errors(),
                'message' => 'Validation Error'
            ], 422);
        }

        $data = $request->all();
        $results = [];
        $created = 0;
        $updated = 0;
        
        // Set default date if not provided
        $stockDate = isset($data['stock_date']) ? $data['stock_date'] : Carbon::today()->toDateString();
        $category = isset($data['category']) ? $data['category'] : null;
        $brand = $data['brand'];
        
        // Process IN transaction if quantity_in is provided
        if (isset($data['quantity_in']) && $data['quantity_in'] > 0) {
            $inData = [
                'brand' => $brand,
                'category' => $category,
                'quantity' => $data['quantity_in'],
                'transaction_type' => 'in',
                'stock_date' => $stockDate,
                'notes' => isset($data['notes_in']) ? $data['notes_in'] : (isset($data['notes']) ? $data['notes'] : null),
            ];
            
            $result = $this->createOrUpdateStock($inData);
            $results['in'] = $result['stock'];
            if ($result['created']) $created++;
            else $updated++;
        }
        
        // Process OUT transaction if quantity_out is provided
        if (isset($data['quantity_out']) && $data['quantity_out'] > 0) {
            $outData = [
                'brand' => $brand,
                'category' => $category,
                'quantity' => $data['quantity_out'],
                'transaction_type' => 'out',
                'stock_date' => $stockDate,
                'notes' => isset($data['notes_out']) ? $data['notes_out'] : (isset($data['notes']) ? $data['notes'] : null),
            ];
            
            $result = $this->createOrUpdateStock($outData);
            $results['out'] = $result['stock'];
            if ($result['created']) $created++;
            else $updated++;
        }
        
        // Process old format (quantity + transaction_type) for backward compatibility
        if (isset($data['quantity']) && isset($data['transaction_type']) && 
            !isset($data['quantity_in']) && !isset($data['quantity_out'])) {
            $oldData = [
                'brand' => $brand,
                'category' => $category,
                'quantity' => $data['quantity'],
                'transaction_type' => $data['transaction_type'],
                'stock_date' => $stockDate,
                'notes' => isset($data['notes']) ? $data['notes'] : null,
            ];
            
            $result = $this->createOrUpdateStock($oldData);
            $results[$data['transaction_type']] = $result['stock'];
            if ($result['created']) $created++;
            else $updated++;
        }
        
        $message = 'Stock transaction';
        if (count($results) > 1) {
            $message = 'Stock transactions';
        }
        $message .= ' added successfully';
        if ($updated > 0) {
            $message .= " (Created: $created, Updated: $updated)";
        }
        
        return response([
            'data' => $results,
            'created' => $created,
            'updated' => $updated,
            'message' => $message
        ], 201);
    }
    
    /**
     * Helper method to create or update stock transaction
     */
    private function createOrUpdateStock($data)
    {
        // Check if transaction already exists
        $query = Stock::where('brand', $data['brand'])
            ->where('transaction_type', $data['transaction_type']);
        
        // Handle category (nullable)
        if (isset($data['category']) && $data['category'] !== null) {
            $query->where('category', $data['category']);
        } else {
            $query->whereNull('category');
        }
        
        $query->where('stock_date', $data['stock_date']);
        $stock = $query->first();

        if ($stock) {
            // Update existing transaction
                $stock->quantity = $data['quantity'];
            if (isset($data['notes'])) {
                $stock->notes = $data['notes'];
            }
            if (isset($data['category'])) {
                $stock->category = $data['category'];
            }
            $stock->save();
            
            return ['stock' => $stock, 'created' => false];
        } else {
            // Create new transaction
            $stock = Stock::create($data);
            
            return ['stock' => $stock, 'created' => true];
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
            'stocks.*.brand' => 'required|in:Apple,Samsung,OPPO,vivo',
            'stocks.*.category' => 'nullable|string|max:255',
            'stocks.*.quantity' => 'required|integer|min:1',
            'stocks.*.transaction_type' => 'required|in:in,out',
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
            if (!isset($stockData['category'])) {
                $stockData['category'] = null;
            }
            $stockData['stock_date'] = $stockDate;
            
            // Check if stock entry already exists
            $query = Stock::where('brand', $stockData['brand'])
                ->where('transaction_type', $stockData['transaction_type']);
            
            // Handle category
            if (isset($stockData['category']) && $stockData['category'] !== null) {
                $query->where('category', $stockData['category']);
            } else {
                $query->whereNull('category');
            }
            
            $query->where('stock_date', $stockDate);
            $stock = $query->first();

            if ($stock) {
                // Update existing
                    $stock->quantity = $stockData['quantity'];
                if (isset($stockData['notes'])) {
                    $stock->notes = $stockData['notes'];
                }
                if (isset($stockData['category'])) {
                    $stock->category = $stockData['category'];
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
     * Update stock by ID - creates new entry with date-wise data
     * POST /api/stock-update/{id}
     * When updating, creates a new entry with current date instead of modifying existing
     * Required: brand, transaction_type, quantity
     * Optional: category, stock_date (defaults to today), notes
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
            'brand' => 'required|in:Apple,Samsung,OPPO,vivo',
            'category' => 'nullable|string|max:255',
            'transaction_type' => 'required|in:in,out',
            'quantity' => 'required|integer|min:1',
            'stock_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response([
                'error' => $validator->errors(),
                'message' => 'Validation Error'
            ], 422);
        }

        // Get the update date (default to today)
        $updateDate = $request->has('stock_date') ? $request->stock_date : Carbon::today()->toDateString();
        
        // Use request data or fallback to existing stock data
        $brand = $request->has('brand') ? $request->brand : $stock->brand;
        $category = $request->has('category') ? $request->category : $stock->category;
        $transactionType = $request->has('transaction_type') ? $request->transaction_type : $stock->transaction_type;
        $newQuantity = $request->quantity;
        
        // Get the previous quantity (from the most recent entry for this brand/category/transaction_type before update date)
        $previousQuery = Stock::where('brand', $brand)
            ->where('transaction_type', $transactionType);
        
        if ($category !== null) {
            $previousQuery->where('category', $category);
                } else {
            $previousQuery->whereNull('category');
        }
        
        $previousStock = $previousQuery->where('stock_date', '<', $updateDate)
            ->orderBy('stock_date', 'desc')
            ->first();
        
        $previousQuantity = $previousStock ? $previousStock->quantity : 0;
        $change = $newQuantity - $previousQuantity;
        $addNew = $change > 0 ? $change : 0;
        $minus = $change < 0 ? abs($change) : 0;

        // Check if entry already exists for this date with same brand/category/transaction_type
        $existingQuery = Stock::where('brand', $brand)
            ->where('transaction_type', $transactionType);
        
        if ($category !== null) {
            $existingQuery->where('category', $category);
        } else {
            $existingQuery->whereNull('category');
        }
        
        $existingQuery->where('stock_date', $updateDate);
        $existingStock = $existingQuery->where('id', '!=', $id)->first();

        if ($existingStock) {
            // Update existing entry for this date
            $existingStock->quantity = $newQuantity;
            if ($request->has('notes')) {
                $existingStock->notes = $request->notes;
            }
            if ($request->has('category')) {
                $existingStock->category = $request->category;
            }
            $existingStock->save();
            
            return response([
                'data' => [
                    'stock' => $existingStock,
                    'add_new' => $addNew,
                    'minus' => $minus,
                    'previous_quantity' => $previousQuantity,
                    'remaining' => $newQuantity,
                ],
                'message' => 'Stock updated successfully (existing entry for this date was updated)'
            ], 200);
        } else {
            // Create new entry with date-wise data
            $newStock = Stock::create([
                'brand' => $brand,
                'category' => $category,
                'transaction_type' => $transactionType,
                'quantity' => $newQuantity,
                'stock_date' => $updateDate,
                'notes' => $request->has('notes') ? $request->notes : $stock->notes,
            ]);
            
            return response([
                'data' => [
                    'stock' => $newStock,
                    'add_new' => $addNew,
                    'minus' => $minus,
                    'previous_quantity' => $previousQuantity,
                    'remaining' => $newQuantity,
                ],
                'message' => 'New stock entry created successfully with date-wise data'
            ], 201);
        }
    }

    /**
     * Bulk update stocks - creates new entries with date-wise data
     * POST /api/stock-bulk-update
     * Body: { "stock_date": "2025-12-13", "stocks": [{ "id": 1, "quantity": 15, "notes": "..." }, ...] }
     * Each update creates a new entry with the specified date
     */
    public function stockBulkUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'stock_date' => 'nullable|date',
            'stocks' => 'required|array|min:1',
            'stocks.*.id' => 'required|integer|exists:stocks,id',
            'stocks.*.brand' => 'required|in:Apple,Samsung,OPPO,vivo',
            'stocks.*.category' => 'nullable|string|max:255',
            'stocks.*.transaction_type' => 'required|in:in,out',
            'stocks.*.quantity' => 'required|integer|min:1',
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
        $results = [];
        $created = 0;
        $updated = 0;
        $errors = [];

        foreach ($request->stocks as $stockUpdate) {
            $originalStock = Stock::find($stockUpdate['id']);
            
            if (!$originalStock) {
                $errors[] = "Stock ID {$stockUpdate['id']} not found";
                continue;
            }

            // Use request data or fallback to existing stock data
            $brand = isset($stockUpdate['brand']) ? $stockUpdate['brand'] : $originalStock->brand;
            $category = isset($stockUpdate['category']) ? $stockUpdate['category'] : $originalStock->category;
            $transactionType = isset($stockUpdate['transaction_type']) ? $stockUpdate['transaction_type'] : $originalStock->transaction_type;
            $newQuantity = $stockUpdate['quantity'];

            // Get the previous quantity (from the most recent entry before update date)
            $previousQuery = Stock::where('brand', $brand)
                ->where('transaction_type', $transactionType);
            
            if ($category !== null) {
                $previousQuery->where('category', $category);
                    } else {
                $previousQuery->whereNull('category');
            }
            
            $previousStock = $previousQuery->where('stock_date', '<', $stockDate)
                ->orderBy('stock_date', 'desc')
                ->first();
            
            $previousQuantity = $previousStock ? $previousStock->quantity : 0;
            $change = $newQuantity - $previousQuantity;
            $addNew = $change > 0 ? $change : 0;
            $minus = $change < 0 ? abs($change) : 0;

            // Check if entry already exists for this date
            $existingQuery = Stock::where('brand', $brand)
                ->where('transaction_type', $transactionType);
            
            if ($category !== null) {
                $existingQuery->where('category', $category);
            } else {
                $existingQuery->whereNull('category');
            }
            
            $existingQuery->where('stock_date', $stockDate);
            $existingStock = $existingQuery->where('id', '!=', $originalStock->id)->first();

            if ($existingStock) {
                // Update existing entry for this date
                $existingStock->quantity = $newQuantity;
                if (isset($stockUpdate['notes'])) {
                    $existingStock->notes = $stockUpdate['notes'];
                }
                if (isset($stockUpdate['category'])) {
                    $existingStock->category = $stockUpdate['category'];
                }
                $existingStock->save();
                $updated++;
                
                $results[] = [
                    'stock' => $existingStock,
                    'add_new' => $addNew,
                    'minus' => $minus,
                    'previous_quantity' => $previousQuantity,
                    'remaining' => $newQuantity,
                ];
            } else {
                // Create new entry with date-wise data
                $newStock = Stock::create([
                    'brand' => $brand,
                    'category' => $category,
                    'transaction_type' => $transactionType,
                    'quantity' => $newQuantity,
                    'stock_date' => $stockDate,
                    'notes' => isset($stockUpdate['notes']) ? $stockUpdate['notes'] : $originalStock->notes,
                ]);
                $created++;
                
                $results[] = [
                    'stock' => $newStock,
                    'add_new' => $addNew,
                    'minus' => $minus,
                    'previous_quantity' => $previousQuantity,
                    'remaining' => $newQuantity,
                ];
            }
        }

        return response([
            'data' => $results,
            'created' => $created,
            'updated' => $updated,
            'stock_date' => $stockDate,
            'errors' => $errors,
            'message' => "Stock bulk update completed. Created: $created new entries, Updated: $updated existing entries"
        ], 201);
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
     * Get date-wise stock report with added, minus, and remaining quantities
     * GET /api/stock-date-report?date=2015-12-13
     * GET /api/stock-date-report?date=2015-12-13&brand=samsung&category=iPhone
     * GET /api/stock-date-report?date=2015-12-13&transaction_type=in
     * GET /api/stock-date-report?date=2015-12-13&transaction_type=out
     * GET /api/stock-date-report?date=2015-12-13&notes_for_in=search_text
     * 
     * Returns: brand, category, transaction_type, add_new (added quantity), minus (reduced quantity), remaining (final quantity)
     */
    public function stockDateReport(Request $request)
    {
        $date = $request->get('date');
        $brand = $request->get('brand');
        $category = $request->get('category');
        $transactionType = $request->get('transaction_type'); // 'in' or 'out'
        $notesForIn = $request->get('notes_for_in'); // Filter notes for 'in' transactions

        if (!$date) {
            return response([
                'error' => 'Date is required',
                'message' => 'Please provide date parameter (format: YYYY-MM-DD)'
            ], 400);
        }

        // Validate transaction_type if provided
        if ($transactionType && !in_array($transactionType, ['in', 'out'])) {
            return response([
                'error' => 'Invalid transaction_type',
                'message' => 'transaction_type must be either "in" or "out"'
            ], 400);
        }

        $query = Stock::where('stock_date', $date);
        
        if ($brand) {
            $query->where('brand', $brand);
        }
        if ($category !== null) {
            if ($category === '') {
                $query->whereNull('category');
            } else {
                $query->where('category', $category);
            }
        }
        if ($transactionType) {
            $query->where('transaction_type', $transactionType);
            // If transaction_type is 'out', notes_for_in filter doesn't apply
            if ($notesForIn && $transactionType === 'in') {
                $query->where('notes', 'LIKE', '%' . $notesForIn . '%');
            }
        } else {
            // Filter notes for 'in' transactions when no specific transaction_type is set
            if ($notesForIn) {
                $query->where(function($q) use ($notesForIn) {
                    $q->where(function($subQ) use ($notesForIn) {
                        $subQ->where('transaction_type', 'in')
                             ->where('notes', 'LIKE', '%' . $notesForIn . '%');
                    })->orWhere('transaction_type', '!=', 'in');
                });
            }
        }

        $stocks = $query->orderBy('brand')
            ->orderBy('category')
            ->orderBy('transaction_type')
            ->get();

        $result = [];
        foreach ($stocks as $stock) {
            // Get previous quantity (from most recent entry before this date)
            $previousQuery = Stock::where('brand', $stock->brand)
                ->where(function($q) use ($stock) {
                    if ($stock->category) {
                        $q->where('category', $stock->category);
                    } else {
                        $q->whereNull('category');
                    }
                })
                ->where('transaction_type', $stock->transaction_type)
                ->where('stock_date', '<', $date)
                ->orderBy('stock_date', 'desc')
                ->first();
            
            $previousQuantity = $previousQuery ? $previousQuery->quantity : 0;
            $currentQuantity = $stock->quantity;
            $change = $currentQuantity - $previousQuantity;
            
            // Calculate added and minus quantities
            $addNew = $change > 0 ? $change : 0;  // Added quantity (positive change)
            $minus = $change < 0 ? abs($change) : 0;  // Minus quantity (negative change)
            $remaining = $currentQuantity;  // Remaining quantity

            $result[] = [
                'id' => $stock->id,
                'date' => $stock->stock_date,
                'brand' => $stock->brand,
                'category' => $stock->category,
                'transaction_type' => $stock->transaction_type,
                'quantity' => $remaining,
                'add_new' => $addNew,
                'minus' => $minus,
                'remaining' => $remaining,
                'previous_quantity' => $previousQuantity,
                'notes' => $stock->notes,
            ];
        }

        return response([
            'data' => $result,
            'date' => $date,
            'message' => 'Date-wise stock report retrieved successfully'
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
            ->orderBy('category')
            ->get();

        // Get previous date stocks
        $previousStocks = Stock::where('stock_date', $previousDate->toDateString())
            ->get()
            ->keyBy(function ($item) {
                return $item->brand . '|' . ($item->category ?? '');
            });

        $report = [];
        foreach ($currentStocks as $current) {
            $key = $current->brand . '|' . ($current->category ?? '');
            $previous = $previousStocks->get($key);

            $previousQuantity = $previous ? $previous->quantity : 0;
            $change = $current->quantity - $previousQuantity;
            $changeType = $change > 0 ? 'plus' : ($change < 0 ? 'minus' : 'no_change');
            $changeText = $change > 0 ? "+$change" : ($change < 0 ? "$change" : "0");
            
            // Calculate added and removed quantities
            $addNew = $change > 0 ? $change : 0;
            $minus = $change < 0 ? abs($change) : 0;

            $report[] = [
                'id' => $current->id,
                'brand' => $current->brand,
                'category' => $current->category,
                'quantity' => $current->quantity,
                'previous_quantity' => $previousQuantity,
                'add_new' => $addNew,
                'minus' => $minus,
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
            ->orderBy('category')
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
                    return $item->brand . '|' . ($item->category ?? '');
                });

            $dayReport = [];
            foreach ($dayStocks as $current) {
                $key = $current->brand . '|' . ($current->category ?? '');
                $previous = $previousStocks->get($key);

                $previousQuantity = $previous ? $previous->quantity : 0;
                $change = $current->quantity - $previousQuantity;
                $changeType = $change > 0 ? 'plus' : ($change < 0 ? 'minus' : 'no_change');
                $changeText = $change > 0 ? "+$change" : ($change < 0 ? "$change" : "0");
                
                // Calculate added and removed quantities
                $addNew = $change > 0 ? $change : 0;
                $minus = $change < 0 ? abs($change) : 0;

                $dayReport[] = [
                    'brand' => $current->brand,
                    'category' => $current->category,
                    'quantity' => $current->quantity,
                    'previous_quantity' => $previousQuantity,
                    'add_new' => $addNew,
                    'minus' => $minus,
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
            ->orderBy('category')
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
                    return $item->brand . '|' . ($item->category ?? '');
                });

            $dayReport = [];
            foreach ($dayStocks as $current) {
                $key = $current->brand . '|' . ($current->category ?? '');
                $previous = $previousStocks->get($key);

                $previousQuantity = $previous ? $previous->quantity : 0;
                $change = $current->quantity - $previousQuantity;
                $changeType = $change > 0 ? 'plus' : ($change < 0 ? 'minus' : 'no_change');
                $changeText = $change > 0 ? "+$change" : ($change < 0 ? "$change" : "0");
                
                // Calculate added and removed quantities
                $addNew = $change > 0 ? $change : 0;
                $minus = $change < 0 ? abs($change) : 0;

                $dayReport[] = [
                    'brand' => $current->brand,
                    'category' => $current->category,
                    'quantity' => $current->quantity,
                    'previous_quantity' => $previousQuantity,
                    'add_new' => $addNew,
                    'minus' => $minus,
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
     * Get stock report for a custom date range
     * GET /api/stock-date-range-report?from_date=2025-12-01&to_date=2025-12-10
     * GET /api/stock-date-range-report?from_date=2025-12-01&to_date=2025-12-10&brand=Apple
     * GET /api/stock-date-range-report?from_date=2025-12-01&to_date=2025-12-10&category=iPhone
     * GET /api/stock-date-range-report?from_date=2025-12-01&to_date=2025-12-10&transaction_type=in
     * GET /api/stock-date-range-report?from_date=2025-12-01&to_date=2025-12-10&notes_for_in=search_text
     * Shows daily breakdown with add_new and minus for each day in the range
     */
    public function stockDateRangeReport(Request $request)
    {
        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');
        $brand = $request->get('brand');
        $category = $request->get('category');
        $transactionType = $request->get('transaction_type'); // 'in' or 'out'
        $notesForIn = $request->get('notes_for_in'); // Filter notes for 'in' transactions
        
        // Validation
        if (!$fromDate || !$toDate) {
            return response([
                'error' => 'Both from_date and to_date are required',
                'message' => 'Please provide from_date and to_date parameters (format: YYYY-MM-DD)'
            ], 400);
        }
        
        $startDate = Carbon::parse($fromDate);
        $endDate = Carbon::parse($toDate);
        
        // Validate date range
        if ($startDate->gt($endDate)) {
            return response([
                'error' => 'Invalid date range',
                'message' => 'from_date must be before or equal to to_date'
            ], 400);
        }

        // Validate transaction_type if provided
        if ($transactionType && !in_array($transactionType, ['in', 'out'])) {
            return response([
                'error' => 'Invalid transaction_type',
                'message' => 'transaction_type must be either "in" or "out"'
            ], 400);
        }

        // Get all stocks in the date range with filters
        $query = Stock::whereBetween('stock_date', [$startDate->toDateString(), $endDate->toDateString()]);
        
        if ($brand) {
            $query->where('brand', $brand);
        }
        if ($category !== null) {
            if ($category === '') {
                $query->whereNull('category');
            } else {
                $query->where('category', $category);
            }
        }
        if ($transactionType) {
            $query->where('transaction_type', $transactionType);
            // If transaction_type is 'out', notes_for_in filter doesn't apply
            if ($notesForIn && $transactionType === 'in') {
                $query->where('notes', 'LIKE', '%' . $notesForIn . '%');
            }
        } else {
            // Filter notes for 'in' transactions when no specific transaction_type is set
            if ($notesForIn) {
                $query->where(function($q) use ($notesForIn) {
                    $q->where(function($subQ) use ($notesForIn) {
                        $subQ->where('transaction_type', 'in')
                             ->where('notes', 'LIKE', '%' . $notesForIn . '%');
                    })->orWhere('transaction_type', '!=', 'in');
                });
            }
        }
        
        $stocks = $query->orderBy('stock_date')
            ->orderBy('brand')
            ->orderBy('category')
            ->orderBy('transaction_type')
            ->get();

        // Group by date
        $dailyReports = [];
        $dates = [];

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dateStr = $date->toDateString();
            $dates[] = $dateStr;
            
            // Filter stocks by comparing date strings
            $dayStocks = $stocks->filter(function($stock) use ($dateStr) {
                return Carbon::parse($stock->stock_date)->toDateString() === $dateStr;
            });
            
            // Skip empty dates
            if ($dayStocks->isEmpty()) {
                continue;
            }
            
            $previousDate = $date->copy()->subDay();
            $previousStocks = Stock::whereDate('stock_date', $previousDate->toDateString())
                ->get()
                ->keyBy(function ($item) {
                    return $item->brand . '|' . ($item->category ?? '');
                });

            $dayReport = [];
            foreach ($dayStocks as $current) {
                $key = $current->brand . '|' . ($current->category ?? '');
                $previous = $previousStocks->get($key);

                $previousQuantity = $previous ? $previous->quantity : 0;
                $change = $current->quantity - $previousQuantity;
                $changeType = $change > 0 ? 'plus' : ($change < 0 ? 'minus' : 'no_change');
                $changeText = $change > 0 ? "+$change" : ($change < 0 ? "$change" : "0");
                
                // Calculate added and removed quantities
                $addNew = $change > 0 ? $change : 0;
                $minus = $change < 0 ? abs($change) : 0;

                $dayReport[] = [
                    'id' => $current->id,
                    'brand' => $current->brand,
                    'category' => $current->category,
                    'transaction_type' => $current->transaction_type,
                    'quantity' => $current->quantity,
                    'previous_quantity' => $previousQuantity,
                    'add_new' => $addNew,
                    'minus' => $minus,
                    'change' => $change,
                    'change_type' => $changeType,
                    'change_text' => $changeText,
                    'stock_date' => $current->stock_date,
                    'notes' => $current->notes,
                ];
            }

            $dailyReports[$dateStr] = $dayReport;
        }

        return response([
            'data' => $dailyReports,
            'from_date' => $startDate->toDateString(),
            'to_date' => $endDate->toDateString(),
            'days_with_data' => count($dailyReports),
            'total_days' => $startDate->diffInDays($endDate) + 1,
            'message' => 'Date range report retrieved successfully'
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
            ->select('brand', 'category', DB::raw('SUM(quantity) as total_quantity'), DB::raw('COUNT(*) as entry_count'))
            ->groupBy('brand', 'category')
            ->orderBy('brand')
            ->orderBy('category')
            ->get();

        return response([
            'data' => $stocks,
            'from_date' => $fromDate,
            'to_date' => $toDate,
            'message' => 'Stock summary retrieved successfully'
        ], 200);
    }

    /**
     * Get current stock quantities grouped by unique items
     * GET /api/stock-current
     * Returns the latest quantity for each unique stock item (brand, category)
     * Calculates total in and out quantities
     */
    public function stockCurrent(Request $request)
    {
        // Get all unique stock combinations
        $uniqueStocks = Stock::select('brand', 'category')
            ->groupBy('brand', 'category')
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
            
            $totalIn = $queryIn->where('transaction_type', 'in')->sum('quantity');
            $totalOut = $queryOut->where('transaction_type', 'out')->sum('quantity');
            $currentQuantity = $totalIn - $totalOut;

            // Get latest transaction date
            $latestQuery = Stock::where('brand', $uniqueStock->brand);
            
            if ($uniqueStock->category) {
                $latestQuery->where('category', $uniqueStock->category);
                    } else {
                $latestQuery->whereNull('category');
                    }
            
            $latestStock = $latestQuery->orderBy('stock_date', 'desc')
                ->orderBy('id', 'desc')
                ->first();

                $currentStocks[] = [
                'id' => $latestStock ? $latestStock->id : null,
                'brand' => $uniqueStock->brand,
                'category' => $uniqueStock->category,
                'total_in' => $totalIn,
                'total_out' => $totalOut,
                'current_quantity' => $currentQuantity,
                'last_transaction_date' => $latestStock ? $latestStock->stock_date : null,
                'last_updated' => $latestStock ? $latestStock->updated_at : null,
            ];
        }

        // Sort by brand, category
        usort($currentStocks, function($a, $b) {
            $brandCompare = strcmp($a['brand'], $b['brand']);
            if ($brandCompare !== 0) return $brandCompare;
            
            return strcmp($a['category'] ?? '', $b['category'] ?? '');
        });

        return response([
            'data' => $currentStocks,
            'total_items' => count($currentStocks),
            'message' => 'Current stock quantities retrieved successfully'
        ], 200);
    }

    /**
     * Get brands grouped by category with available stock
     * GET /api/stock-brands-grouped
     * Returns brands grouped by category showing available stock
     */
    public function stockBrandsGrouped()
    {
        // Get all unique stock combinations
        $uniqueStocks = Stock::select('brand', 'category')
            ->groupBy('brand', 'category')
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

        return response([
            'data' => $result,
            'message' => 'Brands grouped by category retrieved successfully'
        ], 200);
    }

    /**
     * Get in/out transactions by date
     * GET /api/stock-transactions-by-date?date=2025-12-07&brand=Apple&category=iPhone
     */
    public function stockTransactionsByDate(Request $request)
    {
        $date = $request->get('date', Carbon::today()->toDateString());
        $brand = $request->get('brand'); // Optional filter by brand
        $category = $request->get('category'); // Optional filter by category

        $query = Stock::where('stock_date', $date);
        
        if ($brand) {
            $query->where('brand', $brand);
        }
        
        if ($category) {
            $query->where('category', $category);
        }

        $transactions = $query->orderBy('brand')
            ->orderBy('category')
            ->orderBy('transaction_type')
            ->get();

        // Group by item and show in/out totals
        $result = [];
        foreach ($transactions as $transaction) {
            $key = $transaction->brand . '|' . ($transaction->category ?? '');
            
            if (!isset($result[$key])) {
                $result[$key] = [
                    'brand' => $transaction->brand,
                    'category' => $transaction->category,
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

        return response([
            'data' => array_values($result),
            'date' => $date,
            'message' => 'Stock transactions retrieved successfully for date'
        ], 200);
    }
}


