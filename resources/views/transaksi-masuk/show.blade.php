@extends('layout.kai')
@section('page_title', $pageTitle)
@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <h4 class="card-title">#{{ $transaksi->nomor_transaksi }}</h4>
        </div>    
        <a href="{{ route('transaksi-masuk.index') }}" class="text-primary">Kembali</a>
    </div>
    <div class="card-body">
        <x-meta-item label="Pengirim" value="{{ $transaksi->pengirim }}" />
        <x-meta-item label="Kontak" value="{{ $transaksi->kontak }}" />
        <x-meta-item label="Jumlah Barang" value="{{ number_format($transaksi->jumlah_barang) }}" />
        <x-meta-item label="Total Harga" value="{{ number_format($transaksi->total_harga) }}" />
        <x-meta-item label="Keterangan" value="{{ $transaksi->keterangan }}" />
        <x-meta-item label="Petugas" value="{{ $transaksi->petugas }}" />
        <x-meta-item label="Tanggal Transaksi" value="{{ $transaksi->formated_date }}" />

        <div class="mt-4">
            <h5>Detail Barang</h5>
            <table class="table mt-2">
                <thead>
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">Nomor SKU</th>
                        <th scope="col">Nama Barang</th>
                        <th scope="col">Jumlah</th>
                        <th scope="col">Harga Satuan</th>
                        <th scope="col">Total Harga</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($transaksi->items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->nomor_sku }}</td>
                        <td>{{ $item->nama_barang }}</td>
                        <td>{{ number_format($item->jumlah) }}</td>
                        <td>Rp. {{ number_format($item->harga) }}</td>
                        <td>Rp. {{ number_format($item->sub_total) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="5">Grand Total</th>
                        <th >Rp. {{ number_format($transaksi->total_harga) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection