<?php

namespace App\Http\Controllers;

use Http;
use Illuminate\Http\Request;

class RajaOngkirController extends Controller
{
    public function getProvince()
    {
        $response = Http::withHeaders([
            'key' => env('RAJAONGKIR_API_KEY')
        ])->get(env("RAJAONGKIR_API_URL") . 'province');

        if ($response->status() != 200)
            return response('failed api R.O', 503);

        return $response->json()['rajaongkir']['results'];
    }

    public function getCity(Request $request)
    {
        $response = Http::
            withHeaders([
                'key' => env('RAJAONGKIR_API_KEY')
            ])
            ->get(env("RAJAONGKIR_API_URL") . 'city', [
                'province' => $request->province
            ]);

        if ($response->status() != 200)
            return response('failed api R.O', 503);

        return $response->json()['rajaongkir']['results'];
    }

    public function getCost(Request $request)
    {
        $dest = $request->destination;
        $origin = $request->origin;
        $weight = $request->weight;

        $response = Http::withHeaders([
            'key' => env('RAJAONGKIR_API_KEY')
        ])->post(env("RAJAONGKIR_API_URL") . "cost", $request->only(['destination', 'origin', 'weight', 'courier']));

        if ($response->status() != 200)
            return response('failed api R.O' . $response, 503);

        return $response->json()['rajaongkir']['results'];
    }

    private function getUserLocation()
    {
        $response = Http::get('http://ip-api.com/json/?fields=32784');
        return $response['city'];
    }
}
