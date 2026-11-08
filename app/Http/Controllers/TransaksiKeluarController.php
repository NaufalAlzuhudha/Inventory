<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\KartuStok;
use App\Models\VarianProduk;
use App\Models\LaporanKenaikanHarga;
use App\Http\Requests\storeTransaksiKeluarRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransaksiKeluarController extends Controller
{

    public $pageTitle = 'Transaksi Keluar';
    public $jenisTransaksi = 'pengeluaran';

    public function index()
    {
        $penerima = request()->query('penerima');
        $tanggalAwal = request()->query('tanggal_awal');
        $tanggalAkhir = request()->query('tanggal_akhir');
        $perPage = request()->query('per_page', 10);

        $query = Transaksi::query();
        $query->orderBy('created_at', 'desc');
        $query->where('jenis_transaksi', $this->jenisTransaksi);

        if ($penerima) {
            $query->where('penerima', 'like', "%$penerima%");
        }

        if ($tanggalAwal && $tanggalAkhir) {
            $tanggalAwal = Carbon::parse($tanggalAwal)->startOfDay();
            $tanggalAkhir = Carbon::parse($tanggalAkhir)->endOfDay();
            $query->whereBetween('created_at', [$tanggalAwal, $tanggalAkhir]);
        }

        $transaksi = $query->paginate($perPage)->appends(request()->query());

        $pageTitle = $this->pageTitle;
        return view('transaksi-keluar-index', compact('pageTitle', 'transaksi'));
    }

    public function create()
    {
        $pageTitle = $this->pageTitle;
        return view('transaksi-keluar.create', compact('pageTitle'));
    }

    public function show($nomor_transaksi)
    {
        $transaksi = Transaksi::with('items')->where('nomor_transaksi', $nomor_transaksi)->first();
        $pageTitle = $this->pageTitle;
        $transaksi->formated_date = Carbon::parse($transaksi->created_at)->locale('id')->translatedFormat('l, d F Y');
        return view('transaksi-keluar.show', compact('pageTitle', 'transaksi'));
    }

    public function store(storeTransaksiKeluarRequest $request)
    {
        $nomorTransaksi = Transaksi::generateNomorTransaksi($this->jenisTransaksi);
        $items = $request->items;

        DB::beginTransaction();
        try {
            $transaksi = Transaksi::create([
                'nomor_transaksi' => $nomorTransaksi,
                'jenis_transaksi' => $this->jenisTransaksi,
                'jumlah_barang' => count($items),
                'total_harga' => 0,
                'keterangan' => $request->keterangan,
                'petugas' => Auth::user()->name,
                'penerima' => $request->penerima,
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


                $transaksi->items()->create([
                    'transaksi_id' => $transaksi->id,
                    'produk' => $namaProduk,
                    'varian' => $namaVarian,
                    'qty' => $item['qty'],
                    'harga' => $varian->harga_varian,
                    'sub_total' => $varian->harga_varian * $item['qty'],
                    'nomor_sku' => $sku,
                ]);

                if ($varian) {
                    $varian->decrement('stok_varian', $item['qty']);
                    KartuStok::create([
                        'nomor_transaksi' => $nomorTransaksi,
                        'nomor_sku' => $sku,
                        'jenis_transaksi' => 'out',
                        'jumlah_keluar' => $item['qty'],
                        'stok_akhir' => $varian->stok_varian,
                        'petugas' => Auth::user()->name,
                    ]);
                }
                $transaksi->total_harga += $varian->harga_varian * $item['qty'];
                $transaksi->save();
            }

            DB::commit();
            toast()->success('Transaksi berhasil disimpan');
            return response()->json([
                'success' => true,
                'redirect_url' => route('transaksi-keluar.create'),
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
