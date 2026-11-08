<?php

namespace Tests\Feature;

use App\Models\KartuStok;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KartuStokTest extends TestCase
{
    use RefreshDatabase;

    public function test_kartu_stok_route_returns_json(): void
    {
        $user = User::factory()->create();

        KartuStok::create([
            'nomor_transaksi' => 'TRX-001',
            'jenis_transaksi' => 'in',
            'nomor_sku' => 'SKU000001',
            'jumlah_masuk' => 10,
            'jumlah_keluar' => 0,
            'stok_akhir' => 10,
            'petugas' => 'admin',
        ]);

        $response = $this->actingAs($user)->get('/kartu-stok/SKU000001');

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [[
                'nomor_transaksi',
                'jenis_transaksi',
                'nomor_sku',
                'jumlah_masuk',
                'jumlah_keluar',
                'stok_akhir',
                'petugas',
                'tanggal',
            ]],
        ]);
    }
}
