<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanKenaikanHarga;

class LaporanKenaikanHargaController extends Controller
{
    public function index()
    {
        $pageTitle = "Laporan Kenaikan Harga";
        $laporan = LaporanKenaikanHarga::with('varian', 'varian.produk')->where('is_confirmed', false)->get();
        return view('laporan-kenaikan-harga.index', compact('pageTitle', 'laporan'));
    }
}
