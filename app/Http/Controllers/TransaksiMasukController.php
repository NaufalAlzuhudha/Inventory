<?php

namespace App\Http\Controllers;

use App\Http\Requests\storeTransaksiMasukRequest;
use App\Models\LaporanKenaikanHarga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\Transaksi;
use Illuminate\Support\Facades\Auth;
use App\Models\VarianProduk;
use App\Models\KartuStok;
use Carbon\Carbon;

class TransaksiMasukController extends Controller
{
    public $pageTitle = 'Transaksi Masuk';
    public $jenisTransaksi = 'pemasukan';

    public function index()
    {
        $pengirim = request()->query('pengirim');
        $tanggalAwal = request()->query('tanggal_awal');
        $tanggalAkhir = request()->query('tanggal_akhir');
        $perPage = request()->query('per_page', 10);

        $query = Transaksi::query();
        $query->orderBy('created_at', 'desc');
        $query->where('jenis_transaksi', $this->jenisTransaksi);

        if ($pengirim) {
            $query->where('pengirim', 'like', "%$pengirim%");
        }

        if ($tanggalAwal && $tanggalAkhir) {
            $tanggalAwal = Carbon::parse($tanggalAwal)->startOfDay();
            $tanggalAkhir = Carbon::parse($tanggalAkhir)->endOfDay();
            $query->whereBetween('created_at', [$tanggalAwal, $tanggalAkhir]);
        }

        $transaksi = $query->paginate($perPage)->appends(request()->query());

        $pageTitle = $this->pageTitle;
        return view('transaksi-masuk.index', compact('pageTitle', 'transaksi'));
    }

    public function create()
    {
        $pageTitle = $this->pageTitle;
        return view('transaksi-masuk.create', compact('pageTitle'));
    }

    public function show($nomor_transaksi)
    {
        $transaksi = Transaksi::with('items')->where('nomor_transaksi', $nomor_transaksi)->first();
        $pageTitle = $this->pageTitle;
        $transaksi->formated_date = Carbon::parse($transaksi->created_at)->locale('id')->translatedFormat('l, d F Y');
        return view('transaksi-masuk.show', compact('pageTitle', 'transaksi'));
    }

    public function store(storeTransaksiMasukRequest $request)
    {
        $nomorTransaksi = Transaksi::generateNomorTransaksi($this->jenisTransaksi);
        $items = $request->items;

        DB::beginTransaction();
        try {
            $transaksi = Transaksi::create([
                'nomor_transaksi' => $nomorTransaksi,
                'jenis_transaksi' => $this->jenisTransaksi,
                'jumlah_barang' => count($items),
                'total_harga' => array_sum(array_column($items, 'subTotal')),
                'keterangan' => $request->keterangan,
                'petugas' => Auth::user()->name,
                'pengirim' => $request->pengirim,
                'kontak' => $request->kontak
            ]);

            foreach ($items as $item) {
                $sku = $item['nomor_sku'] ?? ($item['nomorSKU'] ?? ($item['sku'] ?? null));
                $batch = $item['nomor_batch'] ?? ($item['nomorBatch'] ?? ($item['batch'] ?? null));
                $subTotal = $item['subTotal'] ?? ($item['sub_total'] ?? ($item['qty'] * $item['harga']));
                $query = explode('-', $item['text'] ?? '');

                $varian = VarianProduk::with('produk')->where('nomor_sku', $sku)->first();

                $namaProduk = $varian && $varian->produk ? $varian->produk->nama_produk : trim($query[0] ?? '-');
                $namaVarian = $varian ? $varian->nama_varian : (isset($query[1]) ? trim($query[1]) : '-');

                if ($varian && $item['harga'] > $varian->harga_varian) {
                    LaporanKenaikanHarga::create([
                        'nomor_transaksi' => $nomorTransaksi,
                        'nomor_batch' => $batch,
                        'nomor_sku' => $sku,
                        'harga_lama' => $varian->harga_varian,
                        'harga_beli' => $item['harga'],
                        'kenaikan_harga' => $item['harga'] - $varian->harga_varian,
                        'jumlah_barang' => $item['qty']
                    ]);
                }


                $transaksi->items()->create([
                    'transaksi_id' => $transaksi->id,
                    'produk' => $namaProduk,
                    'varian' => $namaVarian,
                    'nomor_batch' => $batch,
                    'qty' => $item['qty'],
                    'harga' => $item['harga'],
                    'sub_total' => $subTotal,
                    'nomor_sku' => $sku,
                ]);

                if ($varian) {
                    $varian->increment('stok_varian', $item['qty']);
                    KartuStok::create([
                        'nomor_transaksi' => $nomorTransaksi,
                        'nomor_sku' => $sku,
                        'jenis_transaksi' => 'in',
                        'jumlah_masuk' => $item['qty'],
                        'stok_akhir' => $varian->stok_varian,
                        'petugas' => Auth::user()->name,
                    ]);
                }
            }

            DB::commit();
            toast()->success('Transaksi berhasil disimpan');
            return response()->json([
                'success' => true,
                'redirect_url' => route('transaksi-masuk.create'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan transaksi: ' . $e->getMessage(),
            ], 500);
        }
    }
}
