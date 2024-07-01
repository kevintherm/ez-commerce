<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\Product;
use Http;
use Illuminate\Http\Request;
use App\Models\ProductCategory;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function index()
    {
        $top_on_category = ProductCategory::
            inRandomOrder()
            ->first()
            ->subcategory()
            ->inRandomOrder()
            ->first();

        return view('home', [
            'top_shops' => Shop::
                orderBy('total_ratings', 'desc')
                ->limit(3)
                ->get(),
            'latest_products' => Product::
                latest()
                ->limit(5)
                ->visibility('public')
                ->get(),
            'toc_products' => $top_on_category
                ->products()
                ->limit(5)
                ->visibility('public')
                ->get(),
            'toc' => $top_on_category,
            'best_seller' => Product::
                orderBy('sold', 'desc')
                ->limit(5)
                ->visibility('public')
                ->get()
        ]);
    }

    public function searchProducts(Request $request)
    {
        return view('search', [
            'title' => 'Semua Produk',
            'products' => Product::
                filters(request()->all())
                ->query(request('search'))
                ->filters(request(['order', 'min_price', 'max_price']))
                ->category(request('subcategory'))
                ->visibility('public')
                ->get(),
            'others' => Product::
                visibility('public')
                ->get(),
            'store_location' => Shop::
                get(['location'])
                ->unique('location')
                ->pluck('location'),
        ]);
    }

    public function searchShops(Request $request)
    {
        $shops = Shop::latest()->search($request->search)->get();

        return view('search', [
            'title' => 'Cari Toko',
            'shops' => $shops,
            'store_location' => Shop::all()->pluck('location'),
        ]);
    }
}
