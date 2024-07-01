<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function cart_view(Cart $cart)
    {
        $cart = $cart->find(auth()->user()->id);
        if (!$cart)
            User::setCart(auth()->user()->id);
        if (request('token') !== csrf_token() && !Cart::find(auth()->user()->id))
            return '<h3 class="text-danger text-center">Cart Failed To Load</h3>';
        return view('cart_view', [
            'title' => 'Keranjang',
            'carts' => $cart->products,
            'subtotal' => $cart->getTotalPrice(auth()->user()->id),
        ]);
    }
    public function cartView(Cart $cart, Request $request)
    {
        if (!Cart::find(auth()->user()->id)) {
            User::setStatus(auth()->user()->id, 0);
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect('/login')->with('alert', 'Your Information Was Handled Incorrectly, Please Re-Login With Your Legitimate Credentials.');
        }
        return view('cart', [
            'title' => 'Keranjang',
            'carts' => auth()->user()->cart->products,
            'subtotal' => $cart->getTotalPrice(auth()->user()->id),
        ]);
    }

    public function addToCart(Request $request, Cart $cart)
    {
        $incomingProduct = $request->except('_token'); //  type array

        if ($request->_token !== csrf_token())
            return back()->with('alert', 'CSRF Token does not match.');

        if (Product::find($incomingProduct['product_id'])->disabled)
            return back()->with('alert', 'Cannot Add Item to Cart Because Product is inactive.');

        $incomingProduct['subtotal'] = $incomingProduct['price'] * $incomingProduct['count'];

        $existingProduct = DB::table('cart_products')
            ->where('product_id', $incomingProduct['product_id'])
            ->first();


        // Insert Or Update;
        $cart_products = $cart->find(auth()->user()->id)->products();

        DB::beginTransaction();

        if ($existingProduct) {
            // Update existing product
            $cart_products
                ->updateExistingPivot(
                    $incomingProduct['product_id'],
                    [
                        'count' => $existingProduct->count + $incomingProduct['count'],
                        'subtotal' => $existingProduct->subtotal + $incomingProduct['subtotal'],
                        'notes' => $incomingProduct['notes']
                    ]
                );
        } else {
            // Add new product
            $cart_products->attach($incomingProduct['product_id'], [
                'count' => $incomingProduct['count'],
                'subtotal' => $incomingProduct['subtotal'],
                'notes' => $incomingProduct['notes']
            ]);
        }

        // update totalprice column on table carts
        $total_price = Cart::getTotalPrice(auth()->user()->id);

        try {
            Cart::setTotalPrice(auth()->user()->id, $total_price);
        } catch (\Illuminate\Database\QueryException $queryException) {
            return response('Total price is too high.', 400);
        }

        DB::commit();

        return response('Success', 200);
    }

    // Edit product on cart view
    public function editItem($id)
    {
        $product = Auth::user()
            ->cart
            ->products()
            ->where('product_id', $id)
            ->get();

        if (!$product->count())
            return redirect()->back();

        return view('editCart', [
            'title' => 'Edit ',
            'carts' => $product,
            'product' => $id
        ]);
    }

    public function updateItem(Request $request)
    {
        $product = Cart::find(auth()->user()->id)->products()->where('product_id', $request->id)->first();

        $max_count = implode(':', ['integer|min:0|max', $product->stock]);

        $validatedData = $request->validate([
            'notes' => 'max:255',
            'count' => $max_count
        ]);
        $subtotal = $validatedData['count'] * $product->price;

        DB::beginTransaction();

        Cart::find(auth()->user()->id)->products()->updateExistingPivot($request->id, [
            'notes' => $validatedData['notes'],
            'count' => $validatedData['count'],
            'subtotal' => $subtotal
        ]);

        // update totalprice column on table carts
        $total_price = Cart::getTotalPrice(auth()->user()->id);

        try {
            Cart::setTotalPrice(auth()->user()->id, $total_price);
        } catch (\Illuminate\Database\QueryException $queryException) {
            // Total price is too high.
            return redirect()->back()->with('alert', 'Total price is too high.');
        }

        DB::commit();

        return redirect('/cart');
    }

    public function deleteItem($slug, $id)
    {
        $id = base64_decode($id);
        Cart::deleteItem($id);

        return redirect()->back();
    }

    public function deleteAll()
    {
        if (request()->token !== csrf_token())
            return back();
        Cart::snap();

        return redirect()->back();
    }
}
