<?php

namespace App\Http\Controllers;

use Auth;
use App\Models\User;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{

    /**
     * Display wishlist component
     */
    public function wishlist_view()
    {
        return view('wishlist_view', [
            'wishlist' => Auth::user()->wishlist->first()->products
        ]);
    }

    /**
     * Display wishlist page
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Check if user has a wishlist
        if ($user->wishlist()->count() < 1) {
            Wishlist::createWishlist(auth()->user()->id, 'General');
        }

        return view('wishlist', [
            'title' => 'Wishlist - General'
        ]);
    }

    /**
     * Toggle an item from wishlist
     */
    public function store(Request $request)
    {
        // only allow ajax request
        if (!$request->isXmlHttpRequest())
            return abort(403);

        $user = Auth::user();
        $wishlist = $user->wishlist()->first();

        // Check if user has a wishlist
        if ($user->wishlist()->count() < 1) {
            Wishlist::createWishlist(auth()->user()->id, 'General');
        }

        $product = Product::where('slug', $request->product)->first();

        if ($wishlist->products()->where('id', $product->id)->exists()) {
            // If the product is already in the wishlist, remove it

            $wishlist->products()->detach($product->id);
            return response('Removed From Wishlist', 200);
        }

        // If the product is not in the wishlist, add it
        $wishlist->products()->attach($product->id);

        return response('Added To Wishlist', 201);
    }

    /**
     * Remove an item from wishlist
     */
    public function destroy(Wishlist $wishlist, Request $request)
    {
        // only allow ajax request
        if (!$request->isXmlHttpRequest())
            return abort(403);

        $productId = Product::where('slug', $request->product)->first()->id;

        $wishlist->products()->detach($productId);

        return response('Success', 200);
    }
}
