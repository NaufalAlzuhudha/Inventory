<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Carbon\Carbon;

class ExportBasicLaporanTransaksi implements FromCollection, WithHeadings, WithCustomStartCell, WithEvents
{
    /**
     * @return \Illuminate\Support\Collection
     */

    public $transaksi,
    $jenisTransaksi,
    $tanggalAwal,
    $tanggalAkhir;

    public function __construct($transaksi, $jenisTransaksi, $tanggalAwal, $tanggalAkhir)
    {
        $this->transaksi = $transaksi;
        $this->jenisTransaksi = $jenisTransaksi;
        $this->tanggalAwal = $tanggalAwal;
        $this->tanggalAkhir = $tanggalAkhir;
    }

    public function collection()
    {
        $result = collect();
        $data = $this->transaksi;
        foreach ($data as $index => $item) {
            $result->push([
                'No' => $index + 1,
                'Nomor Transaksi' => $item->nomor_transaksi,
                'Tanggal Transaksi' => Carbon::parse($item->created_at)->locale('id')->translatedFormat('l, d F Y'),
                $this->jenisTransaksi == 'pemasukan' ? 'Pengirim' : 'Penerima' => $this->jenisTransaksi == 'pemasukan' ? $item->pengirim : $item->penerima,
                'Kontak' => $item->kontak,
                'Petugas' => $item->petugas,
                'jumlah Barang' => $item->jumlah_barang,
                'Total Harga' => number_format($item->total_harga),
                'keterangan' => $item->keterangan,
            ]);
        }
        return $result;
    }

    public function headings(): array
    {
        return [
            'No',
            'Nomor Transaksi',
            'Tanggal Transaksi',
            $this->jenisTransaksi == 'pemasukan' ? 'Pengirim' : 'Penerima',
            'Kontak',
            'Petugas',
            'jumlah Barang',
            'Total Harga',
            'keterangan'
        ];
    }

    public function startCell(): string
    {
        return 'A4';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->setCellValue('A1', 'LAPORAN TRANSAKSI ' . strtoupper($this->jenisTransaksi));
                $event->sheet->mergeCells('A1:I1');
                $event->sheet->mergeCells('A2:I2');
                $event->sheet->getStyle('A1:I1')->getFont()->setBold(true)->setSize(14);
                $event->sheet->getStyle('A2:I2')->getFont()->setBold(true)->setSize(12);

                $periode = '-';
                if ($this->tanggalAwal && $this->tanggalAkhir) {
                    $periode = date('d-m-Y', strtotime($this->tanggalAwal)) . ' s/d ' . date('d-m-Y', strtotime($this->tanggalAkhir));
                }

                $event->sheet->setCellValue('A2', 'Periode: ' . $periode);

                $lastRow = $event->sheet->getDelegate()->getHighestRow();
                $event->sheet->getStyle('A4:I' . $lastRow)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                $event->sheet->getStyle('A4:I' . $lastRow)->getFont()->setSize(12);

                $event->sheet->getStyle('A4:I4')->getFont()->setBold(true);
                $event->sheet->getStyle('A4:I4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            }
        ];
    }
}
