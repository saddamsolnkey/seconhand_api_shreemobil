<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains over 1500 video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the Laravel [Patreon page](https://patreon.com/taylorotwell).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Cubet Techno Labs](https://cubettech.com)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[Many](https://www.many.co.uk)**
- **[Webdock, Fast VPS Hosting](https://www.webdock.io/en)**
- **[DevSquad](https://devsquad.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[OP.GG](https://op.gg)**
- **[CMS Max](https://www.cmsmax.com/)**
- **[WebReinvent](https://webreinvent.com/?utm_source=laravel&utm_medium=github&utm_campaign=patreon-sponsors)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

## Stock Management API Documentation

This API provides comprehensive stock management functionality with date-wise tracking, allowing you to track stock changes over time.

### Base URL
```
/api
```

### Stock API Endpoints

#### 1. Get Stock List
Get stock list for a specific date or all stocks.

**Endpoint:** `GET /api/stock-list`

**Query Parameters:**
- `date` (optional): Filter by specific date (format: YYYY-MM-DD). If not provided, returns all stocks.

**Example Request:**
```bash
GET /api/stock-list?date=2025-12-13
```

**Example Response:**
```json
{
  "data": [
    {
      "id": 1,
      "brand": "samsung",
      "size": "256",
      "color": "black",
      "quantity": 10,
      "stock_date": "2025-12-13",
      "notes": "Initial stock",
      "created_at": "2025-12-13T10:00:00.000000Z",
      "updated_at": "2025-12-13T10:00:00.000000Z"
    }
  ],
  "date": "2025-12-13",
  "message": "Stock list retrieved successfully for 2025-12-13"
}
```

---

#### 2. Get Current Stock Quantities
Get the current/latest quantity for each unique stock item, grouped by brand, size, and color.

**Endpoint:** `GET /api/stock-current`

**No Parameters Required**

**Example Request:**
```bash
GET /api/stock-current
```

**Example Response:**
```json
{
  "data": [
    {
      "id": 5,
      "brand": "samsung",
      "size": "256",
      "color": "black",
      "current_quantity": 15,
      "stock_date": "2025-12-14",
      "notes": "Latest update",
      "last_updated": "2025-12-14T15:30:00.000000Z"
    },
    {
      "id": 8,
      "brand": "apple",
      "size": "128",
      "color": "white",
      "current_quantity": 8,
      "stock_date": "2025-12-13",
      "notes": "Current stock",
      "last_updated": "2025-12-13T14:20:00.000000Z"
    }
  ],
  "total_items": 2,
  "message": "Current stock quantities retrieved successfully"
}
```

**Response Fields:**
- `id`: Latest stock entry ID
- `current_quantity`: Most recent quantity for this item
- `stock_date`: Date of the latest entry
- `last_updated`: Timestamp of last update
- `total_items`: Total number of unique stock items

**Use Case:** This endpoint is perfect for showing current inventory levels, as it automatically groups by unique items and shows only the latest quantity for each.

---

#### 3. Add New Stock
Add a new stock entry. If an entry exists for the same brand, size, color, and date, it will be updated instead.

**Endpoint:** `POST /api/stock-add`

**Request Body:**
```json
{
  "brand": "samsung",        // Required
  "size": "256",              // Optional
  "color": "black",           // Optional
  "quantity": 10,             // Optional (default: 0)
  "stock_date": "2025-12-13", // Optional (default: today)
  "notes": "Initial stock"    // Optional
}
```

**Example Request:**
```bash
curl -X POST "http://127.0.0.1:8000/api/stock-add" \
  -H "Content-Type: application/json" \
  -d '{
    "brand": "samsung",
    "size": "256",
    "color": "black",
    "quantity": 10,
    "stock_date": "2025-12-13",
    "notes": "Initial stock"
  }'
```

**Example Response:**
```json
{
  "data": {
    "id": 1,
    "brand": "samsung",
    "size": "256",
    "color": "black",
    "quantity": 10,
    "stock_date": "2025-12-13",
    "notes": "Initial stock"
  },
  "message": "Stock added successfully"
}
```

---

#### 4. Bulk Add Stock
Add multiple stock entries at once for a specific date.

**Endpoint:** `POST /api/stock-bulk-add`

**Request Body:**
```json
{
  "stock_date": "2025-12-13",  // Optional (default: today)
  "stocks": [
    {
      "brand": "samsung",       // Required
      "size": "256",             // Optional
      "color": "black",          // Optional
      "quantity": 10,            // Optional (default: 0)
      "notes": "Note 1"          // Optional
    },
    {
      "brand": "apple",
      "size": "128",
      "color": "white",
      "quantity": 5
    }
  ]
}
```

**Example Request:**
```bash
curl -X POST "http://127.0.0.1:8000/api/stock-bulk-add" \
  -H "Content-Type: application/json" \
  -d '{
    "stock_date": "2025-12-13",
    "stocks": [
      {"brand": "samsung", "size": "256", "color": "black", "quantity": 10},
      {"brand": "apple", "size": "128", "color": "white", "quantity": 5}
    ]
  }'
```

**Example Response:**
```json
{
  "data": [...],
  "created": 2,
  "updated": 0,
  "message": "Stock bulk add completed. Created: 2, Updated: 0"
}
```

---

#### 5. Update Stock (Creates New Date-wise Entry)
Update stock by ID. **Important:** This creates a new entry with the specified date instead of modifying the existing record. This allows you to track stock changes over time.

**Endpoint:** `POST /api/stock-update/{id}`

**Request Body:**
```json
{
  "quantity": 15,              // Required
  "stock_date": "2025-12-14",   // Optional (default: today)
  "notes": "Updated stock"      // Optional
}
```

**Example Request:**
```bash
curl -X POST "http://127.0.0.1:8000/api/stock-update/1" \
  -H "Content-Type: application/json" \
  -d '{
    "quantity": 15,
    "stock_date": "2025-12-14",
    "notes": "Added 5 new items"
  }'
```

**Example Response:**
```json
{
  "data": {
    "stock": {
      "id": 2,
      "brand": "samsung",
      "size": "256",
      "color": "black",
      "quantity": 15,
      "stock_date": "2025-12-14",
      "notes": "Added 5 new items"
    },
    "add_new": 5,
    "minus": 0,
    "previous_quantity": 10,
    "remaining": 15
  },
  "message": "New stock entry created successfully with date-wise data"
}
```

**Note:** 
- `add_new`: Quantity added (positive change)
- `minus`: Quantity reduced/sold (negative change)
- `previous_quantity`: Quantity before this update
- `remaining`: Final quantity after update

---

#### 6. Bulk Update Stock (Creates New Date-wise Entries)
Update multiple stocks at once. Each update creates a new entry with the specified date, allowing you to track stock changes over time for multiple items.

**Endpoint:** `POST /api/stock-bulk-update`

**Request Body:**
```json
{
  "stock_date": "2025-12-14",  // Optional (default: today)
  "stocks": [
    {
      "id": 1,                  // Required - Stock ID to update
      "quantity": 15,           // Required - New quantity
      "notes": "Added 5 items"  // Optional
    },
    {
      "id": 2,
      "quantity": 20,
      "notes": "Stock replenished"
    }
  ]
}
```

**Example Request:**
```bash
curl -X POST "http://127.0.0.1:8000/api/stock-bulk-update" \
  -H "Content-Type: application/json" \
  -d '{
    "stock_date": "2025-12-14",
    "stocks": [
      {
        "id": 1,
        "quantity": 15,
        "notes": "Added 5 new items"
      },
      {
        "id": 2,
        "quantity": 20,
        "notes": "Stock replenished"
      }
    ]
  }'
```

**Example Response:**
```json
{
  "data": [
    {
      "stock": {
        "id": 3,
        "brand": "samsung",
        "size": "256",
        "color": "black",
        "quantity": 15,
        "stock_date": "2025-12-14",
        "notes": "Added 5 new items"
      },
      "add_new": 5,
      "minus": 0,
      "previous_quantity": 10,
      "remaining": 15
    },
    {
      "stock": {
        "id": 4,
        "brand": "apple",
        "size": "128",
        "color": "white",
        "quantity": 20,
        "stock_date": "2025-12-14",
        "notes": "Stock replenished"
      },
      "add_new": 5,
      "minus": 0,
      "previous_quantity": 15,
      "remaining": 20
    }
  ],
  "created": 2,
  "updated": 0,
  "stock_date": "2025-12-14",
  "errors": [],
  "message": "Stock bulk update completed. Created: 2 new entries, Updated: 0 existing entries"
}
```

**Response Fields:**
- `data`: Array of updated stocks with add_new, minus, remaining quantities
- `created`: Number of new entries created
- `updated`: Number of existing entries updated (for same date)
- `stock_date`: Date used for the updates
- `errors`: Array of any errors encountered

**Note:** Each stock update creates a new entry with the specified date, maintaining a complete history of stock changes.

---

#### 7. Delete Stock
Delete a stock entry by ID.

**Endpoint:** `GET /api/stock-delete/{id}`

**Example Request:**
```bash
curl -X GET "http://127.0.0.1:8000/api/stock-delete/1"
```

**Example Response:**
```json
{
  "message": "Stock deleted successfully"
}
```

---

#### 8. Date-wise Stock Report
Get a detailed report for a specific date showing added, minus, and remaining quantities.

**Endpoint:** `GET /api/stock-date-report`

**Query Parameters:**
- `date` (required): Date to get report for (format: YYYY-MM-DD)
- `brand` (optional): Filter by brand
- `size` (optional): Filter by size
- `color` (optional): Filter by color

**Example Request:**
```bash
GET /api/stock-date-report?date=2025-12-13&brand=samsung&size=256&color=black
```

**Example Response:**
```json
{
  "data": [
    {
      "id": 1,
      "date": "2025-12-13",
      "brand": "samsung",
      "size": "256",
      "color": "black",
      "add_new": 5,
      "minus": 6,
      "remaining": 4,
      "previous_quantity": 5,
      "notes": "Stock update"
    }
  ],
  "date": "2025-12-13",
  "message": "Date-wise stock report retrieved successfully"
}
```

**Response Fields:**
- `add_new`: Quantity added on this date
- `minus`: Quantity reduced/sold on this date
- `remaining`: Final quantity after this date's changes
- `previous_quantity`: Quantity before this date

---

#### 9. Daily Report
Get daily report comparing current date with previous date, showing added and removed quantities.

**Endpoint:** `GET /api/stock-daily-report`

**Query Parameters:**
- `date` (optional): Date to get report for (default: today)

**Example Request:**
```bash
GET /api/stock-daily-report?date=2025-12-13
```

**Example Response:**
```json
{
  "data": [
    {
      "id": 1,
      "brand": "samsung",
      "size": "256",
      "color": "black",
      "quantity": 10,
      "previous_quantity": 5,
      "add_new": 5,
      "minus": 0,
      "change": 5,
      "change_type": "plus",
      "change_text": "+5",
      "stock_date": "2025-12-13"
    }
  ],
  "date": "2025-12-13",
  "previous_date": "2025-12-12",
  "message": "Daily report retrieved successfully"
}
```

**Response Fields:**
- `add_new`: Quantity added (positive change)
- `minus`: Quantity removed/sold (negative change)
- `change`: Net change (+/-)
- `change_type`: "plus", "minus", or "no_change"
- `change_text`: Formatted change text (e.g., "+5" or "-3")

---

#### 10. Weekly Report
Get weekly report with daily breakdown, showing added and removed quantities for each day.

**Endpoint:** `GET /api/stock-weekly-report`

**Query Parameters:**
- `week_start` (optional): Start date of week (default: current week Monday)

**Example Request:**
```bash
GET /api/stock-weekly-report?week_start=2025-12-09
```

**Example Response:**
```json
{
  "data": {
    "2025-12-09": [
      {
        "brand": "samsung",
        "size": "256",
        "color": "black",
        "quantity": 10,
        "previous_quantity": 5,
        "add_new": 5,
        "minus": 0,
        "change": 5,
        "change_type": "plus",
        "change_text": "+5"
      }
    ],
    "2025-12-10": [...]
  },
  "week_start": "2025-12-09",
  "week_end": "2025-12-15",
  "message": "Weekly report retrieved successfully"
}
```

**Each day shows:**
- `add_new`: Items added
- `minus`: Items removed/sold
- `change`: Net change

---

#### 11. Monthly Report
Get monthly report with daily breakdown, showing added and removed quantities for each day.

**Endpoint:** `GET /api/stock-monthly-report`

**Query Parameters:**
- `month` (optional): Month in YYYY-MM format (default: current month)

**Example Request:**
```bash
GET /api/stock-monthly-report?month=2025-12
```

**Example Response:**
```json
{
  "data": {
    "2025-12-01": [
      {
        "brand": "samsung",
        "size": "256",
        "color": "black",
        "quantity": 15,
        "previous_quantity": 10,
        "add_new": 5,
        "minus": 0,
        "change": 5,
        "change_type": "plus",
        "change_text": "+5"
      }
    ],
    "2025-12-02": [...]
  },
  "month": "2025-12",
  "month_start": "2025-12-01",
  "month_end": "2025-12-31",
  "message": "Monthly report retrieved successfully"
}
```

**Each day shows:**
- `add_new`: Items added
- `minus`: Items removed/sold
- `change`: Net change

---

#### 12. Date Range Report
Get stock report for a custom date range with daily breakdown showing added and removed quantities. Only shows dates that have data.

**Endpoint:** `GET /api/stock-date-range-report`

**Query Parameters:**
- `from_date` (required): Start date (format: YYYY-MM-DD)
- `to_date` (required): End date (format: YYYY-MM-DD)

**Example Request:**
```bash
GET /api/stock-date-range-report?from_date=2025-12-01&to_date=2025-12-10
```

**Example Response:**
```json
{
  "data": {
    "2025-12-03": [
      {
        "id": 4,
        "brand": "Apple",
        "size": "256",
        "color": "Black",
        "quantity": 6,
        "previous_quantity": 0,
        "add_new": 6,
        "minus": 0,
        "change": 6,
        "change_type": "plus",
        "change_text": "+6",
        "stock_date": "2025-12-03T00:00:00.000000Z"
      }
    ],
    "2025-12-05": [...]
  },
  "from_date": "2025-12-01",
  "to_date": "2025-12-10",
  "days_with_data": 2,
  "total_days": 10,
  "message": "Date range report retrieved successfully"
}
```

**Response Fields:**
- `data`: Daily breakdown (only dates with data)
- `days_with_data`: Number of days that have stock entries
- `total_days`: Total days in the requested range
- Each day shows: `add_new`, `minus`, `change`, `quantity`, etc.

**Benefits:**
- ✅ Flexible date range selection
- ✅ Only returns dates with data (no empty arrays)
- ✅ Perfect for custom period analysis
- ✅ Shows add/remove quantities for each day

---

#### 13. Stock Summary
Get stock summary aggregated by brand, size, and color for a date range.

**Endpoint:** `GET /api/stock-summary`

**Query Parameters:**
- `from_date` (optional): Start date (default: start of current month)
- `to_date` (optional): End date (default: today)

**Example Request:**
```bash
GET /api/stock-summary?from_date=2025-12-01&to_date=2025-12-31
```

---

### Admin Panel

Access the admin panel for stock management:

**URLs:**
- **Login:** `http://your-domain.com/admin/login`
- **Dashboard:** `http://your-domain.com/admin/dashboard`
- **Stock Management:** `http://your-domain.com/admin/stocks`

**Local Development:**
- **Login:** `http://127.0.0.1:8000/admin/login`
- **Dashboard:** `http://127.0.0.1:8000/admin/dashboard`
- **Stock Management:** `http://127.0.0.1:8000/admin/stocks`

**Features:**
- Add new stock entries
- Bulk add multiple stocks
- Update stock (creates new date-wise entries)
- Bulk update multiple stocks (creates new date-wise entries for each)
- Delete stock entries
- View date-wise reports with add_new, minus, and remaining quantities
- View daily, weekly, and monthly reports
- Filter stocks by date

---

### Database Schema

**stocks table:**
- `id`: Primary key
- `brand`: Brand name (required)
- `size`: Size (nullable)
- `color`: Color (nullable)
- `quantity`: Quantity (default: 0)
- `stock_date`: Date of stock entry (required)
- `notes`: Additional notes (nullable)
- `created_at`: Timestamp
- `updated_at`: Timestamp

**Unique Constraint:** `(brand, size, color, stock_date)` - Prevents duplicate entries for the same combination on the same date.

---

### Usage Examples

#### Example 1: Track Stock Changes Over Time

```bash
# Day 1: Add initial stock
POST /api/stock-add
{
  "brand": "samsung",
  "size": "256",
  "color": "black",
  "quantity": 10,
  "stock_date": "2025-12-13"
}

# Day 2: Update stock (adds 5, creates new entry)
POST /api/stock-update/1
{
  "quantity": 15,
  "stock_date": "2025-12-14"
}

# Day 3: Update stock (sells 6, creates new entry)
POST /api/stock-update/2
{
  "quantity": 9,
  "stock_date": "2025-12-15"
}

# Get report for Day 3
GET /api/stock-date-report?date=2025-12-15
# Response shows: add_new: 0, minus: 6, remaining: 9
```

#### Example 2: Bulk Stock Entry

```bash
POST /api/stock-bulk-add
{
  "stock_date": "2025-12-13",
  "stocks": [
    {"brand": "samsung", "size": "256", "color": "black", "quantity": 10},
    {"brand": "samsung", "size": "128", "color": "white", "quantity": 5},
    {"brand": "apple", "size": "256", "color": "gold", "quantity": 8}
  ]
}
```

#### Example 3: Bulk Update Multiple Stocks

```bash
# Update multiple stocks at once (creates new entries for each)
POST /api/stock-bulk-update
{
  "stock_date": "2025-12-14",
  "stocks": [
    {"id": 1, "quantity": 15, "notes": "Added 5 items"},
    {"id": 2, "quantity": 8, "notes": "Sold 2 items"},
    {"id": 3, "quantity": 20, "notes": "Replenished stock"}
  ]
}

# Response shows add_new, minus, and remaining for each stock
```

---

### Notes

- When updating stock, a new entry is created with the specified date instead of modifying the existing record
- This allows you to maintain a complete history of stock changes
- The date-wise report calculates `add_new` and `minus` by comparing with the previous date's entry
- All date parameters should be in `YYYY-MM-DD` format
- The API uses Laravel's validation, so ensure all required fields are provided


php artisan migrate

cd /Users/saddam/Documents/my_php/seconhand_api_shreemobil && php artisan route:list | grep stock



cd /Users/saddam/Documents/my_php/seconhand_api_shreemobil && lsof -ti:8000 || echo "No server running on port 8000"

cd /Users/saddam/Documents/my_php/seconhand_api_shreemobil && php artisan serve --host=127.0.0.1 --port=8000 &


cd /Users/saddam/Documents/my_php/seconhand_api_shreemobil && php artisan make:migration make_size_and_color_nullable_in_stocks_table

cd /Users/saddam/Documents/my_php/seconhand_api_shreemobil && php artisan migrate

cd /Users/saddam/Documents/my_php/seconhand_api_shreemobil && php artisan migrate:rollback --step=1 && php artisan migrate


cd /Users/saddam/Documents/my_php/seconhand_api_shreemobil && curl -s -X POST "http://127.0.0.1:8000/api/stock-add" -H "Content-Type: application/json" -d '{"brand":"test-brand-only"}' | python3 -m json.tool

cd /Users/saddam/Documents/my_php/seconhand_api_shreemobil && curl -s -X POST "http://127.0.0.1:8000/api/stock-bulk-add" -H "Content-Type: application/json" -d '{"stocks":[{"brand":"bulk-test-1"},{"brand":"bulk-test-2","quantity":5}]}' | python3 -m json.tool