@extends('layouts.main')

@push('head')
    <link rel="stylesheet" href="/css/hover.css">
@endpush

@section('content')
    @include('layouts.navbar')
    <main style="margin-top: 4rem;">
        <div class="container-xl py-3">
            @include('utilities.breadcrumb')
            <div class="row g-3">
                <div class="col-12 px-5 col-navtop">
                    <nav class="nav nav-pills flex-column flex-sm-row nav-product">
                        <button class="flex-sm-fill text-sm-center nav-link border border-primary me-1 active"
                            aria-current="page" role="button"
                            onclick="$('html, body').animate({scrollTop: $('#product-info').offset().top - 90}, 500)">Info
                            Produk</button>
                        <button class="flex-sm-fill text-sm-center nav-link border border-primary me-1" role="button"
                            onclick="$('html, body').animate({scrollTop: $('#reviews').offset().top - 90}, 500)">Ulasan</button>
                        <button class="flex-sm-fill text-sm-center nav-link border border-primary me-1"
                            onclick="$('html, body').animate({scrollTop: $('#disqus_thread').offset().top - 90}, 500)">Diskusi</button>
                    </nav>
                </div>
                <div class="col-12 col-lg-3 col-product-image">
                    <div class="content sticky-top" style="top: 4.5em; z-index: 2;">
                        <div class="card border-0 product-image">
                            <div class="row justify-content-center">
                                <div class="col col-md-6 col-lg-12">

                                    <img loading="lazy"role="button" onclick="swal.fire({imageUrl:this.src})"
                                        src="{{ asset('storage/images/products/' . json_decode($product->image)[0]) }}"
                                        class="card-img img-main-thumbnail" alt="Product Thumbnail" id="mainImg">

                                </div>
                            </div>
                            <div class="card-body d-flex justify-content-center">
                                @foreach (json_decode($product->image) as $img)
                                    <img loading="lazy"src="{{ asset('storage/images/products/' . $img) }}" width="50"
                                        height="50"
                                        class="img-sub-thumbnail img-fixed rounded shadow-sm mx-1 outline-salmon"
                                        style="width: 50px;" onclick="$('#mainImg').attr('src', this.src)">
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-product-content border-bottom rounded shadow-sm">
                    <div class="card border-0">
                        <div class="card-body">
                            <h4 class="card-title fw-semibold h4">
                                {{ $product->name }}
                            </h4>
                            <h6 class="card-subtitle mb-2 text-muted display-flex align-items-center">Terjual
                                <span class="font-monospace fw-bold">{{ $product->sold }}</span>
                                &#8226;
                                <img loading="lazy"src="/img/star-big.svg" alt="Ulasan" width="16">
                                {{ $product->getAvgRatings() }} ({{ $product->ratings->count() }} Ulasan)
                            </h6>
                            <h2 class="price fw-bold mt-2 mb-3">Rp{{ number_format($product->price, 0, ',', '.') }}</h2>
                            <ul class="nav nav-tabs">
                                <li class="nav-item">
                                    <a class="nav-link active" aria-current="page" href="#">Detail</a>
                                </li>
                            </ul>
                            <div class="product-description my-2 px-2" id="product-info">
                                <p class="text-muted m-0 p-0">
                                    Kondisi: <span
                                        class="text-success fw-semibold">{{ $product->condition ? 'Baru' : 'Bekas' }}</span>
                                </p>
                                <p class="text-muted m-0 p-0">
                                    Berat: <span class="text-success fw-semibold">{{ $product->weight }} Kg</span>
                                </p>
                                <p class="text-muted m-0 p-0">
                                    Kategori: <a
                                        href="/category/{{ $product->subcategory->category->slug . '/' . $product->subcategory->slug }}"
                                        class="text-success fw-semibold">{{ $product->subcategory->name ?? 'Tidak Ada' }}</a>
                                </p>
                                <p class="text-muted m-0 p-0">
                                    Katalog: <a
                                        href="/{{ $product->catalog->shop->url . '/cat' . 'alog/' . $product->catalog->slug }}"
                                        class="text-success fw-semibold">{{ $product->catalog->name }}</a>
                                </p>
                                @if ($product->getVisibility($product->visibility) !== 'Public')
                                    <p class="text-muted m-0 p-0">
                                        Visibilitas: <small title="Only User That Have The Link To This Item Can Access It"
                                            class="text-muted fst-italic fw-semibold">{{ $product->getVisibility($product->visibility) }}</small>
                                    </p>
                                @endif


                                <div class="desc border-bottom mt-3">

                                    <div>
                                        <p class="desc-p">
                                            @if (Str::length($product->desc) > 200)
                                                {{ Str::limit($product->desc, 200, '...') }}
                                                <a role="button" class="link-success fw-semibold text-decoration-none"
                                                    onclick="descMore()">Read More</a>
                                            @else
                                                <?= nl2br(e($product->desc)) ?>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="seller-info border-bottom my-1 py-2 px-2 d-flex">
                                    <div class="row g-0 justify-content-center">
                                        <div class="col-12 d-flex align-items-center">
                                            <a href="#" class="fw-semibold text-decoration-none link-dark ms-3">
                                                <img loading="lazy"src="{{ asset('storage/images/profiles/' . $product->shop->owner->image) }}"
                                                    alt="Seller Profile" width="40" class="img-fluid rounded-circle">
                                            </a>
                                            <h5><a href="/{{ $product->shop->url }}"
                                                    class="fw-semibold text-decoration-none link-dark ms-3">{{ $product->shop->name }}</a>
                                            </h5>
                                            <button class="btn btn-primary btn-sm ms-3">
                                                Follow
                                            </button>
                                        </div>
                                        <div class="col px-2">
                                            <small class="d-block">{!! $product->shop->owner->status
                                                ? 'Online'
                                                : 'Last Seen <b>' . $product->shop->owner->updated_at->diffForHumans(null) . '</b>' !!}</small>
                                            <small><i class="bi bi-star-fill"></i> {{ $product->shop->total_ratings }} Rata
                                                rata ulasan</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="shipping-info border-bottom my-1 py-2 px-2">
                                    <h6 class="fw-bold">Pengirim</h6>
                                    <small class="d-block">
                                        <i class="bi bi-geo-alt-fill"></i> Dikirim Dari
                                        <b id="shop_location">
                                            @if ($product->shop->location)
                                                {{ json_decode($product->shop->location, 1)['regency'] }}
                                            @endif
                                        </b>
                                    </small>
                                    <div id="estimated_shipping">
                                        <button class="btn btn-sm btn-outline-secondary">
                                            Dapatkan Estimasi
                                        </button>
                                    </div>
                                </div>
                                {{-- <div class="promo border-bottom my-1 py-2 px-2">
                                    <h6 class="fw-bold">Promo</h6>
                                    <div class="row row-cols-1 row-cols-lg-2">
                                        <div class="col">
                                            <div class="card mb-3">
                                                <h6 class="card-header fw-bold">Diskon s.d 500Rb</h6>
                                                <div class="card-body">
                                                    <h6 class="card-title">Syarat & Ketentuan</h6>
                                                    <p class="card-text">- Minimal Pembelian 200Rb</p>
                                                    <button class="btn btn-primary">Pakai</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="card mb-3">
                                                <h6 class="card-header fw-bold">Gratis Ongkir Se-Indonesia</h6>
                                                <div class="card-body">
                                                    <h6 class="card-title">Syarat & Ketentuan</h6>
                                                    <p class="card-text">- Minimal Pembelian 60Rb</p>
                                                    <button class="btn btn-primary">Pakai</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div> --}}
                                <div class="report d-flex justify-content-between">
                                    <small class="text-secondary">Laporkan Produk</small>
                                    <a href="#" class="text-decoration-none link-secondary">
                                        <i class="bi bi-flag"></i> Report
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col col-checkout">
                    <div class="content sticky-top promotion" style="top: 4.5em; z-index: 2;">
                        {{-- <div class="card-body bg-secondary rounded px-3 py-2 mb-3">
                            <a href="#" class="text-decoration-none link-light">
                                <h5 class="card-title">Promotion Sign</h5>
                            </a>
                        </div> --}}
                        <div class="card border-0 shadow checkout-card">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <a class="dropdown-toggle text-decoration-none link-dark" role="button"
                                        data-bs-toggle="collapse" href="#collapseExample" role="button"
                                        aria-expanded="false" aria-controls="collapseExample">
                                        Jumlah & Catatan
                                    </a>
                                </h5>
                                <div class="collapse show" id="collapseExample">
                                    <div class="card card-body border-0 m-0 p-0 mb-3">
                                        <label for="jumlah" class="visually-hidden">Jumlah Barang</label>
                                        <div class="d-flex align-items-center mt-1 mb-3">
                                            <input type="range" name="jumlah" id="jumlah" min="1"
                                                value="1" onclick="$('.jumlah').html(this.value);"
                                                onmouseover="$('.jumlah').html(this.value);"
                                                oninput="$('.jumlah').html(this.value); $('#jumlah_text').val(this.value); $('#count').val(this.value)"
                                                max="{{ $product->stock }}" class="form-range me-2"
                                                style="cursor: pointer; display: none">
                                            <input type="number" min="1" max="{{ $product->stock }}"
                                                maxlength="3" id="jumlah_text" value="1" name="jumlah"
                                                onmouseover="$('.jumlah').html(this.value);"
                                                oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength); $('.jumlah').html(this.value); $('#jumlah').val(this.value); $('#count').val(this.value)"
                                                class="form-control form-control-sm text-center mb-2 fw-bold font-monospace me-2"
                                                placeholder="Jumlah Barang">
                                            <span class="jumlah"
                                                onclick="$('#jumlah_text').toggle(); $('#jumlah').toggle()"
                                                style="cursor: pointer"></span>
                                        </div>
                                        <label for="catatan" class="visually-hidden">Catatan</label>
                                        <textarea name="catatan" id="catatan" class="form-control form-control-sm" placeholder="Catatan Untuk Penjual"
                                            oninput="$('#notes').val(this.value)"></textarea>
                                    </div>
                                </div>
                                <h6 class="card-subtitle mb-2 text-muted">Stok:
                                    <span class="font-monospace fw-bold">{{ $product->stock }}</span>
                                </h6>
                                <div class="d-grid">
                                    <form id="form-buy" class="d-grid" action="/cart" method="POST">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <input type="hidden" name="count" id="count" value="1">
                                        <input type="hidden" name="notes" id="notes" value="">
                                        <input type="hidden" name="price" value="{{ $product->price }}">
                                        @if (Auth::check())
                                            <button class="h5 checkout-button btn btn-primary" name="cart"
                                                {{ $product->disabled || $product->stock <= 0 ? 'disabled title="Product is Inactive"' : '' }}>
                                                <i class="fa-solid fa-cart-arrow-down fa-lg"></i> Keranjang
                                            </button>
                                        @else
                                            <a class="h5 checkout-button btn btn-primary" href="/login">
                                                <i class="fa-solid fa-cart-arrow-down fa-lg"></i> Keranjang
                                            </a>
                                        @endif
                                    </form>
                                </div>
                                </form>
                                @if (Auth::check())
                                    <div class="btn-group d-flex justify-content-center align-items-center mt-2 "
                                        role="group">
                                        @php
                                            $wishlist = auth()->user()->wishlist()->first() ?? false;
                                        @endphp
                                        <button class="border-0 bg-transparent mx-1" id="wishlist">
                                            <i class="{{ $wishlist &&$wishlist->products()->where('id', $product->id)->count()? 'fa-solid': 'fa-regular' }} fa-heart fa-regular"
                                                style="cursor: pointer;" id="wishlist-icon"></i>
                                            <small class="text-muted fw-semibold">Wishlist</small>
                                        </button>|
                                        <button onclick="location.href = '//wa.me/{{ $product->shop->whatsapp }}'"
                                            class="border-0 bg-transparent mx-1">
                                            <i class="fa-regular fa-comments fa-lg"></i>
                                            <small class="text-muted fw-semibold">Chat</small>
                                        </button>|
                                        <button class="border-0 bg-transparent mx-1">
                                            <i class="fa fa-share fa-lg"></i>
                                            <small class="text-muted fw-semibold">Share</small>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="review">
                <div class="row mt-5">
                    <h5 class="fw-bold text-uppercase text-center text-muted" id="reviews">ulasan</h5>
                    <div class="col-md-3"></div>
                    <div class="col-md-6">
                        <div class="well well-sm">
                            <div class="row">
                                <div class="col-xs-12 col-md-6">
                                    <div class="row rating-desc">
                                        <div class="col-xs-3 col-md-3 text-right">
                                            <span class="fa fa-star" style="color: orange"></span> 5
                                        </div>
                                        <div class="col-xs-8 col-md-9">
                                            <div class="progress">
                                                <div class="progress-bar progress-bar-success" role="progressbar"
                                                    aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"
                                                    style="width: 80%">80%
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-3 col-md-3 text-right">
                                            <span class="fa fa-star" style="color: orange"></span> 4
                                        </div>
                                        <div class="col-xs-8 col-md-9">
                                            <div class="progress">
                                                <div class="progress-bar progress-bar-success" role="progressbar"
                                                    aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"
                                                    style="width: 60%">60%
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-3 col-md-3 text-right">
                                            <span class="fa fa-star" style="color: orange"></span> 3
                                        </div>
                                        <div class="col-xs-8 col-md-9">
                                            <div class="progress">
                                                <div class="progress-bar progress-bar-info" role="progressbar"
                                                    aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"
                                                    style="width: 40%">40%
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-3 col-md-3 text-right">
                                            <span class="fa fa-star" style="color: orange"></span> 2
                                        </div>
                                        <div class="col-xs-8 col-md-9">
                                            <div class="progress">
                                                <div class="progress-bar progress-bar-warning" role="progressbar"
                                                    aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"
                                                    style="width: 20%">20%
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-3 col-md-3 text-right">
                                            <span class="fa fa-star" style="color: orange"></span> 1
                                        </div>
                                        <div class="col-xs-8 col-md-9">
                                            <div class="progress">
                                                <div class="progress-bar progress-bar-danger" role="progressbar"
                                                    aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"
                                                    style="width: 15%">15%
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- end row -->
                                </div>
                                <div class="col-xs-12 col-md-6 text-center">
                                    <span class="rating-num h1">{{ $product->getAvgRatings() }}<small
                                            class="fs-6">/5.0</small></span>
                                    <div class="rating">
                                        @php
                                            $averageRatings = $product->getAvgRatings();
                                            $decimal = $averageRatings - floor($averageRatings);
                                            $showDecimalStar = $decimal > 0;
                                        @endphp
                                        @foreach (range(1, $averageRatings) as $i)
                                            <span class="fa fa-star" style="color: orange"></span>
                                        @endforeach
                                        @if ($showDecimalStar)
                                            <span class="fa fa-star-half" style="color: orange"></span>
                                        @endif
                                    </div>
                                    <div>
                                        <span class="fa fa-user"></span>{{ $product->ratings->count() }} Reviews
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3"></div>
                </div>
                <div class="row">
                    <div class="col-12 col-md-3">
                        <div class="container sticky-top mb-5" style="top: 6em; z-index: 2;">
                            <h6 class="text-uppercase fw-semibold">Filter Ulasan</h6>
                            <div class="row">
                                <div class="col border rounded pb-3">
                                    <small class="fw-semibold">Ratings</small>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="" id="star-5">
                                        <label class="form-check-label" for="star-5">
                                            <span class="fa fa-star" style="color: orange"></span> 5
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="" id="star-4">
                                        <label class="form-check-label" for="star-4">
                                            <span class="fa fa-star" style="color: orange"></span> 4
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="" id="star-3">
                                        <label class="form-check-label" for="star-3">
                                            <span class="fa fa-star" style="color: orange"></span> 3
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="" id="star-2">
                                        <label class="form-check-label" for="star-2">
                                            <span class="fa fa-star" style="color: orange"></span> 2
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="" id="star-1">
                                        <label class="form-check-label" for="star-1">
                                            <span class="fa fa-star" style="color: orange"></span> 1
                                        </label>
                                    </div>
                                    <small class="fw-semibold">Urutkan</small>
                                    <select class="form-select" aria-label="Default select example">
                                        <option selected>Terbaru</option>
                                        <option value="1">Terlama</option>
                                    </select>
                                    <hr>
                                    <form action="">
                                        <div class="d-grid">
                                            <button class="btn btn-primary mb-2">Apply</button>
                                            <button class="btn btn-outline-primary" type="button">Reset</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col mt-3">
                        <div class="container">
                            @foreach ($ratings as $rating)
                                <div class="user-info border-bottom my-1 py-2 px-2 d-flex">
                                    <div class="row g-0 justify-content-center">
                                        <div class="col-12 d-flex align-items-center">
                                            <img loading="lazy"src="{{ Storage::url('images/profiles/' . $rating->user->image) }}"
                                                alt="Seller Profile" width="40" height="40"
                                                class="img-fluid rounded-circle">
                                            <h5 class="fw-semibold ms-3">
                                                {{ $rating->user->name }}
                                            </h5>
                                        </div>
                                        <div class="col mt-2">
                                            <small class="d-block">
                                                <div class="star">
                                                    @for ($x = 0; $x < $rating->rating; $x++)
                                                        <i class="fa fa-star" style="color: orange"></i>
                                                    @endfor
                                                </div>
                                                {{ $rating->created_at->diffForHumans() }}
                                            </small>
                                            <p>
                                                {{ $rating->review }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div id="disqus_thread"></div>
                    <script>
                        (function() { // DON'T EDIT BELOW THIS LINE
                            var d = document,
                                s = d.createElement('script');
                            s.src = 'https://ezcommerce.disqus.com/embed.js';
                            s.setAttribute('data-timestamp', +new Date());
                            (d.head || d.body).appendChild(s);
                        })();
                    </script>
                    <noscript>Please enable JavaScript to view the <a href="https://disqus.com/?ref_noscript">comments
                            powered
                            by Disqus.</a></noscript>
                </div>
            </div>
        </div>
    </main>
    {{-- Modal Image --}}
    <div class="modal fade" id="showImage" tabindex="-1" aria-labelledby="showImageLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen-sm-down modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="showImageLabel">Product Image</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body d-flex justify-content-center align-items-center p-4">
                    <img loading="lazy"src="" id="ImagePreview" class="img-fluid" onclick="openNewTab(this.src)"
                        role="button">
                </div>
            </div>
        </div>
    </div>
    @include('utilities.footer')
    <script src="/js/openNewTab.js"></script>
    <script>
        let isDescMore;

        function descMore() {
            if (!isDescMore) {
                $('p.desc-p').html(
                    `<?= nl2br(e($product->desc)) ?> <a role="button" class="link-success fw-semibold text-decoration-none d-block" onclick="descMore()">Read Less</a>`
                );
                isDescMore = true;
            } else {
                $('p.desc-p').html(
                    (`<?= e($product->desc) ?>`.substring(0, 200)) +
                    '... <a role="button" class="link-success fw-semibold text-decoration-none" onclick="descMore()">Read Less</a>'
                );
                isDescMore = false;
            }
        }
    </script>
    <script>
        $('button#wishlist').click((e) => {
            $.ajax({
                type: "POST",
                url: "/wishlist",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
                data: {
                    product: "{{ $product->slug }}"
                },
                success: function(response) {
                    Toastify({
                        text: response,
                        close: true,
                    }).showToast()
                    $('#wishlist-icon').toggleClass('fa-solid')
                },
                error: function(response) {
                    console.log(response)
                    Toastify({
                        text: response.responseJSON.message,
                        close: true,
                        style: {
                            background: "linear-gradient(to right, salmon, #fecdd3)"
                        }
                    }).showToast()
                }
            });
        })
    </script>

    <script>
        $('#form-buy').on('submit', (e) => {
            e.preventDefault();
            var data = $('#form-buy').serializeArray().reduce(function(obj, item) {
                obj[item.name] = item.value;
                return obj;
            }, {});
            $.ajax({
                type: "POST",
                url: '/cart',
                data: data,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
                success: function(response) {
                    swal.fire({
                        icon: 'success',
                        text: 'Added {{ $product->name }} To Cart'
                    })
                },
                error: function(response) {
                    swal.fire({
                        icon: 'error',
                        text: response.responseText
                    })
                }
            });
        })
    </script>
    <script>
        function imagePreview(src) {
            swal.fire({
                imageUrl: src
            })
        }
    </script>
    <script>
        $('#estimated_shipping > button').click(() => {
            Swal.fire({
                title: "Dapatkan Estimasi Ongkir",
                html: `<span>Pilih Provinsi dan Kota</span><select id="location_province" class="swal2-input form-control"><option>Pilih Provinsi</option></select>
         <select id="location_regency" class="swal2-input form-control"><option>Pilih Kota</option></select>`,
                showCancelButton: true,
                confirmButtonText: "Dapatkan Estimasi",
                showLoaderOnConfirm: true,
                allowOutsideClick: () => !Swal.isLoading(),
                preConfirm: async () => {
                    try {
                        const url = new URL(@json(url('/utilities/getcost')))
                        url.searchParams.append('origin', @json($product->shop->location_id))
                        url.searchParams.append('destination', $('#location_regency').val())
                        url.searchParams.append('weight', @json($product->weight * 10))
                        url.searchParams.append('courier', 'jne')
                        const response = await fetch(url.href)

                        const shippingData = await response.json()

                        const formattedData = shippingData.map(courier => {
                            return `${courier.name} : ${courier.costs.map(cost => `${cost.service} (${cost.description}): ${cost.cost.map(c => `Rp ${c.value} - Estimasi: ${c.etd} hari`).join(', ')}`).join('\n')}`;
                        }).join('<br><br>');


                        Swal.fire({
                            title: 'Estimasi Ongkir',
                            html: formattedData,
                        })

                    } catch (error) {
                        Swal.showValidationMessage(`Request failed: ${error}`);
                    }
                }
            })

            fetch('/utilities/getprovince')
                .then(response => response.json())
                .then(provinces => {
                    $('#location_province').empty();
                    provinces.forEach(data => {
                        $('#location_province').append(
                            `<option value="${data.province_id}" data-prov-id="${data.province_id}">${data.province}</option>`
                        );
                    });
                });

            $('#location_province').on('change', () => {
                const prov = $('#location_province').val();
                $('#location_regency').empty();
                fetch(`/utilities/getcity?province=${prov}`)
                    .then(response => response.json())
                    .then(regencies => {
                        regencies.forEach(data => {
                            $('#location_regency').append(
                                `<option value="${data.city_id}">${data.city_name}</option>`
                            );
                        });
                    });
            })

        });
    </script>
    <script id="dsq-count-scr" src="//ezcommerce.disqus.com/count.js" async></script>
@endsection
