<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $table = 'stocks';

    protected $fillable = [
        'brand',
        'size',
        'color',
        'quantity',
        'stock_date',
        'notes',
    ];

    protected $casts = [
        'stock_date' => 'date',
        'quantity' => 'integer',
    ];
}

