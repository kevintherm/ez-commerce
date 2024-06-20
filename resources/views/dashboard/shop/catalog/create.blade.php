@extends('dashboard.layouts.main')

@push('head')
    <link rel="stylesheet" href="/css/hover.css">
    <script src="/js/curr_format.js" defer></script>
@endpush

@push('foot')
    <script>
        $('#name').on('input change', ({
            currentTarget
        }) => {
            $.ajax({
                type: "get",
                url: "/utilities/getslug",
                data: {
                    string: currentTarget.value
                },
                success: function(response) {
                    $('#slug').val(response.slug)
                    $('#catalog_url').text(response.slug)
                }
            });
        })
    </script>
@endpush

@section('main')
    <div class="main p-3">
        @include('utilities.breadcrumb', ['title' => 'Post Produk Baru'])

        <div class="card shadow-hover">
            <div class="card-header">
                <i class="fa-solid fa-plus"></i> Tambah Katalog Baru
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col">

                        {{-- Main Form --}}
                        <form action="{{ route('catalogs.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="slug" id="slug">
                            <div class="mb-3">
                                <label for="name">Nama Katalog</label>
                                <input type="text" id="name" maxlength="75"
                                    class="form-control @error('name') is-invalid @enderror" name="name"
                                    value="{{ old('name') }}" placeholder="ex: Laptop Gaming" required>
                                @error('name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                                <code>
                                    <div class="text-muted d-inline">URL Katalog :</div>
                                    {{ env('APP_URL') }}/{{ auth()->user()->shop->url }}/catalog/<span
                                        id="catalog_url">[NamaKatalog]</span>
                                </code>
                            </div>
                            <div class="mb-3">
                                <label for="desc">Deskripsi Katalog</label>
                                <textarea type="text" id="desc" class="form-control @error('desc') is-invalid @enderror" name="desc"
                                    placeholder="ex: Katalog Yang Berisi Laptop Laptop Gaming">{{ old('desc') }}</textarea>
                                @error('desc')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <button class="btn btn-outline-primary px-4" type="submit">Buat</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $('form').on('submit', (e) => {
            e.preventDefault()
            var data = $('form').serializeArray().reduce(function(obj, item) {
                obj[item.name] = item.value;
                return obj;
            }, {});
            $.ajax({
                type: "post",
                url: "{{ route('catalogs.store') }}",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name=_token]').attr('content')
                },
                data: data,
                success: function(response) {
                    console.log(response)
                    Toastify({
                        text: 'Success',
                        close: true,
                        onClick: () => {
                            location.href = '/shop'
                        }
                    }).showToast()
                    location = '/shop'
                },
                error: (e) => {
                    console.log(e)
                    Toastify({
                        text: e.responseJSON.message || e.responseText
                    }).showToast();
                }
            });
        })
    </script>
@endsection
