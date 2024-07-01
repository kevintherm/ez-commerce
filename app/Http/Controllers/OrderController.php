<?php

namespace App\Http\Controllers;

use Auth;
use Carbon\Carbon;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use LaravelDaily\Invoices\Invoice;
use LaravelDaily\Invoices\Classes\Buyer;
use LaravelDaily\Invoices\Classes\Party;
use LaravelDaily\Invoices\Classes\InvoiceItem;
use App\Services\Midtrans\CreateSnapTokenService;

class OrderController extends Controller
{
    public function index()
    {
        return view('orders.index', [
            'orders' => Auth::user()
                ->order()
                ->latest()
                ->get(),
            'title' => 'Daftar Pesanan'
        ]);
    }

    /**
     * Create new order
     */
    public function store()
    {
        $user = Auth::user();
        $cartProducts = $user->cart->products;

        // Check if the cart is empty
        if ($cartProducts->count() < 1)
            return back()->with('alert', 'Keranjang kosong.');

        $products_json = [];
        foreach ($cartProducts as $product) {
            $propertyToAppend['quantity'] = $product->pivot->count;
            $propertyToAppend['id'] = $product->id;
            $propertyToAppend['slug'] = $product->slug;
            $propertyToAppend['price'] = $product->price;
            $propertyToAppend['name'] = str($product->name)->limit(30, '...');
            $propertyToAppend['merchant_name'] = str($product->shop->name)->limit(30, '...');
            $propertyToAppend['url'] = url($product->shop->url . '/' . $product->slug);

            $products_json[] = $propertyToAppend;
        }

        $products_json = json_encode($products_json);

        // Order Parameter
        $params = [
            'number' => mt_rand(10000000, 99999999), // order number unique
            'user_id' => $user->id,
            'products_json' => $products_json,
            'total_price' => Cart::getTotalPrice($user->id),
            'payment_status' => 1 //Waiting for payment
        ];

        DB::beginTransaction();

        try {
            $order = Order::create($params);

            foreach ($cartProducts as $product) {
                OrderStatus::create([
                    'product_id' => $product->id,
                    'shop_id' => $product->shop_id,
                    'order_id' => $order->number,
                    'status' => 'Un-Confirmed',
                ]);
            }
        } catch (\Illuminate\Database\QueryException $err) {
            return back()->with('alert', $err->getMessage());
        }

        DB::commit();

        // Clear user's cart
        Cart::snap();
        Cart::setTotalPrice(auth()->user()->id, 0);


        return redirect()->to('/orders')->with('alert', 'Order Created');
    }



    public function show(Order $order)
    {
        if (auth()->user()->id !== $order->user_id)
            return abort(404, 'Order Not Found');

        $snapToken = $order->snap_token;
        if (empty($snapToken)) {
            // Jika snap token masih NULL, buat token snap dan simpan ke database

            $midtrans = new CreateSnapTokenService($order);
            $snapToken = $midtrans->getSnapToken();

            $order->snap_token = $snapToken;
            $order->save();
        }

        return view('orders.show', ['snapToken' => $snapToken, 'order' => $order, 'title' => "Detail Pesanan"]);
    }

    public function getInvoice(Order $order)
    {
        $client = new Party([
            'name' => 'EZ Commerce'
        ]);

        $customer = new Party([
            'name' => $order->user->name,
            'custom_fields' => [
                'phone' => $order->user->phone,
                'email' => $order->user->email,
            ]
        ]);

        $products = json_decode($order->products_json);
        $invoiceItems = [];
        foreach ($products as $product) {
            $invoiceItems[] =
                InvoiceItem::make($product->name)
                    ->pricePerUnit($product->price)
                    ->quantity($product->quantity);
        }

        $invoice = Invoice::make('INVOICE')
            ->series('#' . $order->number)
            ->status($order->getPaymentStatus())
            ->serialNumberFormat('{SERIES}')
            ->buyer($customer)
            ->seller($client)
            ->date($order->created_at)
            ->dateFormat('d F Y')
            ->payUntilDays(1)
            ->currencySymbol('Rp. ')
            ->currencyCode('IDR')
            ->currencyFormat('{SYMBOL}{VALUE}')
            ->currencyThousandsSeparator('.')
            ->currencyDecimalPoint(',')
            ->addItems($invoiceItems)
            ->filename('invoice-ezcommerce-' . $order->number)
            ->logo(public_path('\img\icons-512.png'));


        return $invoice->stream();
    }

}
