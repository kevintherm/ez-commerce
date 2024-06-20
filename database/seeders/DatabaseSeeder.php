<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Shop;
use App\Models\User;
use App\Models\Product;
use App\Models\ShopCatalog;
use App\Models\ProductCategory;
use App\Models\ProductSubCategory;
use App\Models\Wishlist;
use Auth;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Notification;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Kevin Darmawan',
            'username' => 'kevind',
            'email' => 'kevindrm4@gmail.com',
            'password' => bcrypt(''),
            'email_verified_at' => now()
        ]);

        ShopCatalog::create([
            'shop_id' => 1,
            'name' => 'Produk Lainnya',
            'slug' => 'produk-lainnya',
            'desc' => 'Produk Lainnya Dari Toko Ini.'
        ]);

        ShopCatalog::create([
            'shop_id' => 1,
            'name' => 'Keyboard Gaming',
            'slug' => 'keyboard-gaming',
            'desc' => 'Produk Gaming Keyboard'
        ]);

        ProductCategory::create([
            'name' => 'Semua Produk',
            'slug' => 'semua-produk',
            'desc' => 'Kategori untuk semua produk atau produk yang belum memiliki kategori-nya sendiri.',
        ]);

        ProductSubCategory::create([
            'category_id' => 1,
            'name' => 'Bagan 1',
            'slug' => 'bagan-1'
        ]);
    }
}
