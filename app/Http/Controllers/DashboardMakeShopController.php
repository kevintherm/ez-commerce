<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\User;
use App\Models\ShopCatalog;
use Illuminate\Http\Request;

class DashboardMakeShopController extends Controller
{

    public function create(User $user)
    {
        $auth = auth()->user();
        if ($user->hasShop($auth->id))
            return redirect()->to('/dashboard/shop');

        return view('dashboard.create_shop', ['user' => $auth]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'url' => 'required|unique:shops',
            'whatsapp' => 'required|min:8|max:14',
            'desc' => 'string'
        ]);

        $otherData = [
            'user_id' => auth()->user()->id,
            'location' => json_encode($request->location),
            'link' => json_encode($request->link),
            'phone' => $validatedData['whatsapp'],
            'desc' => preg_replace('/\r\n/', PHP_EOL, $request->desc)
        ];

        $shop = Shop::create(array_merge($validatedData, $otherData));

        ShopCatalog::create([
            'shop_id' => $shop->id,
            'name' => 'Produk Lainnya',
            'slug' => 'produk-lainnya',
            'desc' => 'Produk Lainnya Dari Toko Ini.'
        ]);

        return redirect()->back()->with('alert', 'Toko Telah Berhasil Dibuat!');
    }
}
