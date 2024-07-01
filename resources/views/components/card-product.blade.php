<div class="col mb-3">
    <div class="card-product card border-0 rounded-4 shadow-hover " style="min-height: 24rem;">
        <img loading="lazy"src="{{ asset('storage/images/products/' . json_decode($product->image)[0]) }}"
            class="card-img-top p-2 rounded-4" alt="Product Thumbnail">
        <div class="card-body">
            <h5 title="{{ $product->name }}">
                <a class="stretched-link card-title fw-semibold text-decoration-none link-dark"
                    href="/{{ $product->shop->url . '/' . $product->slug }}">
                    {{ Str::limit($product->name, 25, '...') }}
                </a>
            </h5>
            <span class="h5 fw-bold d-block">Rp
                {{ number_format($product->price, 0, ',', '.') }}</span>
            <small>
                @if ($product->shop->location)
                    {{ json_decode($product->shop->location, 1)['regency'] }}
                @endif
            </small>
            <p class="card-text">
                <i class="bi bi-star-half"></i> {{ $product->getAvgRatings() }} <i class="bi bi-dot"></i>
                Terjual {{ $product->sold }}
            </p>
        </div>
    </div>
</div>
