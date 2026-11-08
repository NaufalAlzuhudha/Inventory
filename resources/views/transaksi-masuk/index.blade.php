@extends ('layout.kai')
@section ('page_title', $pageTitle)
@section ('content')
<div class="card py-5">
    <div class="card-body">
        <div class="row align-items-end">
            <div class="col-11">
            <form action="{{ route('transaksi-masuk.index') }}" method="GET" class="row col-12 align-items-center">
                <div class="col-4">
                    <label for="pengirim" class="form-label">Pengirim</label>
                    <input type="text" class="form-control" id="pengirim" name="pengirim" value="{{ request('pengirim') }}">
                </div>
                <div class="col-3">
                    <label for="tanggal_awal" class="form-label">Tanggal Awal</label>
                    <input type="date" class="form-control" id="tanggal_awal" name="tanggal_awal" value="{{ request('tanggal_awal') }}">
                </div>
                <div class="col-3">
                    <label for="tanggal_akhir" class="form-label">Tanggal Akhir</label>
                    <input type="date" class="form-control" id="tanggal_akhir" name="tanggal_akhir" value="{{ request('tanggal_akhir') }}">
                </div>
                <div class="col-2 d-flex gap-1">
                    <div>
                        <label for="filter" class="form-label d-block">&nbsp;</label>
                        <button type="submit" class="btn btn-icon btn-round border w-100">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                    <div>
                        <label for="reset" class="form-label d-block">&nbsp;</label>
                        <button type="reset" id="btn-reset" class="btn btn-outline-danger btn-round border w-100" onclick="window.location.href='{{ route('transaksi-masuk.index') }}'">
                            Reset
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-1">
            <x-form-export-laporan jenisTransaksi="pemasukan" />
        </div>
        </div>
        <div>
            <table class="table mt-5">
                <thead>
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">Nomor Transaksi</th>
                        <th scope="col">Jumlah Barang</th>
                        <th scope="col">Total Harga</th>
                        <th scope="col">Pengirim</th>
                        <th scope="col">Tanggal Transaksi</th>
                        <th scope="col">Detail</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transaksi as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->nomor_transaksi }}</td>
                        <td>{{ number_format($item->jumlah_barang) }}</td>
                        <td>{{ number_format($item->total_harga) }}</td>
                        <td>{{ $item->pengirim }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->created_at)->locale('id')->translatedFormat('l, d F Y') }}</td>
                        <td><a href="{{ route('transaksi-masuk.show', $item->nomor_transaksi) }}" class="btn btn-primary">Detail</a></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">Tidak ada data transaksi masuk.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection