<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MobileCategory extends Model
{
    use HasFactory;

    protected $table = 'mobile_categories';

    protected $fillable = [
        'brand_id',
        'name',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'brand_id' => 'integer',
    ];

    /**
     * Get the brand that owns the category.
     */
    public function brand()
    {
        return $this->belongsTo(MobileBrand::class, 'brand_id');
    }
}
