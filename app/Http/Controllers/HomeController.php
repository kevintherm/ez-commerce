<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\ProductCategory;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function index()
    {
        $top_on_category = ProductCategory::find(mt_rand(1, ProductCategory::count()));

        return view('home', [
            'top_shops' => Shop::limit(3)->get(),
            'newest_products' => Product::latest()->visibility('public')->get(),
            'top_on_category' => $top_on_category ? $top_on_category->subcategory->first() : null,
            'best_seller' => Product::orderBy('sold', 'desc')->visibility('public')->get()
        ]);
    }

    public function searchProducts(Request $request)
    {
        return view('search', [
            'title' => 'Semua Produk',
            'products' => Product::filters(request()->all())->query(request('search'))->visibility('public')->get(),
            'others' => Product::visibility('public')->get(),
            'store_location' => Shop::all()->unique('location')->pluck('location'),
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
