<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;

    protected $table = 'device';

    protected $fillable = [
        'usename',
        'uniqueid',
        'devicename',
        'isactive',
    ];

    // Optional: disable timestamps if you don't want them
    // public $timestamps = false;
}