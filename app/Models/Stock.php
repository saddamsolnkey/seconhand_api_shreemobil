<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $table = 'stocks';

    protected $fillable = [
        'brand', // enum: Apple, Samsung, OPPO, vivo
        'category', // text/string type
        'size',
        'color',
        'quantity', // Amount for this transaction (in or out)
        'transaction_type', // 'in' or 'out'
        'stock_date',
        'notes',
    ];

    protected $casts = [
        'stock_date' => 'date',
        'quantity' => 'integer',
    ];
}

