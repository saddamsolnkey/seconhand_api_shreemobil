<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable = [
        'mobile_name', 'mobile_emi','sellerName','buyerName','pro_serial_num','mobile_photo','mobile_bill_photo','mobile_price','buy_date','seller_id_photo','agent_name','is_deleted','deviceuniqueid','devicename','custom_date'
    ];
}
