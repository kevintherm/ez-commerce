<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductRating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    public function giveRatings(Order $order, $product)
    {
        $product = Product::find($product);

        return view('ratings.create', [
            'product' => $product,
            'order' => $order,
            'title' => 'Berikan Ratings'
        ]);
    }

    public function storeRatings(Order $order, Product $product, Request $request)
    {
        $validated = $request->validate([
            'selected_rating' => 'required|integer|min:0|max:5',
            'review' => 'nullable'
        ]);

        ProductRating::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'user_id' => Auth::user()->id,
            'review' => $validated['review'],
            'rating' => $validated['selected_rating']
        ]);

        return redirect(route('orders.show', $order->number))->with('alert', 'Review dibuat! Terimakasih telah memberi masukan!');
    }
}
