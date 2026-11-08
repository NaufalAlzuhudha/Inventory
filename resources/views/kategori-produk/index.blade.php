@extends('layout.kai')

@section('page_title', $pageTitle)

@section('content')
    <div class="card">
        <div class="card-body py-5">
            <div class="row">
                {{-- Filter --}}
                <div class="row col-10">
                    <div class="col-1">
                        <x-per-page-option />
                    </div>
                    <div class="col-9">
                        <x-filter-by-field term="search" placeholder="Cari Kategori Produk" />
                    </div>
                    <div class="col-1">
                        <x-button-reset-filter route="master-data.kategori-produk.index" />
                    </div>
                </div>
                {{-- End Filter --}}
                <div class="col-2 d-flex justify-content-end">
                    <x-kategori-produk.form-kategori-produk />
                </div>
            </div>
            <div class="table-responsive">
                <table class="table mt-5">
                    <thead>
                        <tr>
                            <th style="width: 5%;">NO</th>
                            <th>NAMA KATEGORI</th>
                            <th class="text-end" style="width: 100px;">OPSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($kategori as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->nama_kategori }}</td>
                                <td>
                                    <div class="d-flex align-center gap-1">
                                        <x-kategori-produk.form-kategori-produk id="{{ $item->id }}" />
                                        <x-confirm-delete route="master-data.kategori-produk.destroy" id="{{ $item->id }}" />
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-4">Data Kategori Kosong</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                {{ $kategori->links() }}
            </div>
        </div>
    </div>
@endsection