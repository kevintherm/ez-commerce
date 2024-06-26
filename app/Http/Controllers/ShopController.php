<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use Http;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ShopCatalog;

class ShopController extends Controller
{
    public function all(Shop $shop)
    {
        $data = [
            'title' => 'All Products',
            'shop' => $shop,
            'products' => Product::where('shop_id', $shop->id)
                ->query(request('search'))
                ->filters(request(['orderBy']))
                ->visibility('public')
                ->get()
        ];


        return view('myshop.all', $data);
    }

    public function index(Shop $shop, Product $product)
    {
        return view('myshop.index', [
            'title' => $shop->name,
            'shop' => $shop,
            'catalogs' => $shop
                ->catalog
                ->all(),
            'products' => $shop
                ->products()
                ->latest()
                ->visibility('public')
                ->limit(6)
                ->get(),
            'best_seller' => $shop
                ->products()
                ->orderBy('sold', 'desc')
                ->visibility('public')
                ->limit(6)
                ->get(),
        ]);
    }

    /**
     * Product page
     */
    public function show(Shop $shop, Product $product, Request $request)
    {
        // When the product is not public, only the owner can view it
        if (
            auth()->check() &&
            auth()->user()->username !== $product->shop->owner->username &&
            !$product->visibility
        )
            return abort(403, 'This item is private.');

        return view('myshop.show', [
            'title' => $product->name,
            'product' => $product,
            'ratings' => $product
                ->ratings()
                ->when(
                    auth()->user()?->id,
                    fn($query, $id)
                    => $query
                        ->orderByRaw("CASE
                            WHEN user_id = ? THEN 1
                            ELSE 2
                        END", // Tampilkan review user paling atas
                            [$id]
                        )
                )
                ->get()
        ]);
    }

    public function catalog(Shop $shop, ShopCatalog $catalog)
    {
        return view('myshop.catalog', [
            'title' => $catalog->name,
            'shop' => $shop,
            'catalogs' => $catalog->where('shop_id', $shop->id)->get(),
            'selected' => $catalog
        ]);
    }
}
