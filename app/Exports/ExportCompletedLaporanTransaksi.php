<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;

class ExportCompletedLaporanTransaksi implements FromCollection, WithHeadings, WithCustomStartCell, WithEvents
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
                'Nomor Transaksi' => $item->transaksi->nomor_transaksi,
                'Tanggal Transaksi' => \Carbon\Carbon::parse($item->transaksi->created_at)->locale('id')->translatedFormat('l, d F Y'),
                $this->jenisTransaksi == 'pemasukan' ? 'Pengirim' : 'Penerima' => $this->jenisTransaksi == 'pemasukan' ? $item->transaksi->pengirim : $item->transaksi->penerima,
                'Kontak' => $item->transaksi->kontak,
                'Produk' => $item->produk,
                'Varian' => $item->varian,
                'Qty' => $item->qty,
                'Harga' => number_format($item->harga),
                'Sub Total' => number_format($item->sub_total),
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
            'Produk',
            'Varian',
            'Qty',
            'Harga',
            'Sub Total'
        ];
    }

    public function startCell(): string
    {
        return 'A4';
    }

    public function registerEvents(): array
    {
        return [
            \Maatwebsite\Excel\Events\AfterSheet::class => function (\Maatwebsite\Excel\Events\AfterSheet $event) {

                $event->sheet->mergeCells('A1:J1');
                $event->sheet->setCellValue('A1', 'LAPORAN TRANSAKSI ' . strtoupper($this->jenisTransaksi));
                $event->sheet->getStyle('A1:J1')->getFont()->setBold(true);
                $event->sheet->getStyle('A1:J1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $periode = '-';
                if ($this->tanggalAwal && $this->tanggalAkhir) {
                    $periode = date('d-m-Y', strtotime($this->tanggalAwal)) . ' s/d ' . date('d-m-Y', strtotime($this->tanggalAkhir));
                }

                $event->sheet->mergeCells('A2:J2');
                $event->sheet->setCellValue('A2', 'Periode: ' . $periode);
                $event->sheet->getStyle('A2')->getFont()->setSize(12);
                $event->sheet->getStyle('A2')->getAlignment()->setHorizontal('center');

                $lastRow = $event->sheet->getDelegate()->getHighestRow();
                $event->sheet->getStyle('A4:J' . $lastRow)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                $event->sheet->getStyle('A4:J' . $lastRow)->getFont()->setSize(12);

                $event->sheet->getStyle('A4:J4')->getFont()->setBold(true);
                $event->sheet->getStyle('A4:J4')->getAlignment()->setHorizontal('center');
            },
        ];
    }
}
