<?php

namespace App\View\Components\produk;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Models\VarianProduk;
class FormVarian extends Component
{
    /**
     * Create a new component instance.
     */
    public $id, $produk_id, $nama_varian, $harga_varian, $stok_varian, $action;
    public function __construct($id = null)
    {
        $this->produk_id = request()->route('produk')?->id;
        if ($id) {
            $varian = VarianProduk::findOrFail($id);
            $this->id = $varian->id;
            $this->produk_id = $varian->produk_id;
            $this->nama_varian = $varian->nama_varian;
            $this->harga_varian = $varian->harga_varian;
            $this->stok_varian = $varian->stok_varian;
            $this->action = route('master-data.varian-produk.update', $varian->id);
        } else {
            $this->action = route('master-data.varian-produk.store');
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.produk.form-varian');
    }
}
