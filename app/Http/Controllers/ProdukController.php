<?php

namespace App\Http\Controllers;

use App\Http\Requests\storeKategoriProdukRequest;
use App\Http\Requests\storeProdukRequest;
use App\Http\Requests\updateProdukRequest;
use Faker\Provider\Lorem;
use Illuminate\Http\Request;
use App\Models\Produk;

class ProdukController extends Controller
{
    public $pageTitle = "Daftar Produk";

    public function index()
    {
        $query = Produk::query();
        $perPage = request()->query('perPage') ?? 10;
        $pageTitle = $this->pageTitle;
        $search = request()->query('search');


        $query->with('kategori:id,nama_kategori');

        if ($search) {
            $query->where('nama_produk', 'LIKE', '%' . $search . '%');
        }
        $produk = $query->orderBy('created_at', 'desc')->paginate($perPage)->appends(request()->query());
        confirmDelete('Menghapus data produk akan menghapus semua varian yang ada, lanjutkan ?');
        return view('produk.index', compact('pageTitle', 'produk'));
    }
    public function store(storeProdukRequest $request)
    {

        $produk = Produk::create([
            'nama_produk' => $request->nama_produk,
            'deskripsi_produk' => $request->deskripsi_produk,
            'kategori_produk_id' => $request->kategori_produk_id,
        ]);
        toast()->success('Produk berhasil ditambahkan');
        return redirect()->route('master-data.produk.show', $produk->id)->with('success', 'Data berhasil ditambahkan');
    }
    public function show(Produk $produk)
    {
        $pageTitle = $this->pageTitle;
        $produk->load('kategori:id,nama_kategori');
        return view('produk.show', compact('produk', 'pageTitle'));
    }
    public function destroy(Produk $produk)
    {
        $produk->delete();
        toast()->success('Produk berhasil dihapus');
        return redirect()->route('master-data.produk.index')->with('success', 'Data berhasil dihapus');
    }

    public function update(updateProdukRequest $request, Produk $produk)
    {
        $produk->update([
            'nama_produk' => $request->nama_produk,
            'deskripsi_produk' => $request->deskripsi_produk,
            'kategori_produk_id' => $request->kategori_produk_id,
        ]);
        toast()->success('Produk berhasil diupdate');
        return redirect()->route('master-data.produk.index')->with('success', 'Data berhasil diupdate');
    }
}
