<?php

namespace App\Http\Controllers;

use App\Http\Requests\storeVarianProdukRequest;
use App\Http\Requests\updateVarianProdukRequest;
use App\Models\LaporanKenaikanHarga;
use App\Models\VarianProduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\KartuStok;
use Illuminate\Support\Facades\Auth;

class VarianProdukController extends Controller
{
    public function store(storeVarianProdukRequest $request)
    {
        $fileName = time() . '.' . $request->file('gambar_varian')->getClientOriginalExtension();
        Storage::disk('public')->putFileAs('varian-produk', $request->file('gambar_varian'), $fileName);
        VarianProduk::create([
            'nama_varian' => $request->nama_varian,
            'produk_id' => $request->produk_id,
            'gambar_varian' => $fileName,
            'harga_varian' => $request->harga_varian,
            'stok_varian' => $request->stok_varian,
            'nomor_sku' => VarianProduk::generateNomorSku(),
        ]);
        return response()->json([
            'message' => 'Varian Produk berhasil ditambahkan',
        ]);
    }

    public function update(updateVarianProdukRequest $request, $varian_produk)
    {
        $isAdjusment = false;
        $varian = VarianProduk::findOrFail($varian_produk);
        $existKenaikanHarga = LaporanKenaikanHarga::where('nomor_sku', $varian->nomor_sku)->where('is_confirmed', false)->first();

        if ($request->stok_varian != $varian->stok_varian) {
            $isAdjusment = true;
        }

        $fileName = $varian->gambar_varian;
        if ($request->file('gambar_varian')) {
            Storage::disk('public')->delete('varian-produk/' . $varian->gambar_varian);
            $fileName = time() . '.' . $request->file('gambar_varian')->getClientOriginalExtension();
            Storage::disk('public')->putFileAs('varian-produk', $request->file('gambar_varian'), $fileName);
        }

        $varian->update([
            'nama_varian' => $request->nama_varian,
            'harga_varian' => $request->harga_varian,
            'stok_varian' => $request->stok_varian,
            'gambar_varian' => $fileName,
        ]);

        if ($existKenaikanHarga && $request->harga_varian >= $existKenaikanHarga->harga_beli) {
            $existKenaikanHarga->update([
                'is_confirmed' => true
            ]);
        }

        if ($isAdjusment) {
            KartuStok::create([
                'jenis_transaksi' => 'adjustment',
                'nomor_sku' => $varian->nomor_sku,
                'stok_akhir' => $varian->stok_varian,
                'petugas' => Auth::user()->name,
            ]);
        }

        return response()->json([
            'message' => 'Varian Produk berhasil diupdate',
        ]);
    }

    public function destroy($varian_produk)
    {
        $varian = VarianProduk::findOrFail($varian_produk);
        Storage::disk('public')->delete('varian-produk/' . $varian->gambar_varian);
        $varian->delete();
        toast()->success('Data Berhasil Dihapus');
        return redirect()->route('master-data.produk.show', $varian->produk_id);
    }

    public function getAllVarianJson()
    {
        $search = request()->query('search');
        $varians = VarianProduk::with('produk')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama_varian', 'like', '%' . $search . '%')
                        ->orWhere('nomor_sku', 'like', '%' . $search . '%')
                        ->orWhereHas('produk', function ($p) use ($search) {
                            $p->where('nama_produk', 'like', '%' . $search . '%');
                        });
                });
            })
            ->get()
            ->map(function ($q) {
                $namaProduk = $q->produk ? $q->produk->nama_produk : 'Tanpa Produk';
                return [
                    'id' => $q->id,
                    'text' => "{$namaProduk} - {$q->nama_varian} ({$q->nomor_sku})",
                    'harga' => $q->harga_varian,
                    'stok' => $q->stok_varian,
                    'nomor_sku' => $q->nomor_sku,
                ];
            });
        return response()->json($varians);
    }
}
