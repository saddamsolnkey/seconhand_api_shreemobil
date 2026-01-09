<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MobileBrand;
use App\Models\MobileCategory;

class MobileBrandsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Import all brands and categories from MobileBrands.js
     *
     * @return void
     */
    public function run()
    {
        $mobileBrands = [
            "Apple" => [
                "iPhone 15 Pro Max",
                "iPhone 15 Pro",
                "iPhone 15 Plus",
                "iPhone 15",
                "iPhone 14 Pro Max",
                "iPhone 14 Pro",
                "iPhone 14 Plus",
                "iPhone 14",
                "iPhone 13 Pro Max",
                "iPhone 13 Pro",
                "iPhone 13",
                "iPhone 13 mini",
                "iPhone 12 Pro Max",
                "iPhone 12 Pro",
                "iPhone 12",
                "iPhone 12 mini",
                "iPhone 11 Pro Max",
                "iPhone 11 Pro",
                "iPhone 11",
                "iPhone XS Max",
                "iPhone XS",
                "iPhone XR",
                "iPhone X",
                "iPhone 8 Plus",
                "iPhone 8",
                "iPhone 7 Plus",
                "iPhone 7",
                "iPhone SE (2022)",
                "iPhone SE (2020)",
                "Uncategorized"
            ],
            "Samsung" => [
                "Galaxy S24 Ultra",
                "Galaxy S24+",
                "Galaxy S24",
                "Galaxy S23 Ultra",
                "Galaxy S23+",
                "Galaxy S23",
                "Galaxy S22 Ultra",
                "Galaxy S22+",
                "Galaxy S22",
                "Galaxy S21 Ultra",
                "Galaxy S21+",
                "Galaxy S21",
                "Galaxy Note 20 Ultra",
                "Galaxy Note 20",
                "Galaxy A54",
                "Galaxy A34",
                "Galaxy A24",
                "Galaxy A14",
                "Galaxy A04",
                "Galaxy Z Fold 5",
                "Galaxy Z Fold 4",
                "Galaxy Z Flip 5",
                "Galaxy Z Flip 4",
                "Uncategorized"
            ],
            "OPPO" => [
                "Reno 11 Pro",
                "Reno 11",
                "Reno 10 Pro",
                "Reno 10",
                "Reno 9 Pro",
                "Reno 9",
                "Reno 8 Pro",
                "Reno 8",
                "Reno 7 Pro",
                "Reno 7",
                "Find X7 Ultra",
                "Find X6 Pro",
                "Find X5 Pro",
                "Find X3 Pro",
                "A98",
                "A78",
                "A58",
                "A38",
                "F25 Pro",
                "F23",
                "F21 Pro",
                "Uncategorized"
            ],
            "Vivo" => [
                "X100 Pro",
                "X100",
                "X90 Pro",
                "X90",
                "V30 Pro",
                "V30",
                "V29",
                "V27",
                "V25",
                "Y100",
                "Y56",
                "Y36",
                "Y27",
                "Y17",
                "T2 Pro",
                "T2",
                "T1 Pro",
                "Uncategorized"
            ],
            "Xiaomi" => [
                "Mi 14 Ultra",
                "Mi 14 Pro",
                "Mi 14",
                "Mi 13 Ultra",
                "Mi 13 Pro",
                "Mi 13",
                "Redmi Note 13 Pro",
                "Redmi Note 13",
                "Redmi Note 12 Pro",
                "Redmi Note 12",
                "Redmi 13C",
                "Redmi 12",
                "Redmi 11",
                "POCO X6 Pro",
                "POCO X6",
                "POCO F5 Pro",
                "POCO F5",
                "POCO M6",
                "Uncategorized"
            ],
            "OnePlus" => [
                "OnePlus 12",
                "OnePlus 11",
                "OnePlus 10 Pro",
                "OnePlus 10T",
                "OnePlus 9 Pro",
                "OnePlus 9",
                "OnePlus Nord 3",
                "OnePlus Nord CE 3",
                "OnePlus Nord 2",
                "OnePlus Nord CE 2",
                "Uncategorized"
            ],
            "Realme" => [
                "Realme GT 5 Pro",
                "Realme GT 5",
                "Realme GT Neo 5",
                "Realme 12 Pro+",
                "Realme 12 Pro",
                "Realme 12",
                "Realme 11 Pro+",
                "Realme 11 Pro",
                "Realme 11",
                "Realme C55",
                "Realme C53",
                "Realme C35",
                "Uncategorized"
            ],
            "Motorola" => [
                "Moto Edge 40 Pro",
                "Moto Edge 40",
                "Moto Edge 30 Ultra",
                "Moto G84",
                "Moto G73",
                "Moto G54",
                "Moto G34",
                "Moto E40",
                "Moto E32",
                "Razr 40 Ultra",
                "Razr 40",
                "Uncategorized"
            ],
            "Nokia" => [
                "Nokia G60",
                "Nokia G42",
                "Nokia G22",
                "Nokia XR21",
                "Nokia X30",
                "Nokia C32",
                "Nokia C22",
                "Nokia C12",
                "Uncategorized"
            ],
            "Google" => [
                "Pixel 8 Pro",
                "Pixel 8",
                "Pixel 7 Pro",
                "Pixel 7",
                "Pixel 7a",
                "Pixel 6 Pro",
                "Pixel 6",
                "Pixel 6a",
                "Pixel 5",
                "Pixel 4a",
                "Uncategorized"
            ],
            "Huawei" => [
                "Mate 60 Pro",
                "Mate 50 Pro",
                "Mate X5",
                "P60 Pro",
                "P50 Pro",
                "Nova 12",
                "Nova 11",
                "Nova Y90",
                "Uncategorized"
            ],
            "Honor" => [
                "Honor Magic 6 Pro",
                "Honor Magic 5 Pro",
                "Honor 90",
                "Honor 80",
                "Honor X9a",
                "Honor X8",
                "Uncategorized"
            ],
            "Nothing" => [
                "Nothing Phone (2)",
                "Nothing Phone (1)",
                "Uncategorized"
            ],
            "Infinix" => [
                "Infinix GT 10 Pro",
                "Infinix Note 30 Pro",
                "Infinix Note 30",
                "Infinix Hot 40 Pro",
                "Infinix Hot 40",
                "Infinix Zero 30",
                "Uncategorized"
            ],
            "Tecno" => [
                "Tecno Phantom V Fold",
                "Tecno Phantom X2 Pro",
                "Tecno Camon 20 Pro",
                "Tecno Spark 20",
                "Tecno Pova 5",
                "Uncategorized"
            ],
            "Sony" => [
                "Xperia 1 V",
                "Xperia 5 V",
                "Xperia 10 V",
                "Xperia Pro-I",
                "Uncategorized"
            ],
            "Asus" => [
                "ROG Phone 7",
                "ROG Phone 6",
                "ZenFone 10",
                "ZenFone 9",
                "Uncategorized"
            ],
            "Other" => []
        ];

        $sortOrder = 0;
        foreach ($mobileBrands as $brandName => $categories) {
            $sortOrder++;
            
            // Create or update brand
            $brand = MobileBrand::updateOrCreate(
                ['name' => $brandName],
                [
                    'name' => $brandName,
                    'sort_order' => $sortOrder,
                    'is_active' => true,
                ]
            );

            // Create categories for this brand
            $categorySortOrder = 0;
            foreach ($categories as $categoryName) {
                $categorySortOrder++;
                MobileCategory::updateOrCreate(
                    [
                        'brand_id' => $brand->id,
                        'name' => $categoryName,
                    ],
                    [
                        'brand_id' => $brand->id,
                        'name' => $categoryName,
                        'sort_order' => $categorySortOrder,
                        'is_active' => true,
                    ]
                );
            }
        }

        $this->command->info('Mobile brands and categories seeded successfully!');
    }
}
