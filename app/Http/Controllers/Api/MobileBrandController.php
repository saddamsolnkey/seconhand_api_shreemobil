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

    /**
     * Create a new brand
     * POST /api/brands
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function createBrand(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:mobile_brands,name',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response([
                'error' => 'Validation failed',
                'errors' => $validator->errors(),
                'message' => 'Please check your input data'
            ], 422);
        }

        // Get the next sort_order if not provided
        if (!$request->has('sort_order')) {
            $maxSortOrder = MobileBrand::max('sort_order') ?? 0;
            $request->merge(['sort_order' => $maxSortOrder + 1]);
        }

        $brand = MobileBrand::create([
            'name' => $request->name,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->is_active ?? true,
        ]);

        return response([
            'data' => [
                'id' => $brand->id,
                'name' => $brand->name,
                'sort_order' => $brand->sort_order,
                'is_active' => $brand->is_active,
            ],
            'message' => 'Brand created successfully'
        ], 201);
    }

    /**
     * Update an existing brand
     * PUT /api/brands/{id}
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function updateBrand(Request $request, $id)
    {
        $brand = MobileBrand::find($id);

        if (!$brand) {
            return response([
                'error' => 'Brand not found',
                'message' => 'Brand with ID ' . $id . ' does not exist'
            ], 404);
        }

        $validator = \Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255|unique:mobile_brands,name,' . $id,
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response([
                'error' => 'Validation failed',
                'errors' => $validator->errors(),
                'message' => 'Please check your input data'
            ], 422);
        }

        $brand->update($request->only(['name', 'sort_order', 'is_active']));

        return response([
            'data' => [
                'id' => $brand->id,
                'name' => $brand->name,
                'sort_order' => $brand->sort_order,
                'is_active' => $brand->is_active,
            ],
            'message' => 'Brand updated successfully'
        ], 200);
    }

    /**
     * Delete a brand (soft delete by setting is_active to false)
     * DELETE /api/brands/{id}
     * 
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function deleteBrand($id)
    {
        $brand = MobileBrand::find($id);

        if (!$brand) {
            return response([
                'error' => 'Brand not found',
                'message' => 'Brand with ID ' . $id . ' does not exist'
            ], 404);
        }

        // Soft delete by setting is_active to false
        $brand->update(['is_active' => false]);

        // Also deactivate all categories of this brand
        MobileCategory::where('brand_id', $id)->update(['is_active' => false]);

        return response([
            'message' => 'Brand deleted successfully (deactivated)'
        ], 200);
    }

    /**
     * Create a new category
     * POST /api/categories
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function createCategory(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'brand_id' => 'required|integer|exists:mobile_brands,id',
            'name' => 'required|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response([
                'error' => 'Validation failed',
                'errors' => $validator->errors(),
                'message' => 'Please check your input data'
            ], 422);
        }

        // Check for duplicate category name within the same brand
        $existingCategory = MobileCategory::where('brand_id', $request->brand_id)
            ->where('name', $request->name)
            ->first();

        if ($existingCategory) {
            return response([
                'error' => 'Category already exists',
                'message' => 'A category with this name already exists for this brand'
            ], 422);
        }

        // Get the next sort_order if not provided
        if (!$request->has('sort_order')) {
            $maxSortOrder = MobileCategory::where('brand_id', $request->brand_id)->max('sort_order') ?? 0;
            $request->merge(['sort_order' => $maxSortOrder + 1]);
        }

        $category = MobileCategory::create([
            'brand_id' => $request->brand_id,
            'name' => $request->name,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->is_active ?? true,
        ]);

        return response([
            'data' => [
                'id' => $category->id,
                'brand_id' => $category->brand_id,
                'name' => $category->name,
                'sort_order' => $category->sort_order,
                'is_active' => $category->is_active,
            ],
            'message' => 'Category created successfully'
        ], 201);
    }

    /**
     * Update an existing category
     * PUT /api/categories/{id}
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function updateCategory(Request $request, $id)
    {
        $category = MobileCategory::find($id);

        if (!$category) {
            return response([
                'error' => 'Category not found',
                'message' => 'Category with ID ' . $id . ' does not exist'
            ], 404);
        }

        $validator = \Validator::make($request->all(), [
            'brand_id' => 'sometimes|required|integer|exists:mobile_brands,id',
            'name' => 'sometimes|required|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response([
                'error' => 'Validation failed',
                'errors' => $validator->errors(),
                'message' => 'Please check your input data'
            ], 422);
        }

        // Check for duplicate category name within the same brand (if name or brand_id is being updated)
        if ($request->has('name') || $request->has('brand_id')) {
            $brandId = $request->brand_id ?? $category->brand_id;
            $name = $request->name ?? $category->name;
            
            $existingCategory = MobileCategory::where('brand_id', $brandId)
                ->where('name', $name)
                ->where('id', '!=', $id)
                ->first();

            if ($existingCategory) {
                return response([
                    'error' => 'Category already exists',
                    'message' => 'A category with this name already exists for this brand'
                ], 422);
            }
        }

        $category->update($request->only(['brand_id', 'name', 'sort_order', 'is_active']));

        return response([
            'data' => [
                'id' => $category->id,
                'brand_id' => $category->brand_id,
                'name' => $category->name,
                'sort_order' => $category->sort_order,
                'is_active' => $category->is_active,
            ],
            'message' => 'Category updated successfully'
        ], 200);
    }

    /**
     * Delete a category (soft delete by setting is_active to false)
     * DELETE /api/categories/{id}
     * 
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function deleteCategory($id)
    {
        $category = MobileCategory::find($id);

        if (!$category) {
            return response([
                'error' => 'Category not found',
                'message' => 'Category with ID ' . $id . ' does not exist'
            ], 404);
        }

        // Soft delete by setting is_active to false
        $category->update(['is_active' => false]);

        return response([
            'message' => 'Category deleted successfully (deactivated)'
        ], 200);
    }
}
