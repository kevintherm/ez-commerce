<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use App\Models\User;

class Cart extends Model
{
    use HasFactory;

    public $guarded = ['id'];
    protected $primaryKey = 'user_id';

    static public function setCart($user_id)
    {
        User::setCart($user_id);
    }

    static public function deleteItem($id)
    {
        Cart::find(auth()->user()->id)->products()->detach($id);
    }

    static public function getTotalPrice($user_id)
    {
        $subtotal = 0;
        foreach (User::find($user_id)->cart->products as $item) {
            $subtotal += $item->price * $item->pivot->count;
        }

        return $subtotal;
    }

    static public function setTotalPrice($user_id, $totalprice)
    {
        return Cart::where('user_id', $user_id)->update(['totalprice' => $totalprice]);
    }

    static public function snap()
    {
        Cart::find(auth()->user()->id)->products()->detach();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'cart_products', 'user_id', 'product_id')->withTimestamps()->withPivot(['count', 'notes', 'created_at', 'subtotal']);
    }

    public function checkout()
    {
        return 'true';
    }
}
