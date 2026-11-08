@extends('layout.kai')
@section('page_title', $pageTitle)
@section('content')
    <div class="card py-5">
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nomor Transaksi</th>
                        <th>Produk</th>
                        <th>Nomor Batch</th>
                        <th>Harga Lama</th>
                        <th>Harga Beli</th>
                        <th>Kenaikan Harga</th>
                        <th>Jumlah Barang</th>
                        <th>Konfirmasi</th>
                    </tr>
                </thead>
                @forelse($laporan as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->nomor_transaksi }}</td>
                        <td>{{ $item->varian && $item->varian->produk ? $item->varian->produk->nama_produk : '-' }} -
                            {{ $item->varian ? $item->varian->nama_varian : '-' }}
                        </td>
                        <td>{{ $item->nomor_batch}}</td>
                        <td>{{ number_format($item->harga_lama) }}</td>
                        <td>{{ number_format($item->harga_beli) }}</td>
                        <td>{{ number_format($item->kenaikan_harga) }}</td>
                        <td>{{ number_format($item->jumlah_barang) }} pcs</td>
                        <td>
                            <a href="{{ route('master-data.produk.show', $item->varian->produk->id) }}" class="btn btn-dark">
                                Konfirmasi
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center">Tidak ada data</td>
                    </tr>
                @endforelse
            </table>
        </div>
    </div>
@endsection