<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UpiData extends Model
{
    use HasFactory;

    protected $table = 'upi_data';

    protected $fillable = [
        'upi_serial_num',
        'customer_name',
        'customer_number',
        'amount',
        'customer_photo',
        'customer_id_photo',
        'upi_screenshot_photo',
        'comment',
        'deviceuniqueid',
        'devicename',
        'is_deleted',
    ];
}
