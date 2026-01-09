<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MobileBrand;
use App\Models\MobileCategory;

class MobileBrandController extends Controller
{
    /**
     * Get all brands with their categories
     * GET /api/brands
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function getBrands(Request $request)
    {
        $brands = MobileBrand::where('is_active', true)
            ->with(['categories' => function($query) {
                $query->where('is_active', true)->orderBy('sort_order');
            }])
            ->orderBy('sort_order')
            ->get();

        // Format response similar to MobileBrands.js structure
        $formattedData = [];
        foreach ($brands as $brand) {
            $formattedData[$brand->name] = $brand->categories->pluck('name')->toArray();
        }

        return response([
            'data' => $formattedData,
            'brands' => $brands->map(function($brand) {
                return [
                    'id' => $brand->id,
                    'name' => $brand->name,
                    'sort_order' => $brand->sort_order,
                    'categories_count' => $brand->categories->count(),
                ];
            }),
            'message' => 'Brands retrieved successfully'
        ], 200);
    }

    /**
     * Get all brands (simple list)
     * GET /api/brands/list
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function getBrandsList(Request $request)
    {
        $brands = MobileBrand::where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'name', 'sort_order']);

        return response([
            'data' => $brands,
            'message' => 'Brands list retrieved successfully'
        ], 200);
    }

    /**
     * Get categories for a specific brand
     * GET /api/brands/{brandId}/categories
     * or
     * GET /api/categories?brand_id={brandId}
     * or
     * GET /api/categories?brand_name={brandName}
     * 
     * @param Request $request
     * @param int|null $brandId
     * @return \Illuminate\Http\Response
     */
    public function getCategories(Request $request, $brandId = null)
    {
        $query = MobileCategory::where('is_active', true);

        // Get brand ID from route parameter, query parameter, or brand name
        if ($brandId) {
            $query->where('brand_id', $brandId);
        } elseif ($request->has('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        } elseif ($request->has('brand_name')) {
            $brand = MobileBrand::where('name', $request->brand_name)->where('is_active', true)->first();
            if ($brand) {
                $query->where('brand_id', $brand->id);
            } else {
                return response([
                    'data' => [],
                    'message' => 'Brand not found'
                ], 404);
            }
        } else {
            // Return all categories if no brand specified
            $categories = $query->with('brand')
                ->orderBy('brand_id')
                ->orderBy('sort_order')
                ->get();

            return response([
                'data' => $categories,
                'message' => 'All categories retrieved successfully'
            ], 200);
        }

        $categories = $query->orderBy('sort_order')->get(['id', 'brand_id', 'name', 'sort_order']);

        return response([
            'data' => $categories,
            'message' => 'Categories retrieved successfully'
        ], 200);
    }

    /**
     * Get brand with categories (nested structure)
     * GET /api/brands/{brandId}
     * 
     * @param int $brandId
     * @return \Illuminate\Http\Response
     */
    public function getBrand($brandId)
    {
        $brand = MobileBrand::where('id', $brandId)
            ->where('is_active', true)
            ->with(['categories' => function($query) {
                $query->where('is_active', true)->orderBy('sort_order');
            }])
            ->first();

        if (!$brand) {
            return response([
                'error' => 'Brand not found',
                'message' => 'Brand with ID ' . $brandId . ' does not exist or is inactive'
            ], 404);
        }

        return response([
            'data' => [
                'id' => $brand->id,
                'name' => $brand->name,
                'sort_order' => $brand->sort_order,
                'categories' => $brand->categories->map(function($category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name,
                        'sort_order' => $category->sort_order,
                    ];
                }),
            ],
            'message' => 'Brand retrieved successfully'
        ], 200);
    }
}
