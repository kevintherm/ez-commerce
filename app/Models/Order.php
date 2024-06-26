<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    public $primaryKey = 'number';
    public $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    static public function getTotalPrice($json)
    {
        $arr1 = collect(json_decode($json, false));
        $totalPrice = 0;
        foreach ($arr1 as $arr) {
            $totalPrice += $arr->price * $arr->quantity;
        }
        return $totalPrice;
    }

    public function getPaymentStatus($status = null)
    {
        switch ($status ?? $this->payment_status) {
            case 1:
                return 'Menunggu Pembayaran';
                break;
            case 2:
                return 'Dibayar';
                break;
            case 3:
                return 'Kadaluarsa';
                break;
            case 4:
            default:
                return 'Dibatalkan';
                break;
        }
    }

    public function groupProductsByMerchant()
    {
        $result = [];

        $products = json_decode($this->products_json, true);
        $key = 'merchant_name';

        foreach ($products as $val) {
            if (array_key_exists($key, $val)) {
                $result[$val[$key]][] = $val;
            } else {
                $result[""][] = $val;
            }
        }


        return $result;
    }
}
