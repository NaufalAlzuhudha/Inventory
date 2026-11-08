<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KartuStok extends Model
{
    protected $table = 'kartu_stoks';
    protected $guarded = ['id'];

    protected $fillable = [
        'nomor_transaksi',
        'jenis_transaksi',
        'nomor_sku',
        'jumlah_masuk',
        'jumlah_keluar',
        'stok_akhir',
        'petugas',
    ];
}
