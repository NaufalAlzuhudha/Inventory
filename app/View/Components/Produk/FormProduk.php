<?php

namespace App\View\Components\Produk;

use Closure;
use App\Models\KategoriPeoduk;
use App\Models\Produk;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class FormProduk extends Component
{
    /**
     * Create a new component instance.
     */
    public $id, $nama_produk, $kategori, $kategori_produk_id, $deskripsi_produk, $action;

    public function __construct($id = null)
    {
        $this->kategori = KategoriPeoduk::all();
        if ($id) {
            $produk = Produk::find($id);
            $this->id = $produk->id;
            $this->nama_produk = $produk->nama_produk;
            $this->kategori_produk_id = $produk->kategori_produk_id;
            $this->deskripsi_produk = $produk->deskripsi_produk;
            $this->action = route('master-data.produk.update', $produk->id);
        } else {
            $this->action = route('master-data.produk.store');
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.produk.form-produk');
    }
}
