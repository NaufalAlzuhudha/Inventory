@extends('layout.kai')

@section('page_title', $pageTitle)

@section('content')
    <div class="card">
        <div class="card-body py-5">
            <div class="row align-item_center">
                {{-- filter --}}
                <div class="row col-10 align-items-center justify-content-between">
                    <div class="col-1">
                        <x-per-page-option />
                    </div>
                    <div class="col-8">
                        <x-filter-by-field term="search" placeholder="Cari Produk..." />
                    </div>
                    <div class="col-2">
                        <x-button-reset-filter route="master-data.produk.index" />
                    </div>
                </div>
                {{-- end filter --}}

                {{-- form --}}
                <div class="col-2 d-flex justify-content-end">
                    <x-produk.form-produk />
                </div>
                {{-- end form --}}
            </div>
            <table class="table mt-5">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 15px">No</th>
                        <th class="">Produk</th>
                        <th class="">Kategori</th>
                        <th class="text-center" style="width: 100px">Opsi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($produk as $index => $item)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class=""><a href="{{ route('master-data.produk.show', $item->id) }}"
                                    class="text-decoration-none">{{ $item->nama_produk }}</a></td>
                            <td class="">{{ $item->kategori?->nama_kategori ?? '-' }}</td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <x-produk.form-produk id="{{ $item->id }}" />
                                    <x-confirm-delete id="{{ $item->id }}" route="master-data.produk.destroy" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4">Data Produk tidak tersedia</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $produk->links() }}
        </div>
    </div>
@endsection