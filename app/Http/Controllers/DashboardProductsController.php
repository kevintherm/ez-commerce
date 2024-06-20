<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Shop;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ProductCategory;
use Auth;
use File;
use GuzzleHttp\Handler\Proxy;
use Illuminate\Support\Arr;

class DashboardProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return back();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('dashboard.shop.products.create', [
            'shop' => Shop::where('user_id', auth()->user()->id)->firstOrFail(),
            'categories' => ProductCategory::latest()->get(),
        ]);
    }


    public function store(Request $request)
    {
        $valid = $request->validate([
            'name' => 'required|max:256',
            'desc' => 'required',
            'weight' => 'required|min:0',
            'price' => 'required',
            'condition' => 'required|integer',
            'stock' => 'required|integer',
            'catalog_id' => 'required|integer',
            'sub_category_id' => 'required|integer',
            'image.*' =>  'image|mimes:png,jpg,jpeg'
        ]);


        if ($request->hasFile('image')) {
            $imgNames = [];
            foreach ($request->file('image') as $image) {
                $storeImg = $image->store('/images/products', 'public');
                $imgNames[] = str_replace('images/products/', '', $storeImg);
            }
            $valid['image'] = json_encode($imgNames);
        } else $valid['image'] = json_encode(['default_product.png']);


        $valid['shop_id'] = Shop::where('user_id', Auth::user()->id)->first()->id;
        $valid['slug'] = Str::of($valid['name'])->slug();
        $valid['sold'] = 0;

        Product::create($valid);

        return redirect('/shop')->with('alert', 'Produk Berhasil Ditambahkan!');
    }


    public function show()
    {
        return back();
    }


    public function edit(Product $product)
    {
        if ($product->shop->owner->username !== auth()->user()->username) return abort(403);
        return view('dashboard.shop.products.edit', [
            'product' => $product,
            'shop' => Shop::where('user_id', auth()->user()->id)->firstOrFail(),
            'categories' => ProductCategory::latest()->get(),
        ]);
    }


    public function update(Request $request, Product $product)
    {
        if ($product->shop->owner->username !== auth()->user()->username) return abort(403);
        // return $request->all();
        $valid = $request->validate([
            'desc' => 'required',
            'weight' => 'required|min:0',
            'price' => 'required',
            'condition' => 'required|integer',
            'stock' => 'required|integer',
            'catalog_id' => 'required|integer',
            'sub_category_id' => 'required|integer',
            'disabled' => 'required|integer',
            'visibility' => 'required|integer',
            'image.*' =>  'image|mimes:png,jpg,jpeg'
        ]);

        if ($request->hasFile('image')) {
            $imgNames = [];
            foreach ($request->file('image') as $image) {
                // $imageName = $image->hashName();
                // $imgNames[] = $imageName;
                $storeImg = $image->store('/images/products', 'public');
                $imgNames[] = str_replace('images/products/', '', $storeImg);
            }
            // delete old image
            $old = json_decode($product->image);
            foreach ($old as $image) {
                if (File::exists(public_path("/storage/images/products/$image"))) {
                    File::delete(public_path("/storage/images/products/$image"));
                }
            }
            $valid['image'] = json_encode($imgNames);
        }

        $product->update($valid);

        return redirect('/shop')->with('alert', "Updated: $product->name");
    }

    public function destroy(Product $product)
    {
        if ($product->shop->owner->username !== auth()->user()->username) return abort(403);
        $carts = Cart::all();
        $exist = false;
        foreach ($carts as $cart) {
            foreach ($cart->products as $prod) {
                if ($prod->id === $product->id) {
                    $exist = true;
                }
            }
        }
        if ($exist)
            return back()->with('alert', "Error Deleting: $product->name, reason: Product is on someone else`s cart");
        else {
            $product->delete();
            $productImages = json_decode($product->image);
            foreach ($productImages as $image) {
                File::delete(public_path("/storage/images/products/$image"));
            }
            return back()->with('alert', "Deleted: $product->name");
        }
    }

    public function snap()
    {
        $carts = Cart::all();
        $exist = false;
        $existing_products_id = [];
        $products = Shop::where('user_id', auth()->user()->id)->first()->products->pluck('id');
        foreach ($carts as $cart) {
            foreach ($cart->products as $prod) {
                $existing_products_id[] = $prod->id;
            }
        }
        for ($i = 0; $i < $products->count(); $i++) {
            if (in_array($products[$i], $existing_products_id))
                $exist = true;
        }
        if (!$exist) {
            auth()->user()->shop->products()->delete();
            return redirect()->back()->with('alert', 'Deleted All Products');
        } else {
            return back()->with('alert', "Error Deleting, reason: Product(s) is on someone else`s cart");
        }
    }
}