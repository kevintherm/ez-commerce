@extends('layouts.main')

@push('head')
    <script>
        jQuery(document).ready(function($) {

            $(".btnrating").on('click', (function(e) {

                var previous_value = $("#selected_rating").val();

                var selected_value = $(this).attr("data-attr");
                $("#selected_rating").val(selected_value);

                $(".selected-rating").empty();
                $(".selected-rating").html(selected_value);

                for (i = 1; i <= selected_value; ++i) {
                    $("#rating-star-" + i).toggleClass('btn-warning');
                    $("#rating-star-" + i).toggleClass('btn-default');
                }

                for (ix = 1; ix <= previous_value; ++ix) {
                    $("#rating-star-" + ix).toggleClass('btn-warning');
                    $("#rating-star-" + ix).toggleClass('btn-default');
                }

            }));


        });
    </script>
@endpush

@section('content')
    @include('layouts.navbar')

    <main>
        <div class="container-xl py-3">
            @include('utilities.breadcrumb', ['title' => $title])

            <a href="/orders" class="btn btn-outline-warning btn-sm d-print-none"><i class="bi bi-arrow-left"></i> Back</a>

            <div class="box my-4">
                <div class="card shadow border-0 rounded-4">
                    <div class="card-body">
                        <div id="product-details" class="display-flex align-items-center">
                            <img src="{{ Storage::url('images/products/' . $product->getFirstImage()) }}"
                                alt="Image Thumbnail" width="10%" class="w-full">
                            <h5>{{ $product->name }}</h5>
                            <a href="{{ url($product->shop->url . '/' . $product->slug) }}">
                                Lihat produk
                            </a>
                        </div>

                        <form action="{{ route('ratings.store', [$order->number, $product->slug]) }}" method="POST">
                            @csrf
                            <div class="form-group" id="rating-ability-wrapper">
                                <label class="control-label" for="rating">
                                    <span class="field-label-header">Berikan Rating dari 1 - 5 untuk produk ini.</span><br>
                                    <span class="field-label-info"></span>
                                    <input type="hidden" id="selected_rating" name="selected_rating" value=""
                                        required="required">
                                </label>
                                <h2 class="bold rating-header" style="">
                                    <span class="selected-rating">0</span><small> / 5</small>
                                </h2>
                                <button type="button" class="btnrating btn btn-default btn-lg" data-attr="1"
                                    id="rating-star-1">
                                    <i class="fa fa-star" aria-hidden="true"></i>
                                </button>
                                <button type="button" class="btnrating btn btn-default btn-lg" data-attr="2"
                                    id="rating-star-2">
                                    <i class="fa fa-star" aria-hidden="true"></i>
                                </button>
                                <button type="button" class="btnrating btn btn-default btn-lg" data-attr="3"
                                    id="rating-star-3">
                                    <i class="fa fa-star" aria-hidden="true"></i>
                                </button>
                                <button type="button" class="btnrating btn btn-default btn-lg" data-attr="4"
                                    id="rating-star-4">
                                    <i class="fa fa-star" aria-hidden="true"></i>
                                </button>
                                <button type="button" class="btnrating btn btn-default btn-lg" data-attr="5"
                                    id="rating-star-5">
                                    <i class="fa fa-star" aria-hidden="true"></i>
                                </button>
                            </div>
                            <textarea name="review" cols="30" rows="10" class="form-control mt-3" placeholder="Tuliskan pengalaman anda"></textarea>
                            <button class="btn btn-secondary mt-3">
                                Kirim
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </main>
@endsection
