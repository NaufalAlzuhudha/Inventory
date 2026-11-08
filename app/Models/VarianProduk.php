<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VarianProduk extends Model
{

    protected $fillable = [
        'nama_varian',
        'produk_id',
        'gambar_varian',
        'harga_varian',
        'stok_varian',
        'nomor_sku',
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }

    public static function generateNomorSku()
    {
        $maxId = self::max('id');
        $prefix = 'SKU';
        $nomorSku = $prefix . str_pad($maxId + 1, 6, '0', STR_PAD_LEFT);
        return $nomorSku;
    }
}
