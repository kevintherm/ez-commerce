<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Auth;
use Notification;
use App\Models\Cart;
use App\Models\Shop;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\Wishlist;
use App\Models\ShopCatalog;
use Illuminate\Support\Str;
use App\Models\ProductCategory;
use Illuminate\Database\Seeder;
use App\Models\ProductSubCategory;

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

        User::factory(15)->create();

        foreach (range(1, 16) as $i) {
            Shop::create([
                'user_id' => $i,
                'name' => fake()->name(),
                'url' => str(fake()->name())->slug(),
                'whatsapp' => fake()->phoneNumber(),
                'location' => json_encode([
                    'provice' => 'DKI Jakarta',
                    'regency' => fake()->randomElement(['Duren Sawit', 'Jatinegara', 'Cakung', 'Kramat Jati', 'Pulo Gadung', 'Ciracas'])
                ]),
                'desc' => fake()->sentence()
            ]);

            ShopCatalog::create([
                'shop_id' => $i,
                'name' => 'Produk Lainnya',
                'slug' => 'produk-lainnya',
                'desc' => 'Produk Lainnya Dari Toko Ini.'
            ]);
        }

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
            'name' => 'Semua Produk',
            'slug' => 'semua-produk'
        ]);

        $categories = [
            [
                'name' => 'Elektronik',
                'slug' => 'elektronik',
                'desc' => 'Kategori untuk produk elektronik',
                'subcategories' => [
                    ['name' => 'Smartphone', 'slug' => 'smartphone'],
                    ['name' => 'Laptop', 'slug' => 'laptop'],
                    ['name' => 'Televisi', 'slug' => 'televisi'],
                ],
            ],
            [
                'name' => 'Pakaian',
                'slug' => 'pakaian',
                'desc' => 'Kategori untuk produk pakaian',
                'subcategories' => [
                    ['name' => 'Pria', 'slug' => 'pria'],
                    ['name' => 'Wanita', 'slug' => 'wanita'],
                    ['name' => 'Anak-anak', 'slug' => 'anak-anak'],
                ],
            ],
            [
                'name' => 'Kesehatan & Kecantikan',
                'slug' => 'kesehatan-kecantikan',
                'desc' => 'Kategori untuk produk kesehatan dan kecantikan',
                'subcategories' => [
                    ['name' => 'Perawatan Kulit', 'slug' => 'perawatan-kulit'],
                    ['name' => 'Suplemen', 'slug' => 'suplemen'],
                    ['name' => 'Makeup', 'slug' => 'makeup'],
                ],
            ],
            [
                'name' => 'Rumah Tangga',
                'slug' => 'rumah-tangga',
                'desc' => 'Kategori untuk produk rumah tangga',
                'subcategories' => [
                    ['name' => 'Perabotan', 'slug' => 'perabotan'],
                    ['name' => 'Dekorasi', 'slug' => 'dekorasi'],
                    ['name' => 'Alat Kebersihan', 'slug' => 'alat-kebersihan'],
                ],
            ],
            [
                'name' => 'Makanan & Minuman',
                'slug' => 'makanan-minuman',
                'desc' => 'Kategori untuk produk makanan dan minuman',
                'subcategories' => [
                    ['name' => 'Makanan', 'slug' => 'makanan'],
                    ['name' => 'Minuman', 'slug' => 'minuman'],
                    ['name' => 'Snack', 'slug' => 'snack'],
                ],
            ],
            [
                'name' => 'Olahraga',
                'slug' => 'olahraga',
                'desc' => 'Kategori untuk produk olahraga',
                'subcategories' => [
                    ['name' => 'Pakaian Olahraga', 'slug' => 'pakaian-olahraga'],
                    ['name' => 'Alat Olahraga', 'slug' => 'alat-olahraga'],
                    ['name' => 'Sepatu Olahraga', 'slug' => 'sepatu-olahraga'],
                ],
            ],
            [
                'name' => 'Otomotif',
                'slug' => 'otomotif',
                'desc' => 'Kategori untuk produk otomotif',
                'subcategories' => [
                    ['name' => 'Mobil', 'slug' => 'mobil'],
                    ['name' => 'Motor', 'slug' => 'motor'],
                    ['name' => 'Aksesoris', 'slug' => 'aksesoris'],
                ],
            ],
            [
                'name' => 'Bayi & Anak',
                'slug' => 'bayi-anak',
                'desc' => 'Kategori untuk produk bayi dan anak',
                'subcategories' => [
                    ['name' => 'Pakaian Bayi', 'slug' => 'pakaian-bayi'],
                    ['name' => 'Mainan', 'slug' => 'mainan'],
                    ['name' => 'Perlengkapan Bayi', 'slug' => 'perlengkapan-bayi'],
                ],
            ],
            [
                'name' => 'Buku',
                'slug' => 'buku',
                'desc' => 'Kategori untuk produk buku',
                'subcategories' => [
                    ['name' => 'Novel', 'slug' => 'novel'],
                    ['name' => 'Komik', 'slug' => 'komik'],
                    ['name' => 'Majalah', 'slug' => 'majalah'],
                ],
            ],
            [
                'name' => 'Hobi & Koleksi',
                'slug' => 'hobi-koleksi',
                'desc' => 'Kategori untuk produk hobi dan koleksi',
                'subcategories' => [
                    ['name' => 'Mainan Koleksi', 'slug' => 'mainan-koleksi'],
                    ['name' => 'Barang Antik', 'slug' => 'barang-antik'],
                    ['name' => 'Aksesoris Hobi', 'slug' => 'aksesoris-hobi'],
                ],
            ],
        ];

        foreach ($categories as $categoryData) {
            $category = ProductCategory::create([
                'name' => $categoryData['name'],
                'slug' => $categoryData['slug'],
                'desc' => $categoryData['desc'],
            ]);

            foreach ($categoryData['subcategories'] as $subcategoryData) {
                ProductSubCategory::create([
                    'category_id' => $category->id,
                    'name' => $subcategoryData['name'],
                    'slug' => $subcategoryData['slug'],
                ]);
            }
        }

    }
}
