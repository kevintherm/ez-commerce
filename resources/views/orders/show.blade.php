@extends('layouts.main')

@push('head')
    <link rel="stylesheet" href="//cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css">
    <script src="//cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
    {{-- @TODO: replace SET_YOUR_CLIENT_KEY_HERE with your client key --}}
    <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
    {{-- Note: replace with src="https://app.midtrans.com/snap/snap.js" for Production environment --}}
@endpush

@push('script')
    <script>
        $('table').DataTable();
        $('#pay-button').click(() => {
            snap.pay('{{ $snapToken }}', {
                onSuccess: function(result) {
                    /* You may add your own implementation here */
                    alert("payment success!");
                    window.location.reload()
                },
                onPending: function(result) {
                    /* You may add your own implementation here */
                    alert("wating your payment!");
                },
                onError: function(result) {
                    /* You may add your own implementation here */
                    alert("payment failed!");
                },
                onClose: function() {
                    /* You may add your own implementation here */
                    alert('you closed the popup without finishing the payment');
                }
            })
        })
    </script>
    @if (request('hi'))
        <script>
            setTimeout(() => {
                $('#{{ request('hi') }}').focus().click();
            }, 500);
        </script>
    @endif
@endpush

@section('content')
    @include('layouts.navbar')

    <main>
        <div class="container-xl py-3">
            @include('utilities.breadcrumb', ['title' => '#' . $order->number])

            <a href="/orders" class="btn btn-outline-warning btn-sm d-print-none"><i class="bi bi-arrow-left"></i> Back</a>
            <h4 class="text-muted py-3">{{ $title }}</h4>

            <div class="box">
                <div class="card shadow border-0 rounded-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between" id="order_status">
                            <div>
                                <time class="d-block">{{ $order->created_at->format('Y-m-d H:i A') }}</time>
                                <small class="text-muted">#{{ $order->number }}</small>
                            </div>
                            <div>
                                <span
                                    class="badge bg-opacity-25 @switch($order->payment_status)
                                                @case(2)
                                                bg-success text-success
                                                @break

                                                @case(1)
                                                    bg-warning text-warning
                                                @break

                                                @case(3)
                                                    bg-warning text-danger
                                                @break

                                                @default
                                                    bg-danger text-danger
                                            @endswitch fs-6">{{ $order->getPaymentStatus() }}</span>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between py-3" id="products_detail">
                            <div>
                                @foreach ($order->groupProductsByMerchant() as $merchant_name => $products)
                                    <strong>{{ $merchant_name }}</strong>
                                    @foreach ($products as $product)
                                        @php
                                            $reviewed = \App\Models\ProductRating::where('product_id', $product['id'])
                                                ->where('user_id', Auth::user()->id)
                                                ->where('order_id', $order->id)
                                                ->first();
                                        @endphp
                                        <ul>
                                            <li>
                                                <a href="{{ $product['url'] }}" class="link">
                                                    {{ $product['name'] }}
                                                </a>
                                                x{{ $product['quantity'] }}
                                                @if ($order->payment_status == 2)
                                                    @if (!$reviewed)
                                                        <a class="link-warning"
                                                            href="{{ route('ratings.create', [$order->number, $product['id']]) }}">
                                                            Berikan Rating
                                                        </a>
                                                    @else
                                                        <a class="link-info" href="{{ $product['url'] }}#reviews">
                                                            Lihat Review
                                                        </a>
                                                    @endif
                                                @endif
                                            </li>
                                        </ul>
                                    @endforeach
                                @endforeach
                            </div>
                            <div class="border-start d-flex flex-column justify-content-center align-items-center px-4">
                                <small class="text-muted">Total Harga</small>
                                <strong>Rp{{ number_format($order->total_price, '0', ',', '.') }}</strong>
                            </div>
                        </div>
                        <div class="d-flex justify-content-start align-items-center gap-3 py-2 d-print-none"
                            id="button_group">
                            <a class="btn btn-secondary" href="{{ route('orders.invoice', $order->number) }}">
                                <i class="bi bi-printer"></i> Cetak
                            </a>
                            @if ($order->payment_status == 1)
                                <button class="btn btn-outline-primary fw-semibold px-5" id="pay-button">Bayar</button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>
@endsection
