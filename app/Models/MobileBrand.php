<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MobileBrand extends Model
{
    use HasFactory;

    protected $table = 'mobile_brands';

    protected $fillable = [
        'name',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the categories for the brand.
     */
    public function categories()
    {
        return $this->hasMany(MobileCategory::class, 'brand_id')->where('is_active', true)->orderBy('sort_order');
    }

    /**
     * Get all categories (including inactive) for the brand.
     */
    public function allCategories()
    {
        return $this->hasMany(MobileCategory::class, 'brand_id')->orderBy('sort_order');
    }
}
