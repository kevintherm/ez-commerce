<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use App\Models\ProductSubCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::with('shop.catalog')->get();
        $subCategories = ProductSubCategory::all();

        $conditions = [1, 2]; // 1 = baru, 2 = bekas

        foreach (range(1, 100) as $index) {
            $user = $users->random();
            $shop = $user->shop;
            $catalog = $shop->catalog->first();
            $subCategory = $subCategories->random();

            Product::create([
                'image' => json_encode(['default.png']), // Dummy image URL
                'shop_id' => $shop->id,
                'catalog_id' => $catalog->id,
                'sub_category_id' => $subCategory->id,
                'name' => $name = 'Product ' . $index,
                'slug' => Str::slug($name),
                'desc' => 'Description for ' . $name,
                'weight' => rand(100, 2000), // Weight in grams
                'condition' => $conditions[array_rand($conditions)],
                'stock' => rand(1, 100),
                'price' => rand(10000, 1000000),
                'sold' => rand(0, 50),
            ]);
        }
    }
}
