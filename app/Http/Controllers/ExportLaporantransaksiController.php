<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ExportLaporanTransaksiRequest;
use App\Exports\ExportBasicLaporanTransaksi;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\TransaksiItems;
use App\Exports\ExportCompletedLaporanTransaksi;

class ExportLaporantransaksiController extends Controller
{
    public function exportLaporanTransaksi(ExportLaporanTransaksiRequest $request)
    {
        $jenisTransaksi = $request->jenis_transaksi;
        $pengirim = $request->pengirim;
        $penerima = $request->penerima;
        $tanggalAwal = $request->tanggal_awal;
        $tanggalAkhir = $request->tanggal_akhir;
        $isCompleted = $request->is_completed;

        if($isCompleted){
            return $this->downloadFullReport($jenisTransaksi, $pengirim, $penerima, $tanggalAwal, $tanggalAkhir);
        } else {
            return $this->downloadBasicReport($jenisTransaksi, $pengirim, $penerima, $tanggalAwal, $tanggalAkhir);
        }
    }

    public function downloadBasicReport($jenisTransaksi, $pengirim, $penerima, $tanggalAwal, $tanggalAkhir){
        $query = \App\Models\Transaksi::query();
        $query->where('jenis_transaksi', $jenisTransaksi);

        if ($jenisTransaksi === 'pemasukan' && $pengirim) {
            $query->where('pengirim', 'like', "%$pengirim%");
        }
        if ($jenisTransaksi === 'pengeluaran' && $penerima) {
            $query->where('penerima', 'like', "%$penerima%");
        }

        if ($tanggalAwal && $tanggalAkhir) {
            $tanggalAwal = \Carbon\Carbon::parse($tanggalAwal)->startOfDay();
            $tanggalAkhir = \Carbon\Carbon::parse($tanggalAkhir)->endOfDay();
            $query->whereBetween('created_at', [$tanggalAwal, $tanggalAkhir]);
        }

        $transaksi = $query->get();
        return Excel::download(new ExportBasicLaporanTransaksi($transaksi, $jenisTransaksi, $tanggalAwal, $tanggalAkhir), 'LAPORAN TRANSAKSI ' . strtoupper($jenisTransaksi) . ' ' . $tanggalAwal->format('d-m-Y') . ' sampai ' . $tanggalAkhir->format('d-m-Y') . '.xlsx');
    }

    public function downloadFullReport($jenisTransaksi, $pengirim, $penerima, $tanggalAwal, $tanggalAkhir){
        $query = TransaksiItems::query();
        $query->whereHas('transaksi', function ($q) use ($jenisTransaksi, $pengirim, $penerima, $tanggalAwal, $tanggalAkhir) {
            $q->where('jenis_transaksi', $jenisTransaksi);

            if ($pengirim && $jenisTransaksi === 'pemasukan') {
                $q->where('pengirim', 'like', "%$pengirim%");
            }
            if ($penerima && $jenisTransaksi === 'pengeluaran') {
                $q->where('penerima', 'like', "%$penerima%");
            }

            if ($tanggalAwal && $tanggalAkhir) {
                $tanggalAwal = \Carbon\Carbon::parse($tanggalAwal)->startOfDay();
                $tanggalAkhir = \Carbon\Carbon::parse($tanggalAkhir)->endOfDay();
                $q->whereBetween('created_at', [$tanggalAwal, $tanggalAkhir]);
            }
        });
        $transaksi = $query->get();
        return Excel::download(new ExportCompletedLaporanTransaksi($transaksi, $jenisTransaksi, $tanggalAwal, $tanggalAkhir), 'LAPORAN TRANSAKSI ' . strtoupper($jenisTransaksi). '.xlsx');
    }
}
