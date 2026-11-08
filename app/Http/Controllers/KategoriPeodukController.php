<?php

namespace App\Http\Controllers;

use App\Http\Requests\storeKategoriProdukRequest;
use App\Http\Requests\updateKategoriProdukRequest;
use App\Models\KategoriPeoduk;
use Illuminate\Http\Request;

class KategoriPeodukController extends Controller
{
    public $pageTitle = "Kategori Produk";

    public function index()
    {
        $pageTitle = $this->pageTitle;
        $perPage = request()->query('perPage') ?? 10;
        $search = request()->query('search');
        $query = KategoriPeoduk::query();
        if ($search) {
            $query->where('nama_kategori', 'LIKE', '%' . $search . '%');
        }
        $kategori = $query->paginate($perPage)->appends(request()->query());
        confirmDelete('Hapus Data', 'Hapus data kategori Produk tidak dapat dibatalkan, lanjutkan ?');
        return view('kategori-produk.index', compact('pageTitle', 'kategori'));
    }
    public function store(storeKategoriProdukRequest $request)
    {
        KategoriPeoduk::create([
            'nama_kategori' => $request->nama_kategori,
        ]);
        toast()->success('Data berhasil ditambahkan');
        return redirect()->route('master-data.kategori-produk.index');
    }

    public function update(updateKategoriProdukRequest $request, KategoriPeoduk $kategori_produk)
    {
        $kategori_produk->nama_kategori = $request->nama_kategori;
        $kategori_produk->save();
        toast()->success('Data berhasil diupdate');
        return redirect()->route('master-data.kategori-produk.index');
    }

    public function destroy(KategoriPeoduk $kategori_produk)
    {
        $kategori_produk->delete();
        toast()->success('Data berhasil dihapus');
        return redirect()->route('master-data.kategori-produk.index');
    }
}
